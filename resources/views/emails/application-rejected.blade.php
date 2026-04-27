@extends('emails.template')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <div style="width: 80px; height: 80px; background-color: #fee2e2; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M15 9l-6 6M9 9l6 6"></path>
        </svg>
    </div>
    <h2 style="color: #dc2626; margin: 0 0 10px; font-size: 28px;">Application Update</h2>
</div>

<p style="margin: 0 0 20px;">Dear <strong>{{ $notifiable->first_name }} {{ $notifiable->last_name }}</strong>,</p>

<p style="margin: 0 0 20px;">Thank you for your interest in {{ app_name() }}. After careful review, we regret to inform you that your application was not successful at this time.</p>

<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                <strong style="color: #666;">Application Number:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">
                <strong style="color: #00008B;">{{ $application->application_number }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                <strong style="color: #666;">Program Applied:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; text-align: right;">
                {{ $application->program->name ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">
                <strong style="color: #666;">Status:</strong>
            </td>
            <td style="padding: 8px 0; text-align: right;">
                <span style="background-color: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">NOT SUCCESSFUL</span>
            </td>
        </tr>
    </table>
</div>

@if($application->review_notes)
<div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
    <p style="margin: 0 0 5px; font-size: 14px; color: #92400e;"><strong>Feedback:</strong></p>
    <p style="margin: 0; font-size: 14px; color: #78350f;">{{ $application->review_notes }}</p>
</div>
@endif

<p style="margin: 0 0 20px;">This decision does not reflect your potential. We encourage you to apply for future admission cycles when you meet the eligibility requirements.</p>

<p style="margin: 0 0 20px; color: #666; font-size: 14px;">For more information or to discuss your options, please contact our admissions office.</p>

<p style="margin: 0; color: #666;">Best regards,<br><strong>{{ app_name() }} Admissions Team</strong></p>
@endsection
