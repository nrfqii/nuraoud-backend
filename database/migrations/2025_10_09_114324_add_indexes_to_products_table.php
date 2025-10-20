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
        if (Schema::hasTable('products')) {
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->index('category');
                    $table->index('brand');
                    $table->index('bestseller');
                });
            } catch (\Exception $e) {
                // index might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropIndex(['category']);
                    $table->dropIndex(['brand']);
                    $table->dropIndex(['bestseller']);
                });
            } catch (\Exception $e) {
                // ignore
            }
        }
    }
};
