<?php

use App\Models\User;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('description');
            $table->string('main_image');
            $table->string('video')->nullable();
            $table->string('sku')->nullable();
            $table->string('slug');
            $table->string('status');
            $table->integer('price');
            $table->decimal('discount', 8, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->integer('stock_threshold_for_customers')->default(10);
            $table->json('tags')->nullable();
            $table->json('variant_options')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('archived_at')->nullable();
            $table->foreignIdFor(User::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
