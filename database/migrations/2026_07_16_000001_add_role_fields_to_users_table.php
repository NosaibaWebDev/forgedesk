<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'client'])->default('client')->after('name');
            $table->string('phone')->nullable()->after('role');
            $table->string('company')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('company');
            $table->text('address')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'company', 'avatar', 'address', 'is_active']);
        });
    }
};
