<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Product $product)
    {

        $data = [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'description' => $product->description,
            'price_in_dollars' => $product->price_in_dollars,
            'taxes' => $product->taxes, //todo: formatted taxes
            'price_with_taxes_in_dollars' => $product->price_with_taxes_in_dollars,
            'images' => $product->productImagesForList,
        ];

        return Inertia::render('Product', [
            'product' => $data
        ]);
    }
}
