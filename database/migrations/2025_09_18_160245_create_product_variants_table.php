<?php

use App\Models\Product;
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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('color')->nullable();
            $table->json('sizes')->nullable();
            $table->foreignIdFor(Product::class)->constrained()->onDelete('cascade');
            $table->integer('price');
            $table->decimal('discount', 8, 2)->nullable();
            $table->string('status');
            $table->string('sku');
            $table->integer('stock');
            $table->dateTime('published_at')->nullable();
            $table->dateTime('archived_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
