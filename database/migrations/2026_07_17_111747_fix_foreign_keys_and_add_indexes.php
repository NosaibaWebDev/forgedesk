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

        if ($driver !== 'sqlite') {
            Schema::table('messages', function (Blueprint $table) {
                try { $table->dropForeign(['sender_id']); } catch (\Exception) {}
                $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();
                try { $table->dropForeign(['receiver_id']); } catch (\Exception) {}
                $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        if ($driver !== 'sqlite') {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasIndex('messages', ['sender_id'])) {
                    $table->index('sender_id');
                }
            });
        }

        if ($driver !== 'sqlite') {
            Schema::table('project_files', function (Blueprint $table) {
                try { $table->dropForeign(['uploaded_by']); } catch (\Exception) {}
                $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        if ($driver !== 'sqlite') {
            Schema::table('comments', function (Blueprint $table) {
                try { $table->dropForeign(['user_id']); } catch (\Exception) {}
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        if ($driver !== 'sqlite') {
            Schema::table('time_entries', function (Blueprint $table) {
                try { $table->dropForeign(['user_id']); } catch (\Exception) {}
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (!Schema::hasIndex('users', ['role'])) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role');
            });
        }
        if (!Schema::hasIndex('users', ['is_active'])) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('is_active');
            });
        }

        if (!Schema::hasIndex('tasks', ['assigned_to'])) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('assigned_to');
            });
        }
        if (!Schema::hasIndex('tasks', ['due_date'])) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('due_date');
            });
        }

        if (!Schema::hasIndex('time_entries', ['user_id'])) {
            Schema::table('time_entries', function (Blueprint $table) {
                $table->index('user_id');
            });
        }
        if (!Schema::hasIndex('time_entries', ['project_id'])) {
            Schema::table('time_entries', function (Blueprint $table) {
                $table->index('project_id');
            });
        }

        if ($driver !== 'sqlite') {
            Schema::table('project_files', function (Blueprint $table) {
                try { $table->dropIndex('project_files_project_id_index'); } catch (\Exception) {}
            });
            Schema::table('comments', function (Blueprint $table) {
                try { $table->dropIndex('comments_task_id_index'); } catch (\Exception) {}
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['due_date']);
        });

        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['project_id']);
        });

        if ($driver !== 'sqlite') {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropForeign(['sender_id']);
                $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
                $table->dropForeign(['receiver_id']);
                $table->foreign('receiver_id')->references('id')->on('users')->cascadeOnDelete();
                $table->dropIndex('sender_id');
            });

            Schema::table('project_files', function (Blueprint $table) {
                $table->dropForeign(['uploaded_by']);
                $table->foreign('uploaded_by')->references('id')->on('users')->cascadeOnDelete();
                $table->index('project_id');
            });

            Schema::table('comments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->index('task_id');
            });

            Schema::table('time_entries', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
