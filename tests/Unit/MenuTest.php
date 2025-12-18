<?php

use App\Models\Menu;
use App\Models\MenuItem;

test('can have many items', function () {
    $menu = Menu::factory()->create([
        'title' => 'Main',
        'slug' => 'main',
    ]);

    $itemA = MenuItem::factory()->create([
        'menu_id' => $menu->id,
        'slug' => 'home',
        'label' => 'Home',
        'url' => '/',
    ]);
    $itemB = MenuItem::factory()->create([
        'menu_id' => $menu->id,
        'slug' => 'about',
        'label' => 'About',
        'url' => 'about',
    ]);

    $itemC = MenuItem::factory()->create([
        'menu_id' => $menu->id,
        'slug' => 'contact',
        'label' => 'Contact',
        'url' => 'contact',
    ]);

    expect($menu->items)->toHaveCount(3);
    expect($menu->items[0])->toBeInstanceOf(MenuItem::class);

    $menu->fresh()->items->assertEquals([
        $itemA,
        $itemB,
        $itemC,
    ]);

});

test('an item can have subitems', function () {
    $menu = Menu::factory()->create([
        'slug' => 'main',
    ]);

    $itemA = MenuItem::factory()->create([
        'menu_id' => $menu->id,
        'slug' => 'resources',
        'label' => 'Resources',
        'url' => 'resources',
        'items' => [
            [
                'slug' => 'blog',
                'label' => 'Blog',
                'url' => 'blog',
            ],
            [
                'slug' => 'events',
                'label' => 'Events',
                'url' => 'events',
            ],
        ],
    ]);

    expect($itemA->items)->toHaveCount(2);
    /*  expect($itemA->items[0])->toBeInstanceOf(MenuItem::class); */
    expect($itemA->items[0]['label'])->toBe('Blog');
    expect($itemA->items[0]['url'])->toBe('blog');
    expect($itemA->items[0]['slug'])->toBe('blog');

    expect($itemA->items[1]['label'])->toBe('Events');
    expect($itemA->items[1]['url'])->toBe('events');
    expect($itemA->items[1]['slug'])->toBe('events');

});

test('can get main menu', function () {
    $menu = Menu::factory()->create([
        'title' => 'Main',
        'slug' => 'main',
    ]);
    expect(Menu::byName('main')->id)->toBe($menu->id);
});
