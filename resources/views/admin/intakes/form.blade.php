@extends('layouts.admin')

@section('title', isset($intake) ? __('Edit Intake') : __('Create Intake'))

@section('header', isset($intake) ? __('Edit Intake') : __('Create Intake'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($intake) ? __('Edit Intake') : __('Create New Intake') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ isset($intake) ? 'Update intake details and dates' : 'Set up a new admission intake period' }}</p>
        </div>
        <a href="{{ route('admin.intakes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
        </a>
    </div>

    <form action="{{ isset($intake) ? route('admin.intakes.update', $intake->id) : route('admin.intakes.store') }}" method="POST">
        @csrf
        @if(isset($intake))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Basic Information') }}</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Intake Name') }} *</label>
                        <input type="text" name="name" id="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required
                               value="{{ old('name', $intake->name ?? '') }}" placeholder="e.g., September 2026 Intake">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Code') }} *</label>
                            <input type="text" name="code" id="code" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required
                                   value="{{ old('code', $intake->code ?? '') }}" placeholder="e.g., SEP2026">
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Year') }} *</label>
                            <input type="number" name="year" id="year" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required
                                   value="{{ old('year', $intake->year ?? date('Y')) }}">
                        </div>
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Semester') }}</label>
                            <select name="semester" id="semester" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]">
                                <option value="">N/A</option>
                                @for($i = 1; $i <= 3; $i++)
                                    <option value="{{ $i }}" {{ old('semester', $intake->semester ?? '') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                        <textarea name="description" id="description" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="3">{{ old('description', $intake->description ?? '') }}</textarea>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" class="w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]" value="1"
                                   {{ old('is_active', $intake->is_active ?? false) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">{{ __('Active') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_current" id="is_current" class="w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]" value="1"
                                   {{ old('is_current', $intake->is_current ?? false) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">{{ __('Current Intake') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Important Dates') }}</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-[#00008B] mb-3">{{ __('Application Period') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="application_start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }} *</label>
                                <input type="date" name="application_start_date" id="application_start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required
                                       value="{{ old('application_start_date', optional($intake)->application_start_date?->format('Y-m-d') ?? '') }}">
                            </div>
                            <div>
                                <label for="application_end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }} *</label>
                                <input type="date" name="application_end_date" id="application_end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required
                                       value="{{ old('application_end_date', optional($intake)->application_end_date?->format('Y-m-d') ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-[#00008B] mb-3 mt-4">{{ __('Review Period') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="review_start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                                <input type="date" name="review_start_date" id="review_start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]"
                                       value="{{ old('review_start_date', optional($intake)->review_start_date?->format('Y-m-d') ?? '') }}">
                            </div>
                            <div>
                                <label for="review_end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                                <input type="date" name="review_end_date" id="review_end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]"
                                       value="{{ old('review_end_date', optional($intake)->review_end_date?->format('Y-m-d') ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="results_release_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Results Release Date') }}</label>
                        <input type="date" name="results_release_date" id="results_release_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]"
                               value="{{ old('results_release_date', optional($intake)->results_release_date?->format('Y-m-d') ?? '') }}">
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-[#00008B] mb-3 mt-4">{{ __('Registration Period') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="registration_start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                                <input type="date" name="registration_start_date" id="registration_start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]"
                                       value="{{ old('registration_start_date', optional($intake)->registration_start_date?->format('Y-m-d') ?? '') }}">
                            </div>
                            <div>
                                <label for="registration_end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                                <input type="date" name="registration_end_date" id="registration_end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]"
                                       value="{{ old('registration_end_date', optional($intake)->registration_end_date?->format('Y-m-d') ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                <i class="fas fa-save mr-2"></i> {{ isset($intake) ? __('Update Intake') : __('Create Intake') }}
            </button>
        </div>
    </form>
</div>
@endsection
