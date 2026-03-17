<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('slug');
            $table->index('status');
            $table->index(['status', 'published_at']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'status']);
        });

        Schema::table('product_tax', function (Blueprint $table) {
            $table->index('tax_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['purchasable_type', 'purchasable_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['purchasable_type', 'purchasable_id']);
        });

        Schema::table('discountables', function (Blueprint $table) {
            $table->index(['discountable_type', 'discountable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'published_at']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
        });

        Schema::table('product_tax', function (Blueprint $table) {
            $table->dropIndex(['tax_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['purchasable_type', 'purchasable_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['purchasable_type', 'purchasable_id']);
        });

        Schema::table('discountables', function (Blueprint $table) {
            $table->dropIndex(['discountable_type', 'discountable_id']);
        });
    }
};
