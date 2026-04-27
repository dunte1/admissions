<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<form action="{{ route('super-admin.settings.update', 'seo') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">SEO Settings</h2>
                <p class="text-sm text-gray-500">Configure search engine optimization settings</p>
            </div>
            <button type="submit" class="bg-[#00008B] text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Meta Tags</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title"
                            value="{{ old('meta_title', $settings['meta_title'] ?? '') }}"
                            placeholder="{{ app_name() }} - Admission Portal"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Recommended: 50-60 characters</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="3"
                            placeholder="Apply to {{ app_name() }} easily online. Streamlined admission process for students."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                        <input type="text" name="meta_keywords"
                            value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                            placeholder="admissions, online application, {{ app_name() }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Comma separated keywords</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Open Graph (Facebook)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">OG Title</label>
                        <input type="text" name="og_title"
                            value="{{ old('og_title', $settings['og_title'] ?? system_setting('meta_title', app_name() . ' - Admission Portal')) }}"
                            placeholder="{{ app_name() }} - Online Admissions"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">OG Description</label>
                        <textarea name="og_description" rows="3"
                            placeholder="Apply to {{ app_name() }} easily online. Streamlined admission process for students."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('og_description', $settings['og_description'] ?? system_setting('meta_description')) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">OG Image</label>
                        <div id="og-image-upload-area" class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <div id="og-image-preview-container">
                                    @if(isset($settings['og_image']) && $settings['og_image'])
                                        <img src="{{ asset('storage/' . $settings['og_image']) }}" alt="OG Image" class="mx-auto h-20 w-auto" id="current-og-image">
                                    @else
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </div>
                                <img id="og-image-preview" src="" alt="Preview" class="mx-auto h-20 w-auto hidden">
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="og-image-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span id="og-image-upload-text">{{ isset($settings['og_image']) && $settings['og_image'] ? 'Change' : 'Upload' }} a file</span>
                                        <input id="og-image-upload" name="og_image" type="file" class="sr-only" accept="image/*" onchange="openCropper(this, 'og_image')">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB (1200x630px recommended)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Twitter Card</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Twitter Card Type</label>
                        <select name="twitter_card" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="summary" {{ ($settings['twitter_card'] ?? 'summary_large_image') === 'summary' ? 'selected' : '' }}>Summary</option>
                            <option value="summary_large_image" {{ ($settings['twitter_card'] ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}>Summary with Large Image</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
                        <input type="url" name="canonical_url"
                            value="{{ old('canonical_url', $settings['canonical_url'] ?? url('/')) }}"
                            placeholder="{{ url('/') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
                    <option value="16/9" selected>16:9 (Wide)</option>
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

        document.getElementById('cropper-title').textContent = 'Crop OG Image';
        document.getElementById('cropper-modal').classList.remove('hidden');

        if (currentCropper) {
            currentCropper.destroy();
        }

        setTimeout(() => {
            currentCropper = new Cropper(image, {
                aspectRatio: 16/9,
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
        maxWidth: 1200,
        maxHeight: 630,
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
            document.getElementById('og-image-preview').src = e.target.result;
            document.getElementById('og-image-preview').classList.remove('hidden');
            document.getElementById('og-image-preview-container').classList.add('hidden');
            document.getElementById('og-image-upload-text').textContent = 'Change';
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

['og-image-upload-area'].forEach(function(areaId) {
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
