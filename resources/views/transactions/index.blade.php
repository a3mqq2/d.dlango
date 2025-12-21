@extends('layouts.app')

@section('title', __('messages.transactions'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.transactions') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-arrows-exchange me-2"></i>
                    {{ __('messages.transactions') }}
                </h5>
                <div>
                    <a href="{{ route('transactions.statement') }}" class="btn btn-outline-primary me-2">
                        <i class="ti ti-file-text me-1"></i>
                        {{ __('messages.account_statement') }}
                    </a>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.add_transaction') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Search Filters --}}
                <form method="GET" action="{{ route('transactions.index') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('messages.search_transactions') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="cashbox_id" class="form-select">
                            <option value="">{{ __('messages.all_cashboxes') }}</option>
                            @foreach($cashboxes as $cashbox)
                                <option value="{{ $cashbox->id }}" {{ request('cashbox_id') == $cashbox->id ? 'selected' : '' }}>
                                    {{ $cashbox->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">{{ __('messages.all_types') }}</option>
                            <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>
                                {{ __('messages.deposit') }}
                            </option>
                            <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>
                                {{ __('messages.withdrawal') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="ti ti-filter me-1"></i>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-1"></i>
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="12%">{{ __('messages.date') }}</th>
                                <th width="18%">{{ __('messages.cashbox') }}</th>
                                <th width="20%">{{ __('messages.recipient_payer') }}</th>
                                <th width="12%">{{ __('messages.type') }}</th>
                                <th width="18%">{{ __('messages.amount') }}</th>
                                <th width="20%" class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <small class="text-muted" dir="ltr">{{ $transaction->created_at->format('Y-m-d') }}</small><br>
                                        <small class="text-muted" dir="ltr">{{ $transaction->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($transaction->cashbox)
                                                <i class="ti ti-cash text-primary me-2"></i>
                                                <span>{{ $transaction->cashbox->name }}</span>
                                            @else
                                                <i class="ti ti-calendar-due text-warning me-2"></i>
                                                <span class="text-muted">{{ __('messages.credit_transaction') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $transaction->recipient_name }}</div>
                                        @if($transaction->recipient_number)
                                            <small class="text-muted" dir="ltr">{{ $transaction->recipient_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}">
                                            <i class="ti ti-{{ $transaction->type == 'deposit' ? 'arrow-down' : 'arrow-up' }} me-1"></i>
                                            {{ __('messages.' . $transaction->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }}" dir="ltr">
                                            {{ $transaction->type == 'deposit' ? '+' : '-' }}
                                            {{ number_format($transaction->amount, 2) }}
                                            <small class="text-muted">{{ __('messages.currency') }}</small>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('transactions.show', $transaction) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="{{ __('messages.view') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            {{-- <a href="{{ route('transactions.receipt', $transaction) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="{{ __('messages.print_receipt') }}"
                                               target="_blank">
                                                <i class="ti ti-printer"></i>
                                            </a> --}}
                                            <a href="{{ route('transactions.edit', $transaction) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="{{ __('messages.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('transactions.destroy', $transaction) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('messages.confirm_delete_transaction') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="{{ __('messages.delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-arrows-exchange-off" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">{{ __('messages.no_transactions') }}</h5>
                                            <p>{{ __('messages.no_transactions_desc') }}</p>
                                            <a href="{{ route('transactions.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i>
                                                {{ __('messages.add_first_transaction') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
