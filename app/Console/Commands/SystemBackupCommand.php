<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SystemBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:backup {--keep=14 : Number of recent backups to preserve}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a full timestamped backup archive of the database and assets';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService): int
    {
        $nowPh = Carbon::now('Asia/Manila')->format('Y-m-d h:i:s A');
        $this->info("[{$nowPh} PHT] Initiating HYDE PVP automated system backup...");

        try {
            $backup = $backupService->createBackup();
            $this->info("✔ Backup created: {$backup['filename']} ({$backup['size_formatted']})");

            $keep = (int) $this->option('keep');
            $pruned = $backupService->cleanOldBackups($keep);
            if ($pruned > 0) {
                $this->comment("Cleaned up {$pruned} older backup archive(s).");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
