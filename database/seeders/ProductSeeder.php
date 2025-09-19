<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCollections = ProductCollection::factory(10)->create();
        $categories = Category::factory(10)->create();

        $productCollectionsIds = $productCollections->random(4);
        $categoriesIds = $categories->random(2);

        $products = Product::factory(5)->published()->create();

        $rp = $products->random(2);

        foreach ($products as $product) {
            $product->productCollections()->sync($productCollectionsIds->pluck('id')->all());
            $product->categories()->sync($categoriesIds->pluck('id')->all());
        }

        foreach ($rp as $product) {
            ProductVariant::factory(4)->published()->create([
                'product_id' => $product->id,
            ]);
        }
    }
}
