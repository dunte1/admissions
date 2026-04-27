<form action="{{ route('admin.settings.update', 'document') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Document Settings</h2>
                <p class="text-sm text-gray-500">Configure required documents and upload settings</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div>
                <h3 class="text-md font-medium text-gray-900 mb-4">Required Documents</h3>
                <p class="text-sm text-gray-500 mb-4">Select the documents that applicants must upload</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @php
                        $requiredDocs = old('required_documents', $settings['required_documents'] ?? ['id_document', 'kcse_certificate']);
                        if (is_string($requiredDocs)) {
                            $requiredDocs = json_decode($requiredDocs, true) ?? ['id_document', 'kcse_certificate'];
                        }
                    @endphp

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="id_document"
                            {{ in_array('id_document', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">National ID / Passport</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="birth_certificate"
                            {{ in_array('birth_certificate', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Birth Certificate</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="kcse_certificate"
                            {{ in_array('kcse_certificate', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">KCSE Certificate</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="kcse_results"
                            {{ in_array('kcse_results', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">KCSE Results Slip</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="certificate"
                            {{ in_array('certificate', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Academic Certificates</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="transcript"
                            {{ in_array('transcript', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Academic Transcripts</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="photo"
                            {{ in_array('photo', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Passport Photo</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="recommendation_letter"
                            {{ in_array('recommendation_letter', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Recommendation Letter</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="medical_form"
                            {{ in_array('medical_form', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Medical Form</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="required_documents[]" value="police_clearance"
                            {{ in_array('police_clearance', $requiredDocs) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Police Clearance</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Upload Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maximum File Size (MB)</label>
                        <input type="number" name="document_max_size" 
                            value="{{ old('document_max_size', $settings['document_max_size'] ?? 5) }}"
                            min="1" max="50"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Maximum: 50MB</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allowed File Types</label>
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_document_types[]" value="pdf"
                                    {{ in_array('pdf', old('allowed_document_types', $settings['allowed_document_types'] ?? ['pdf', 'jpg', 'png'])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_document_types[]" value="jpg"
                                    {{ in_array('jpg', old('allowed_document_types', $settings['allowed_document_types'] ?? ['pdf', 'jpg', 'png'])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">JPG</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_document_types[]" value="png"
                                    {{ in_array('png', old('allowed_document_types', $settings['allowed_document_types'] ?? ['pdf', 'jpg', 'png'])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">PNG</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>