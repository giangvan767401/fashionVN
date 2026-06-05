<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TryOnController extends Controller
{
    /**
     * Hugging Face IDM-VTON Gradio Space base URL.
     * Configurable via HF_TRYON_SPACE_URL in .env
     */
    private function spaceUrl(): string
    {
        return rtrim(config('services.huggingface.space_url',
            'https://kwai-kolors-kolors-virtual-try-on.hf.space'), '/');
    }

    /**
     * Wake up the HF Space if it is sleeping (cold start).
     */
    private function wakeUpSpace(string $apiToken): void
    {
        try {
            Http::timeout(30)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiToken])
                ->get($this->spaceUrl() . '/');
            sleep(3);
            Log::info('TryOn: Space wake-up ping sent.');
        } catch (\Exception $e) {
            Log::warning('TryOn: Wake-up ping failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the try-on upload page for a given product.
     */
    public function index($productSlug)
    {
        $product = \App\Models\Product::with('images', 'categories')
            ->where('slug', $productSlug)
            ->firstOrFail();

        // Auto-detect clothing category from product categories
        $detectedCategory = 'tops';
        if ($product->categories) {
            foreach ($product->categories as $category) {
                $name = mb_strtolower($category->name);
                if (
                    str_contains($name, 'đầm') || str_contains($name, 'váy liền') ||
                    str_contains($name, 'one-piece') || str_contains($name, 'jumpsuit') ||
                    str_contains($name, 'set bộ')
                ) {
                    $detectedCategory = 'one-pieces';
                    break;
                } elseif (
                    str_contains($name, 'quần') || str_contains($name, 'chân váy') ||
                    str_contains($name, 'bottom') || str_contains($name, 'skirt') ||
                    str_contains($name, 'pant')
                ) {
                    $detectedCategory = 'bottoms';
                    break;
                } elseif (
                    str_contains($name, 'áo') || str_contains($name, 'top') ||
                    str_contains($name, 'shirt') || str_contains($name, 't-shirt')
                ) {
                    $detectedCategory = 'tops';
                    break;
                }
            }
        }

        return view('tryon.index', compact('product', 'detectedCategory'));
    }

    /**
     * Process the try-on request and call Hugging Face IDM-VTON API.
     *
     * Flow:
     * 1. Upload both images to HF Space via /upload endpoint
     * 2. Join the prediction queue via /queue/join with SSE v3 protocol
     * 3. Poll /queue/data SSE stream for process_completed event
     * 4. Extract result image URL and download to local storage
     */
    public function process(Request $request)
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $product = \App\Models\Product::with('images')->findOrFail($request->product_id);
        // Get primary garment image (or first available)
        $garmentImage = $product->images()->where('is_primary', true)->first()
            ?? $product->images()->first();

        if (!$garmentImage) {
            return back()->with('error', 'Sản phẩm chưa có ảnh để thử đồ.');
        }

        // Save uploaded user photo to temporary local storage
        $userPhotoPath    = $request->file('user_photo')->store('tryon/temp', 'public');
        $userPhotoAbsPath = Storage::disk('public')->path($userPhotoPath);

        // Resolve garment image to an absolute local path (handles remote URLs)
        $garmentUrl       = $garmentImage->url;
        $garmentAbsPath   = $this->resolveGarmentPath($garmentUrl);

        if (empty($garmentAbsPath) || !file_exists($garmentAbsPath)) {
            Storage::disk('public')->delete($userPhotoPath);
            return back()->with('error', 'Không thể tải ảnh sản phẩm. Vui lòng thử lại.');
        }

        // Check if the Hugging Face API token is configured
        $apiToken = config('services.huggingface.token');
        $hasToken = !empty($apiToken) && $apiToken !== 'your_huggingface_token_here';

        if (!$hasToken) {
            // ── DEMO / MOCK MODE ─────────────────────────────────────────────
            // No API token provided — show a side-by-side preview so the developer
            // can still verify the full UI flow works correctly.
            sleep(2);
            $resultPath = 'tryon/results/' . basename($userPhotoPath);
            Storage::disk('public')->copy($userPhotoPath, $resultPath);
            Storage::disk('public')->delete($userPhotoPath);

            return view('tryon.result', [
                'resultUrl'  => asset('storage/' . $resultPath),
                'garmentUrl' => $garmentUrl,
                'product'    => $product,
                'isMock'     => true,
            ]);
        }

        // ── REAL API MODE ─────────────────────────────────────────────────────
        $tempFilesToDelete = [];
        try {
            set_time_limit(300);
            $this->wakeUpSpace($apiToken);

            // Convert images if they are not standard JPEG/PNG formats
            $userPhotoAbsPath = $this->ensureCompatibleImage($userPhotoAbsPath, $tempFilesToDelete);
            $garmentAbsPath   = $this->ensureCompatibleImage($garmentAbsPath, $tempFilesToDelete);

            $spaceUrl = $this->spaceUrl();
            Log::info("TryOn: Starting process with Space URL: {$spaceUrl}");

            // Step 1: Upload human photo
            Log::info('TryOn: Uploading human photo to HF Space...');
            $humanFileData = $this->uploadToHFSpace($userPhotoAbsPath, 'human.jpg', $apiToken);

            if (!$humanFileData) {
                Storage::disk('public')->delete($userPhotoPath);
                foreach ($tempFilesToDelete as $tmpFile) {
                    @unlink($tmpFile);
                }
                return back()->with('error', 'Không thể tải ảnh của bạn lên dịch vụ AI. Vui lòng thử lại.');
            }
            Log::info('TryOn: Human photo uploaded successfully.', ['path' => $humanFileData['path']]);

            // Step 2: Upload garment photo
            Log::info('TryOn: Uploading garment photo to HF Space...');
            $garmentFileData = $this->uploadToHFSpace($garmentAbsPath, 'garment.jpg', $apiToken);

            if (!$garmentFileData) {
                Storage::disk('public')->delete($userPhotoPath);
                foreach ($tempFilesToDelete as $tmpFile) {
                    @unlink($tmpFile);
                }
                return back()->with('error', 'Không thể tải ảnh sản phẩm lên dịch vụ AI. Vui lòng thử lại.');
            }
            Log::info('TryOn: Garment photo uploaded successfully.', ['path' => $garmentFileData['path']]);

            // Step 3: Submit prediction to the Gradio queue
            $sessionHash = Str::lower(Str::random(11));
            $garmentDescription = $product->name ?? 'A high quality clothing item';

            Log::info("TryOn: Joining queue with session: {$sessionHash}");

            // Kolors Virtual Try-On /queue/join:
            // fn_index=2, data=[human_image, garment_image, seed, randomize_seed]
            $queuePayload = [
                'data' => [
                    $humanFileData,            // Human photo (FileData)
                    $garmentFileData,          // Garment image (FileData)
                    0,                         // Seed (0 = random)
                    true,                      // randomize_seed (bool)
                ],
                'event_data'   => null,
                'fn_index'     => 2,
                'trigger_id'   => 26,
                'session_hash' => $sessionHash,
            ];

            Log::info('TryOn: Queue payload prepared.', [
                'fn_index' => 2,
                'trigger_id' => 26,
            ]);

            $queueResponse = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type'  => 'application/json',
                ])
                ->post($spaceUrl . '/queue/join', $queuePayload);

            if (!$queueResponse->successful()) {
                Log::error('TryOn: HF queue join failed.', [
                    'status' => $queueResponse->status(),
                    'body'   => $queueResponse->body(),
                ]);
                Storage::disk('public')->delete($userPhotoPath);
                foreach ($tempFilesToDelete as $tmpFile) {
                    @unlink($tmpFile);
                }
                return back()->with('error', 'Không thể kết nối dịch vụ AI (HTTP ' . $queueResponse->status() . '). Vui lòng thử lại sau ít phút.');
            }

            Log::info('TryOn: Queue join successful. Polling SSE for result...');

            // Step 4: Poll the SSE stream for the completed result
            $resultImageUrl = $this->pollHFSpaceResult($sessionHash, $apiToken);

            // Clean up user temp file and any converted temp files
            Storage::disk('public')->delete($userPhotoPath);
            foreach ($tempFilesToDelete as $tmpFile) {
                @unlink($tmpFile);
            }

            if (!$resultImageUrl) {
                return back()->with('error', 'Xử lý ảnh thất bại hoặc quá thời gian chờ. Vui lòng thử lại.');
            }

            Log::info('TryOn: Raw result URL: ' . $resultImageUrl);

            // Step 5: Download result image to local storage for reliable display
            $localResultUrl = $this->downloadResultImage($resultImageUrl, $apiToken);
            $finalUrl = $localResultUrl ?: $resultImageUrl;

            Log::info('TryOn: Final result URL: ' . $finalUrl);

            return view('tryon.result', [
                'resultUrl'  => $finalUrl,
                'garmentUrl' => $garmentUrl,
                'product'    => $product,
                'isMock'     => false,
            ]);

        } catch (\Exception $e) {
            Log::error('TryOn Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            Storage::disk('public')->delete($userPhotoPath);
            foreach ($tempFilesToDelete as $tmpFile) {
                @unlink($tmpFile);
            }
            return back()->with('error', 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Standardize and optimize images before uploading to HF Space.
     * Resizes the image to maximum 1024x1024 preserving aspect ratio and converts to JPEG.
     * Keeps track of converted files in $tempFilesToDelete for cleanup.
     */
    private function ensureCompatibleImage(string $path, array &$tempFilesToDelete): string
    {
        if (!file_exists($path)) {
            return $path;
        }

        Log::info("TryOn: Optimizing and resizing image at {$path}...");

        $outputPath = storage_path('app/tryon_converted_' . Str::random(10) . '.jpg');
        $nodeScript = base_path('convert_image.js');
        
        $command = 'node ' . escapeshellarg($nodeScript) . ' ' . escapeshellarg($path) . ' ' . escapeshellarg($outputPath);
        
        $output = [];
        $returnVar = -1;
        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($outputPath)) {
            Log::info("TryOn: Image successfully optimized: {$outputPath}");
            $tempFilesToDelete[] = $outputPath;
            return $outputPath;
        }

        Log::error("TryOn: Image optimization failed with exit code {$returnVar}. Output: " . implode("\n", $output));
        return $path; // Fallback to original path on failure
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Upload a local image file to the Hugging Face Gradio Space's /upload endpoint.
     * Returns the Gradio FileData dictionary required by the predict payload,
     * or null on failure.
     */
    private function uploadToHFSpace(string $localPath, string $fileName, string $apiToken): ?array
    {
        try {
            if (!file_exists($localPath)) {
                Log::error('TryOn upload: File not found at ' . $localPath);
                return null;
            }

            $uploadId   = Str::uuid()->toString();
            $fileContent = file_get_contents($localPath);
            $mimeType   = mime_content_type($localPath) ?: 'image/jpeg';
            $fileSize   = filesize($localPath);

            $response = Http::timeout(60)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiToken])
                ->attach('files', $fileContent, $fileName, ['Content-Type' => $mimeType])
                ->post($this->spaceUrl() . '/upload?upload_id=' . $uploadId);

            if (!$response->successful()) {
                Log::error('TryOn upload failed (' . $fileName . '): HTTP ' . $response->status() . ' — ' . $response->body());
                return null;
            }

            $uploaded = $response->json();
            if (empty($uploaded) || !isset($uploaded[0])) {
                Log::error('TryOn upload: unexpected response — ' . $response->body());
                return null;
            }

            $uploadedPath = $uploaded[0];
            Log::info("TryOn upload success ({$fileName}): path={$uploadedPath}");

            // Return the FileData dict Gradio expects (matching the schema exactly)
            return [
                'path'      => $uploadedPath,
                'url'       => $this->spaceUrl() . '/file=' . $uploadedPath,
                'orig_name' => $fileName,
                'mime_type' => $mimeType,
                'size'      => $fileSize,
                'is_stream' => false,
                'meta'      => ['_type' => 'gradio.FileData'],
            ];
        } catch (\Exception $e) {
            Log::error('TryOn upload exception (' . $fileName . '): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Poll the HF Space SSE stream for the completed prediction result.
     * Uses cURL's WRITEFUNCTION to process the stream in real-time.
     *
     * SSE v3 protocol:
     * - Events: estimation, process_starts, process_generating, process_completed
     * - Each event has: event: <type>\n data: <json>\n\n
     * - The result is in process_completed → output.data[0] (FileData with url/path)
     */
    private function pollHFSpaceResult(string $sessionHash, string $apiToken): ?string
    {
        $result    = null;
        $buffer    = '';
        $timeout   = (int) env('HF_POLL_TIMEOUT', 600);
        $connectTimeout = (int) env('HF_CONNECT_TIMEOUT', 30);

        $spaceUrl = $this->spaceUrl();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $spaceUrl . '/queue/data?session_hash=' . $sessionHash,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiToken,
                'Accept: text/event-stream',
                'Cache-Control: no-cache',
            ],
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_WRITEFUNCTION  => function ($ch, $chunk) use (&$buffer, &$result, $spaceUrl) {
                $buffer .= $chunk;

                // Process complete SSE lines
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line   = rtrim(substr($buffer, 0, $pos), "\r");
                    $buffer = substr($buffer, $pos + 1);

                    // Skip empty lines (SSE block separators)
                    if ($line === '') {
                        continue;
                    }

                    // We only care about data: lines (SSE v3 puts msg inside data JSON)
                    if (str_starts_with($line, 'data:')) {
                        $eventData = trim(substr($line, 5));
                        $payload   = json_decode($eventData, true);

                        if (!$payload || !is_array($payload)) {
                            continue;
                        }

                        $msg = $payload['msg'] ?? '';

                        // Log progress
                        if ($msg === 'estimation') {
                            $rank = $payload['rank'] ?? '?';
                            $eta  = $payload['rank_eta'] ?? '?';
                            Log::info("TryOn SSE: estimation — rank={$rank}, eta={$eta}s");
                        } elseif ($msg === 'process_starts') {
                            Log::info('TryOn SSE: process_starts — AI is now processing.');
                        } elseif ($msg === 'process_generating') {
                            Log::info('TryOn SSE: process_generating — AI is generating result...');
                        }

                        // ---------- Process completed ----------
                        if ($msg === 'process_completed') {
                            Log::info('TryOn SSE: process_completed received.');
                            $success = $payload['success'] ?? true;
                            if (!$success) {
                                Log::error('TryOn: HF Space returned success=false — ' . $eventData);
                                return -1; // abort stream
                            }

                            // Extract result image URL from output.data
                            $result = $this->extractResultUrl($payload, $spaceUrl);

                            if ($result) {
                                Log::info('TryOn SSE: Extracted result URL: ' . $result);
                            } else {
                                Log::error('TryOn SSE: Could not extract result URL from: ' . $eventData);
                            }

                            return -1; // abort stream — we have our result
                        }

                        // ---------- Error states ----------
                        if ($msg === 'queue_full') {
                            Log::warning('TryOn: HF Space queue is full.');
                            return -1;
                        }

                        if ($msg === 'process_error') {
                            Log::error('TryOn: HF Space process error — ' . $eventData);
                            return -1;
                        }
                    }
                }

                return strlen($chunk);
            },
        ]);

        curl_exec($ch);

        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($curlError && $result === null) {
            Log::error("TryOn cURL SSE error (HTTP {$httpCode}): {$curlError}");
        }

        curl_close($ch);

        if ($result) {
            Log::info('TryOn: polling returned result URL: ' . $result);
        } else {
            Log::warning('TryOn: pollHFSpaceResult did not yield a result within timeout.');
        }

        return $result;
    }

    /**
     * Extract the result image URL from the process_completed SSE payload.
     * Handles multiple possible output formats from Gradio.
     */
    private function extractResultUrl(array $payload, string $spaceUrl): ?string
    {
        // Helper to resolve a FileData dict to a URL
        $resolveUrl = function ($fileData) use ($spaceUrl) {
            if (is_array($fileData)) {
                if (!empty($fileData['url'])) {
                    return $fileData['url'];
                }
                if (!empty($fileData['path'])) {
                    return $spaceUrl . '/file=' . $fileData['path'];
                }
            }
            if (is_string($fileData) && str_starts_with($fileData, 'http')) {
                return $fileData;
            }
            return null;
        };

        $output = $payload['output'] ?? [];

        // Format 1: output.data[0] → FileData (most common for IDM-VTON)
        if (isset($output['data']) && is_array($output['data'])) {
            $first = $output['data'][0] ?? null;
            $url = $resolveUrl($first);
            if ($url) return $url;

            // Nested gallery format: output.data[0][0].image
            if (is_array($first)) {
                $nested = $first[0]['image'] ?? $first[0] ?? $first;
                $url = $resolveUrl($nested);
                if ($url) return $url;
            }
        }

        // Format 2: output.url directly
        if (isset($output['url'])) {
            return $output['url'];
        }

        // Format 3: output.files array
        if (isset($output['files']) && is_array($output['files'])) {
            return $resolveUrl($output['files'][0] ?? null);
        }

        // Format 4: output.file string
        if (isset($output['file']) && is_string($output['file'])) {
            return $output['file'];
        }

        // Format 5: output is itself a FileData
        return $resolveUrl($output);
    }

    /**
     * Download the AI result image from HF Space to local storage.
     * Returns the public asset URL, or null on failure.
     */
    private function downloadResultImage(string $url, string $apiToken): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiToken])
                ->get($url);

            if (!$response->successful()) {
                Log::warning('TryOn: Failed to download result image (HTTP ' . $response->status() . ')');
                return null;
            }

            $extension = 'png';
            $contentType = $response->header('Content-Type');
            if ($contentType && str_contains($contentType, 'jpeg')) {
                $extension = 'jpg';
            }

            $filename = 'tryon/results/' . Str::random(20) . '.' . $extension;
            Storage::disk('public')->put($filename, $response->body());

            Log::info('TryOn: Result image saved to: ' . $filename);
            return asset('storage/' . $filename);
        } catch (\Exception $e) {
            Log::warning('TryOn: Exception downloading result image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to check if a URL is local to this app.
     */
    private function isLocalUrl(string $url): bool
    {
        if (empty($url)) return false;

        $appUrl = rtrim(config('app.url'), '/');
        
        if (str_starts_with($url, $appUrl)) {
            return true;
        }

        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        
        if (in_array($host, ['localhost', '127.0.0.1'])) {
            return true;
        }

        $appHost = parse_url($appUrl, PHP_URL_HOST);
        if ($host === $appHost) {
            return true;
        }

        return false;
    }

    /**
     * Resolve a product image URL to an absolute local filesystem path.
     * Reads directly from disk — never uses HTTP to avoid self-request deadlock.
     */
    private function resolveLocalPath(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $relative = ltrim($path, '/');

        // /storage/... → public disk
        if (str_starts_with($relative, 'storage/')) {
            $storagePath = substr($relative, strlen('storage/'));
            $absPath = Storage::disk('public')->path($storagePath);
            if (file_exists($absPath)) {
                return $absPath;
            }
        }

        // /images/... hoặc path khác → public folder
        $absPath = public_path($relative);
        if (file_exists($absPath)) {
            return $absPath;
        }

        return null;
    }

    /**
     * Resolve a garment image URL to a local filesystem path.
     * Local URLs (APP_URL / localhost / 127.0.0.1) → đọc thẳng từ disk.
     * External URLs (CDN thật) → download về temp file using Laravel HTTP client.
     */
    private function resolveGarmentPath(string $url): string
    {
        if (empty($url)) {
            Log::error('TryOn: Garment URL is empty');
            return '';
        }

        // Luôn ưu tiên resolve local path trước (KHÔNG dùng HTTP cho URL local)
        $localPath = $this->resolveLocalPath($url);
        if ($localPath && file_exists($localPath)) {
            Log::info('TryOn: Garment resolved locally: ' . $localPath);
            return $localPath;
        }

        // Nếu không tìm thấy local → mới download (external thật)
        Log::info('TryOn: garment is external URL, downloading: ' . $url);
        $tempPath = storage_path('app/tryon_garment_' . Str::random(10) . '.jpg');
        
        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                Log::error('TryOn: Cannot download external garment: ' . $url . ' - HTTP ' . $response->status());
                return '';
            }

            file_put_contents($tempPath, $response->body());
            Log::info('TryOn: External garment downloaded to: ' . $tempPath);
            return $tempPath;
            
        } catch (\Exception $e) {
            Log::error('TryOn: Download external garment failed: ' . $e->getMessage());
            return '';
        }
    }
}
