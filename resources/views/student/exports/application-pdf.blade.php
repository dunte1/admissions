<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application {{ $application->application_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #333; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #00008B; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #00008B; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .app-number { background: #f0f0f0; padding: 5px 10px; display: inline-block; margin-top: 10px; border-radius: 4px; }
        .section { margin-bottom: 15px; }
        .section-title { background: #00008B; color: white; padding: 6px 10px; font-size: 12px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px 8px; border: 1px solid #ddd; }
        .label { font-weight: bold; background: #f9f9f9; width: 35%; }
        .status-approved { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; font-size: 9px; text-align: center; color: #666; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ system_setting('app_name', 'Admission Portal') }}</h1>
            <p>Official Application Confirmation</p>
            <div class="app-number">
                <strong>Application No:</strong> {{ $application->application_number }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">Application Status</div>
            <table>
                <tr>
                    <td class="label">Status</td>
                    <td>
                        <span class="status-{{ $application->status }}">
                            @if($application->status === 'approved') APPROVED
                            @elseif($application->status === 'rejected') REJECTED
                            @elseif($application->status === 'pending') PENDING REVIEW
                            @elseif($application->status === 'under_review') UNDER REVIEW
                            @elseif($application->status === 'draft') DRAFT
                            @else {{ strtoupper(str_replace('_', ' ', $application->status)) }}
                            @endif
                        </span>
                    </td>
                    <td class="label">Submitted Date</td>
                    <td>{{ $application->submitted_at ? $application->submitted_at->format('d M Y') : 'Not Submitted' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Program Choices</div>
            <table>
                <tr>
                    <td class="label">First Choice</td>
                    <td>{{ $application->program->name ?? 'N/A' }}</td>
                </tr>
                @if($application->programChoice2)
                <tr>
                    <td class="label">Second Choice</td>
                    <td>{{ $application->programChoice2->name ?? 'N/A' }}</td>
                </tr>
                @endif
                @if($application->programChoice3)
                <tr>
                    <td class="label">Third Choice</td>
                    <td>{{ $application->programChoice3->name ?? 'N/A' }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="section">
            <div class="section-title">Personal Information</div>
            <table>
                @if(isset($formData['personal']))
                <tr>
                    <td class="label">Full Name</td>
                    <td>{{ $application->user->fullName() }}</td>
                    <td class="label">Email</td>
                    <td>{{ $application->user->email }}</td>
                </tr>
                <tr>
                    <td class="label">Date of Birth</td>
                    <td>{{ $formData['personal']['date_of_birth'] ?? 'N/A' }}</td>
                    <td class="label">Gender</td>
                    <td>{{ ucfirst($formData['personal']['gender'] ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">Nationality</td>
                    <td>{{ $formData['personal']['nationality'] ?? 'N/A' }}</td>
                    <td class="label">ID/Passport</td>
                    <td>{{ $formData['personal']['id_number'] ?? $formData['personal']['passport_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Phone</td>
                    <td>{{ $formData['personal']['phone'] ?? $application->user->phone ?? 'N/A' }}</td>
                    <td class="label">County</td>
                    <td>{{ $formData['personal']['county'] ?? 'N/A' }}</td>
                </tr>
                @else
                <tr>
                    <td class="label">Full Name</td>
                    <td>{{ $application->user->fullName() }}</td>
                    <td class="label">Email</td>
                    <td>{{ $application->user->email }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if(isset($formData['academic']))
        <div class="section">
            <div class="section-title">Academic Background</div>
            <table>
                <tr>
                    <td class="label">Education Level</td>
                    <td>{{ strtoupper($formData['academic']['education_level'] ?? 'N/A') }}</td>
                    <td class="label">Institution</td>
                    <td>{{ $formData['academic']['institution_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Year of Completion</td>
                    <td>{{ $formData['academic']['year_of_completion'] ?? 'N/A' }}</td>
                    <td class="label">Average Grade</td>
                    <td>{{ $formData['academic']['average_grade'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        @endif

        @if(isset($formData['guardian']))
        <div class="section">
            <div class="section-title">Guardian/ Sponsor Information</div>
            <table>
                <tr>
                    <td class="label">Name</td>
                    <td>{{ $formData['guardian']['guardian_name'] ?? 'N/A' }}</td>
                    <td class="label">Relationship</td>
                    <td>{{ ucfirst($formData['guardian']['guardian_relationship'] ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">Phone</td>
                    <td>{{ $formData['guardian']['guardian_phone'] ?? 'N/A' }}</td>
                    <td class="label">Email</td>
                    <td>{{ $formData['guardian']['guardian_email'] ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        @endif

        @if(isset($formData['financial']))
        <div class="section">
            <div class="section-title">Financial Information</div>
            <table>
                <tr>
                    <td class="label">Sponsorship Type</td>
                    <td colspan="3">{{ ucfirst($formData['financial']['sponsorship_type'] ?? 'N/A') }}</td>
                </tr>
                @if(isset($formData['financial']['sponsor_name']))
                <tr>
                    <td class="label">Sponsor Name</td>
                    <td>{{ $formData['financial']['sponsor_name'] }}</td>
                    <td class="label">Sponsor Phone</td>
                    <td>{{ $formData['financial']['sponsor_phone'] }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        @if($application->documents && $application->documents->count() > 0)
        <div class="section">
            <div class="section-title">Uploaded Documents</div>
            <table>
                <tr>
                    <th style="background:#f0f0f0;">Document Type</th>
                    <th style="background:#f0f0f0;">File Name</th>
                    <th style="background:#f0f0f0;">Status</th>
                </tr>
                @foreach($application->documents as $doc)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $doc->type)) }}</td>
                    <td>{{ $doc->original_name }}</td>
                    <td>{{ ucfirst($doc->status) }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        @if($application->payments && $application->payments->count() > 0)
        <div class="section">
            <div class="section-title">Payment History</div>
            <table>
                <tr>
                    <th style="background:#f0f0f0;">Reference</th>
                    <th style="background:#f0f0f0;">Amount</th>
                    <th style="background:#f0f0f0;">Method</th>
                    <th style="background:#f0f0f0;">Status</th>
                    <th style="background:#f0f0f0;">Date</th>
                </tr>
                @foreach($application->payments as $payment)
                <tr>
                    <td>{{ $payment->reference }}</td>
                    <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ strtoupper($payment->payment_method) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y') : 'Pending' }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <div class="footer">
            <p>This is an official document from {{ system_setting('app_name', 'Admission Portal') }}</p>
            <p>Generated on {{ now()->format('d M Y H:i') }} | Application ID: {{ $application->id }}</p>
            <p style="margin-top: 5px;">{{ system_setting('app_url', config('app.url')) }}</p>
        </div>
    </div>
</body>
</html>
