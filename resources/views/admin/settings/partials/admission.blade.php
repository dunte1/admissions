<form action="{{ route('admin.settings.update', 'admission') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Admission Settings</h2>
                <p class="text-sm text-gray-500">Configure admission periods and requirements</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h3 class="font-medium text-gray-900">Admissions Open</h3>
                    <p class="text-sm text-gray-500">Allow students to submit applications</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="admissions_open" value="0">
                    <input type="checkbox" name="admissions_open" value="1" 
                        {{ old('admissions_open', $settings['admissions_open'] ?? '1') == '1' ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application Fee</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">{{ $settings['currency_symbol'] ?? 'KSh' }}</span>
                        <input type="number" name="application_fee" 
                            value="{{ old('application_fee', $settings['application_fee'] ?? 2000) }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admission Fee</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">{{ $settings['currency_symbol'] ?? 'KSh' }}</span>
                        <input type="number" name="admission_fee_amount" 
                            value="{{ old('admission_fee_amount', $settings['admission_fee_amount'] ?? 5000) }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commitment Fee</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">{{ $settings['currency_symbol'] ?? 'KSh' }}</span>
                        <input type="number" name="commitment_fee_amount" 
                            value="{{ old('commitment_fee_amount', $settings['commitment_fee_amount'] ?? 2000) }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Applications per Student</label>
                    <input type="number" name="max_applications_per_student" 
                        value="{{ old('max_applications_per_student', $settings['max_applications_per_student'] ?? 3) }}"
                        min="1" max="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application Start Date</label>
                    <input type="date" name="application_start_date" 
                        value="{{ old('application_start_date', $settings['application_start_date'] ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application End Date</label>
                    <input type="date" name="application_end_date" 
                        value="{{ old('application_end_date', $settings['application_end_date'] ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Additional Options</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="require_payment_before_submission" value="0">
                        <input type="checkbox" name="require_payment_before_submission" value="1"
                            {{ old('require_payment_before_submission', $settings['require_payment_before_submission'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Require Payment Before Submission</span>
                            <span class="block text-xs text-gray-500">Students must pay before submitting</span>
                        </span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="allow_multiple_programs" value="0">
                        <input type="checkbox" name="allow_multiple_programs" value="1"
                            {{ old('allow_multiple_programs', $settings['allow_multiple_programs'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Allow Multiple Programs</span>
                            <span class="block text-xs text-gray-500">Students can apply to multiple programs</span>
                        </span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="require_document_verification" value="0">
                        <input type="checkbox" name="require_document_verification" value="1"
                            {{ old('require_document_verification', $settings['require_document_verification'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Require Document Verification</span>
                            <span class="block text-xs text-gray-500">Documents must be verified before approval</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>