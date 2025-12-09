@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="row">
    <div class="col-12">
        {{-- User Info Card --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-user me-2"></i>
                    {{ __('messages.user_details') }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm" style="background-color: #b65f7a; border-color: #b65f7a;">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit') }}
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('layouts.messages')

                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3" style="background: linear-gradient(135deg, #b65f7a 0%, #2a9d8f 100%); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        @if($user->isAdmin())
                            <span class="badge bg-danger">
                                <i class="ti ti-shield me-1"></i>
                                {{ __('messages.role_admin') }}
                            </span>
                        @else
                            <span class="badge bg-primary">
                                <i class="ti ti-user me-1"></i>
                                {{ __('messages.role_user') }}
                            </span>
                        @endif
                        @if($user->is_active)
                            <span class="badge bg-success">{{ __('messages.active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">{{ __('messages.created_at') }}</p>
                        <p class="fw-medium">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">{{ __('messages.last_updated') }}</p>
                        <p class="fw-medium">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Statistics --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-bar me-2"></i>
                    {{ __('messages.statistics') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0" style="color: #b65f7a;">{{ $user->cases->count() }}</h3>
                            <small class="text-muted">{{ __('messages.total_cases') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0 text-success">{{ $user->cases->where('status', 'open')->count() }}</h3>
                            <small class="text-muted">{{ __('messages.open_cases') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0 text-secondary">{{ $user->cases->where('status', 'closed')->count() }}</h3>
                            <small class="text-muted">{{ __('messages.closed_cases') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
