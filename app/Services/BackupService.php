<?php

namespace App\Services;

use App\Models\Backup;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupService
{
    protected string $backupPath = 'backups';
    protected int $retentionDays = 30;

    public function __construct()
    {
        if (!Storage::disk('local')->exists($this->backupPath)) {
            Storage::disk('local')->makeDirectory($this->backupPath);
        }
    }

    public function createFullBackup(?int $createdBy = null): Backup
    {
        $backup = Backup::create([
            'type' => Backup::TYPE_FULL,
            'status' => Backup::STATUS_PENDING,
            'created_by' => $createdBy ?? auth()->id(),
        ]);

        try {
            $backup->markAsRunning();

            $timestamp = now()->format('Y-m-d_His');
            $fileName = "full_backup_{$timestamp}.zip";
            $filePath = $this->backupPath . '/' . $fileName;

            $zip = new ZipArchive();
            if ($zip->open(storage_path('app/' . $filePath), ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Failed to create ZIP archive');
            }

            $this->addDatabaseDump($zip);
            $this->addFilesBackup($zip);
            
            $zip->close();

            $fileSize = Storage::disk('local')->size($filePath);
            $backup->markAsCompleted($filePath, $fileSize);

            $this->cleanupOldBackups();

            Log::info('Full backup completed', [
                'backup_id' => $backup->id,
                'file_size' => $fileSize,
            ]);

            return $backup;
        } catch (\Exception $e) {
            $backup->markAsFailed($e->getMessage());
            Log::error('Full backup failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createSchoolBackup(School $school, ?int $createdBy = null): Backup
    {
        $backup = Backup::create([
            'type' => Backup::TYPE_SCHOOL,
            'school_id' => $school->id,
            'status' => Backup::STATUS_PENDING,
            'created_by' => $createdBy ?? auth()->id(),
        ]);

        try {
            $backup->markAsRunning();

            $timestamp = now()->format('Y-m-d_His');
            $sanitizedName = preg_replace('/[^a-zA-Z0-9]/', '_', $school->code);
            $fileName = "school_{$sanitizedName}_{$timestamp}.zip";
            $filePath = $this->backupPath . '/schools/' . $fileName;

            if (!Storage::disk('local')->exists($this->backupPath . '/schools')) {
                Storage::disk('local')->makeDirectory($this->backupPath . '/schools');
            }

            $zip = new ZipArchive();
            if ($zip->open(storage_path('app/' . $filePath), ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Failed to create ZIP archive');
            }

            $this->addSchoolDatabaseDump($zip, $school);
            $this->addSchoolFilesBackup($zip, $school);
            $this->addSchoolMetadata($zip, $school);
            
            $zip->close();

            $fileSize = Storage::disk('local')->size($filePath);
            $backup->markAsCompleted($filePath, $fileSize);

            Log::info('School backup completed', [
                'backup_id' => $backup->id,
                'school_id' => $school->id,
                'school_code' => $school->code,
            ]);

            return $backup;
        } catch (\Exception $e) {
            $backup->markAsFailed($e->getMessage());
            Log::error('School backup failed', [
                'backup_id' => $backup->id,
                'school_id' => $school->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createDatabaseBackup(?int $createdBy = null): Backup
    {
        $backup = Backup::create([
            'type' => Backup::TYPE_DATABASE,
            'status' => Backup::STATUS_PENDING,
            'created_by' => $createdBy ?? auth()->id(),
        ]);

        try {
            $backup->markAsRunning();

            $timestamp = now()->format('Y-m-d_His');
            $fileName = "database_backup_{$timestamp}.sql";
            $filePath = $this->backupPath . '/database/' . $fileName;

            if (!Storage::disk('local')->exists($this->backupPath . '/database')) {
                Storage::disk('local')->makeDirectory($this->backupPath . '/database');
            }

            $this->addDatabaseDumpToFile($filePath);

            $fileSize = Storage::disk('local')->size($filePath);
            $backup->markAsCompleted($filePath, $fileSize);

            return $backup;
        } catch (\Exception $e) {
            $backup->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    public function restoreFromBackup(Backup $backup): bool
    {
        if ($backup->status !== Backup::STATUS_COMPLETED) {
            throw new \Exception('Can only restore from completed backups');
        }

        $fullPath = storage_path('app/' . $backup->file_path);
        
        if (!file_exists($fullPath)) {
            throw new \Exception('Backup file not found');
        }

        Log::info('Starting backup restoration', [
            'backup_id' => $backup->id,
            'type' => $backup->type,
        ]);

        $zip = new ZipArchive();
        if ($zip->open($fullPath) !== true) {
            throw new \Exception('Failed to open backup archive');
        }

        try {
            if ($backup->type === Backup::TYPE_FULL) {
                $this->restoreFullBackup($zip);
            } elseif ($backup->type === Backup::TYPE_SCHOOL) {
                $this->restoreSchoolBackup($zip, $backup->school);
            } elseif ($backup->type === Backup::TYPE_DATABASE) {
                $this->restoreDatabaseBackup($zip);
            }

            $zip->close();

            Log::info('Backup restoration completed', [
                'backup_id' => $backup->id,
            ]);

            return true;
        } catch (\Exception $e) {
            $zip->close();
            Log::error('Backup restoration failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function addDatabaseDump(ZipArchive $zip): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $zip->addFile($dbPath, 'database/database.sqlite');
            }
        } else {
            $timestamp = now()->format('Y-m-d_His');
            $tempFile = storage_path('app/temp_db_' . $timestamp . '.sql');
            $this->dumpDatabase($tempFile);
            if (file_exists($tempFile)) {
                $zip->addFile($tempFile, 'database/backup.sql');
                unlink($tempFile);
            }
        }
    }

    protected function addDatabaseDumpToFile(string $filePath): void
    {
        $this->dumpDatabase(storage_path('app/' . $filePath));
    }

    protected function dumpDatabase(string $filePath): void
    {
        Artisan::call('db:export', ['--file' => $filePath]);
    }

    protected function addFilesBackup(ZipArchive $zip, string $sourcePath = null): void
    {
        $sourcePath = $sourcePath ?? storage_path('app/public');
        
        if (is_dir($sourcePath)) {
            $this->addDirectoryToZip($zip, $sourcePath, 'files');
        }
    }

    protected function addSchoolDatabaseDump(ZipArchive $zip, School $school): void
    {
        $schoolData = [
            'school' => $school->toArray(),
            'users' => $school->users()->get()->toArray(),
            'applications' => $school->applications()->get()->toArray(),
            'programs' => $school->programs()->get()->toArray(),
            'departments' => $school->departments()->get()->toArray(),
            'intakes' => $school->intakes()->get()->toArray(),
            'payments' => $school->payments()->get()->toArray(),
            'documents' => $school->documents()->get()->toArray(),
            'settings' => $school->settings()->get()->toArray(),
            'audit_logs' => $school->auditLogs()->get()->toArray(),
        ];

        $zip->addFromString('school_data.json', json_encode($schoolData, JSON_PRETTY_PRINT));
    }

    protected function addSchoolFilesBackup(ZipArchive $zip, School $school): void
    {
        $schoolPath = storage_path('app/public/schools/' . $school->id);
        
        if (is_dir($schoolPath)) {
            $this->addDirectoryToZip($zip, $schoolPath, 'files');
        }
    }

    protected function addSchoolMetadata(ZipArchive $zip, School $school): void
    {
        $metadata = [
            'school_id' => $school->id,
            'school_code' => $school->code,
            'backup_date' => now()->toIso8601String(),
            'version' => '1.0',
        ];

        $zip->addFromString('metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipPath): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->isDir() 
                ? $zipPath . '/' . $files->getSubPathName()
                : $zipPath . '/' . $file->getFilename();
            
            if ($file->isDir()) {
                $zip->addEmptyDir($filePath);
            } else {
                $zip->addFile($file->getPathname(), $filePath);
            }
        }
    }

    protected function restoreFullBackup(ZipArchive $zip): void
    {
        $tempDir = storage_path('app/temp_restore_' . now()->timestamp);
        mkdir($tempDir, 0755, true);

        $zip->extractTo($tempDir);

        if (file_exists($tempDir . '/database/database.sqlite')) {
            copy($tempDir . '/database/database.sqlite', database_path('database.sqlite'));
        }

        if (is_dir($tempDir . '/files')) {
            $this->copyDirectory($tempDir . '/files', storage_path('app/public'));
        }

        $this->recursiveDelete($tempDir);
    }

    protected function restoreSchoolBackup(ZipArchive $zip, School $school): void
    {
        $tempDir = storage_path('app/temp_school_restore_' . now()->timestamp);
        mkdir($tempDir, 0755, true);

        $zip->extractTo($tempDir);

        if (file_exists($tempDir . '/school_data.json')) {
            $data = json_decode(file_get_contents($tempDir . '/school_data.json'), true);
            
            if (isset($data['settings'])) {
                foreach ($data['settings'] as $setting) {
                    DB::table('settings')->updateOrInsert(
                        ['school_id' => $school->id, 'key' => $setting['key']],
                        ['value' => $setting['value'], 'group' => $setting['group'] ?? 'general']
                    );
                }
            }
        }

        if (is_dir($tempDir . '/files/schools/' . $school->id)) {
            $targetPath = storage_path('app/public/schools/' . $school->id);
            if (!is_dir(dirname($targetPath))) {
                mkdir(dirname($targetPath), 0755, true);
            }
            $this->copyDirectory($tempDir . '/files/schools/' . $school->id, $targetPath);
        }

        $this->recursiveDelete($tempDir);
    }

    protected function restoreDatabaseBackup(ZipArchive $zip): void
    {
        $tempDir = storage_path('app/temp_db_restore_' . now()->timestamp);
        mkdir($tempDir, 0755, true);

        $zip->extractTo($tempDir);

        $sqlFiles = glob($tempDir . '/*.sql');
        if (!empty($sqlFiles)) {
            Artisan::call('db:import', ['--file' => $sqlFiles[0]]);
        }

        $this->recursiveDelete($tempDir);
    }

    protected function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $srcPath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;
            
            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $destPath);
            } else {
                copy($srcPath, $destPath);
            }
        }
        closedir($dir);
    }

    protected function recursiveDelete(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->recursiveDelete($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function cleanupOldBackups(): int
    {
        $deletedCount = 0;

        Backup::where('created_at', '<', now()->subDays($this->retentionDays))
            ->where('status', Backup::STATUS_COMPLETED)
            ->each(function ($backup) use (&$deletedCount) {
                if ($backup->file_path && Storage::disk('local')->exists($backup->file_path)) {
                    Storage::disk('local')->delete($backup->file_path);
                }
                $backup->delete();
                $deletedCount++;
            });

        return $deletedCount;
    }

    public function getBackupStats(): array
    {
        return [
            'total_backups' => Backup::count(),
            'completed' => Backup::successful()->count(),
            'failed' => Backup::failed()->count(),
            'pending' => Backup::where('status', Backup::STATUS_PENDING)->count(),
            'running' => Backup::where('status', Backup::STATUS_RUNNING)->count(),
            'total_size' => Backup::successful()->sum('file_size'),
            'last_backup' => Backup::successful()->latest()->first(),
        ];
    }

    /**
     * Accept an externally uploaded backup file (zip or sql) and register it.
     */
    public function uploadExternalBackup($file, ?int $createdBy = null): Backup
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('Invalid uploaded file.');
        }

        $original = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['zip', 'sql'], true)) {
            throw new \Exception('Only .zip and .sql backup files are allowed.');
        }

        if ($file->getSize() > 512 * 1024 * 1024) {
            throw new \Exception('Backup file must be 512MB or smaller.');
        }

        $timestamp = now()->format('Y-m-d_His');
        $safeName = 'uploaded_' . $timestamp . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $filePath = $this->backupPath . '/' . $safeName;

        $file->storeAs($this->backupPath, $safeName, 'local');

        $type = $ext === 'sql' ? Backup::TYPE_DATABASE : Backup::TYPE_FULL;
        $backup = Backup::create([
            'type' => $type,
            'status' => Backup::STATUS_COMPLETED,
            'file_path' => $filePath,
            'file_name' => $safeName,
            'file_size' => Storage::disk('local')->size($filePath),
            'created_by' => $createdBy ?? auth()->id(),
        ]);

        Log::info('External backup uploaded', [
            'backup_id' => $backup->id,
            'original_name' => $original,
        ]);

        return $backup;
    }

    /**
     * Restore from an uploaded backup file (zip or sql) without requiring a prior system record.
     */
    public function restoreFromUploadedFile($file): bool
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('Invalid uploaded file.');
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['zip', 'sql'], true)) {
            throw new \Exception('Only .zip and .sql backup files are allowed.');
        }

        if ($file->getSize() > 512 * 1024 * 1024) {
            throw new \Exception('Backup file must be 512MB or smaller.');
        }

        $timestamp = now()->format('Y-m-d_His');
        $tempName = 'restore_upload_' . $timestamp . '.' . $ext;
        $tempPath = storage_path('app/' . $this->backupPath . '/' . $tempName);
        $file->move(storage_path('app/' . $this->backupPath), $tempName);

        try {
            if ($ext === 'sql') {
                $this->restoreSqlFile($tempPath);
            } else {
                $zip = new ZipArchive();
                if ($zip->open($tempPath) !== true) {
                    throw new \Exception('Failed to open uploaded backup archive');
                }
                $this->restoreFullBackup($zip);
                $zip->close();
            }

            Log::warning('Backup restored from uploaded file', [
                'file' => $tempName,
                'user_id' => auth()->id(),
            ]);

            return true;
        } catch (\Exception $e) {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            Log::error('Uploaded backup restore failed', ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    protected function restoreSqlFile(string $sqlPath): void
    {
        if (!file_exists($sqlPath)) {
            throw new \Exception('SQL backup file not found');
        }

        $driver = DB::connection()->getDriverName();
        $sql = file_get_contents($sqlPath);

        if ($driver === 'sqlite') {
            $pdo = DB::connection()->getPdo();
            $pdo->exec($sql);
            return;
        }

        if ($driver === 'mysql') {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host', '127.0.0.1');

            $cnf = tempnam(sys_get_temp_dir(), 'mycnf_');
            file_put_contents($cnf, "[client]\nhost={$host}\nuser={$username}\npassword={$password}\n");
            @chmod($cnf, 0600);

            $cmd = sprintf(
                'mysql --defaults-extra-file=%s %s < %s',
                escapeshellarg($cnf),
                escapeshellarg($database),
                escapeshellarg($sqlPath)
            );
            exec($cmd, $output, $returnVar);
            @unlink($cnf);

            if ($returnVar !== 0) {
                throw new \Exception('MySQL restore failed: ' . implode(' ', $output));
            }
            return;
        }

        throw new \Exception('Unsupported database driver for SQL restore: ' . $driver);
    }
}
