@extends('layouts.app')

@section('title', __('messages.cash_registers'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-safe me-2"></i>
                    {{ __('messages.cash_registers') }}
                </h5>
                <a href="{{ route('cash-registers.create') }}" class="btn btn-primary" style="background-color: #b65f7a; border-color: #b65f7a;">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('messages.add_cash_register') }}
                </a>
            </div>
            <div class="card-body">
                @include('layouts.messages')

                {{-- Filters --}}
                <form method="GET" action="{{ route('cash-registers.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="currency" class="form-select">
                            <option value="">{{ __('messages.all_currencies') }}</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ request('currency') == $currency->id ? 'selected' : '' }}>{{ $currency->name }}</option>
                            @endforeach
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
                        <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary">
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
                                <th>{{ __('messages.cash_register_name') }}</th>
                                <th>{{ __('messages.currency') }}</th>
                                <th>{{ __('messages.opening_balance') }}</th>
                                <th>{{ __('messages.current_balance') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashRegisters as $register)
                                <tr>
                                    <td>{{ $loop->iteration + ($cashRegisters->currentPage() - 1) * $cashRegisters->perPage() }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $register->name }}</span>
                                        @if($register->description)
                                            <br><small class="text-muted">{{ Str::limit($register->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $register->currency->name }}
                                        @if($register->currency->symbol)
                                            ({{ $register->currency->symbol }})
                                        @endif
                                    </td>
                                    <td>{{ number_format($register->opening_balance, 2) }}</td>
                                    <td>
                                        <span class="fw-bold {{ $register->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($register->current_balance, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($register->is_active)
                                            <span class="badge bg-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('cash-registers.show', $register) }}" class="btn btn-sm btn-outline-info" title="{{ __('messages.view') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('cash-registers.edit', $register) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('cash-registers.toggle-status', $register) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $register->is_active ? 'warning' : 'success' }}" title="{{ $register->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                                    <i class="ti ti-{{ $register->is_active ? 'toggle-right' : 'toggle-left' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('cash-registers.destroy', $register) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-safe fs-1 d-block mb-2"></i>
                                            {{ __('messages.no_cash_registers') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $cashRegisters->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
