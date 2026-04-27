<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application {{ $application->application_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #00008B;
        }
        
        .header h1 {
            font-size: 18pt;
            color: #00008B;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            color: #333;
            font-weight: normal;
        }
        
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .meta-item {
            text-align: left;
        }
        
        .meta-item strong {
            display: block;
            color: #666;
            font-size: 8pt;
            text-transform: uppercase;
        }
        
        .meta-item span {
            font-size: 11pt;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #FEF3C7; color: #92400E; }
        .status-approved { background-color: #D1FAE5; color: #065F46; }
        .status-rejected { background-color: #FEE2E2; color: #991B1B; }
        .status-under_review { background-color: #DBEAFE; color: #1E40AF; }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #00008B;
            padding: 8px 0;
            border-bottom: 2px solid #00008B;
            margin-bottom: 15px;
        }
        
        .section-title i {
            margin-right: 8px;
        }
        
        .grid {
            display: table;
            width: 100%;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .field {
            margin-bottom: 10px;
        }
        
        .field-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .field-value {
            font-size: 10pt;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th {
            background-color: #00008B;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
        }
        
        .table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9pt;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .checklist {
            list-style: none;
        }
        
        .checklist li {
            padding: 5px 0;
            padding-left: 25px;
            position: relative;
        }
        
        .checklist li:before {
            content: "☐";
            position: absolute;
            left: 0;
            color: #00008B;
        }
        
        .checklist li.submitted:before {
            content: "☑";
            color: #059669;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        @page {
            margin: 20mm;
        }
        
        .print-date {
            text-align: right;
            font-size: 8pt;
            color: #999;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="print-date">
        Printed on: {{ now()->format('F d, Y H:i:s') }}
    </div>
    
    <div class="header">
        <h1>MUTOMO COLLEGE</h1>
        <h2>Application Form</h2>
    </div>
    
    <div class="meta-info">
        <div class="meta-item">
            <strong>Application Number</strong>
            <span>{{ $application->application_number }}</span>
        </div>
        <div class="meta-item">
            <strong>Status</strong>
            <span class="status-badge status-{{ $application->status }}">
                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
            </span>
        </div>
        <div class="meta-item">
            <strong>Program</strong>
            <span>{{ $application->program->name ?? 'N/A' }}</span>
        </div>
        <div class="meta-item">
            <strong>Applied On</strong>
            <span>{{ $application->created_at->format('M d, Y') }}</span>
        </div>
    </div>
    
    @if($application->student)
    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="grid-3">
            <div class="field">
                <div class="field-label">Full Name</div>
                <div class="field-value">{{ $application->student->full_name ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Gender</div>
                <div class="field-value">{{ ucfirst($formData['personal']['gender'] ?? 'N/A') }}</div>
            </div>
            <div class="field">
                <div class="field-label">Date of Birth</div>
                <div class="field-value">{{ $formData['personal']['date_of_birth'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">ID Number</div>
                <div class="field-value">{{ $formData['personal']['id_number'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Nationality</div>
                <div class="field-value">{{ $formData['personal']['nationality'] ?? 'Kenyan' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Phone</div>
                <div class="field-value">{{ $formData['personal']['phone'] ?? $application->user->phone ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">County</div>
                <div class="field-value">{{ $formData['personal']['county'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">City</div>
                <div class="field-value">{{ $formData['personal']['city'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($formData['academic']))
    <div class="section">
        <div class="section-title">Academic Background</div>
        <div class="grid-3">
            <div class="field">
                <div class="field-label">Education Level</div>
                <div class="field-value">{{ strtoupper($formData['academic']['education_level'] ?? 'N/A') }}</div>
            </div>
            <div class="field">
                <div class="field-label">Institution</div>
                <div class="field-value">{{ $formData['academic']['institution_name'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Certificate Type</div>
                <div class="field-value">{{ $formData['academic']['certificate_type'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Year Completed</div>
                <div class="field-value">{{ $formData['academic']['year_of_completion'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Average Grade</div>
                <div class="field-value">{{ $formData['academic']['average_grade'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($formData['guardian']))
    <div class="section">
        <div class="section-title">Guardian / Next of Kin</div>
        <div class="grid-3">
            <div class="field">
                <div class="field-label">Name</div>
                <div class="field-value">{{ $formData['guardian']['guardian_name'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Relationship</div>
                <div class="field-value">{{ ucfirst($formData['guardian']['guardian_relationship'] ?? 'N/A') }}</div>
            </div>
            <div class="field">
                <div class="field-label">Phone</div>
                <div class="field-value">{{ $formData['guardian']['guardian_phone'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Email</div>
                <div class="field-value">{{ $formData['guardian']['guardian_email'] ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Occupation</div>
                <div class="field-value">{{ $formData['guardian']['guardian_occupation'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($formData['financial']))
    <div class="section">
        <div class="section-title">Financial Information</div>
        <div class="grid-3">
            <div class="field">
                <div class="field-label">Sponsorship Type</div>
                <div class="field-value">{{ ucfirst(str_replace('_', ' ', $formData['financial']['sponsorship_type'] ?? 'N/A')) }}</div>
            </div>
            @if(isset($formData['financial']['sponsor_name']))
            <div class="field">
                <div class="field-label">Sponsor Name</div>
                <div class="field-value">{{ $formData['financial']['sponsor_name'] }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <div class="section">
        <div class="section-title">Documents Submitted</div>
        <ul class="checklist">
            @php
                $docTypes = [
                    'id_document' => 'ID Document / Passport',
                    'certificate' => 'Academic Certificate',
                    'photo' => 'Passport Photo',
                    'birth_certificate' => 'Birth Certificate',
                    'results_slip' => 'Results Slip',
                ];
            @endphp
            @forelse($application->documents as $doc)
                <li class="submitted">{{ $docTypes[$doc->type] ?? $doc->type }} - {{ $doc->original_name ?? 'Uploaded' }}</li>
            @empty
                @foreach($docTypes as $type => $label)
                    <li>{{ $label }}</li>
                @endforeach
            @endforelse
        </ul>
    </div>
    
    <div class="section">
        <div class="section-title">Payment Information</div>
        @if($application->payments->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($application->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td>KES {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->mpesa_receipt ?? '-' }}</td>
                            <td>{{ ucfirst($payment->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No payments recorded for this application.</p>
        @endif
    </div>
    
    @if($application->review_notes)
    <div class="section">
        <div class="section-title">Review Notes</div>
        <p>{{ $application->review_notes }}</p>
        @if($application->reviewer)
            <p class="mt-2"><strong>Reviewed by:</strong> {{ $application->reviewer->name }}</p>
            <p><strong>Reviewed on:</strong> {{ $application->reviewed_at?->format('M d, Y H:i') }}</p>
        @endif
    </div>
    @endif
    
    <div class="footer">
        <p><strong>{{ app_name() }} Admissions Portal</strong></p>
        <p>This document is generated automatically and is valid for official use.</p>
        <p>For queries, contact: {{ system_setting('contact_email', 'support@example.com') }}</p>
    </div>
</body>
</html>
