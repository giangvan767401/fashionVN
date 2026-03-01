<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Collection;
use App\Models\AttributeGroup;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Define attribute groups
        $sizeGroup = AttributeGroup::firstOrCreate(['name' => 'Kích Thước']);
        $colorGroup = AttributeGroup::firstOrCreate(['name' => 'Màu Sắc']);
        $materialGroup = AttributeGroup::firstOrCreate(['name' => 'Chất Liệu']);

        // Define Attribute Values
        $sizeS = AttributeValue::firstOrCreate(['group_id' => $sizeGroup->id, 'value' => 'S']);
        $sizeM = AttributeValue::firstOrCreate(['group_id' => $sizeGroup->id, 'value' => 'M']);
        $sizeL = AttributeValue::firstOrCreate(['group_id' => $sizeGroup->id, 'value' => 'L']);
        
        $colorBlack = AttributeValue::firstOrCreate(['group_id' => $colorGroup->id, 'value' => 'Đen', 'color_hex' => '#000000']);
        $colorWhite = AttributeValue::firstOrCreate(['group_id' => $colorGroup->id, 'value' => 'Trắng', 'color_hex' => '#FFFFFF']);
        $colorBeige = AttributeValue::firstOrCreate(['group_id' => $colorGroup->id, 'value' => 'Be', 'color_hex' => '#F5F5DC']);

        $matCotton = AttributeValue::firstOrCreate(['group_id' => $materialGroup->id, 'value' => 'Cotton']);
        $matLinen = AttributeValue::firstOrCreate(['group_id' => $materialGroup->id, 'value' => 'Linen']);
        $matSilk = AttributeValue::firstOrCreate(['group_id' => $materialGroup->id, 'value' => 'Lụa']);

        // Define Collections
        $hangMoi = Collection::firstOrCreate(['name' => 'Hàng Mới', 'slug' => 'hang-moi']);
        $banChay = Collection::firstOrCreate(['name' => 'Bán Chạy Nhất', 'slug' => 'ban-chay-nhat']);

        $mockProducts = [
            [
                'name' => 'Áo Quấn Cách Điệu',
                'sku' => 'AT-COT-01',
                'description' => 'Áo kiểu thắt eo thời thượng',
                'price' => 4000000,
                'sale_price' => null,
                'image_url' => 'user/img/modiweek/1.webp',
                'sizes' => [$sizeS->id, $sizeM->id],
                'collections' => [$hangMoi->id],
                'material' => $matCotton->id,
                'color' => $colorWhite->id,
            ],
            [
                'name' => 'Áo Thun Cơ Bản',
                'sku' => 'DL-A-01',
                'description' => 'Áo thun năng động, trẻ trung',
                'price' => 2375000,
                'sale_price' => null,
                'image_url' => 'user/img/modiweek/2.webp',
                'sizes' => [$sizeM->id, $sizeL->id],
                'collections' => [$banChay->id],
                'material' => $matLinen->id,
                'color' => $colorBeige->id,
            ],
            [
                'name' => 'Váy Sơ Mi',
                'sku' => 'SM-L-01',
                'description' => 'Đầm dáng sơ mi thanh lịch',
                'price' => 6125000,
                'sale_price' => null,
                'image_url' => 'user/img/modiweek/3.webp',
                'sizes' => [$sizeS->id],
                'collections' => [$hangMoi->id, $banChay->id],
                'material' => $matSilk->id,
                'color' => $colorBlack->id,
            ],
            [
                'name' => 'Áo Khoác Zip Rule',
                'sku' => 'QT-S-01',
                'description' => 'Áo khoác khóa kéo cá tính',
                'price' => 4975000,
                'sale_price' => null,
                'image_url' => 'user/img/modiweek/4.webp',
                'sizes' => [$sizeL->id],
                'collections' => [],
                'material' => $matCotton->id,
                'color' => $colorBlack->id,
            ],
            [
                'name' => 'Quần Vải Linen',
                'sku' => 'QV-L-01',
                'description' => 'Quần dài chất liệu linen thoáng mát',
                'price' => 4500000,
                'sale_price' => null,
                'image_url' => 'user/img/modiweek/5.webp',
                'sizes' => [$sizeS->id, $sizeM->id],
                'collections' => [$banChay->id, $hangMoi->id],
                'material' => $matLinen->id,
                'color' => $colorBlack->id,
            ],
            [
                'name' => 'Áo Len Chui Đầu Boss',
                'sku' => 'AL-B-01',
                'description' => 'Áo len dệt kim cao cấp',
                'price' => 7000000,
                'sale_price' => null,
                'image_url' => 'user/img/collection/Lifestyle_Detail_Something_Tailored_Shirt_White_1400x.webp',
                'sizes' => [$sizeM->id, $sizeL->id],
                'collections' => [$hangMoi->id],
                'material' => $matCotton->id,
                'color' => $colorWhite->id,
            ],
        ];

        foreach ($mockProducts as $data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . rand(100, 999), // to avoid conflicts
                'sku' => $data['sku'],
                'short_desc' => $data['description'],
                'base_price' => $data['price'],
                'sale_price' => $data['sale_price'],
                'is_active' => true,
            ]);

            // Add Image to ProductImages
            $product->images()->create([
                'url' => $data['image_url'],
                'is_primary' => true,
                'sort_order' => 1
            ]);

            // Attach collections
            if (!empty($data['collections'])) {
                $product->collections()->attach($data['collections']);
            }

            // Create Variants for the product
            foreach ($data['sizes'] as $sizeId) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->sku . '-' . $sizeId,
                    'price' => $product->base_price,
                    'sale_price' => $product->sale_price,
                    'is_active' => true,
                ]);

                // Attach size, color, material to variant
                $variant->attributeValues()->attach([
                    $sizeId => ['attribute_group_id' => $sizeGroup->id],
                    $data['color'] => ['attribute_group_id' => $colorGroup->id],
                    $data['material'] => ['attribute_group_id' => $materialGroup->id],
                ]);
            }
        }
    }
}
