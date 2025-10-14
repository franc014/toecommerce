<?php

use App\Models\Cart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('purchasable_id');
            $table->string('title');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->string('purchasable_type');
            $table->string('color')->nullable();
            $table->json('sizes')->nullable();
            $table->integer('price');
            $table->integer('quantity');
            $table->json('taxes')->nullable();
            $table->integer('total');
            $table->integer('total_with_taxes');
            $table->integer('computed_taxes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
