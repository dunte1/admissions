<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterTemplate extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'header_content',
        'body_content',
        'footer_content',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(LetterTemplateVersion::class, 'letter_template_id')->orderByDesc('version');
    }

    public function currentVersion(): LetterTemplateVersion
    {
        return $this->versions()->first();
    }

    public function createVersion(string $header, string $body, string $footer, ?string $note = null, ?int $userId = null): LetterTemplateVersion
    {
        $latestVersion = $this->versions()->max('version') ?? 0;
        
        return LetterTemplateVersion::create([
            'letter_template_id' => $this->id,
            'version' => $latestVersion + 1,
            'header_content' => $header,
            'body_content' => $body,
            'footer_content' => $footer,
            'change_note' => $note,
            'created_by' => $userId ?? auth()->id(),
            'created_at' => now(),
        ]);
    }

    public function revertTo(int $versionNumber): bool
    {
        $version = $this->versions()->where('version', $versionNumber)->first();
        
        if (!$version) {
            return false;
        }

        $this->update([
            'header_content' => $version->header_content,
            'body_content' => $version->body_content,
            'footer_content' => $version->footer_content,
        ]);

        $this->createVersion(
            $version->header_content,
            $version->body_content,
            $version->footer_content,
            "Reverted to version {$versionNumber}",
            auth()->id()
        );

        return true;
    }

    public static function getAvailableTypes(): array
    {
        return [
            'admission' => 'Admission Letter',
            'rejection' => 'Rejection Letter',
            'provisional' => 'Provisional Offer',
            'deferral' => 'Deferral Letter',
            'calling' => 'Calling/Interview Letter',
        ];
    }

    public static function getDefaultContent(string $type): array
    {
        return match($type) {
            'admission' => [
                'header' => '<h1>{{institution}}</h1><p>{{address}}</p><p>Tel: {{phone}} | Email: {{email}}</p>',
                'body' => '<p>Dear {{student_name}},</p><p>We are pleased to offer you admission to <strong>{{program}}</strong>.</p><p>Letter Reference: {{letter_number}}</p>',
                'footer' => '<p>This is a system-generated document.</p>',
            ],
            'rejection' => [
                'header' => '<h1>{{institution}}</h1><p>{{address}}</p>',
                'body' => '<p>Dear {{student_name}},</p><p>After careful review, we regret to inform you that we cannot offer you admission at this time.</p>',
                'footer' => '<p>For any inquiries, contact the admissions office.</p>',
            ],
            'calling' => [
                'header' => '<h1>{{institution}}</h1><p>{{address}}</p>',
                'body' => '<p>Dear {{student_name}},</p><p>You are invited to attend an interview on <strong>{{interview_date}}</strong> at <strong>{{interview_venue}}</strong>.</p>',
                'footer' => '<p>Please bring ID and application number.</p>',
            ],
            default => [
                'header' => '<h1>{{institution}}</h1>',
                'body' => '<p>Dear {{student_name}},</p><p>Your application status: {{status}}</p>',
                'footer' => '<p>System-generated document.</p>',
            ],
        };
    }
}