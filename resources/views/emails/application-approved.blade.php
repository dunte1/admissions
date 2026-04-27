@extends('emails.template')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <div style="width: 80px; height: 80px; background-color: #dcfce7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
            <path d="M20 6L9 17l-5-5"></path>
        </svg>
    </div>
    <h2 style="color: #16a34a; margin: 0 0 10px; font-size: 28px;">Congratulations!</h2>
</div>

<p style="margin: 0 0 20px;">Dear <strong>{{ $notifiable->first_name }} {{ $notifiable->last_name }}</strong>,</p>

<p style="margin: 0 0 20px;">We are pleased to inform you that your application has been <strong style="color: #16a34a;">APPROVED</strong>.</p>

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
                <span style="background-color: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">APPROVED</span>
            </td>
        </tr>
    </table>
</div>

<p style="margin: 0 0 20px;">Congratulations on your acceptance! Please log in to your student portal to view your admission letter and complete the enrollment process.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('student.dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #00008B 0%, #1e3a8a 100%); color: #ffffff; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: bold;">View Your Portal</a>
</div>

<p style="margin: 0 0 20px; color: #666; font-size: 14px;">If you have any questions about your admission, please contact our admissions office.</p>

<p style="margin: 0; color: #666;">Best regards,<br><strong>{{ app_name() }} Admissions Team</strong></p>
@endsection
