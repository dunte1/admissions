<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letter->letter_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6; padding: 40px; max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header .logo { max-height: 80px; max-width: 200px; margin-bottom: 10px; }
        .header h1 { font-size: 18pt; margin-bottom: 5px; color: #1a1a1a; }
        .header .tagline { font-size: 10pt; color: #666; font-style: italic; margin-bottom: 5px; }
        .header .address { font-size: 9pt; color: #555; }
        .content { margin-bottom: 30px; }
        .content p { margin-bottom: 15px; text-align: justify; }
        .content .recipient { margin: 20px 0; }
        .content .subject { font-weight: bold; margin-bottom: 15px; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; }
        .conditions { background: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #000; }
        .conditions h4 { margin-bottom: 10px; font-size: 11pt; }
        .signature { margin-top: 50px; page-break-inside: avoid; }
        .signature .name { font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; width: 250px; }
        .signature .title { color: #666; font-size: 10pt; }
        .signature .seal { width: 80px; height: 80px; border: 2px solid #000; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-top: 10px; font-size: 8pt; text-transform: uppercase; }
        .footer { margin-top: 40px; font-size: 9pt; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 15px; page-break-inside: avoid; }
        .footer .qr-section { margin: 15px 0; }
        .footer .qr-section img { width: 60px; height: 60px; }
        .admission { color: #2e7d32; }
        .rejection { color: #c62828; }
        .provisional { color: #f57c00; }
        .deferral { color: #1565c0; }
        .calling { color: #7c3aed; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 10pt; font-weight: bold; text-transform: uppercase; }
        .badge-admission { background: #e8f5e9; color: #2e7d32; }
        .badge-rejection { background: #ffebee; color: #c62828; }
        .badge-provisional { background: #fff3e0; color: #f57c00; }
        .badge-deferral { background: #e3f2fd; color: #1565c0; }
        .badge-calling { background: #ede9fe; color: #7c3aed; }
    </style>
</head>
<body>
    <div class="header">
        @if($school?->logo)
            <img src="{{ $school->logoUrl }}" alt="{{ $institution }}" class="logo">
        @endif
        <h1>{{ $institution }}</h1>
        @if($school?->description)
            <p class="tagline">{{ $school->description }}</p>
        @endif
        <div class="address">
            <p>{{ $address }}</p>
            <p>Tel: {{ $phone }} | Email: {{ $email }}@if($website) | Website: {{ $website }}@endif</p>
            @if($school?->facebook || $school?->twitter)
                <p>
                    @if($school?->facebook)FB: {{ $school->facebook }}@endif
                    @if($school?->twitter) | TW: {{ $school->twitter }}@endif
                </p>
            @endif
        </div>
    </div>

    <div class="content">
        <p>{{ $letter->issue_date->format('F d, Y') }}</p>
        
        <div class="recipient">
            <p><strong>To:</strong></p>
            <p>{{ $letter->application->student?->fullName() ?? ($letter->application->user?->first_name . ' ' . $letter->application->user?->last_name ?? 'N/A') }}</p>
            <p>Application No: {{ $letter->application->application_number }}</p>
            <p>Email: {{ $letter->application->user->email ?? 'N/A' }}</p>
            @if($letter->application->student?->id_number)
                <p>ID Number: {{ $letter->application->student->id_number }}</p>
            @endif
        </div>

        <p class="subject">
            <span class="badge badge-{{ $letter->type }}">{{ $letter->type }}</span> LETTER
        </p>

        <p>Dear {{ $letter->application->student?->first_name ?? $letter->application->user?->first_name ?? 'Applicant' }},</p>

        @if($letter->type === 'admission')
            <p class="admission">We are pleased to inform you that after careful consideration of your application, the Admissions Committee has offered you a place in our <strong>{{ $letter->application->program->name ?? 'Program' }}</strong>.</p>

            <div class="conditions">
                <h4>Offer Details:</h4>
                <table>
                    <tr>
                        <th width="35%">Program</th>
                        <td>{{ $letter->application->program?->name ?? '-' }}</td>
                    </tr>
                    @if($letter->application->program?->code)
                    <tr>
                        <th>Program Code</th>
                        <td>{{ $letter->application->program->code }}</td>
                    </tr>
                    @endif
                    @if($letter->application->intake)
                    <tr>
                        <th>Intake</th>
                        <td>{{ $letter->application->intake->name }}</td>
                    </tr>
                    @endif
                    @if($letter->application->program?->duration)
                    <tr>
                        <th>Duration</th>
                        <td>{{ $letter->application->program->duration }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Application Number</th>
                        <td>{{ $letter->application->application_number }}</td>
                    </tr>
                    <tr>
                        <th>Letter Reference</th>
                        <td>{{ $letter->letter_number }}</td>
                    </tr>
                    <tr>
                        <th>Date of Issue</th>
                        <td>{{ $letter->issue_date->format('F d, Y') }}</td>
                    </tr>
                </table>
            </div>

            @if($letter->additional_conditions)
            <div class="conditions">
                <h4>Conditions of Offer:</h4>
                <p>{!! nl2br(e($letter->additional_conditions)) !!}</p>
            </div>
            @endif

            @if($letter->response_deadline)
            <p><strong>Response Deadline:</strong> Please confirm your acceptance by <u>{{ $letter->response_deadline->format('F d, Y') }}</u>. Failure to respond by this date may result in the offer being withdrawn.</p>
            @endif

            <p>To accept this offer, please:</p>
            <ol>
                <li>Log in to the student portal</li>
                <li>Navigate to "My Applications" and view your offer</li>
                <li>Click "Accept Offer" and follow the instructions</li>
            </ol>

            <p>We look forward to welcoming you to {{ $institution }}.</p>

        @elseif($letter->type === 'rejection')
            <p class="rejection">After careful review of your application, we regret to inform you that we are unable to offer you admission at this time.</p>

            <p>This decision was based on a comprehensive review of all applications received, and unfortunately, we had to make difficult choices due to the high volume of qualified candidates.</p>

            @if($letter->additional_conditions)
            <p>{{ $letter->additional_conditions }}</p>
            @endif

            <p>We encourage you to apply again in the future, and we wish you the best in your academic pursuits.</p>

        @elseif($letter->type === 'provisional')
            <p class="provisional">Your application has been provisionally accepted, subject to the fulfillment of the conditions listed below.</p>

            <div class="conditions">
                <h4>Conditions to be Fulfilled:</h4>
                <p>{!! nl2br($letter->additional_conditions ?? 'Please check the requirements in your student portal.') !!}</p>
            </div>

            @if($letter->response_deadline)
            <p><strong>Deadline:</strong> All conditions must be fulfilled by <u>{{ $letter->response_deadline->format('F d, Y') }}</u>.</p>
            @endif

        @elseif($letter->type === 'deferral')
            <p class="deferral">We regret to inform you that your admission has been deferred to the next intake.</p>

            @if($letter->additional_conditions)
            <div class="conditions">
                <h4>Reason:</h4>
                <p>{!! nl2br(e($letter->additional_conditions)) !!}</p>
            </div>
            @endif
        @endif

        @if($letter->remarks)
        <p><strong>Additional Remarks:</strong> {{ $letter->remarks }}</p>
        @endif

        @if($letter->type === 'calling')
            <p class="calling">You are invited to attend an interview/practical session as part of your admission process.</p>

            <div class="conditions">
                <h4>Interview Details:</h4>
                <table>
                    <tr>
                        <th width="35%">Date & Time</th>
                        <td>{{ $letter->interview_date?->format('F d, Y - g:i A') ?? 'To be announced' }}</td>
                    </tr>
                    <tr>
                        <th>Venue / Location</th>
                        <td>{{ $letter->interview_venue ?? 'To be announced' }}</td>
                    </tr>
                    <tr>
                        <th>Application Number</th>
                        <td>{{ $letter->application->application_number }}</td>
                    </tr>
                </table>
            </div>

            @if($letter->interview_instructions)
            <div class="conditions">
                <h4>Instructions:</h4>
                <p>{!! nl2br(e($letter->interview_instructions)) !!}</p>
            </div>
            @endif

            <p><strong>Please bring the following:</strong></p>
            <ul>
                <li>Original ID/Passport</li>
                <li>Application reference number</li>
                <li>Passport-sized photos</li>
                <li>Any other documents specified</li>
            </ul>

            <p>Please arrive at least 30 minutes before the scheduled time.</p>

        @endif
    </div>

    <div class="signature">
        <p>Sincerely,</p>
        <br>
        @if($school?->signatureImageUrl)
        <img src="{{ $school->signatureImageUrl }}" alt="Signature" style="max-height: 60px; margin-bottom: 10px;">
        @endif
        <div class="name">{{ $school?->signatureName ?? ($school?->admissions_contact_name ?? 'Admissions Office') }}</div>
        <p class="title">{{ $school?->signatureTitle ?? ($school?->admissions_contact_email ? 'Contact: ' . $school->admissions_contact_email : 'On behalf of the Admissions Committee') }}</p>
        @if($school?->sealImageUrl)
        <div style="margin-top: 15px;">
            <img src="{{ $school->sealImageUrl }}" alt="Seal" style="width: 60px; height: 60px;">
        </div>
        @endif
        @if($school?->admissions_contact_phone)
        <p class="title">Tel: {{ $school->admissions_contact_phone }}</p>
        @endif
    </div>

    <div class="footer">
        @if($qrCode)
        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            <p>Scan to verify this document</p>
        </div>
        @endif
        <p>This is a system-generated document from {{ $institution }}.</p>
        <p>Letter Reference: {{ $letter->letter_number }} | Generated: {{ now()->format('F d, Y H:i:s') }}</p>
        <p>For verification, visit: {{ url('/letter/verify/' . $letter->letter_number) }}</p>
    </div>
</body>
</html>
