<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

        // Backup SQLite database
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $backupPath = "backups/database_{$timestamp}.sqlite";
            Storage::disk($disk)->put($backupPath, file_get_contents($dbPath));
            $this->info("Database backup: {$backupPath}");
        }

        // Backup uploaded files
        $storagePath = storage_path('app/public');
        if (is_dir($storagePath)) {
            $tarPath = storage_path("app/backups/files_{$timestamp}.tar.gz");
            $cmd = sprintf('tar -czf %s -C %s .', escapeshellarg($tarPath), escapeshellarg($storagePath));
            exec($cmd, result_code: $exitCode);
            if ($exitCode === 0) {
                $this->info("Files backup: files_{$timestamp}.tar.gz");
            }
        }

        // Clean old backups
        $this->cleanOldBackups($keep);

        $this->info('Backup complete.');
        return self::SUCCESS;
    }

    private function cleanOldBackups(int $keep): void
    {
        $backups = collect(Storage::disk('local')->files('backups'))
            ->filter(fn ($f) => str_starts_with(basename($f), 'database_'))
            ->sort()
            ->reverse()
            ->values();

        foreach ($backups->slice($keep) as $old) {
            Storage::disk('local')->delete($old);
            $this->line("Removed old backup: {$old}");
        }
    }
}
