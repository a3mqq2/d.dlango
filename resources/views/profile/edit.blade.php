@extends('layouts.app')

@section('title', __('messages.profile'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @include('layouts.messages')

        {{-- Profile Information --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-user me-2"></i>
                    {{ __('messages.profile_info') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.role') }}</label>
                        <input type="text" class="form-control" value="{{ $user->getRoleLabel() }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.member_since') }}</label>
                        <input type="text" class="form-control" value="{{ $user->created_at->format('Y-m-d') }}" disabled>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" style="background-color: #b65f7a; border-color: #b65f7a;">
                            <i class="ti ti-check me-1"></i>
                            {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-lock me-2"></i>
                    {{ __('messages.change_password') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">{{ __('messages.current_password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.new_password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('messages.password_requirements') }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('messages.confirm_new_password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-key me-1"></i>
                            {{ __('messages.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
