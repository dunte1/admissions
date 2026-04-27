@extends('emails.template')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    @if($letter->type === 'admission')
    <div style="width: 80px; height: 80px; background-color: #dcfce7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
            <path d="M20 6L9 17l-5-5"></path>
        </svg>
    </div>
    <h2 style="color: #16a34a; margin: 0 0 10px; font-size: 28px;">Congratulations!</h2>
    @else
    <div style="width: 80px; height: 80px; background-color: #fef2f2; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
    </div>
    <h2 style="color: #dc2626; margin: 0 0 10px; font-size: 28px;">Application Update</h2>
    @endif
</div>

<p style="margin: 0 0 20px;">Dear <strong>{{ $letter->application->user->first_name ?? 'Applicant' }}</strong>,</p>

@if($letter->type === 'admission')
<p style="margin: 0 0 20px;">We are pleased to inform you that you have been <strong style="color: #16a34a;">OFFERED ADMISSION</strong> to {{ $letter->application->program->name ?? 'our program' }}.</p>
@elseif($letter->type === 'rejection')
<p style="margin: 0 0 20px;">Thank you for your interest in {{ app_name() }}. After careful consideration, we regret to inform you that we are unable to offer you admission at this time.</p>
@else
<p style="margin: 0 0 20px;">Please find attached an official letter regarding your application.</p>
@endif

<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                <strong style="color: #666;">Letter Number:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">
                <strong style="color: #00008B;">{{ $letter->letter_number }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                <strong style="color: #666;">Application Number:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">
                {{ $letter->application->application_number }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                <strong style="color: #666;">Program:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">
                {{ $letter->application->program->name ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <strong style="color: #666;">Letter Type:</strong>
            </td>
            <td style="padding: 8px 0; text-align: right;">
                <span style="background-color: #e0e7ff; color: #00008B; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">{{ strtoupper($letter->type) }}</span>
            </td>
        </tr>
    </table>
</div>

@if($letter->type === 'admission' && $letter->response_deadline)
<p style="margin: 0 0 20px;">Please respond to this offer by <strong>{{ $letter->response_deadline->format('F d, Y') }}</strong> to secure your place.</p>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('student.offers.show', $letter->id) }}" style="display: inline-block; background: linear-gradient(135deg, #00008B 0%, #1e3a8a 100%); color: #ffffff; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: bold;">View Offer Letter</a>
</div>

<p style="margin: 0; color: #666; font-size: 14px;">If you have any questions, please contact our admissions office.</p>

<p style="margin: 0; color: #666;">Best regards,<br><strong>{{ app_name() }} Admissions Team</strong></p>
@endsection
