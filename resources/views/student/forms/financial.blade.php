@php
$programId = session('selected_program_id', $formData['academic']['programme_id'] ?? null);
$program = null;
if ($programId) {
    $program = \App\Models\Program::find($programId);
}

$applicationFee = $program ? ($program->application_fee ?? 0) : school_setting('application_fee', system_setting('application_fee', 2000));
$admissionFee = $program ? ($program->admission_fee ?? 0) : school_setting('admission_fee_amount', system_setting('admission_fee_amount', 5000));
$commitmentFee = $program ? ($program->commitment_fee ?? 0) : school_setting('commitment_fee_amount', system_setting('commitment_fee_amount', 2000));

$totalFee = $applicationFee + $admissionFee + $commitmentFee;

$mpesaEnabled = true; // Force enable for demo
$manualEnabled = school_setting('payment_manual_enabled', system_setting('payment_manual_enabled', true));
$bankEnabled = school_setting('payment_bank_enabled', system_setting('payment_bank_enabled', true));
$bankName = school_setting('bank_name', system_setting('bank_name', ''));
$bankAccountName = school_setting('bank_account_name', system_setting('bank_account_name', ''));
$bankAccountNumber = school_setting('bank_account_number', system_setting('bank_account_number', ''));
$bankBranch = school_setting('bank_branch', system_setting('bank_branch', ''));
$mpesaPaybill = school_setting('mpesa_paybill', system_setting('mpesa_paybill', ''));
$currencySymbol = system_setting('currency_symbol', 'KSh');

$sponsorshipType = $formData['financial']['sponsorship_type'] ?? 'self';
$payOption = $formData['financial']['pay_option'] ?? 'full';
$paymentMode = $formData['financial']['payment_mode'] ?? 'mpesa';
$applicationFeePaid = $formData['financial']['application_fee_paid'] ?? 'no';
$commitmentFeePaid = $formData['financial']['commitment_fee_paid'] ?? 'no';
$showBursary = $formData['financial']['requires_bursary'] ?? 'no';
$showSponsorFields = ($formData['financial']['sponsorship_type'] ?? 'self') !== 'self' ? 'true' : 'false';
@endphp

<div x-data="financialForm()" class="space-y-6">

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">Select your funding type to help us process your application correctly.</p>
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-3">Funding Type *</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-all" :class="sponsorshipType === 'self' ? 'border-blue-500 bg-blue-50' : ''">
                <input type="radio" name="sponsorship_type" value="self" x-model="sponsorshipType" @change="updateSponsorVisibility()" class="mt-1 mr-3" required>
                <div class="flex-1">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        <span class="font-semibold text-gray-900">Self-sponsored</span>
                    </div>
                    <p class="text-xs text-gray-500">I'll fund my own studies</p>
                </div>
            </label>
            
            <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 transition-all" :class="sponsorshipType === 'government' ? 'border-blue-500 bg-blue-50' : ''">
                <input type="radio" name="sponsorship_type" value="government" x-model="sponsorshipType" @change="updateSponsorVisibility()" class="mt-1 mr-3">
                <div class="flex-1">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-landmark text-red-500 mr-2"></i>
                        <span class="font-semibold text-gray-900">Government</span>
                    </div>
                    <p class="text-xs text-gray-500">Government Scholarship</p>
                </div>
            </label>
            
            <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-all" :class="sponsorshipType === 'ngo' ? 'border-blue-500 bg-blue-50' : ''">
                <input type="radio" name="sponsorship_type" value="ngo" x-model="sponsorshipType" @change="updateSponsorVisibility()" class="mt-1 mr-3">
                <div class="flex-1">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-hands-helping text-green-500 mr-2"></i>
                        <span class="font-semibold text-gray-900">NGO</span>
                    </div>
                    <p class="text-xs text-gray-500">NGO Sponsorship</p>
                </div>
            </label>
            
            <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-all" :class="sponsorshipType === 'corporate' ? 'border-blue-500 bg-blue-50' : ''">
                <input type="radio" name="sponsorship_type" value="corporate" x-model="sponsorshipType" @change="updateSponsorVisibility()" class="mt-1 mr-3">
                <div class="flex-1">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-building text-purple-500 mr-2"></i>
                        <span class="font-semibold text-gray-900">Corporate</span>
                    </div>
                    <p class="text-xs text-gray-500">Company Sponsorship</p>
                </div>
            </label>
            
            <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-all" :class="sponsorshipType === 'family' ? 'border-blue-500 bg-blue-50' : ''">
                <input type="radio" name="sponsorship_type" value="family" x-model="sponsorshipType" @change="updateSponsorVisibility()" class="mt-1 mr-3">
                <div class="flex-1">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-users text-orange-500 mr-2"></i>
                        <span class="font-semibold text-gray-900">Family</span>
                    </div>
                    <p class="text-xs text-gray-500">Family Support</p>
                </div>
            </label>
        </div>
    </div>

    <div x-show="showSponsorFields" x-transition class="bg-green-50 border border-green-200 rounded-xl p-6 space-y-4">
        <h4 class="text-sm font-bold text-green-800 flex items-center">
            <i class="fas fa-user-shield mr-2"></i>
            Sponsor Information
        </h4>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Name *</label>
                <input type="text" name="sponsor_name" value="{{ $formData['financial']['sponsor_name'] ?? '' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    placeholder="Full name or organization">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Phone *</label>
                <input type="tel" name="sponsor_phone" value="{{ $formData['financial']['sponsor_phone'] ?? '' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    placeholder="+254700000000">
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Email</label>
                <input type="email" name="sponsor_email" value="{{ $formData['financial']['sponsor_email'] ?? '' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship/Type</label>
                <input type="text" name="sponsor_relationship" value="{{ $formData['financial']['sponsor_relationship'] ?? '' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg"
                    placeholder="e.g., Employer, NGO">
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-receipt text-gray-600 mr-2"></i>
            Fee Summary
        </h4>
        
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500">Application Fee</p>
                <p class="text-2xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($applicationFee) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500">Admission Fee</p>
                <p class="text-2xl font-bold text-blue-600">{{ $currencySymbol }} {{ number_format($admissionFee) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-500">Commitment Fee</p>
                <p class="text-2xl font-bold text-purple-600">{{ $currencySymbol }} {{ number_format($commitmentFee) }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-700">TOTAL PAYABLE</p>
                <p class="text-3xl font-bold text-yellow-700">{{ $currencySymbol }} {{ number_format($totalFee) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h4 class="text-sm font-bold text-blue-800 mb-4 flex items-center">
            <i class="fas fa-money-check mr-2"></i>
            Payment Option *
        </h4>
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-4">
            <label class="flex items-center">
                <input type="radio" name="pay_option" value="full" x-model="payOption" class="mr-2" checked>
                <span class="font-medium">Pay Full Amount</span>
                <span class="ml-2 text-xs text-green-600">(Recommended)</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="pay_option" value="per_fee" x-model="payOption" class="mr-2">
                <span class="font-medium">Pay Per Fee</span>
            </label>
        </div>

        <div x-show="payOption === 'full'" x-transition class="bg-white rounded-lg p-4 border border-gray-200 space-y-4">
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div>
                    <p class="font-bold text-yellow-800">Total to Pay: {{ $currencySymbol }} {{ number_format($totalFee) }}</p>
                    <p class="text-xs text-gray-600">Pay application + commitment fee together</p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method *</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @if($mpesaEnabled)
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-all" :class="paymentMode === 'mpesa' ? 'border-green-500 bg-green-50' : ''">
                        <input type="radio" name="payment_mode" value="mpesa" x-model="paymentMode" class="mr-2">
                        <div>
                            <span class="font-medium">M-PESA</span>
                            <p class="text-xs text-gray-500">Via Safaricom</p>
                        </div>
                    </label>
                    @endif
                    
                    @if($bankEnabled && $bankName)
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-all" :class="paymentMode === 'bank' ? 'border-purple-500 bg-purple-50' : ''">
                        <input type="radio" name="payment_mode" value="bank" x-model="paymentMode" class="mr-2">
                        <div>
                            <span class="font-medium">Bank</span>
                            <p class="text-xs text-gray-500">Direct deposit</p>
                        </div>
                    </label>
                    @endif
                    
                    @if($manualEnabled)
                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all" :class="paymentMode === 'cash' ? 'border-blue-500 bg-blue-50' : ''">
                        <input type="radio" name="payment_mode" value="cash" x-model="paymentMode" class="mr-2">
                        <div>
                            <span class="font-medium">Cash/Office</span>
                            <p class="text-xs text-gray-500">Pay at campus</p>
                        </div>
                    </label>
                    @endif
                </div>
            </div>

            <div x-show="paymentMode === 'mpesa'" x-transition class="p-4 bg-green-50 rounded-lg space-y-3">
                <p class="text-sm font-medium text-green-800">M-PESA Payment Instructions:</p>
                <ol class="text-xs text-gray-600 list-decimal list-inside space-y-1">
                    <li>Go to M-PESA → Lipa na M-PESA</li>
                    @if($mpesaPaybill)
                    <li>Enter Paybill: <strong>{{ $mpesaPaybill }}</strong></li>
                    @endif
                    <li>Enter Account: <strong>Your Application ID</strong></li>
                    <li>Enter Amount: <strong>{{ $currencySymbol }} {{ number_format($totalFee) }}</strong></li>
                </ol>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">M-PESA Transaction Code *</label>
                    <input type="text" name="full_payment_reference" value="{{ $formData['financial']['full_payment_reference'] ?? '' }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg"
                        placeholder="e.g., ABC123DEF">
                </div>
            </div>

            <div x-show="paymentMode === 'bank'" x-transition class="p-4 bg-purple-50 rounded-lg space-y-3">
                <p class="text-sm font-medium text-purple-800">Bank Payment Details:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs mb-2">
                    <div><span class="text-gray-600">Bank:</span> <span class="font-medium">{{ $bankName }}</span></div>
                    <div><span class="text-gray-600">Account:</span> <span class="font-medium">{{ $bankAccountNumber }}</span></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Reference *</label>
                    <input type="text" name="full_payment_reference" value="{{ $formData['financial']['full_payment_reference'] ?? '' }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg"
                        placeholder="Bank transaction ID">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Deposit Slip (Optional)</label>
                    <input type="file" name="full_payment_receipt" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <div x-show="paymentMode === 'cash'" x-transition class="p-4 bg-blue-50 rounded-lg space-y-3">
                <p class="text-sm font-medium text-blue-800">Cash Payment Instructions:</p>
                <p class="text-xs text-gray-600">Please visit the campus finance office during working hours to make your payment.</p>
                <p class="text-xs text-gray-600">Hours: Monday - Friday, 8:00 AM - 5:00 PM</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Office Reference (if given)</label>
                    <input type="text" name="full_payment_reference" value="{{ $formData['financial']['full_payment_reference'] ?? '' }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg"
                        placeholder="Reference number if given">
                </div>
            </div>
            
            <button type="button" @click="confirmFullPayment()" 
                class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center">
                <i class="fas fa-check-circle mr-2"></i>
                Confirm Payment
            </button>
        </div>

        <div x-show="payOption === 'per_fee'" x-transition class="space-y-6">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h4 class="text-sm font-bold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Application Fee *
                </h4>
                
                <div class="flex items-center mb-4">
                    <span class="mr-4 text-sm text-gray-700">Have you paid?</span>
                    <label class="mr-6 flex items-center">
                        <input type="radio" name="application_fee_paid" value="yes" x-model="applicationFeePaid" class="mr-2">
                        <span>Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="application_fee_paid" value="no" x-model="applicationFeePaid" class="mr-2">
                        <span>No</span>
                    </label>
                </div>
                
                <div x-show="applicationFeePaid === 'yes'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number *</label>
                        <input type="text" name="application_fee_reference" value="{{ $formData['financial']['application_fee_reference'] ?? '' }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Receipt</label>
                        <input type="file" name="application_fee_receipt" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                <h4 class="text-sm font-bold text-purple-800 mb-4 flex items-center">
                    <i class="fas fa-hand-holding-usd mr-2"></i>
                    Commitment Fee
                </h4>
                
                <div class="flex items-center mb-4">
                    <span class="mr-4 text-sm text-gray-700">Have you paid?</span>
                    <label class="mr-6 flex items-center">
                        <input type="radio" name="commitment_fee_paid" value="yes" x-model="commitmentFeePaid" class="mr-2">
                        <span>Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="commitment_fee_paid" value="no" x-model="commitmentFeePaid" class="mr-2">
                        <span>No</span>
                    </label>
                </div>
                
                <div x-show="commitmentFeePaid === 'yes'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number *</label>
                        <input type="text" name="commitment_fee_reference" value="{{ $formData['financial']['commitment_fee_reference'] ?? '' }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Receipt</label>
                        <input type="file" name="commitment_fee_receipt" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h4 class="text-sm font-bold text-yellow-800 mb-4 flex items-center">
            <i class="fas fa-graduation-cap mr-2"></i>
            Bursary / Financial Aid
        </h4>
        
        <div class="flex items-center mb-4">
            <span class="mr-4 text-sm text-gray-700">Are you applying?</span>
            <label class="mr-6 flex items-center">
                <input type="radio" name="requires_bursary" value="yes" x-model="showBursary" class="mr-2">
                <span>Yes</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="requires_bursary" value="no" x-model="showBursary" class="mr-2">
                <span>No</span>
            </label>
        </div>
        
        <div x-show="showBursary === 'yes'" x-transition class="space-y-3">
            <p class="text-sm text-gray-600">Select all that apply:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <label class="flex items-center">
                    <input type="checkbox" name="bursary_type[]" value="afya_elimu" class="w-4 h-4 text-yellow-600 border-gray-300 rounded mr-2">
                    <span>Afya Elimu Fund</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="bursary_type[]" value="cdf" class="w-4 h-4 text-yellow-600 border-gray-300 rounded mr-2">
                    <span>CDF Bursary</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="bursary_type[]" value="county" class="w-4 h-4 text-yellow-600 border-gray-300 rounded mr-2">
                    <span>County Bursary</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="bursary_type[]" value="other" class="w-4 h-4 text-yellow-600 border-gray-300 rounded mr-2">
                    <span>Other</span>
                </label>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">If Other, specify</label>
                <input type="text" name="bursary_other" value="{{ $formData['financial']['bursary_other'] ?? '' }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>
    </div>

    <div class="bg-gray-50 rounded-xl p-6">
        <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-wallet mr-2"></i>
            Mode of Payment
        </h4>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @if($mpesaEnabled)
            <button type="button" @click="openMpesaModal()" 
                class="p-4 border-2 border-gray-200 rounded-lg bg-white hover:border-green-500 hover:bg-green-50 transition-all text-left">
                <div class="flex items-center mb-2">
                    <i class="fas fa-mobile-alt text-green-500 text-xl mr-2"></i>
                    <span class="font-semibold text-gray-900">M-PESA</span>
                </div>
                <p class="text-xs text-gray-500">Pay via Safaricom</p>
            </button>
            @endif
            
            @if($bankEnabled && $bankName)
            <button type="button" @click="openBankModal()" 
                class="p-4 border-2 border-gray-200 rounded-lg bg-white hover:border-purple-500 hover:bg-purple-50 transition-all text-left">
                <div class="flex items-center mb-2">
                    <i class="fas fa-university text-purple-500 text-xl mr-2"></i>
                    <span class="font-semibold text-gray-900">Bank Deposit</span>
                </div>
                <p class="text-xs text-gray-500">Direct bank payment</p>
            </button>
            @endif
            
            @if($manualEnabled)
            <button type="button" @click="openOnlineModal()" 
                class="p-4 border-2 border-gray-200 rounded-lg bg-white hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center mb-2">
                    <i class="fas fa-building text-blue-500 text-xl mr-2"></i>
                    <span class="font-semibold text-gray-900">Cash/Office</span>
                </div>
                <p class="text-xs text-gray-500">Pay at campus</p>
            </button>
            @endif
        </div>
    </div>

    @if($mpesaEnabled)
    <div x-show="showMpesaModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl" @click.outside="showMpesaModal = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-mobile-alt text-green-600 mr-2"></i>
                    M-PESA Payment
                </h3>
                <button @click="showMpesaModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Instructions:</p>
                <ol class="text-xs text-gray-600 list-decimal list-inside space-y-1">
                    <li>Go to M-PESA on your phone</li>
                    <li>Select "Lipa na M-PESA"</li>
                    <li>Choose "Paybill"</li>
                    @if($mpesaPaybill)
                    <li>Enter business number: <strong>{{ $mpesaPaybill }}</strong></li>
                    @endif
                    <li>Enter account: <strong>Your Application ID</strong></li>
                    <li>Enter amount: <strong>{{ $currencySymbol }} {{ number_format($totalFee) }}</strong> (Full Amount)</li>
                </ol>
            </div>

            <div x-show="!mpesaSuccess">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Phone Number</label>
                    <input type="tel" x-model="mpesaPhone" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-lg"
                        placeholder="254700000000">
                </div>

                <button type="button" @click="initiateMpesa()" 
                    :disabled="mpesaLoading || !mpesaPhone"
                    class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 disabled:opacity-50">
                    <span x-show="!mpesaLoading"><i class="fas fa-mobile-alt mr-2"></i> Pay Now</span>
                    <span x-show="mpesaLoading"><i class="fas fa-spinner fa-spin mr-2"></i> Sending STK Push...</span>
                </button>

                <div x-show="mpesaWaiting" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-center">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        STK Push sent to <span x-text="mpesaPhone"></span>. Please enter your PIN on your phone.
                    </p>
                </div>

                <p x-show="mpesaError" x-text="mpesaError" class="mt-3 text-sm text-red-600 text-center"></p>
            </div>

            <div x-show="mpesaSuccess" class="text-center py-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
                <p class="text-lg font-semibold text-green-600">Payment Successful!</p>
                <p class="text-sm text-gray-600 mt-1">Your payment has been recorded.</p>
            </div>
        </div>
    </div>
    @endif

    @if($bankEnabled && $bankName)
    <div x-show="showBankModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl" @click.outside="showBankModal = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-university text-purple-600 mr-2"></i>
                    Bank Deposit
                </h3>
                <button @click="showBankModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div><span class="text-gray-500">Bank:</span> <span class="font-medium">{{ $bankName }}</span></div>
                    <div><span class="text-gray-500">Account:</span> <span class="font-medium">{{ $bankAccountNumber }}</span></div>
                    <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $bankAccountName }}</span></div>
                    @if($bankBranch)
                    <div><span class="text-gray-500">Branch:</span> <span class="font-medium">{{ $bankBranch }}</span></div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Reference *</label>
                    <input type="text" id="bankRefInput" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Deposit Slip (optional)</label>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <button type="button" @click="submitBankPayment()" 
                class="w-full mt-4 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700">
                <i class="fas fa-check mr-2"></i> Confirm Payment
            </button>
        </div>
    </div>
    @endif

    @if($manualEnabled)
    <div x-show="showOnlineModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl" @click.outside="showOnlineModal = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-building text-blue-600 mr-2"></i>
                    Cash / Office Payment
                </h3>
                <button @click="showOnlineModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-4 bg-blue-50 rounded-lg mb-4">
                <p class="text-sm text-blue-800">Please visit the campus finance office for cash payment.</p>
                <p class="text-sm text-blue-800 mt-2">Hours: Monday - Friday, 8:00 AM - 5:00 PM</p>
            </div>

            <button type="button" @click="showOnlineModal = false" 
                class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                <i class="fas fa-check mr-2"></i> I Understand
            </button>
        </div>
    </div>
    @endif
</div>

<script>
function financialForm() {
    return {
        sponsorshipType: '{{ $sponsorshipType }}',
        payOption: '{{ $payOption }}',
        paymentMode: '{{ $paymentMode }}',
        showSponsorFields: {{ $showSponsorFields }},
        applicationFeePaid: '{{ $applicationFeePaid }}',
        commitmentFeePaid: '{{ $commitmentFeePaid }}',
        showBursary: '{{ $showBursary }}',
        showMpesaModal: false,
        showBankModal: false,
        showOnlineModal: false,
        mpesaPhone: '',
        mpesaLoading: false,
        mpesaSuccess: false,
        mpesaError: '',
        mpesaWaiting: false,
        
        init: function() {
            this.updateSponsorVisibility();
        },
        
        updateSponsorVisibility: function() {
            this.showSponsorFields = this.sponsorshipType !== 'self';
        },
        
        openMpesaModal: function() {
            this.paymentMode = 'mpesa';
            this.showMpesaModal = true;
        },
        
        openBankModal: function() {
            this.paymentMode = 'bank';
            this.showBankModal = true;
        },
        
        openOnlineModal: function() {
            this.paymentMode = 'cash';
            this.showOnlineModal = true;
        },
        
        initiateMpesa: function() {
            // For local development, simulate STK push
            var isLocal = window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost';
            
            if (isLocal) {
                // Simulate STK push for local development
                this.mpesaLoading = true;
                this.mpesaWaiting = true;
                this.mpesaError = '';
                
                var self = this;
                
                // Simulate user completing payment after 3 seconds
                setTimeout(function() {
                    self.mpesaWaiting = false;
                    self.mpesaSuccess = true;
                    if (self.payOption === 'full') {
                        self.applicationFeePaid = 'yes';
                        self.commitmentFeePaid = 'yes';
                    }
                    // Auto close after success
                    setTimeout(function() {
                        self.showMpesaModal = false;
                        self.mpesaPhone = '';
                        self.mpesaSuccess = false;
                    }, 5000);
                }, 3000);
                
                return;
            }
            
            // Production: actual M-PESA API call
            this.mpesaLoading = true;
            this.mpesaError = '';
            this.mpesaSuccess = false;
            this.mpesaWaiting = false;
            
            var self = this;
            fetch('{{ route('student.payment.initiate-mpesa') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    phone: this.mpesaPhone,
                    amount: {{ $totalFee }}
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                self.mpesaLoading = false;
                
                if (data.success) {
                    // STK Push sent successfully - show waiting message
                    self.mpesaWaiting = true;
                    
                    // Poll for payment status every 3 seconds for up to 60 seconds
                    var pollCount = 0;
                    var maxPolls = 20;
                    
                    var pollInterval = setInterval(function() {
                        pollCount++;
                        
                        // Check if we should stop polling
                        if (pollCount >= maxPolls) {
                            clearInterval(pollInterval);
                            self.mpesaWaiting = false;
                            self.mpesaError = 'Payment timeout. Please check your phone and try again.';
                            return;
                        }
                        
                        // In production, you'd call a status check endpoint here
                        // For now, assume user will complete payment
                    }, 3000);
                    
                } else {
                    self.mpesaError = data.message || 'STK Push failed. Please try again.';
                }
            })
            .catch(function(error) {
                self.mpesaLoading = false;
                self.mpesaError = 'Network error. Please check your connection.';
            });
        },
        
        submitBankPayment: function() {
            var ref = document.getElementById('bankRefInput').value;
            if (!ref) {
                alert('Transaction reference is required');
                return;
            }
            
            if (this.payOption === 'full') {
                this.applicationFeePaid = 'yes';
                this.commitmentFeePaid = 'yes';
            }
            
            this.showBankModal = false;
            alert('Bank payment submitted. Reference: ' + ref);
        },
        
        confirmFullPayment: function() {
            var reference = document.querySelector('[name="full_payment_reference"]').value;
            if (!reference && this.paymentMode !== 'cash') {
                alert('Please enter your transaction reference number');
                return;
            }
            
            this.applicationFeePaid = 'yes';
            this.commitmentFeePaid = 'yes';
            
            alert('Payment recorded! Please proceed to the next step.');
        }
    };
}
</script>