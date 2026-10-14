<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Display the Database & System Backup Manager (Super Admin only).
     */
    public function index(): View
    {
        $backups = $this->backupService->getBackups();

        // Calculate time in Asia/Manila (PHT, UTC+8)
        $nowPh = Carbon::now('Asia/Manila');
        $nowUtc = Carbon::now('UTC');

        // Next 6:00 AM PHT
        $next6AmPh = Carbon::now('Asia/Manila')->setHour(6)->setMinute(0)->setSecond(0);
        if ($nowPh->greaterThanOrEqualTo($next6AmPh)) {
            $next6AmPh->addDay();
        }

        $nextBackupDiff = $next6AmPh->diffForHumans($nowPh, [
            'parts' => 2,
            'short' => false,
        ]);

        $dbConnection = config('database.default');

        return view('admin.backups.index', compact(
            'backups',
            'nowPh',
            'nowUtc',
            'next6AmPh',
            'nextBackupDiff',
            'dbConnection'
        ));
    }

    /**
     * Trigger a manual backup generation.
     */
    public function store(): RedirectResponse
    {
        try {
            $backup = $this->backupService->createBackup();
            return back()->with('status', "Manual backup successfully created: {$backup['filename']} ({$backup['size_formatted']}).");
        } catch (Exception $e) {
            return back()->with('error', "Backup failed: " . $e->getMessage());
        }
    }

    /**
     * Download a specific backup archive.
     */
    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        $safeName = basename($filename);
        $path = BackupService::getBackupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (! file_exists($path)) {
            return back()->with('error', "Backup file '{$safeName}' not found.");
        }

        return response()->download($path, $safeName, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Restore from an uploaded backup archive or existing backup.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['nullable', 'file', 'mimes:zip,sqlite', 'max:102400'], // max 100MB
            'filename' => ['nullable', 'string'],
        ]);

        try {
            if ($request->hasFile('backup_file')) {
                $this->backupService->restoreBackup($request->file('backup_file'));
                return back()->with('status', "Database and assets successfully restored from uploaded archive.");
            } elseif ($request->filled('filename')) {
                $this->backupService->restoreBackup($request->filename);
                return back()->with('status', "Database and assets successfully restored from snapshot '{$request->filename}'.");
            }

            return back()->with('error', "Please select a backup file to restore.");
        } catch (Exception $e) {
            return back()->with('error', "Restore failed: " . $e->getMessage());
        }
    }

    /**
     * Delete an existing backup file.
     */
    public function destroy(string $filename): RedirectResponse
    {
        $safeName = basename($filename);
        if ($this->backupService->deleteBackup($safeName)) {
            return back()->with('status', "Backup '{$safeName}' deleted.");
        }

        return back()->with('error', "Backup file '{$safeName}' could not be deleted or does not exist.");
    }
}
