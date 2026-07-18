<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--disk=local : Storage disk for backup}
                            {--keep=14 : Number of backups to retain}';

    protected $description = 'Backup the database and uploaded files';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $keep = (int) $this->option('keep');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $driver = DB::getDriverName();

        $this->backupDatabase($disk, $timestamp, $driver);
        $this->backupFiles($timestamp);
        $this->cleanOldBackups($keep, $disk);

        $this->info('Backup complete.');
        return self::SUCCESS;
    }

    private function backupDatabase(string $disk, string $timestamp, string $driver): void
    {
        match ($driver) {
            'sqlite' => $this->backupSqlite($disk, $timestamp),
            'mysql' => $this->backupMysql($disk, $timestamp),
            'pgsql' => $this->backupPgsql($disk, $timestamp),
            default => $this->warn("Unsupported database driver: {$driver}. Skipping database backup."),
        };
    }

    private function backupSqlite(string $disk, string $timestamp): void
    {
        $dbPath = database_path('database.sqlite');
        if (!file_exists($dbPath)) {
            $this->warn('SQLite database file not found.');
            return;
        }

        $backupPath = "backups/database_{$timestamp}.sqlite";
        Storage::disk($disk)->put($backupPath, file_get_contents($dbPath));
        $this->info("Database backup: {$backupPath}");
    }

    private function backupMysql(string $disk, string $timestamp): void
    {
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $backupPath = "backups/database_{$timestamp}.sql";
        $tempFile = storage_path("app/backups/database_{$timestamp}.sql");

        $cmd = sprintf(
            'mysqldump -h %s -P %s -u %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($tempFile)
        );

        if (!empty($password)) {
            $cmd = sprintf(
                'MYSQL_PWD=%s mysqldump -h %s -P %s -u %s %s > %s 2>&1',
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($database),
                escapeshellarg($tempFile)
            );
        }

        exec($cmd, output: $output, result_code: $exitCode);

        if ($exitCode === 0 && file_exists($tempFile)) {
            Storage::disk($disk)->put($backupPath, file_get_contents($tempFile));
            unlink($tempFile);
            $this->info("Database backup: {$backupPath}");
        } else {
            $this->error("MySQL backup failed: " . implode("\n", $output));
        }
    }

    private function backupPgsql(string $disk, string $timestamp): void
    {
        $host = config('database.connections.pgsql.host', '127.0.0.1');
        $port = config('database.connections.pgsql.port', '5432');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');

        $backupPath = "backups/database_{$timestamp}.sql";
        $tempFile = storage_path("app/backups/database_{$timestamp}.sql");

        $cmd = sprintf(
            'PGHOST=%s PGPORT=%s PGUSER=%s pg_dump %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($tempFile)
        );

        exec($cmd, output: $output, result_code: $exitCode);

        if ($exitCode === 0 && file_exists($tempFile)) {
            Storage::disk($disk)->put($backupPath, file_get_contents($tempFile));
            unlink($tempFile);
            $this->info("Database backup: {$backupPath}");
        } else {
            $this->error("PostgreSQL backup failed: " . implode("\n", $output));
        }
    }

    private function backupFiles(string $timestamp): void
    {
        $storagePath = storage_path('app/public');
        if (!is_dir($storagePath)) {
            return;
        }

        $tarPath = storage_path("app/backups/files_{$timestamp}.tar.gz");
        $cmd = sprintf('tar -czf %s -C %s .', escapeshellarg($tarPath), escapeshellarg($storagePath));
        exec($cmd, result_code: $exitCode);

        if ($exitCode === 0) {
            $this->info("Files backup: files_{$timestamp}.tar.gz");
        }
    }

    private function cleanOldBackups(int $keep, string $disk): void
    {
        $backups = collect(Storage::disk($disk)->files('backups'))
            ->filter(fn ($f) => str_starts_with(basename($f), 'database_'))
            ->sort()
            ->reverse()
            ->values();

        foreach ($backups->slice($keep) as $old) {
            Storage::disk($disk)->delete($old);
            $this->line("Removed old backup: {$old}");
        }
    }
}
