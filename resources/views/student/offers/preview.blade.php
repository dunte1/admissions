<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter - {{ $letter->letter_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto py-6 px-4">
        <div class="no-print flex flex-col sm:flex-row items-center justify-between gap-3 mb-6">
            <a href="{{ route('student.offers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Back to Offers
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('student.offers.preview', $letter->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fas fa-eye mr-2"></i> Preview
                </a>
                <a href="{{ route('student.offers.download', $letter->id) }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] transition-colors shadow-sm">
                    <i class="fas fa-download mr-2"></i> Download PDF
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-900 to-[#00008B] text-white px-8 py-6 no-print">
                <div class="flex items-center gap-2 text-sm opacity-90">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ $institution }}</span>
                </div>
            </div>

            <div class="p-8" style="max-width: 800px; margin: 0 auto; font-family: 'Times New Roman', serif;">
                <div class="text-center mb-8 border-b-2 border-gray-800 pb-6">
                    @if($school?->logo)
                        <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $institution }}" class="h-20 mx-auto mb-4 object-contain" style="max-height: 80px;">
                    @endif
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $institution }}</h1>
                    @if($school?->description)
                        <p class="text-sm text-gray-600 italic mb-2">{{ $school->description }}</p>
                    @endif
                    <div class="text-sm text-gray-600">
                        <p>{{ $address }}</p>
                        <p>Tel: {{ $phone }} | Email: {{ $email }}@if($website) | Website: {{ $website }}@endif</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="mb-4">{{ $letter->issue_date->format('F d, Y') }}</p>
                    
                    <div class="mb-4">
                        <p><strong>To:</strong></p>
                        <p>{{ $letter->application->student?->fullName() ?? ($letter->application->user?->first_name . ' ' . $letter->application->user?->last_name ?? 'N/A') }}</p>
                        <p>Application No: {{ $letter->application->application_number }}</p>
                        <p>Email: {{ $letter->application->user->email ?? 'N/A' }}</p>
                        @if($letter->application->student?->id_number)
                            <p>ID Number: {{ $letter->application->student->id_number }}</p>
                        @endif
                    </div>

                    <h2 class="text-lg font-bold mb-4">
                        <span class="px-3 py-1 rounded text-sm font-bold uppercase
                            @if($letter->type === 'admission') bg-green-100 text-green-800
                            @elseif($letter->type === 'rejection') bg-red-100 text-red-800
                            @elseif($letter->type === 'provisional') bg-yellow-100 text-yellow-800
                            @elseif($letter->type === 'deferral') bg-blue-100 text-blue-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ $letter->type }} LETTER
                        </span>
                    </h2>

                    <p class="mb-4">Dear {{ $letter->application->student?->first_name ?? $letter->application->user?->first_name ?? 'Applicant' }},</p>

                    @if($letter->type === 'admission')
                        <p class="mb-4 text-green-700 font-semibold">We are pleased to inform you that after careful consideration of your application, the Admissions Committee has offered you a place in our <strong>{{ $letter->application->program->name ?? 'Program' }}</strong>.</p>

                        <div class="bg-gray-50 p-4 mb-4 border-l-4 border-gray-800">
                            <h4 class="font-bold mb-3">Offer Details:</h4>
                            <table class="w-full text-sm">
                                <tr class="border-b">
                                    <th class="text-left py-2 w-1/3">Program</th>
                                    <td class="py-2">{{ $letter->application->program?->name ?? '-' }}</td>
                                </tr>
                                @if($letter->application->program?->code)
                                <tr class="border-b">
                                    <th class="text-left py-2">Program Code</th>
                                    <td class="py-2">{{ $letter->application->program->code }}</td>
                                </tr>
                                @endif
                                @if($letter->application->intake)
                                <tr class="border-b">
                                    <th class="text-left py-2">Intake</th>
                                    <td class="py-2">{{ $letter->application->intake->name }}</td>
                                </tr>
                                @endif
                                @if($letter->application->program?->duration)
                                <tr class="border-b">
                                    <th class="text-left py-2">Duration</th>
                                    <td class="py-2">{{ $letter->application->program->duration }}</td>
                                </tr>
                                @endif
                                <tr class="border-b">
                                    <th class="text-left py-2">Application Number</th>
                                    <td class="py-2">{{ $letter->application->application_number }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left py-2">Letter Reference</th>
                                    <td class="py-2">{{ $letter->letter_number }}</td>
                                </tr>
                                <tr>
                                    <th class="text-left py-2">Date of Issue</th>
                                    <td class="py-2">{{ $letter->issue_date->format('F d, Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        @if($letter->additional_conditions)
                        <div class="bg-yellow-50 p-4 mb-4 border-l-4 border-yellow-500">
                            <h4 class="font-bold mb-2">Conditions of Offer:</h4>
                            <p class="text-sm">{!! nl2br(e($letter->additional_conditions)) !!}</p>
                        </div>
                        @endif

                        @if($letter->response_deadline)
                        <p class="mb-4"><strong>Response Deadline:</strong> Please confirm your acceptance by <u>{{ $letter->response_deadline->format('F d, Y') }}</u>. Failure to respond by this date may result in the offer being withdrawn.</p>
                        @endif

                        <p class="mb-4">To accept this offer, please:</p>
                        <ol class="list-decimal list-inside mb-4 space-y-1">
                            <li>Log in to the student portal</li>
                            <li>Navigate to "My Applications" and view your offer</li>
                            <li>Click "Accept Offer" and follow the instructions</li>
                        </ol>

                        <p class="mb-4">We look forward to welcoming you to {{ $institution }}.</p>

                    @elseif($letter->type === 'rejection')
                        <p class="mb-4 text-red-700">After careful review of your application, we regret to inform you that we are unable to offer you admission at this time.</p>
                        <p class="mb-4">This decision was based on a comprehensive review of all applications received, and unfortunately, we had to make difficult choices due to the high volume of qualified candidates.</p>
                        @if($letter->additional_conditions)
                            <p class="mb-4">{{ $letter->additional_conditions }}</p>
                        @endif
                        <p class="mb-4">We encourage you to apply again in the future, and we wish you the best in your academic pursuits.</p>

                    @elseif($letter->type === 'provisional')
                        <p class="mb-4 text-yellow-700">Your application has been provisionally accepted, subject to the fulfillment of the conditions listed below.</p>
                        <div class="bg-yellow-50 p-4 mb-4 border-l-4 border-yellow-500">
                            <h4 class="font-bold mb-2">Conditions to be Fulfilled:</h4>
                            <p>{!! nl2br($letter->additional_conditions ?? 'Please check the requirements in your student portal.') !!}</p>
                        </div>
                        @if($letter->response_deadline)
                            <p class="mb-4"><strong>Deadline:</strong> All conditions must be fulfilled by <u>{{ $letter->response_deadline->format('F d, Y') }}</u>.</p>
                        @endif

                    @elseif($letter->type === 'deferral')
                        <p class="mb-4 text-blue-700">We regret to inform you that your admission has been deferred to the next intake.</p>
                        @if($letter->additional_conditions)
                            <div class="bg-blue-50 p-4 mb-4 border-l-4 border-blue-500">
                                <h4 class="font-bold mb-2">Reason:</h4>
                                <p>{!! nl2br(e($letter->additional_conditions)) !!}</p>
                            </div>
                        @endif

                    @elseif($letter->type === 'calling')
                        <p class="mb-4 text-purple-700">You are invited to attend an interview/practical session as part of your admission process.</p>
                        <div class="bg-gray-50 p-4 mb-4 border-l-4 border-gray-800">
                            <h4 class="font-bold mb-3">Interview Details:</h4>
                            <table class="w-full text-sm">
                                <tr class="border-b">
                                    <th class="text-left py-2 w-1/3">Date & Time</th>
                                    <td class="py-2">{{ $letter->interview_date?->format('F d, Y - g:i A') ?? 'To be announced' }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="text-left py-2">Venue / Location</th>
                                    <td class="py-2">{{ $letter->interview_venue ?? 'To be announced' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-left py-2">Application Number</th>
                                    <td class="py-2">{{ $letter->application->application_number }}</td>
                                </tr>
                            </table>
                        </div>
                        @if($letter->interview_instructions)
                            <div class="bg-gray-50 p-4 mb-4 border-l-4 border-gray-800">
                                <h4 class="font-bold mb-2">Instructions:</h4>
                                <p>{!! nl2br(e($letter->interview_instructions)) !!}</p>
                            </div>
                        @endif
                        <p class="mb-2"><strong>Please bring the following:</strong></p>
                        <ul class="list-disc list-inside mb-4 space-y-1">
                            <li>Original ID/Passport</li>
                            <li>Application reference number</li>
                            <li>Passport-sized photos</li>
                            <li>Any other documents specified</li>
                        </ul>
                        <p>Please arrive at least 30 minutes before the scheduled time.</p>
                    @endif

                    @if($letter->remarks)
                        <p class="mt-4"><strong>Additional Remarks:</strong> {{ $letter->remarks }}</p>
                    @endif
                </div>

                <div class="mt-12">
                    <p>Sincerely,</p>
                    <br><br>
                    @if($school?->signatureImageUrl)
                        <img src="{{ $school->signatureImageUrl }}" alt="Signature" style="max-height: 50px; margin-bottom: 10px;">
                    @endif
                    <p class="font-bold border-b border-black pb-1 inline-block">{{ $school?->signatureName ?? ($school?->admissions_contact_name ?? 'Admissions Office') }}</p>
                    <p class="text-sm text-gray-600">{{ $school?->signatureTitle ?? ($school?->admissions_contact_email ? 'Contact: ' . $school->admissions_contact_email : 'On behalf of the Admissions Committee') }}</p>
                    @if($school?->sealImageUrl)
                        <div style="margin-top: 15px;">
                            <img src="{{ $school->sealImageUrl }}" alt="Seal" style="width: 50px; height: 50px;">
                        </div>
                    @endif
                    @if($school?->admissions_contact_phone)
                        <p class="text-sm text-gray-600 mt-2">Tel: {{ $school->admissions_contact_phone }}</p>
                    @endif
                </div>

                <div class="mt-12 pt-6 border-t border-gray-300 text-center text-sm text-gray-600">
                    @if($qrCode)
                    <div class="mb-3">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" class="w-16 h-16 mx-auto">
                        <p class="text-xs mt-1">Scan to verify this document</p>
                    </div>
                    @endif
                    <p>This is a system-generated document from {{ $institution }}.</p>
                    <p>Letter Reference: {{ $letter->letter_number }} | Generated: {{ now()->format('F d, Y H:i:s') }}</p>
                    <p>For verification, visit: {{ url('/letter/verify/' . $letter->letter_number) }}</p>
                </div>
            </div>
        </div>

        <div class="no-print mt-6 flex justify-center">
            <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-lg">
                <i class="fas fa-print mr-2"></i> Print Letter
            </button>
        </div>
    </div>
</body>
</html>
