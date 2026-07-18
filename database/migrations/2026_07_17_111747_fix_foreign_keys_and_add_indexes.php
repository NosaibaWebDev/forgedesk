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

        // messages: change sender_id and receiver_id from cascade to nullOnDelete
        // (preserve message history when a user is deleted)
        if ($driver !== 'sqlite') {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropForeign(['sender_id']);
                $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();
                $table->dropForeign(['receiver_id']);
                $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete();
                $table->index('sender_id');
            });
        }

        // project_files: change uploaded_by from cascade to nullOnDelete
        // (preserve file records when the uploading user is deleted)
        if ($driver !== 'sqlite') {
            Schema::table('project_files', function (Blueprint $table) {
                $table->dropForeign(['uploaded_by']);
                $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // comments: change user_id from cascade to nullOnDelete
        if ($driver !== 'sqlite') {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // time_entries: change user_id from cascade to nullOnDelete
        // (preserve time entries when the user is deleted)
        if ($driver !== 'sqlite') {
            Schema::table('time_entries', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Add missing indexes for frequently queried columns
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('is_active');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('assigned_to');
            $table->index('due_date');
        });

        Schema::table('time_entries', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('project_id');
        });

        // Remove duplicate indexes (project_id already indexed by constrained())
        if ($driver !== 'sqlite') {
            Schema::table('project_files', function (Blueprint $table) {
                $table->dropIndex('project_files_project_id_index');
            });
            Schema::table('comments', function (Blueprint $table) {
                $table->dropIndex('comments_task_id_index');
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
