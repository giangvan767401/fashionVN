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
        
        return view('admin.products.create', compact('categories', 'sizes'));
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
            'sizes.*' => 'exists:attribute_values,id',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
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

            // Create variants for each size
            if ($request->filled('sizes')) {
                foreach ($request->sizes as $sizeId) {
                    $sizeValue = \App\Models\AttributeValue::find($sizeId);
                    $variant = \App\Models\ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => strtoupper(Str::slug($product->name)) . '-' . strtoupper($sizeValue->value) . '-' . time(),
                        'price' => $request->base_price,
                        'quantity' => 100, // Default stock
                        'status' => 'active'
                    ]);

                    // Link to attribute
                    DB::table('variant_attributes')->insert([
                        'variant_id' => $variant->id,
                        'attribute_group_id' => $sizeValue->group_id,
                        'attribute_value_id' => $sizeId
                    ]);
                }
            }

            // Handle image upload or paste
            $imagePath = null;
            if ($request->hasFile('image_file')) {
                $imagePath = $request->file('image_file')->store('products', 'public');
            } elseif ($request->filled('image_paste')) {
                $imagePath = $this->handleBase64Image($request->image_paste);
            }

            if ($imagePath) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $imagePath,
                    'is_primary' => true,
                    'sort_order' => 0
                ]);
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('status', 'Sản phẩm đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
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
        
        $selectedSizes = $product->variants()->whereHas('attributeValues.group', function($q) {
            $q->where('name', 'like', '%Size%')->orWhere('name', 'like', '%Kích thước%');
        })->with('attributeValues')->get()->flatMap(function($v) {
            return $v->attributeValues->pluck('id');
        })->unique()->toArray();
        $selectedCategories = $product->categories->pluck('id')->toArray();

        return view('admin.products.edit', compact('product', 'categories', 'selectedCategories', 'sizes', 'selectedSizes'));
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
            'sizes.*' => 'exists:attribute_values,id',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'image_paste' => 'nullable|string',
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

            // Sync Sizes (Variants)
            if ($request->has('sizes')) {
                $newSizeIds = array_map('intval', $request->sizes);
                
                // Get all variant IDs that have a "Size" attribute associated with this product
                $existingVariants = $product->variants()
                    ->whereHas('attributeValues.group', function($q) {
                        $q->where('name', 'like', '%Size%')->orWhere('name', 'like', '%Kích thước%');
                    })->with('attributeValues')->get();

                $existingSizeIds = $existingVariants->flatMap(function($v) {
                    return $v->attributeValues->pluck('id');
                })->toArray();

                // Delete removed sizes
                foreach ($existingVariants as $variant) {
                    $variantSizeId = $variant->attributeValues->first()->id;
                    if (!in_array($variantSizeId, $newSizeIds)) {
                        DB::table('variant_attributes')->where('variant_id', $variant->id)->delete();
                        $variant->delete();
                    }
                }

                // Add new sizes
                foreach ($newSizeIds as $sizeId) {
                    if (!in_array($sizeId, $existingSizeIds)) {
                        $sizeValue = \App\Models\AttributeValue::find($sizeId);
                        $variant = \App\Models\ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => strtoupper(Str::slug($product->name)) . '-' . strtoupper($sizeValue->value) . '-' . time(),
                            'price' => $request->base_price,
                            'quantity' => 100,
                            'status' => 'active'
                        ]);

                        DB::table('variant_attributes')->insert([
                            'variant_id' => $variant->id,
                            'attribute_group_id' => $sizeValue->group_id,
                            'attribute_value_id' => $sizeId
                        ]);
                    }
                }
            }

            // Handle new image if provided
            $imagePath = null;
            if ($request->hasFile('image_file')) {
                $imagePath = $request->file('image_file')->store('products', 'public');
            } elseif ($request->filled('image_paste')) {
                $imagePath = $this->handleBase64Image($request->image_paste);
            }

            if ($imagePath) {
                // For simplicity, we replace the primary image in this implementation
                ProductImage::where('product_id', $product->id)->where('is_primary', true)->delete();
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $imagePath,
                    'is_primary' => true,
                    'sort_order' => 0
                ]);
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
