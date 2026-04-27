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
        .content { margin-bottom: 30px; }
        .content p { margin-bottom: 15px; text-align: justify; }
        .signature { margin-top: 50px; page-break-inside: avoid; }
        .signature .name { font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; width: 250px; }
        .signature .title { color: #666; font-size: 10pt; }
        .footer { margin-top: 40px; font-size: 9pt; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 15px; page-break-inside: avoid; }
        .footer .qr-section { margin: 15px 0; }
        .footer .qr-section img { width: 60px; height: 60px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background: #f5f5f5; font-weight: bold; }
        .conditions { background: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #000; }
    </style>
</head>
<body>
    @if($customHeader)
    <div class="header">
        {!! $customHeader !!}
    </div>
    @else
    <div class="header">
        @if($school?->logo)
            <img src="{{ $school->logoUrl }}" alt="{{ $institution }}" class="logo">
        @endif
        <h1>{{ $institution }}</h1>
        @if($school?->description)
            <p>{{ $school->description }}</p>
        @endif
        <p>{{ $address }}</p>
        <p>Tel: {{ $phone }} | Email: {{ $email }}@if($website) | Website: {{ $website }}@endif</p>
    </div>
    @endif

    <div class="content">
        @if($customBody)
            {!! $customBody !!}
        @else
            <p>{{ $letter->issue_date->format('F d, Y') }}</p>
            <p>Dear {{ $letter->application->student?->fullName() ?? 'Applicant' }},</p>
            
            @if($letter->type === 'admission')
                <p>We are pleased to offer you admission to <strong>{{ $letter->application->program->name }}</strong>.</p>
            @endif
        @endif
    </div>

    @if($customFooter)
    <div class="footer">
        {!! $customFooter !!}
        @if($qrCode)
        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            <p>Scan to verify</p>
        </div>
        @endif
    </div>
    @else
    <div class="signature">
        <p>Sincerely,</p>
        <br><br>
        <div class="name">{{ $school?->admissions_contact_name ?? 'Admissions Office' }}</div>
        <p class="title">On behalf of the Admissions Committee</p>
    </div>
    <div class="footer">
        @if($qrCode)
        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            <p>Scan to verify</p>
        </div>
        @endif
        <p>Letter Reference: {{ $letter->letter_number }}</p>
    </div>
    @endif
</body>
</html>