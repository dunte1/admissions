@extends('layouts.super-admin')

@section('title', $program->name)

@section('page-title', $program->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super-admin.programs.index') }}">Programs</a></li>
    <li class="breadcrumb-item active">{{ $program->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Program Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Code:</th>
                                <td><span class="badge bg-dark">{{ $program->code }}</span></td>
                            </tr>
                            <tr>
                                <th>School:</th>
                                <td>{{ $program->school?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td>{{ $program->department?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Level:</th>
                                <td><span class="badge bg-info">{{ ucfirst($program->level) }}</span></td>
                            </tr>
                            <tr>
                                <th>Duration:</th>
                                <td>{{ $program->duration_years }} Years</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Tuition/Year:</th>
                                <td>{{ number_format($program->tuition_per_year, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Capacity:</th>
                                <td>{{ $program->capacity ?? 'Unlimited' }}</td>
                            </tr>
                            <tr>
                                <th>Available Slots:</th>
                                <td>{{ $program->getAvailableSlots() }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($program->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Total Cost:</th>
                                <td><strong>{{ number_format($program->tuition_per_year * $program->duration_years, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($program->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $program->description }}</p>
                    </div>
                @endif

                @if($program->requirements)
                    <div class="mt-3">
                        <h6>Requirements</h6>
                        <p class="text-muted">{{ $program->requirements }}</p>
                    </div>
                @endif

                @if($program->career_opportunities)
                    <div class="mt-3">
                        <h6>Career Opportunities</h6>
                        <p class="text-muted">{{ $program->career_opportunities }}</p>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('super-admin.programs.edit', $program) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Program
                    </a>
                    <form action="{{ route('super-admin.programs.destroy', $program) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Applications</h5>
            </div>
            <div class="card-body">
                @if($program->applications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Application #</th>
                                    <th>Student</th>
                                    <th>Intake</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($program->applications->take(10) as $application)
                                    <tr>
                                        <td><span class="badge bg-light text-dark">{{ $application->application_number }}</span></td>
                                        <td>{{ $application->student?->user?->fullName() ?? 'N/A' }}</td>
                                        <td>{{ $application->intake?->name ?? 'N/A' }}</td>
                                        <td>
                                            @switch($application->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('reviewing')
                                                    <span class="badge bg-info">Reviewing</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($application->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">No applications yet</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Applications</span>
                    <strong>{{ $stats['total_applications'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Pending</span>
                    <strong class="text-warning">{{ $stats['pending'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Approved</span>
                    <strong class="text-success">{{ $stats['approved'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Rejected</span>
                    <strong class="text-danger">{{ $stats['rejected'] }}</strong>
                </div>

                <hr>

                @if($stats['total_applications'] > 0)
                    <div class="mt-3">
                        <p class="mb-2">Approval Rate</p>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($stats['approved'] / $stats['total_applications']) * 100 }}%">
                                {{ round(($stats['approved'] / $stats['total_applications']) * 100, 1) }}%
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
