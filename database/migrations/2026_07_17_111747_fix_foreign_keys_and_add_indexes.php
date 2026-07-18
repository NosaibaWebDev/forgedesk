<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        try { Schema::table('messages', fn (Blueprint $table) => $table->dropForeign(['sender_id'])); } catch (\Exception) {}
        try { Schema::table('messages', fn (Blueprint $table) => $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete()); } catch (\Exception) {}
        try { Schema::table('messages', fn (Blueprint $table) => $table->dropForeign(['receiver_id'])); } catch (\Exception) {}
        try { Schema::table('messages', fn (Blueprint $table) => $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete()); } catch (\Exception) {}
        try { Schema::table('messages', fn (Blueprint $table) => $table->index('sender_id')); } catch (\Exception) {}

        try { Schema::table('project_files', fn (Blueprint $table) => $table->dropForeign(['uploaded_by'])); } catch (\Exception) {}
        try { Schema::table('project_files', fn (Blueprint $table) => $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete()); } catch (\Exception) {}

        try { Schema::table('comments', fn (Blueprint $table) => $table->dropForeign(['user_id'])); } catch (\Exception) {}
        try { Schema::table('comments', fn (Blueprint $table) => $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()); } catch (\Exception) {}

        try { Schema::table('time_entries', fn (Blueprint $table) => $table->dropForeign(['user_id'])); } catch (\Exception) {}
        try { Schema::table('time_entries', fn (Blueprint $table) => $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()); } catch (\Exception) {}

        try { Schema::table('users', fn (Blueprint $table) => $table->index('role')); } catch (\Exception) {}
        try { Schema::table('users', fn (Blueprint $table) => $table->index('is_active')); } catch (\Exception) {}

        try { Schema::table('tasks', fn (Blueprint $table) => $table->index('assigned_to')); } catch (\Exception) {}
        try { Schema::table('tasks', fn (Blueprint $table) => $table->index('due_date')); } catch (\Exception) {}

        try { Schema::table('time_entries', fn (Blueprint $table) => $table->index('project_id')); } catch (\Exception) {}

        try { Schema::table('project_files', fn (Blueprint $table) => $table->dropIndex('project_files_project_id_index')); } catch (\Exception) {}
        try { Schema::table('comments', fn (Blueprint $table) => $table->dropIndex('comments_task_id_index')); } catch (\Exception) {}
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        try { Schema::table('users', fn (Blueprint $table) => $table->dropIndex(['role'])); } catch (\Exception) {}
        try { Schema::table('users', fn (Blueprint $table) => $table->dropIndex(['is_active'])); } catch (\Exception) {}
        try { Schema::table('tasks', fn (Blueprint $table) => $table->dropIndex(['assigned_to'])); } catch (\Exception) {}
        try { Schema::table('tasks', fn (Blueprint $table) => $table->dropIndex(['due_date'])); } catch (\Exception) {}
        try { Schema::table('time_entries', fn (Blueprint $table) => $table->dropIndex(['user_id'])); } catch (\Exception) {}
        try { Schema::table('time_entries', fn (Blueprint $table) => $table->dropIndex(['project_id'])); } catch (\Exception) {}
    }
};
