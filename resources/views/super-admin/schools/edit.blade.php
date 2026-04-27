@extends('layouts.super-admin')

@section('header', 'Edit School')
@section('breadcrumb', 'Update school information')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit School</h1>
            <p class="text-sm text-gray-500">Update school information</p>
        </div>
        <a href="{{ route('super-admin.schools.show', $school) }}" class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
    </div>

    <form action="{{ route('super-admin.schools.update', $school) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">School Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $school->code) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent uppercase">
                    @error('code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $school->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('address', $school->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                    <input type="text" name="domain" value="{{ old('domain', $school->domain) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="school.kmtc.ac.ke">
                    @error('domain')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subdomain</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain', $school->subdomain) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="schoolname">
                    <p class="mt-1 text-xs text-gray-500">Auto-detected from subdomain.domain.com</p>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $school->slug) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="school-name">
                    <p class="mt-1 text-xs text-gray-500">Used in login URLs: /login?school=slug</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="active" {{ old('status', $school->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $school->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $school->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description', $school->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    @if($school->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-20 w-auto rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">Leave empty to keep current logo</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                    @if($school->favicon)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $school->favicon) }}" alt="Favicon" class="h-10 w-auto rounded">
                        </div>
                    @endif
                    <input type="file" name="favicon" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">Leave empty to keep current favicon</p>
                    @error('favicon')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signature Image (for letters)</label>
                    @if($school->signature_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $school->signature_image) }}" alt="Signature" class="h-16 w-auto rounded">
                        </div>
                    @endif
                    <input type="file" name="signature_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">PNG with transparent background recommended</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seal Image (for letters)</label>
                    @if($school->seal_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $school->seal_image) }}" alt="Seal" class="h-12 w-auto rounded">
                        </div>
                    @endif
                    <input type="file" name="seal_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">PNG with transparent background</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Name</label>
                    <input type="text" name="signatory_name" value="{{ old('signatory_name', $school->signatory_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="e.g., Dr. John Smith">
                    <p class="mt-1 text-xs text-gray-500">Name shown on official letters</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Title</label>
                    <input type="text" name="signatory_title" value="{{ old('signatory_title', $school->signatory_title) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="e.g., Registrar">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $school->primary_color ?? '#7C3AED') }}" class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <input type="text" name="primary_color_text" value="{{ old('primary_color', $school->primary_color ?? '#7C3AED') }}" placeholder="#7C3AED"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" 
                            onchange="document.querySelector('input[name=primary_color]').value = this.value">
                    </div>
                    @error('primary_color')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $school->secondary_color ?? '#10B981') }}" class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <input type="text" name="secondary_color_text" value="{{ old('secondary_color', $school->secondary_color ?? '#10B981') }}" placeholder="#10B981"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                            onchange="document.querySelector('input[name=secondary_color]').value = this.value">
                    </div>
                    @error('secondary_color')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('super-admin.schools.show', $school) }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Update School
            </button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">School Staff</h3>
        </div>

        @if($staff->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($staff as $member)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $member->fullName() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $member->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($member->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($member->role === 'registrar') bg-blue-100 text-blue-800
                                @elseif($member->role === 'accountant') bg-green-100 text-green-800
                                @elseif($member->role === 'reviewer') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($member->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($member->is_active)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500">No staff members yet.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign New Staff</h3>
        
        <form action="{{ route('super-admin.schools.admin.create', $school) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="registrar" {{ old('role') == 'registrar' ? 'selected' : '' }}>Registrar</option>
                        <option value="accountant" {{ old('role') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                        <option value="reviewer" {{ old('role') == 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                        <option value="support" {{ old('role') == 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Assign Staff
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-red-600">Danger Zone</h3>
        <p class="text-sm text-gray-500 mb-4">Once you delete a school, there is no going back.</p>
        <form action="{{ route('super-admin.schools.destroy', $school) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this school? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Delete School
            </button>
        </form>
    </div>
</div>
@endsection
