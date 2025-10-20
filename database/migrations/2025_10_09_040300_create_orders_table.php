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
            if (!Schema::hasTable('orders')) {
                Schema::create('orders', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->decimal('total_price', 10, 2);
                    $table->string('status')->default('pending'); // pending, processing, shipped, delivered
                    $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
                    $table->string('payment_method')->nullable();
                    $table->text('shipping_address')->nullable();
                    $table->timestamps();
                });
            }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
