<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicationsExport implements FromCollection, WithHeadings
{
    protected $applications;

    public function __construct($applications = null)
    {
        $this->applications = $applications;
    }

    public function collection()
    {
        $apps = $this->applications ?? Application::with(['student', 'program'])->get();

        return $apps->map(function ($app) {
            return [
                'Application Number' => $app->application_number,
                'Applicant Name' => $app->student ? $app->student->full_name : ($app->user->fullName() ?? 'N/A'),
                'Email' => $app->user->email ?? 'N/A',
                'Phone' => $app->user->phone ?? 'N/A',
                'Program' => $app->program->name ?? 'N/A',
                'Status' => ucfirst(str_replace('_', ' ', $app->status)),
                'Applied Date' => $app->created_at->format('Y-m-d H:i:s'),
                'Reviewed Date' => $app->reviewed_at?->format('Y-m-d H:i:s') ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Application Number',
            'Applicant Name',
            'Email',
            'Phone',
            'Program',
            'Status',
            'Applied Date',
            'Reviewed Date',
        ];
    }
}
