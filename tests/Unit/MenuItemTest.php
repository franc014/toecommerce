<?php

use App\Models\Menu;
use App\Models\MenuItem;

test('menu item belongs to a menu', function () {
    $menu = Menu::factory()->create(['slug' => 'test-menu']);
    $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

    expect($menuItem->menu->id)->toBe($menu->id);
    expect($menuItem->menu->slug)->toBe('test-menu');
});

test('menu item menu relationship returns correct type', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

    expect($menuItem->menu)->toBeInstanceOf(Menu::class);
});

test('menu item can access menu through relationship for cache clearing', function () {
    $menu = Menu::factory()->create(['slug' => 'main']);
    $menuItem = MenuItem::factory()->create([
        'menu_id' => $menu->id,
        'label' => 'Home',
        'slug' => 'home',
    ]);

    expect($menuItem->menu)->not->toBeNull();
    expect($menuItem->menu->slug)->toBe('main');
});
