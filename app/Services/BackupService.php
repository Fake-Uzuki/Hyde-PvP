<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupService
{
    /**
     * Directory path where backups are stored.
     */
    public static function getBackupDir(): string
    {
        $dir = storage_path('app/backups');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        return $dir;
    }

    /**
     * Create a full backup archive of the database and uploaded storage assets.
     *
     * @return array{filename: string, path: string, size: int, size_formatted: string, created_at: Carbon}
     * @throws Exception
     */
    public function createBackup(): array
    {
        $backupDir = self::getBackupDir();
        $timestamp = Carbon::now('Asia/Manila')->format('Y-m-d_H-i-s');
        $zipFilename = "hyde-pvp-backup-{$timestamp}.zip";
        $zipPath = $backupDir . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new ZipArchive();
        $status = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($status !== true) {
            throw new Exception("Unable to create zip backup archive. Code: {$status}");
        }

        $connection = config('database.default');

        // 1. Database backup based on connection type
        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if (empty($dbPath) || $dbPath === ':memory:') {
                $dbPath = database_path('database.sqlite');
            }

            if (File::exists($dbPath)) {
                // Checkpoint SQLite WAL journal if applicable
                try {
                    DB::connection('sqlite')->statement('PRAGMA wal_checkpoint(TRUNCATE)');
                } catch (\Throwable $e) {
                    // Ignore if not in WAL mode
                }

                $zip->addFile($dbPath, 'database.sqlite');
            }
        } else {
            // MySQL, PostgreSQL or generic PDO: Generate SQL dump
            $sqlDump = $this->generateSqlDump();
            $zip->addFromString('database.sql', $sqlDump);
        }

        // 2. Include uploaded public assets if they exist (skins, avatars, news images)
        $publicStorage = storage_path('app/public');
        if (File::isDirectory($publicStorage)) {
            $files = File::allFiles($publicStorage);
            foreach ($files as $file) {
                $relativePath = 'storage/' . $file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        // 3. Include backup metadata JSON
        $metadata = [
            'app_name' => config('app.name', 'HYDE PVP'),
            'backup_timestamp_ph' => Carbon::now('Asia/Manila')->toIso8601String(),
            'backup_timestamp_utc' => Carbon::now('UTC')->toIso8601String(),
            'db_connection' => $connection,
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
        ];
        $zip->addFromString('backup-manifest.json', json_encode($metadata, JSON_PRETTY_PRINT));

        $zip->close();

        $size = File::size($zipPath);

        Log::info("Backup successfully generated: {$zipFilename} ({$this->formatBytes($size)})");

        return [
            'filename' => $zipFilename,
            'path' => $zipPath,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
            'created_at' => Carbon::now('Asia/Manila'),
        ];
    }

    /**
     * Retrieve all existing backups sorted by newest first.
     *
     * @return array<int, array{filename: string, path: string, size: int, size_formatted: string, created_at_ph: string, created_at_diff: string}>
     */
    public function getBackups(): array
    {
        $dir = self::getBackupDir();
        $files = File::files($dir);

        $backups = [];
        foreach ($files as $file) {
            if ($file->getExtension() !== 'zip' && $file->getExtension() !== 'sqlite') {
                continue;
            }

            $mtime = Carbon::createFromTimestamp($file->getMTime(), 'Asia/Manila');
            $backups[] = [
                'filename' => $file->getFilename(),
                'path' => $file->getRealPath(),
                'size' => $file->getSize(),
                'size_formatted' => $this->formatBytes($file->getSize()),
                'created_at_ph' => $mtime->format('M d, Y h:i A') . ' PHT',
                'created_at_diff' => $mtime->diffForHumans(),
                'mtime' => $file->getMTime(),
            ];
        }

        usort($backups, fn ($a, $b) => $b['mtime'] <=> $a['mtime']);

        return $backups;
    }

    /**
     * Delete an existing backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        // Sanitize filename to prevent directory traversal
        $safeName = basename($filename);
        $path = self::getBackupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($path)) {
            return File::delete($path);
        }

        return false;
    }

    /**
     * Restore database and assets from a backup file.
     *
     * @throws Exception
     */
    public function restoreBackup(string|UploadedFile $backupSource): bool
    {
        if ($backupSource instanceof UploadedFile) {
            $path = $backupSource->getRealPath();
            $ext = strtolower($backupSource->getClientOriginalExtension());
        } else {
            $safeName = basename($backupSource);
            $path = self::getBackupDir() . DIRECTORY_SEPARATOR . $safeName;
            $ext = pathinfo($path, PATHINFO_EXTENSION);
        }

        if (! File::exists($path)) {
            throw new Exception("Backup file not found at: {$path}");
        }

        // If raw SQLite file
        if ($ext === 'sqlite') {
            $dest = config('database.connections.sqlite.database');
            if (empty($dest) || $dest === ':memory:') {
                $dest = database_path('database.sqlite');
            }

            File::copy($path, $dest);
            return true;
        }

        // If ZIP archive
        if ($ext === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($path) !== true) {
                throw new Exception("Failed to open backup ZIP archive.");
            }

            // Extract database
            $connection = config('database.default');

            if ($connection === 'sqlite') {
                $dest = config('database.connections.sqlite.database');
                if (empty($dest) || $dest === ':memory:') {
                    $dest = database_path('database.sqlite');
                }

                $sqliteContent = $zip->getFromName('database.sqlite');
                if ($sqliteContent !== false) {
                    File::put($dest, $sqliteContent);
                }
            } else {
                $sqlContent = $zip->getFromName('database.sql');
                if ($sqlContent !== false) {
                    DB::unprepared($sqlContent);
                }
            }

            // Extract public assets if present
            $extractDir = storage_path('app/public');
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_starts_with($filename, 'storage/')) {
                    $relative = substr($filename, strlen('storage/'));
                    $fullDest = $extractDir . DIRECTORY_SEPARATOR . $relative;
                    File::ensureDirectoryExists(dirname($fullDest));
                    File::put($fullDest, $zip->getFromIndex($i));
                }
            }

            $zip->close();
            return true;
        }

        throw new Exception("Unsupported backup format. Must be a .zip or .sqlite archive.");
    }

    /**
     * Clean up old backups keeping the most recent N files.
     */
    public function cleanOldBackups(int $keepCount = 14): int
    {
        $backups = $this->getBackups();
        $deleted = 0;

        if (count($backups) > $keepCount) {
            $toDelete = array_slice($backups, $keepCount);
            foreach ($toDelete as $item) {
                if ($this->deleteBackup($item['filename'])) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * Generate an SQL dump string for non-sqlite databases.
     */
    protected function generateSqlDump(): string
    {
        $buffer = "-- HYDE PVP Database Backup\n";
        $buffer .= "-- Generated: " . Carbon::now('Asia/Manila')->toIso8601String() . " (PHT)\n\n";

        $schema = DB::connection()->getSchemaBuilder();
        $tables = $schema->getTableListing();

        foreach ($tables as $table) {
            // Skip Laravel internal migrations or cache if desired, but good to keep all
            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $buffer .= "\n-- Table data: {$table}\n";
            foreach ($rows as $row) {
                $array = (array) $row;
                $cols = array_keys($array);
                $values = array_map(function ($val) {
                    if ($val === null) {
                        return 'NULL';
                    }
                    return DB::getPdo()->quote((string) $val);
                }, array_values($array));

                $buffer .= "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
        }

        return $buffer;
    }

    /**
     * Format byte sizes into human readable units.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
