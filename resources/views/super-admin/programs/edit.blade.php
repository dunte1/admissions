@extends('layouts.super-admin')

@section('title', 'Edit Program')

@section('page-title', 'Edit Program')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super-admin.programs.index') }}">Programs</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Program</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.programs.update', $program) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">School <span class="text-danger">*</span></label>
                            <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ $program->school_id == $school->id ? 'selected' : '' }}>
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
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $program->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Program Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $program->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $program->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Short Name</label>
                            <input type="text" name="short_name" class="form-control" value="{{ old('short_name', $program->short_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                                <option value="certificate" {{ $program->level == 'certificate' ? 'selected' : '' }}>Certificate</option>
                                <option value="diploma" {{ $program->level == 'diploma' ? 'selected' : '' }}>Diploma</option>
                                <option value="degree" {{ $program->level == 'degree' ? 'selected' : '' }}>Degree</option>
                                <option value="masters" {{ $program->level == 'masters' ? 'selected' : '' }}>Masters</option>
                                <option value="phd" {{ $program->level == 'phd' ? 'selected' : '' }}>PhD</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Duration (Years) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_years" class="form-control @error('duration_years') is-invalid @enderror" value="{{ old('duration_years', $program->duration_years) }}" min="1" max="10" required>
                            @error('duration_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tuition Per Year <span class="text-danger">*</span></label>
                            <input type="number" name="tuition_per_year" class="form-control @error('tuition_per_year') is-invalid @enderror" value="{{ old('tuition_per_year', $program->tuition_per_year) }}" step="0.01" min="0" required>
                            @error('tuition_per_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $program->capacity) }}" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $program->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" class="form-control" rows="3">{{ old('requirements', $program->requirements) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Career Opportunities</label>
                        <textarea name="career_opportunities" class="form-control" rows="2">{{ old('career_opportunities', $program->career_opportunities) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $program->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.programs.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
