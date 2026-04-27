<?php

namespace App\Services;

use App\Models\School;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadFile($file, string $directory, ?string $oldPath = null, ?int $schoolId = null): ?string
    {
        if (!$file) {
            return $oldPath;
        }

        if ($oldPath) {
            $this->deleteFile($oldPath);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        $schoolId = $schoolId ?? School::getCurrentId();
        
        if ($schoolId) {
            $directory = "schools/{$schoolId}/{$directory}";
        }
        
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    public function uploadSchoolFile($file, string $directory, ?string $oldPath = null): ?string
    {
        return $this->uploadFile($file, $directory, $oldPath);
    }

    public function uploadApplicationFile($file, string $type, ?string $oldPath = null, ?int $applicationId = null): ?string
    {
        $schoolId = School::getCurrentId();
        
        if (!$schoolId) {
            $schoolId = config('app.default_school_id');
        }
        
        $directory = "schools/{$schoolId}/applications/{$type}";
        
        return $this->uploadFile($file, $directory, $oldPath);
    }

    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    public function getFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return asset("storage/{$path}");
    }

    public function validateFile($file, array $allowedTypes, int $maxSize): array
    {
        $errors = [];

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
        ];

        $allowedMimes = array_values(array_intersect_key($mimeTypes, array_flip($allowedTypes)));

        if ($file && !in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
        }

        if ($file && $file->getSize() > $maxSize * 1024) {
            $errors[] = "File size must not exceed {$maxSize}KB";
        }

        return $errors;
    }

    public function getSchoolStoragePath(?int $schoolId = null): string
    {
        $schoolId = $schoolId ?? School::getCurrentId();
        
        if (!$schoolId) {
            return 'uploads';
        }
        
        return "schools/{$schoolId}";
    }

    public function getApplicationStoragePath(?int $schoolId = null): string
    {
        $schoolId = $schoolId ?? School::getCurrentId();
        
        if (!$schoolId) {
            return 'applications';
        }
        
        return "schools/{$schoolId}/applications";
    }
}
