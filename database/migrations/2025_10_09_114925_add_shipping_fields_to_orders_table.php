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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'shipping_method')) {
                    $table->string('shipping_method')->nullable()->after('shipping_address');
                }
                if (!Schema::hasColumn('orders', 'phone')) {
                    $table->string('phone')->nullable()->after('shipping_method');
                }
                if (!Schema::hasColumn('orders', 'name')) {
                    $table->string('name')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('orders', 'city')) {
                    $table->string('city')->nullable()->after('name');
                }
                if (!Schema::hasColumn('orders', 'postal_code')) {
                    $table->string('postal_code')->nullable()->after('city');
                }
                if (!Schema::hasColumn('orders', 'notes')) {
                    $table->text('notes')->nullable()->after('postal_code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $cols = ['shipping_method', 'phone', 'name', 'city', 'postal_code', 'notes'];
                $exists = array_filter($cols, fn($c) => Schema::hasColumn('orders', $c));
                if (!empty($exists)) {
                    $table->dropColumn($exists);
                }
            });
        }
    }
};
