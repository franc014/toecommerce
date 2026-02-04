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
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('has_discount')->default(false)->after('price');
            $table->integer('discount_percentage')->nullable()->after('price');
            $table->integer('discounted_price')->nullable()->after('price');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('has_discount');
            $table->dropColumn('discount_percentage');
            $table->dropColumn('discounted_price');
        });
    }
};
