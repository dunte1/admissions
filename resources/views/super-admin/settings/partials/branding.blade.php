<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<form action="{{ route('super-admin.settings.update', 'branding') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">System Branding</h2>
                <p class="text-sm text-gray-500">Configure default branding for the entire system</p>
            </div>
            <button type="submit" class="bg-[#00008B] text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">System Identity</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">System Name</label>
                        <input type="text" name="app_name"
                            value="{{ old('app_name', $settings['app_name'] ?? system_setting('app_name', 'Admission Portal')) }}"
                            placeholder="Admission Portal"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Brand Colors</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="primary_color_picker"
                                value="{{ old('primary_color', $settings['primary_color'] ?? '#00008B') }}"
                                oninput="document.getElementById('primary_color_text').value = this.value"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="primary_color" id="primary_color_text"
                                value="{{ old('primary_color', $settings['primary_color'] ?? '#00008B') }}"
                                oninput="document.getElementById('primary_color_picker').value = this.value"
                                placeholder="#00008B"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="secondary_color_picker"
                                value="{{ old('secondary_color', $settings['secondary_color'] ?? '#10B981') }}"
                                oninput="document.getElementById('secondary_color_text').value = this.value"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="secondary_color" id="secondary_color_text"
                                value="{{ old('secondary_color', $settings['secondary_color'] ?? '#10B981') }}"
                                oninput="document.getElementById('secondary_color_picker').value = this.value"
                                placeholder="#10B981"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="accent_color_picker"
                                value="{{ old('accent_color', $settings['accent_color'] ?? '#F59E0B') }}"
                                oninput="document.getElementById('accent_color_text').value = this.value"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="accent_color" id="accent_color_text"
                                value="{{ old('accent_color', $settings['accent_color'] ?? '#F59E0B') }}"
                                oninput="document.getElementById('accent_color_picker').value = this.value"
                                placeholder="#F59E0B"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Logo & Favicon</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">System Logo</label>
                        <div id="logo-upload-area" class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <div id="logo-preview-container">
                                    @if(isset($settings['system_logo']) && $settings['system_logo'])
                                        <img src="{{ asset('storage/' . $settings['system_logo']) }}" alt="System Logo" class="mx-auto h-20 w-auto" id="current-logo">
                                    @else
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" id="current-logo-icon">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </div>
                                <img id="logo-preview" src="" alt="Preview" class="mx-auto h-20 w-auto hidden">
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="system-logo-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span id="logo-upload-text">{{ isset($settings['system_logo']) && $settings['system_logo'] ? 'Change' : 'Upload' }} a file</span>
                                        <input id="system-logo-upload" name="system_logo" type="file" class="sr-only" accept="image/*" onchange="openCropper(this, 'logo')">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        <div id="favicon-upload-area" class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <div id="favicon-preview-container">
                                    @if(isset($settings['favicon']) && $settings['favicon'])
                                        <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Favicon" class="mx-auto h-12 w-12 object-contain" id="current-favicon">
                                    @else
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" id="current-favicon-icon">
                                            <path d="M24 4l-4 4m0 0L4 24m4-4v32m28-20l-4-4m0 0l16 16m-4-4V8m0 0H12m12 0l-4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </div>
                                <img id="favicon-preview" src="" alt="Preview" class="mx-auto h-12 w-12 object-contain hidden">
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="system-favicon-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span id="favicon-upload-text">{{ isset($settings['favicon']) && $settings['favicon'] ? 'Change' : 'Upload' }} a file</span>
                                        <input id="system-favicon-upload" name="favicon" type="file" class="sr-only" accept="image/*,.ico" onchange="openCropper(this, 'favicon')">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">ICO, PNG up to 512KB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Footer Branding</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                        <input type="text" name="footer_text"
                            value="{{ old('footer_text', $settings['footer_text'] ?? '© ' . date('Y') . ' ' . (app_name()) . '. Powered by Duncowebsolutions') }}"
                            placeholder="© 2024 Your Institution. All rights reserved."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Use {year} for current year, {system_name} for system name</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="show_footer_branding" id="show_footer_branding"
                            value="1"
                            {{ old('show_footer_branding', system_setting('show_footer_branding', true)) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="show_footer_branding" class="ml-2 block text-sm text-gray-700">
                            Show Footer Branding (Show "Powered by Duncowebsolutions")
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Cropper Modal -->
<div id="cropper-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeCropper()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-full max-w-2xl">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="cropper-title">Crop Image</h3>
            <button type="button" onclick="closeCropper()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 max-h-[60vh] overflow-hidden">
            <img id="cropper-image" src="" class="max-h-[50vh] mx-auto">
        </div>
        <div class="p-4 border-t flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <label class="text-sm text-gray-600">Aspect Ratio:</label>
                <select id="cropper-aspect-ratio" class="px-3 py-1 border rounded-lg text-sm">
                    <option value="NaN">Free</option>
                    <option value="1">1:1 (Square)</option>
                    <option value="16/9">16:9 (Wide)</option>
                    <option value="4/3">4:3</option>
                </select>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="closeCropper()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="button" onclick="cropImage()" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-blue-700">Crop & Save</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentCropper = null;
let currentCropperType = null;
let currentFileInput = null;

function openCropper(input, type) {
    if (!input.files || !input.files[0]) return;

    currentFileInput = input;
    currentCropperType = type;

    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const image = document.getElementById('cropper-image');
        image.src = e.target.result;

        document.getElementById('cropper-title').textContent = type === 'logo' ? 'Crop Logo' : 'Crop Favicon';
        document.getElementById('cropper-modal').classList.remove('hidden');

        if (currentCropper) {
            currentCropper.destroy();
        }

        setTimeout(() => {
            currentCropper = new Cropper(image, {
                aspectRatio: type === 'favicon' ? 1 : NaN,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }, 100);
    };

    reader.readAsDataURL(file);
}

function cropImage() {
    if (!currentCropper) return;

    const canvas = currentCropper.getCroppedCanvas({
        maxWidth: currentCropperType === 'logo' ? 800 : 64,
        maxHeight: currentCropperType === 'logo' ? 400 : 64,
        fillColor: '#fff',
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    canvas.toBlob(function(blob) {
        const file = new File([blob], currentFileInput.files[0].name, {
            type: 'image/png',
            lastModified: Date.now()
        });

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        currentFileInput.files = dataTransfer.files;

        const reader = new FileReader();
        reader.onload = function(e) {
            if (currentCropperType === 'logo') {
                document.getElementById('logo-preview').src = e.target.result;
                document.getElementById('logo-preview').classList.remove('hidden');
                document.getElementById('logo-preview-container').classList.add('hidden');
                document.getElementById('logo-upload-text').textContent = 'Change';
            } else {
                document.getElementById('favicon-preview').src = e.target.result;
                document.getElementById('favicon-preview').classList.remove('hidden');
                document.getElementById('favicon-preview-container').classList.add('hidden');
                document.getElementById('favicon-upload-text').textContent = 'Change';
            }
        };
        reader.readAsDataURL(file);

        closeCropper();
    }, 'image/png');
}

function closeCropper() {
    document.getElementById('cropper-modal').classList.add('hidden');
    if (currentCropper) {
        currentCropper.destroy();
        currentCropper = null;
    }

    if (!currentFileInput?.files?.length) {
        currentFileInput.value = '';
    }
}

document.getElementById('cropper-aspect-ratio').addEventListener('change', function() {
    if (currentCropper) {
        const ratio = this.value === 'NaN' ? NaN : parseFloat(this.value);
        currentCropper.setAspectRatio(ratio);
    }
});

// Drag and drop functionality
['logo-upload-area', 'favicon-upload-area'].forEach(function(areaId) {
    var area = document.getElementById(areaId);
    if (area) {
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            area.classList.add('border-blue-500', 'bg-blue-50');
        });
        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            area.classList.remove('border-blue-500', 'bg-blue-50');
        });
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            area.classList.remove('border-blue-500', 'bg-blue-50');
            var input = area.querySelector('input[type="file"]');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }
});
</script>
@endpush
