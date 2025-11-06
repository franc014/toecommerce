<?php

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Cart::class)->constrained();
            $table->string('code');
            $table->integer('total_amount')->default(0);
            $table->integer('total_with_taxes')->default(0);
            $table->integer('total_without_taxes')->default(0);
            $table->integer('total_computed_taxes')->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->json('payphone_metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
