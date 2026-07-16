<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
            $table->index(['admin_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['admin_id', 'role']);
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
