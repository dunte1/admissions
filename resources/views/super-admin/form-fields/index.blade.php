@extends('layouts.super-admin')

@section('header', 'Form Fields Management')
@section('breadcrumb', 'Customize admission form fields per school')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <form method="GET" action="{{ route('super-admin.form-fields.index') }}" class="flex items-center space-x-2">
            <select name="school_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Select School...</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </form>
        @if($schoolId)
            <a href="{{ route('super-admin.form-fields.create-section', ['school_id' => $schoolId]) }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>
                Add Section
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(!$schoolId)
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
            <i class="fas fa-info-circle mr-2"></i>
            Select a school to manage its form fields, or manage default fields.
        </div>
    @endif

    <div class="space-y-6">
        @forelse($sections as $section)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas {{ $section->icon ?? 'fa-circle' }} text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $section->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $section->fields->count() }} fields | {{ $section->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($section->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Active</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">Inactive</span>
                        @endif
                        
                        @if($section->school_id)
                            <form action="{{ route('super-admin.form-fields.toggle-section', $section) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="school_id" value="{{ $schoolId }}">
                                <button type="submit" class="p-2 text-gray-400 hover:text-purple-600" title="{{ $section->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="fas {{ $section->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('super-admin.form-fields.create-field', [$section, 'school_id' => $schoolId]) }}" 
                           class="p-2 text-gray-400 hover:text-green-600" title="Add Field">
                            <i class="fas fa-plus"></i>
                        </a>
                        <a href="{{ route('super-admin.form-fields.edit-section', [$section, 'school_id' => $schoolId]) }}" 
                           class="p-2 text-gray-400 hover:text-blue-600" title="Edit Section">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($section->school_id)
                            <form action="{{ route('super-admin.form-fields.destroy-section', $section) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="school_id" value="{{ $schoolId }}">
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600" title="Delete Section">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                
                <div class="p-4">
                    @if($section->fields->count() > 0)
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Required</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($section->fields->sortBy('order') as $field)
                                    <tr class="{{ !$field->is_active ? 'bg-gray-50 text-gray-400' : '' }}">
                                        <td class="px-4 py-2 text-sm">{{ $field->order }}</td>
                                        <td class="px-4 py-2">
                                            <div class="font-medium text-gray-900">{{ $field->label }}</div>
                                            <div class="text-xs text-gray-500">Key: {{ $field->key }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                                <i class="fas fa-{{ $field->type == 'text' ? 'fa-font' : ($field->type == 'select' ? 'fa-list' : ($field->type == 'file' ? 'fa-file' : 'fa-edit')) }} mr-1"></i>
                                                {{ $field->type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($field->is_required)
                                                <span class="text-red-500"><i class="fas fa-check-circle"></i> Yes</span>
                                            @else
                                                <span class="text-gray-400">No</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($field->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <div class="flex items-center justify-end space-x-1">
                                                <form action="{{ route('super-admin.form-fields.toggle-field', $field) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="school_id" value="{{ $schoolId }}">
                                                    <button type="submit" class="p-1 text-gray-400 hover:text-purple-600" title="Toggle">
                                                        <i class="fas {{ $field->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('super-admin.form-fields.edit-field', [$field, 'school_id' => $schoolId]) }}" 
                                                   class="p-1 text-gray-400 hover:text-blue-600" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('super-admin.form-fields.destroy-field', $field) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="school_id" value="{{ $schoolId }}">
                                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-4">No fields in this section. <a href="{{ route('super-admin.form-fields.create-field', [$section, 'school_id' => $schoolId]) }}" class="text-purple-600 hover:underline">Add a field</a></p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <i class="fas fa-forms text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No form sections found</h3>
                <p class="text-gray-500 mt-1">Get started by creating a section.</p>
                @if($schoolId)
                    <a href="{{ route('super-admin.form-fields.create-section', ['school_id' => $schoolId]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 mt-4">
                        <i class="fas fa-plus mr-2"></i>
                        Create Section
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Payment Fields Section --}}
    @if($schoolId)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-credit-card text-green-600 mr-2"></i>
                Payment Fields Configuration
            </h3>
            <p class="text-sm text-gray-600 mt-1">Configure payment-related form fields for this school</p>
        </div>
        <div class="p-6">
            @php
                $admissionFeeAmount = system_setting('admission_fee_amount', 2000);
                $commitmentFeeAmount = system_setting('commitment_fee_amount', 5000);
                $admissionFeeLabel = system_setting('admission_fee_label', 'Application/Admission Fee');
                $commitmentFeeLabel = system_setting('commitment_fee_label', 'Commitment Fee');
                $commitmentDueDays = system_setting('commitment_fee_due_days', 14);
                $mpesaPaybill = system_setting('mpesa_paybill_number', 'N/A');
                $stkEnabled = system_setting('mpesa_stk_enabled', true);
                $manualEnabled = system_setting('manual_payment_enabled', false);
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Admission Fee</span>
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Global</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">KSh {{ number_format($admissionFeeAmount) }}</p>
                    <p class="text-sm text-gray-600">{{ $admissionFeeLabel }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Commitment Fee</span>
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Global</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">KSh {{ number_format($commitmentFeeAmount) }}</p>
                    <p class="text-sm text-gray-600">{{ $commitmentFeeLabel }} (Due: {{ $commitmentDueDays }} days after offer)</p>
                </div>
            </div>

            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Payment Methods Enabled:</h4>
                <div class="flex flex-wrap gap-3">
                    @if($stkEnabled)
                    <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm">
                        <i class="fas fa-mobile-alt mr-2"></i> M-PESA STK Push
                    </span>
                    @endif
                    @if($manualEnabled)
                    <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm">
                        <i class="fas fa-hand-paper mr-2"></i> Manual Payment
                    </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-full text-sm">
                        <i class="fas fa-credit-card mr-2"></i> PayPal
                    </span>
                </div>
            </div>

            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i> M-PESA Payment Details
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-blue-700">
                    <p><strong>Paybill Number:</strong> {{ $mpesaPaybill }}</p>
                    <p><strong>Account Reference:</strong> ADM-{application_number}</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Configurable Payment Fields:</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Payment Method Selection</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Built-in</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">M-PESA Phone Number</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Built-in</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Transaction Code (Manual)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Built-in</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Payment Proof Upload</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Optional</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('super-admin.settings', ['tab' => 'payment']) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-cog mr-2"></i> Configure Payment Settings
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
