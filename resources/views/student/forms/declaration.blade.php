<div x-data="{
    agreed1: false,
    agreed2: false,
    agreed3: false,
    agreed4: false,
    agreed5: false,
    allAgreed() {
        return this.agreed1 && this.agreed2 && this.agreed3 && this.agreed4 && this.agreed5;
    },
    currentDate: new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
}" class="space-y-6">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0 mt-1">
                <i class="fas fa-scroll text-blue-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-bold text-gray-900">Declaration & Agreement</h4>
                <p class="text-sm text-gray-600 mt-1">Please read and accept ALL the following terms before submitting your application.</p>
            </div>
        </div>
    </div>

    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
        <div class="prose prose-sm text-gray-600">
            <h5 class="font-bold text-gray-900">1. Accuracy of Information</h5>
            <p>I declare that all information provided in this application is true, accurate, and complete to the best of my knowledge. I understand that providing false or misleading information may result in the rejection of my application or revocation of admission.</p>
            
            <h5 class="font-bold text-gray-900 mt-4">2. Admission Terms</h5>
            <p>I understand that admission to {{ app_name() }} is competitive and meeting the minimum requirements does not guarantee admission. The college reserves the right to admit or reject any applicant without disclosing reasons.</p>
            
            <h5 class="font-bold text-gray-900 mt-4">3. Fees and Payments</h5>
            <p>I agree to pay all required fees as specified by the college, including tuition, registration, and other applicable charges. I understand that fees are subject to change.</p>
            
            <h5 class="font-bold text-gray-900 mt-4">4. Code of Conduct</h5>
            <p>I agree to abide by all college rules, regulations, and policies, including the student code of conduct.</p>
            
            <h5 class="font-bold text-gray-900 mt-4">5. Document Authenticity</h5>
            <p>I declare that all documents submitted with this application are authentic and were legally obtained.</p>
        </div>
    </div>

    {{-- Multi-checkbox declarations --}}
    <div class="space-y-3 border-t border-gray-200 pt-4">
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" name="agreed_accuracy" x-model="agreed1" value="1" required 
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
            <div class="ml-3">
                <p class="text-sm text-gray-700">I confirm that all information provided is accurate and true <span class="text-red-500">*</span></p>
            </div>
        </label>
        
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" name="agreed_rules" x-model="agreed2" value="1" required 
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
            <div class="ml-3">
                <p class="text-sm text-gray-700">I agree to abide by all college rules and regulations <span class="text-red-500">*</span></p>
            </div>
        </label>
        
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" name="agreed_fees" x-model="agreed3" value="1" required 
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
            <div class="ml-3">
                <p class="text-sm text-gray-700">I understand and accept the fee payment obligations <span class="text-red-500">*</span></p>
            </div>
        </label>
        
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" name="agreed_documents" x-model="agreed4" value="1" required 
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
            <div class="ml-3">
                <p class="text-sm text-gray-700">All submitted documents are authentic and legally obtained <span class="text-red-500">*</span></p>
            </div>
        </label>
        
        <label class="flex items-start cursor-pointer">
            <input type="checkbox" name="agreed_communication" x-model="agreed5" value="1" required 
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
            <div class="ml-3">
                <p class="text-sm text-gray-700">I consent to receive communications via email and SMS <span class="text-red-500">*</span></p>
            </div>
        </label>
    </div>

    {{-- Auto-fill Name and Date --}}
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Applicant Name</label>
                <input type="text" name="applicant_name" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" readonly
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="text" name="declaration_date" :value="currentDate" readonly
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
            </div>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-amber-500"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-amber-800">
                <strong>Important:</strong> Once you submit your application, you will not be able to make changes. Please review all sections carefully before proceeding.
            </p>
        </div>
    </div>

    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-shield-alt text-green-500 mr-3 text-xl"></i>
            <div>
                <p class="text-sm font-semibold text-gray-900">Secure Submission</p>
                <p class="text-xs text-gray-500">Your data is protected and encrypted</p>
            </div>
        </div>
        <div x-show="allAgreed()" x-transition class="flex items-center text-green-600">
            <i class="fas fa-check-circle mr-2"></i>
            <span class="text-sm font-medium">Ready to submit</span>
        </div>
        <div x-show="!allAgreed()" x-transition class="flex items-center text-gray-400">
            <i class="fas fa-minus-circle mr-2"></i>
            <span class="text-sm font-medium">Accept terms to continue</span>
        </div>
    </div>
</div>
