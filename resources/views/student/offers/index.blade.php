@extends('layouts.student')

@section('title', __('My Offers'))

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Admission Offers') }}</h1>
        <p class="text-gray-600 mt-2">View and manage your admission letters</p>
    </div>

    {{-- Offers Cards --}}
    @forelse($applications as $application)
        @php
        $letter = $application->admissionLetter->first();
        @endphp
        @if($letter)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4 hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0
                                @if($letter->type == 'admission')
                                    bg-green-100
                                @elseif($letter->type == 'rejection')
                                    bg-red-100
                                @else
                                    bg-yellow-100
                                @endif">
                                @if($letter->type == 'admission')
                                    <i class="fas fa-award text-2xl text-green-600"></i>
                                @elseif($letter->type == 'rejection')
                                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                                @else
                                    <i class="fas fa-file-alt text-2xl text-yellow-600"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $application->program->name ?? 'Program' }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-hashtag mr-1"></i>{{ $application->application_number }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium
                                        @if($letter->type == 'admission')
                                            bg-green-100 text-green-700
                                        @elseif($letter->type == 'rejection')
                                            bg-red-100 text-red-700
                                        @else
                                            bg-yellow-100 text-yellow-700
                                        @endif">
                                        <i class="fas fa-{{ $letter->type == 'admission' ? 'check-circle' : ($letter->type == 'rejection' ? 'times' : 'file') }} mr-1"></i>
                                        {{ ucfirst($letter->type) }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium
                                        @if($letter->status == 'accepted')
                                            bg-green-100 text-green-700
                                        @elseif($letter->status == 'declined')
                                            bg-red-100 text-red-700
                                        @else
                                            bg-blue-100 text-blue-700
                                        @endif">
                                        {{ ucfirst($letter->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        @if($letter->status === 'sent')
                            @if(!$letter->isExpired())
                                <div class="mb-3">
                                    <p class="text-sm text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        Response deadline: {{ $letter->response_deadline->format('M d, Y') }}
                                    </p>
                                </div>
                                <a href="{{ route('student.offers.show', $letter->id) }}" class="inline-flex items-center px-5 py-2.5 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] font-medium transition-colors">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Offer
                                </a>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Offer Expired
                                </span>
                            @endif
                        @elseif($letter->isAccepted())
                            <div class="text-right">
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Accepted
                                </span>
                                <p class="text-xs text-gray-500 mt-2">
                                    Responded on {{ $letter->responded_at->format('M d, Y') }}
                                </p>
                            </div>
                        @elseif($letter->isDeclined())
                            <div class="text-right">
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Declined
                                </span>
                                <p class="text-xs text-gray-500 mt-2">
                                    Responded on {{ $letter->responded_at->format('M d, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-inbox text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No admission offers yet</h3>
        <p class="text-gray-500 max-w-sm mx-auto">
            Once your applications are approved, official admission letters will appear here.
        </p>
    </div>
    @endforelse

    {{-- FAQ Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-question-circle text-[#00008B] mr-2"></i>
                Frequently Asked Questions
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left" onclick="toggleFaq(this)">
                        <span class="font-medium text-gray-900">How do I accept my offer?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden p-4 bg-white">
                        <p class="text-gray-600">Click on "View Offer" to see your official admission letter, then click "Accept Offer" to confirm your acceptance. Make sure to respond before the deadline.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left" onclick="toggleFaq(this)">
                        <span class="font-medium text-gray-900">What happens if I decline the offer?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden p-4 bg-white">
                        <p class="text-gray-600">You can decline the offer if you've decided to pursue other opportunities. This action cannot be undone once submitted.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left" onclick="toggleFaq(this)">
                        <span class="font-medium text-gray-900">When do I need to respond?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden p-4 bg-white">
                        <p class="text-gray-600">Each offer has a response deadline stated in your admission letter. Please respond before that date to secure your place at the institution.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left" onclick="toggleFaq(this)">
                        <span class="font-medium text-gray-900">How will I know if I've been admitted?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden p-4 bg-white">
                        <p class="text-gray-600">You will receive an email notification when your application is approved. You can also check this page regularly for updates and download your official admission letter.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFaq(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i.fa-chevron-down');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>
@endpush
