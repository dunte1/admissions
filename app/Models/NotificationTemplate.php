<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplate extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'type',
        'event',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public static function getAvailableEvents(): array
    {
        return [
            'application_received' => 'Application Received',
            'application_submitted' => 'Application Submitted',
            'application_approved' => 'Application Approved',
            'application_rejected' => 'Application Rejected',
            'application_info_requested' => 'Information Requested',
            'payment_received' => 'Payment Received',
            'payment_failed' => 'Payment Failed',
            'admission_letter' => 'Admission Letter Issued',
            'offer_accepted' => 'Offer Accepted',
            'offer_declined' => 'Offer Declined',
            'document_verified' => 'Document Verified',
            'document_rejected' => 'Document Rejected',
            'reminder_deadline' => 'Application Deadline Reminder',
            'welcome' => 'Welcome Message',
        ];
    }

    public static function getAvailableVariables(): array
    {
        return [
            '{{student_name}}' => 'Student Full Name',
            '{{student_first_name}}' => 'Student First Name',
            '{{student_email}}' => 'Student Email',
            '{{student_phone}}' => 'Student Phone',
            '{{application_number}}' => 'Application Number',
            '{{program_name}}' => 'Program Name',
            '{{school_name}}' => 'School Name',
            '{{school_email}}' => 'School Email',
            '{{school_phone}}' => 'School Phone',
            '{{amount}}' => 'Payment Amount',
            '{{currency}}' => 'Currency',
            '{{deadline}}' => 'Deadline Date',
            '{{date}}' => 'Current Date',
            '{{portal_link}}' => 'Portal Link',
        ];
    }

    public static function getDefaultTemplates(): array
    {
        return [
            [
                'type' => 'email',
                'event' => 'application_received',
                'subject' => 'Application Received - {{application_number}}',
                'body' => "Dear {{student_name}},\n\nThank you for submitting your application to {{school_name}}. We have received your application with reference number {{application_number}}.\n\nYour application is currently being reviewed. We will notify you of any updates via email.\n\nBest regards,\n{{school_name}} Admissions Team",
            ],
            [
                'type' => 'email',
                'event' => 'application_approved',
                'subject' => 'Congratulations! Application Approved - {{application_number}}',
                'body' => "Dear {{student_name}},\n\nWe are pleased to inform you that your application to {{school_name}} for the program {{program_name}} has been approved!\n\nYou will receive further instructions regarding the next steps via email.\n\nCongratulations and welcome aboard!\n\nBest regards,\n{{school_name}} Admissions Team",
            ],
            [
                'type' => 'email',
                'event' => 'application_rejected',
                'subject' => 'Application Update - {{application_number}}',
                'body' => "Dear {{student_name}},\n\nThank you for your interest in {{school_name}}. After careful consideration, we regret to inform you that we are unable to offer you a place in the {{program_name}} program at this time.\n\nThis decision does not reflect on your abilities, and we encourage you to apply for future intakes.\n\nBest regards,\n{{school_name}} Admissions Team",
            ],
            [
                'type' => 'sms',
                'event' => 'application_received',
                'body' => "Hi {{student_first_name}}, your application {{application_number}} to {{school_name}} has been received. We'll update you soon.",
            ],
            [
                'type' => 'sms',
                'event' => 'payment_received',
                'body' => "Hi {{student_first_name}}, payment of {{currency}}{{amount}} received for application {{application_number}}. Thank you!",
            ],
        ];
    }

    public function renderBody(array $data = []): string
    {
        $body = $this->body;
        
        foreach ($data as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        
        return $body;
    }
}
