@extends('emails.template')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <div style="width: 80px; height: 80px; background-color: #fef3c7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 16v-4M12 8h.01"></path>
        </svg>
    </div>
    <h2 style="color: #d97706; margin: 0 0 10px; font-size: 28px;">Action Required</h2>
</div>

<p style="margin: 0 0 20px;">Dear <strong>{{ $notifiable->first_name }} {{ $notifiable->last_name }}</strong>,</p>

<p style="margin: 0 0 20px;">We have reviewed your application and require additional information before we can proceed with your admission.</p>

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
                <strong style="color: #666;">Program:</strong>
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
                <span style="background-color: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">ADDITIONAL INFO NEEDED</span>
            </td>
        </tr>
    </table>
</div>

<div style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
    <p style="margin: 0 0 5px; font-size: 14px; color: #1e40af;"><strong>Information Requested:</strong></p>
    <p style="margin: 0; font-size: 14px; color: #1e3a8a;">{{ $application->review_notes ?? 'Please provide the requested information through your student portal.' }}</p>
</div>

<p style="margin: 0 0 20px;">Please log in to your student portal and update your application with the requested information as soon as possible to avoid delays in processing.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('student.dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #00008B 0%, #1e3a8a 100%); color: #ffffff; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: bold;">Update Application</a>
</div>

<p style="margin: 0 0 20px; color: #666; font-size: 14px;">If you have any questions or need clarification, please don't hesitate to contact our admissions office.</p>

<p style="margin: 0; color: #666;">Best regards,<br><strong>{{ app_name() }} Admissions Team</strong></p>
@endsection
