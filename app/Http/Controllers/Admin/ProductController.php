<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['categories', 'images'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $sizes = \App\Models\AttributeValue::whereHas('group', function($q) {
            $q->where('name', 'like', '%Size%')->orWhere('name', 'like', '%Kích thước%');
        })->get();

        $colors = \App\Models\AttributeValue::whereHas('group', function($q) {
            $q->where('name', 'like', '%Color%')->orWhere('name', 'like', '%Màu sắc%');
        })->get();
        
        return view('admin.products.create', compact('categories', 'sizes', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string|max:100',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|max:5120',
            'image_paste' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'base_price' => $request->base_price,
                'description' => $request->description,
                'is_active' => true,
            ]);

            // Sync categories
            $product->categories()->sync($request->categories);

            // Resolve attributes
            $sizeValues = $this->resolveAttributeValues($request->sizes, 'Kích thước');
            $colorValues = $this->resolveAttributeValues($request->colors, 'Màu sắc');

            // Generate variants
            $combinations = [];
            if ($sizeValues->isEmpty() && $colorValues->isEmpty()) {
                $combinations[] = ['size' => null, 'color' => null];
            } elseif ($sizeValues->isEmpty()) {
                foreach ($colorValues as $cv) $combinations[] = ['size' => null, 'color' => $cv];
            } elseif ($colorValues->isEmpty()) {
                foreach ($sizeValues as $sv) $combinations[] = ['size' => $sv, 'color' => null];
            } else {
                foreach ($sizeValues as $sv) {
                    foreach ($colorValues as $cv) {
                        $combinations[] = ['size' => $sv, 'color' => $cv];
                    }
                }
            }

            foreach ($combinations as $combo) {
                $skuParts = [strtoupper(Str::slug($product->name))];
                if ($combo['color']) $skuParts[] = strtoupper($combo['color']->value);
                if ($combo['size']) $skuParts[] = strtoupper($combo['size']->value);
                $skuParts[] = time();
                
                $sId = $combo['size']?->id ?? 0;
                $cId = $combo['color']?->id ?? 0;
                // Default variant quantity to 0 if not provided in variant_stock
                $variantQty = $request->input("variant_stock.{$cId}.{$sId}", 0);

                $variant = \App\Models\ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => implode('-', $skuParts),
                    'price' => $request->base_price,
                    'quantity' => $variantQty,
                    'status' => 'active'
                ]);

                if ($combo['size']) {
                    DB::table('variant_attributes')->insert([
                        'variant_id' => $variant->id,
                        'attribute_group_id' => $combo['size']->group_id,
                        'attribute_value_id' => $combo['size']->id
                    ]);
                }
                if ($combo['color']) {
                    DB::table('variant_attributes')->insert([
                        'variant_id' => $variant->id,
                        'attribute_group_id' => $combo['color']->group_id,
                        'attribute_value_id' => $combo['color']->id
                    ]);
                }
            }

            // Handle multiple images
            if ($request->hasFile('image_files')) {
                foreach ($request->file('image_files') as $index => $file) {
                    $imagePath = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $imagePath,
                        'is_primary' => $index === 0,
                        'sort_order' => $index
                    ]);
                }
            } elseif ($request->filled('image_paste')) {
                $imagePath = $this->handleBase64Image($request->image_paste);
                if ($imagePath) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $imagePath,
                        'is_primary' => true,
                        'sort_order' => 0
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('status', 'Sản phẩm đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Resolve attribute values from input (numeric IDs or custom strings)
     */
    private function resolveAttributeValues($inputs, $groupName)
    {
        if (empty($inputs)) return collect();

        $group = \App\Models\AttributeGroup::where('name', 'like', "%{$groupName}%")->first();
        if (!$group) return collect();

        $values = collect();
        foreach ($inputs as $input) {
            if (is_numeric($input)) {
                $val = \App\Models\AttributeValue::find($input);
            } else {
                $val = \App\Models\AttributeValue::firstOrCreate(
                    ['group_id' => $group->id, 'value' => trim($input)],
                    ['sort_order' => 0]
                );
            }
            if ($val) $values->push($val);
        }
        return $values;
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $sizes = \App\Models\AttributeValue::whereHas('group', function($q) {
            $q->where('name', 'like', '%Size%')->orWhere('name', 'like', '%Kích thước%');
        })->get();
        
        $colors = \App\Models\AttributeValue::whereHas('group', function($q) {
            $q->where('name', 'like', '%Color%')->orWhere('name', 'like', '%Màu sắc%');
        })->get();

        $sizeGroupIds = $sizes->pluck('group_id')->unique();
        $colorGroupIds = $colors->pluck('group_id')->unique();

        $selectedSizes = $product->variants()->with('attributeValues')->get()->flatMap(function($v) use ($sizeGroupIds) {
            return $v->attributeValues->whereIn('group_id', $sizeGroupIds)->pluck('id');
        })->unique()->filter()->values()->toArray();

        $selectedColors = $product->variants()->with('attributeValues')->get()->flatMap(function($v) use ($colorGroupIds) {
            return $v->attributeValues->whereIn('group_id', $colorGroupIds)->pluck('id');
        })->unique()->filter()->values()->toArray();

        $selectedCategories = $product->categories->pluck('id')->toArray();

        // Map existing variant stock for the frontend
        $variantStock = [];
        foreach ($product->variants()->with('attributeValues')->get() as $v) {
            $vSize = $v->attributeValues->whereIn('group_id', $sizeGroupIds)->first();
            $vColor = $v->attributeValues->whereIn('group_id', $colorGroupIds)->first();
            $sId = $vSize ? $vSize->id : 0;
            $cId = $vColor ? $vColor->id : 0;
            $variantStock["{$cId}_{$sId}"] = $v->quantity;
        }

        return view('admin.products.edit', compact('product', 'categories', 'selectedCategories', 'sizes', 'selectedSizes', 'colors', 'selectedColors', 'variantStock'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string|max:100',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string|max:100',
            // 'quantity' => 'required|integer|min:0', // Removed as per instruction
            'description' => 'nullable|string',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|max:5120',
            'image_paste' => 'nullable|string',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
        ]);

        try {
            DB::beginTransaction();

            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . $product->id,
                'base_price' => $request->base_price,
                'description' => $request->description,
            ]);

            $product->categories()->sync($request->categories);

            // Sync Variants (Sizes x Colors)
            $sizeValues = $this->resolveAttributeValues($request->sizes, 'Kích thước');
            $colorValues = $this->resolveAttributeValues($request->colors, 'Màu sắc');

            // Find group IDs
            $sizeGroupId = \App\Models\AttributeGroup::where('name', 'like', '%Size%')->orWhere('name', 'like', '%Kích thước%')->value('id');
            $colorGroupId = \App\Models\AttributeGroup::where('name', 'like', '%Color%')->orWhere('name', 'like', '%Màu sắc%')->value('id');

            // Generate desired combinations: [ ['size_id' => ..., 'color_id' => ...], ... ]
            $desiredCombos = [];
            if ($sizeValues->isEmpty() && $colorValues->isEmpty()) {
                $desiredCombos[] = ['size' => null, 'color' => null];
            } elseif ($sizeValues->isEmpty()) {
                foreach ($colorValues as $cv) $desiredCombos[] = ['size' => null, 'color' => $cv];
            } elseif ($colorValues->isEmpty()) {
                foreach ($sizeValues as $sv) $desiredCombos[] = ['size' => $sv, 'color' => null];
            } else {
                foreach ($sizeValues as $sv) {
                    foreach ($colorValues as $cv) {
                        $desiredCombos[] = ['size' => $sv, 'color' => $cv];
                    }
                }
            }

            // Map desired combos to a searchable format (signature)
            $desiredSignatures = array_map(function($c) {
                $sId = $c['size']?->id ?? 0;
                $cId = $c['color']?->id ?? 0;
                return "{$sId}-{$cId}";
            }, $desiredCombos);

            // Get existing variants with their attributes
            $existingVariants = $product->variants()->with('attributeValues')->get();
            $variantsToDelete = [];
            $existingSignatures = [];

            foreach ($existingVariants as $v) {
                $vSizeId = $v->attributeValues->where('group_id', $sizeGroupId)->first()?->id ?? 0;
                $vColorId = $v->attributeValues->where('group_id', $colorGroupId)->first()?->id ?? 0;
                $sig = "{$vSizeId}-{$vColorId}";
                
                if (in_array($sig, $desiredSignatures)) {
                    $v->update([
                        'price' => $request->base_price,
                        // Default variant quantity to 0 if not provided in variant_stock
                        'quantity' => $request->input("variant_stock.{$vColorId}.{$vSizeId}", 0),
                    ]);
                    $existingSignatures[] = $sig;
                } else {
                    $variantsToDelete[] = $v;
                }
            }

            // Delete obsolete variants
            foreach ($variantsToDelete as $v) {
                DB::table('variant_attributes')->where('variant_id', $v->id)->delete();
                $v->delete();
            }

            // Create new variants
            foreach ($desiredCombos as $combo) {
                $sId = $combo['size']?->id ?? 0;
                $cId = $combo['color']?->id ?? 0;
                $sig = "{$sId}-{$cId}";

                if (!in_array($sig, $existingSignatures)) {
                    $skuParts = [strtoupper(Str::slug($product->name))];
                    if ($combo['color']) $skuParts[] = strtoupper($combo['color']->value);
                    if ($combo['size']) $skuParts[] = strtoupper($combo['size']->value);
                    $skuParts[] = time();

                    // Determine variant quantity, falling back to 0 if not specified
                    $variantQty = 0;
                    if ($combo['color'] && $combo['size']) {
                        $variantQty = $request->input("variant_stock.{$combo['color']->id}.{$combo['size']->id}", 0);
                    } elseif ($combo['color']) {
                        $variantQty = $request->input("variant_stock.{$combo['color']->id}.0", 0); // Use 0 for no size
                    } elseif ($combo['size']) {
                        $variantQty = $request->input("variant_stock.0.{$combo['size']->id}", 0); // Use 0 for no color
                    } else {
                        $variantQty = $request->input("variant_stock.0.0", 0); // For no attributes
                    }

                    $variant = \App\Models\ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => implode('-', $skuParts),
                        'price' => $request->base_price,
                        'quantity' => $variantQty,
                        'status' => 'active'
                    ]);

                    if ($combo['size']) {
                        DB::table('variant_attributes')->insert([
                            'variant_id' => $variant->id,
                            'attribute_group_id' => $combo['size']->group_id,
                            'attribute_value_id' => $combo['size']->id
                        ]);
                    }
                    if ($combo['color']) {
                        DB::table('variant_attributes')->insert([
                            'variant_id' => $variant->id,
                            'attribute_group_id' => $combo['color']->group_id,
                            'attribute_value_id' => $combo['color']->id
                        ]);
                    }
                }
            }

            // Delete removed images
            if ($request->has('delete_images')) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                                              ->whereIn('id', $request->delete_images)
                                              ->get();
                foreach($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->url);
                    $img->delete();
                }
            }

            // Upload new images
            if ($request->hasFile('image_files')) {
                $maxSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? -1;
                $hasPrimary = ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists();
                
                foreach ($request->file('image_files') as $index => $file) {
                    $imagePath = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $imagePath,
                        'is_primary' => !$hasPrimary && $index === 0,
                        'sort_order' => $maxSort + 1 + $index
                    ]);
                }
            } elseif ($request->filled('image_paste')) {
                $imagePath = $this->handleBase64Image($request->image_paste);
                if ($imagePath) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $imagePath,
                        'is_primary' => !ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists(),
                        'sort_order' => (ProductImage::where('product_id', $product->id)->max('sort_order') ?? -1) + 1
                    ]);
                }
            }
            
            // Ensure at least one primary image exists if there are any images left
            if (!ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists()) {
                $firstImg = ProductImage::where('product_id', $product->id)->orderBy('sort_order')->first();
                if ($firstImg) {
                    $firstImg->update(['is_primary' => true]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('status', 'Sản phẩm đã được cập nhật.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('status', 'Sản phẩm đã được xóa.');
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        return back()->with('status', 'Đã cập nhật trạng thái sản phẩm.');
    }

    /**
     * Handle base64 image from paste.
     */
    private function handleBase64Image($base64String)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                throw new \Exception('Định dạng ảnh không hợp lệ.');
            }

            $data = base64_decode($data);
            if ($data === false) {
                throw new \Exception('Dữ liệu ảnh không hợp lệ.');
            }
        } else {
            throw new \Exception('Chuỗi ảnh không hợp lệ.');
        }

        $fileName = 'pasted_' . time() . '_' . Str::random(10) . '.' . $type;
        $path = 'products/' . $fileName;

        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
