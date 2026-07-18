<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dateTime('completed_at')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('estimated_hours', 8, 2)->nullable()->change();
            $table->decimal('actual_hours', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('completed_at')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('estimated_hours')->nullable()->change();
            $table->integer('actual_hours')->nullable()->change();
        });
    }
};
