@extends('layouts.app')

@section('title', __('messages.users'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    {{ __('messages.users') }}
                </h5>
                <a href="{{ route('users.create') }}" class="btn btn-primary" style="background-color: #b65f7a; border-color: #b65f7a;">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_user') }}
                </a>
            </div>
            <div class="card-body">
                @include('layouts.messages')

                {{-- Filters --}}
                <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-select">
                            <option value="">{{ __('messages.all_roles') }}</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('messages.role_admin') }}</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>{{ __('messages.role_user') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-1"></i>
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.email') }}</th>
                                <th>{{ __('messages.role') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.created_at') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2" style="background: #b65f7a; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-info ms-2">{{ __('messages.you') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info" title="{{ __('messages.view') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" title="{{ $user->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                                        <i class="ti ti-{{ $user->is_active ? 'toggle-right' : 'toggle-left' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-users-off fs-1 d-block mb-2"></i>
                                            {{ __('messages.no_users') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
