<div x-data="{
    files: {},
    previews: {},
    maxSize: 2 * 1024 * 1024, // 2MB
    sponsorshipType: '{{ $formData['financial']['sponsorship_type'] ?? 'self' }}',
    showSponsorLetter: false,
    init() {
        this.updateSponsorVisibility();
    },
    updateSponsorVisibility() {
        this.showSponsorLetter = this.sponsorshipType !== 'self';
    },
    handleFileUpload(fieldName, event) {
        const file = event.target.files[0];
        if (!file) return;
        
        if (file.size > this.maxSize) {
            alert('File size exceeds 2MB limit');
            event.target.value = '';
            return;
        }
        
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Accepted formats: PDF, JPG, PNG');
            event.target.value = '';
            return;
        }
        
        this.files[fieldName] = file;
        
        // Create preview for images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previews[fieldName] = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    handleDrop(fieldName, event) {
        event.preventDefault();
        const file = event.dataTransfer.files[0];
        if (!file) return;
        
        if (file.size > this.maxSize) {
            alert('File size exceeds 2MB limit');
            return;
        }
        
        this.files[fieldName] = file;
        
        // Update the file input
        const input = document.getElementById(fieldName);
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
    },
    handleDragOver(event) {
        event.preventDefault();
        event.target.closest('.drop-zone').classList.add('border-purple-500', 'bg-purple-50');
    },
    handleDragLeave(event) {
        event.target.closest('.drop-zone').classList.remove('border-purple-500', 'bg-purple-50');
    },
    removeFile(fieldName) {
        delete this.files[fieldName];
        delete this.previews[fieldName];
        document.getElementById(fieldName).value = '';
    },
    getFileIcon(type) {
        if (!type) return 'fa-file';
        if (type.startsWith('image/')) return 'fa-image';
        if (type.includes('pdf')) return 'fa-file-pdf';
        return 'fa-file-alt';
    },
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}" class="space-y-6">

    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-0.5"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-semibold text-blue-900">Upload Requirements</h4>
                <ul class="mt-2 text-sm text-blue-800 space-y-1">
                    <li>• Accepted formats: PDF, JPG, PNG</li>
                    <li>• Maximum file size: 2MB per document</li>
                    <li>• Ensure documents are clear and readable</li>
                    <li>• Files can be uploaded via drag & drop or click to browse</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- National ID / Passport --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-id-card text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">National ID / Passport</h4>
                        <p class="text-xs text-gray-500">Valid identification document</p>
                    </div>
                </div>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">Required</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('id_document', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.id_document">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="id_document" name="id_document" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('id_document', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.id_document && files.id_document.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.id_document.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.id_document.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.id_document.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('id_document')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- KCSE Certificate --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-certificate text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">KCSE Certificate</h4>
                        <p class="text-xs text-gray-500">KCSE certificate or result slip</p>
                    </div>
                </div>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">Required</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('kcse_certificate', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.kcse_certificate">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="kcse_certificate" name="kcse_certificate" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('kcse_certificate', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.kcse_certificate && files.kcse_certificate.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.kcse_certificate.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.kcse_certificate.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.kcse_certificate.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('kcse_certificate')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Passport Photo --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-camera text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Passport Photo</h4>
                        <p class="text-xs text-gray-500">Recent passport-size photo</p>
                    </div>
                </div>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">Required</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('passport_photo', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.passport_photo">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="passport_photo" name="passport_photo" class="hidden" accept=".jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('passport_photo', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.passport_photo && files.passport_photo.name">
                    <div class="space-y-3">
                        <img x-ref="photoPreview" class="hidden max-h-32 mx-auto rounded-lg shadow-sm">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.passport_photo.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.passport_photo.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.passport_photo.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('passport_photo')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Birth Certificate --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-birthday-cake text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Birth Certificate</h4>
                        <p class="text-xs text-gray-500">Official birth certificate</p>
                    </div>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">Optional</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('birth_certificate', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.birth_certificate">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="birth_certificate" name="birth_certificate" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('birth_certificate', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.birth_certificate && files.birth_certificate.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.birth_certificate.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.birth_certificate.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.birth_certificate.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('birth_certificate')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- KCSE Result Slip --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-file-alt text-amber-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">KCSE Result Slip</h4>
                        <p class="text-xs text-gray-500">KCSE Result Slip with grades</p>
                    </div>
                </div>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">Required</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('kcse_result_slip', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.kcse_result_slip">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="kcse_result_slip" name="kcse_result_slip" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('kcse_result_slip', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.kcse_result_slip && files.kcse_result_slip.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.kcse_result_slip.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.kcse_result_slip.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.kcse_result_slip.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('kcse_result_slip')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Any Prior Certificate --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-graduation-cap text-indigo-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Any Prior Certificate</h4>
                        <p class="text-xs text-gray-500">Previous academic certificates (if any)</p>
                    </div>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">Optional</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('prior_certificate', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.prior_certificate">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="prior_certificate" name="prior_certificate" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('prior_certificate', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.prior_certificate && files.prior_certificate.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.prior_certificate.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.prior_certificate.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.prior_certificate.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('prior_certificate')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Sponsorship / Bursary Letter --}}
        <div x-show="showSponsorLetter" x-transition class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-pink-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Sponsorship / Bursary Letter</h4>
                        <p class="text-xs text-gray-500">Letter from sponsor or bursary provider</p>
                    </div>
                </div>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">Required</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('sponsorship_letter', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.sponsorship_letter">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="sponsorship_letter" name="sponsorship_letter" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('sponsorship_letter', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.sponsorship_letter && files.sponsorship_letter.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.sponsorship_letter.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.sponsorship_letter.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.sponsorship_letter.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('sponsorship_letter')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Medical Certificate --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-md text-teal-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Medical Certificate</h4>
                        <p class="text-xs text-gray-500">Medical clearance (if required)</p>
                    </div>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">Optional</span>
            </div>
            <div class="drop-zone relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition-all cursor-pointer"
                x-on:drop="handleDrop('medical_certificate', $event)"
                x-on:dragover="handleDragOver($event)"
                x-on:dragleave="handleDragLeave($event)">
                
                <template x-if="!files.medical_certificate">
                    <div>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag & drop or click to upload</p>
                        <label class="inline-block bg-[#00008B] text-white px-4 py-2 rounded-lg text-sm cursor-pointer hover:bg-[#1e40af] transition-colors">
                            Choose File
                            <input type="file" id="medical_certificate" name="medical_certificate" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                x-on:change="handleFileUpload('medical_certificate', $event)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG, PNG (max 2MB)</p>
                    </div>
                </template>
                
                <template x-if="files.medical_certificate && files.medical_certificate.name">
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas text-gray-500" :class="getFileIcon(files.medical_certificate.type)"></i>
                            <p class="text-sm text-gray-700 font-medium" x-text="files.medical_certificate.name"></p>
                        </div>
                        <p class="text-xs text-gray-500" x-text="formatFileSize(files.medical_certificate.size)"></p>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" x-on:click="removeFile('medical_certificate')">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Upload Progress Summary --}}
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
        <h4 class="font-semibold text-gray-700 mb-3">Upload Summary</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="text-center p-3 bg-white rounded-lg">
                <template x-if="files.id_document">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </template>
                <template x-if="!files.id_document">
                    <i class="fas fa-times-circle text-gray-400 text-xl"></i>
                </template>
                <p class="text-xs text-gray-600 mt-1">ID/Passport</p>
            </div>
            <div class="text-center p-3 bg-white rounded-lg">
                <template x-if="files.kcse_certificate">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </template>
                <template x-if="!files.kcse_certificate">
                    <i class="fas fa-times-circle text-gray-400 text-xl"></i>
                </template>
                <p class="text-xs text-gray-600 mt-1">KCSE Cert</p>
            </div>
            <div class="text-center p-3 bg-white rounded-lg">
                <template x-if="files.passport_photo">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </template>
                <template x-if="!files.passport_photo">
                    <i class="fas fa-times-circle text-gray-400 text-xl"></i>
                </template>
                <p class="text-xs text-gray-600 mt-1">Photo</p>
            </div>
            <div class="text-center p-3 bg-white rounded-lg">
                <template x-if="files.kcse_result_slip">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </template>
                <template x-if="!files.kcse_result_slip">
                    <i class="fas fa-times-circle text-gray-400 text-xl"></i>
                </template>
                <p class="text-xs text-gray-600 mt-1">Result Slip</p>
            </div>
        </div>
    </div>
</div>