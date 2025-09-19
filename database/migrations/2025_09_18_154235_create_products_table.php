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
            $table->text('description');
            $table->string('sku')->nullable();
            $table->string('slug');
            $table->string('status');
            $table->integer('price');
            $table->decimal('discount', 8, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->json('tags')->nullable();
            $table->string('main_image_path');
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
