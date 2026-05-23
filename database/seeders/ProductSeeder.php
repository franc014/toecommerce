<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use JFA\ToecommerceCore\Models\Category;
use JFA\ToecommerceCore\Models\Product;
use JFA\ToecommerceCore\Models\ProductCollection;
use JFA\ToecommerceCore\Models\ProductVariant;

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
