<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->middleware(['auth', 'role:super_admin']);
        $this->backupService = $backupService;
    }

    public function index(Request $request)
    {
        $query = Backup::with(['school', 'creator'])->latest();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $backups = $query->paginate(20);
        $stats = $this->backupService->getBackupStats();

        return view('super-admin.backups.index', compact('backups', 'stats'));
    }

    public function createFullBackup(Request $request)
    {
        try {
            $backup = $this->backupService->createFullBackup(auth()->id());

            return redirect()->back()->with('success', 'Full backup created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function createDatabaseBackup(Request $request)
    {
        try {
            $backup = $this->backupService->createDatabaseBackup(auth()->id());

            return redirect()->back()->with('success', 'Database backup created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        if ($backup->status !== Backup::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Cannot download incomplete backup.');
        }

        $path = storage_path('app/' . $backup->file_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        return response()->download($path, $backup->file_name ?? 'backup.zip');
    }

    public function restore(Request $request, Backup $backup)
    {
        $request->validate([
            'confirm' => 'required',
        ], [
            'confirm.required' => 'You must confirm the restore action.',
        ]);

        try {
            $this->backupService->restoreFromBackup($backup);

            return redirect()->back()->with('success', 'Backup restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Backup $backup)
    {
        try {
            if ($backup->file_path && \Storage::disk('local')->exists($backup->file_path)) {
                \Storage::disk('local')->delete($backup->file_path);
            }

            $backup->delete();

            return redirect()->back()->with('success', 'Backup deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function cleanup(Request $request)
    {
        try {
            $deleted = $this->backupService->cleanupOldBackups();

            return redirect()->back()->with('success', "Cleaned up {$deleted} old backup(s).");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cleanup failed: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip,sql|max:524288',
        ], [
            'backup_file.mimes' => 'Only .zip and .sql backup files are allowed.',
            'backup_file.max' => 'Backup file must be 512MB or smaller.',
        ]);

        try {
            $backup = $this->backupService->uploadExternalBackup($request->file('backup_file'), auth()->id());
            return redirect()->back()->with('success', 'Backup uploaded: ' . $backup->file_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function restoreUpload(Request $request)
    {
        $request->validate([
            'restore_file' => 'required|file|mimes:zip,sql|max:524288',
            'confirm' => 'required',
        ], [
            'restore_file.mimes' => 'Only .zip and .sql backup files are allowed.',
            'restore_file.max' => 'Backup file must be 512MB or smaller.',
            'confirm.required' => 'You must confirm the restore action.',
        ]);

        try {
            $this->backupService->restoreFromUploadedFile($request->file('restore_file'));
            return redirect()->back()->with('success', 'Uploaded backup restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}
