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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('notes');
            $table->decimal('voucher_discount', 15, 2)->default(0)->after('voucher_code');
            $table->decimal('subtotal', 15, 2)->nullable()->after('voucher_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'voucher_discount', 'subtotal']);
        });
    }
};
