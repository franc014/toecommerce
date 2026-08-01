<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DataExamplesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ProductVariantsTableSeeder::class);
        $this->call(ProductCollectionsTableSeeder::class);
        $this->call(ProductProductCollectionTableSeeder::class);
        $this->call(ProductTaxTableSeeder::class);
        $this->call(TaxesTableSeeder::class);
        $this->call(DiscountsTableSeeder::class);
        $this->call(DiscountablesTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(TaggablesTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(SectionsTableSeeder::class);
        $this->call(PageSectionTableSeeder::class);
        $this->call(ImportsTableSeeder::class);
        $this->call(FailedImportRowsTableSeeder::class);
        $this->call(CartsTableSeeder::class);
        $this->call(CartItemsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(OrderItemsTableSeeder::class);
        $this->call(OrderStatusHistoriesTableSeeder::class);
        $this->call(MediaTableSeeder::class);
        $this->call(UserInfoEntriesTableSeeder::class);
    }
}