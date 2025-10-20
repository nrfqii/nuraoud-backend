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
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('price', 10, 2);
                $table->string('category');
                $table->string('brand');
                $table->string('scent');
                $table->text('description');
                $table->string('image');
                $table->decimal('rating', 3, 1)->default(0);
                $table->integer('reviews')->default(0);
                $table->integer('stock')->default(0);
                $table->boolean('bestseller')->default(false);
                $table->string('volume');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
