<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\Section;
use App\Models\User;

it('gives successful response for home page', function () {
    $section1 = Section::factory()->create([
        'slug' => 'hero',
        'content' => [
            [
                'type' => 'heading',
                'data' => [
                    'content' => 'Heading 1',
                    'level' => 'h1',
                ],
            ],
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => 'Paragraph 1',
                ],
            ],
        ],
    ]);

    $section2 = Section::factory()->create([
        'slug' => 'values',
        'content' => [
            [
                'type' => 'heading',
                'data' => [
                    'content' => 'Heading 2',
                    'level' => 'h2',
                ],
            ],
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => 'Paragraph 2',
                ],
            ],

        ],
    ]);

    $page = Page::factory()->published()->create([
        'slug' => 'home',
    ]);

    $page->sections()->attach([$section1->id, $section2->id]);
    $response = $this->get(route('storefront.home'));
    $response->assertStatus(200);
});

it('gives successful response for products page', function () {
    $response = $this->get(route('storefront.products'));
    $response->assertStatus(200);
});

it('gives successful response for collections page', function () {
    $response = $this->get(route('storefront.collections'));
    $response->assertStatus(200);
});

it('gives successful response for collection page', function () {
    $collection = ProductCollection::factory()->create();
    $response = $this->get(route('storefront.collection', ['collection' => $collection->slug]));
    $response->assertStatus(200);
});

it('gives successful response for product page', function () {
    $product = Product::factory()->published()->create();
    $response = $this->get(route('storefront.product', ['product' => $product->slug]));
    $response->assertStatus(200);
});

it('gives successful response for checkout page', function () {
    $cart = Cart::factory()->create();
    CartItem::factory()->create(['cart_id' => $cart->id]);
    $user = User::factory()->create();
    $response = $this->actingAs($user)->withCookie('cart', $cart->ui_cart_id)->get(route('storefront.checkout'));
    $response->assertStatus(200);
});

it('gives successful response for about page', function () {
    $section1 = Section::factory()->create([
        'slug' => 'hero',
        'content' => [
            [
                'type' => 'heading',
                'data' => [
                    'content' => 'Heading 1',
                    'level' => 'h1',
                ],
            ],
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => 'Paragraph 1',
                ],
            ],
        ],
    ]);
    $page = Page::factory()->published()->create([
        'slug' => 'acerca-de',
    ]);


    $page->sections()->attach([$section1->id]);
    $response = $this->get(route('storefront.about'));
    $response->assertStatus(200);
});

it('gives successful response for contact page', function () {
    $response = $this->get(route('storefront.contact'));
    $response->assertStatus(200);
});
