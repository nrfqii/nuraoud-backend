<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'is_admin_read')) {
                $table->boolean('is_admin_read')->default(false)->after('is_admin')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'is_admin_read')) {
                $table->dropColumn('is_admin_read');
            }
        });
    }
};
