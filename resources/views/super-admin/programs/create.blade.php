@extends('layouts.super-admin')

@section('title', 'Create Program')

@section('page-title', 'Create New Program')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super-admin.programs.index') }}">Programs</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Program Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.programs.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">School <span class="text-danger">*</span></label>
                            <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                <option value="">Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">Select Department</option>
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Program Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Short Name</label>
                            <input type="text" name="short_name" class="form-control" value="{{ old('short_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Code (for Admission Numbers)</label>
                            <input type="text" name="program_code" class="form-control" value="{{ old('program_code') }}" placeholder="e.g., MED, NUR, ENG">
                            <small class="text-muted">Used in admission number generation</small>
                        </div>
                    </div>
                            <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                                <option value="">Select Level</option>
                                <option value="certificate" {{ old('level') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                                <option value="diploma" {{ old('level') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                                <option value="degree" {{ old('level') == 'degree' ? 'selected' : '' }}>Degree</option>
                                <option value="masters" {{ old('level') == 'masters' ? 'selected' : '' }}>Masters</option>
                                <option value="phd" {{ old('phd') == 'phd' ? 'selected' : '' }}>PhD</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Duration (Years) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_years" class="form-control @error('duration_years') is-invalid @enderror" value="{{ old('duration_years', 3) }}" min="1" max="10" required>
                            @error('duration_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tuition Per Year <span class="text-danger">*</span></label>
                            <input type="number" name="tuition_per_year" class="form-control @error('tuition_per_year') is-invalid @enderror" value="{{ old('tuition_per_year') }}" step="0.01" min="0" required>
                            @error('tuition_per_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" class="form-control" rows="3">{{ old('requirements') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Career Opportunities</label>
                        <textarea name="career_opportunities" class="form-control" rows="2">{{ old('career_opportunities') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.programs.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('school_id').addEventListener('change', function() {
    const schoolId = this.value;
    const departmentSelect = document.getElementById('department_id');
    
    departmentSelect.innerHTML = '<option value="">Loading...</option>';
    
    if (schoolId) {
        fetch(`/super-admin/programs/get-departments?school_id=${schoolId}`)
            .then(response => response.json())
            .then(data => {
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                data.forEach(dept => {
                    departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                });
            });
    } else {
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
    }
});
</script>
@endpush
@endsection
