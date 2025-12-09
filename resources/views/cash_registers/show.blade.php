@extends('layouts.app')

@section('title', __('messages.cash_register_details'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-safe me-2"></i>
                    {{ __('messages.cash_register_details') }}
                </h5>
                <div>
                    <a href="{{ route('cash-registers.edit', $cashRegister) }}" class="btn btn-primary btn-sm" style="background-color: #b65f7a; border-color: #b65f7a;">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('messages.edit') }}
                    </a>
                    <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%">{{ __('messages.cash_register_name') }}</th>
                                <td>{{ $cashRegister->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.currency') }}</th>
                                <td>{{ $cashRegister->currency->name }} @if($cashRegister->currency->symbol)({{ $cashRegister->currency->symbol }})@endif</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.opening_balance') }}</th>
                                <td>{{ number_format($cashRegister->opening_balance, 2) }} {{ $cashRegister->currency->symbol }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.current_balance') }}</th>
                                <td>
                                    <span class="fw-bold {{ $cashRegister->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($cashRegister->current_balance, 2) }} {{ $cashRegister->currency->symbol }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.status') }}</th>
                                <td>
                                    @if($cashRegister->is_active)
                                        <span class="badge bg-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.description') }}</th>
                                <td>{{ $cashRegister->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.created_at') }}</th>
                                <td>{{ $cashRegister->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($cashRegister->transactions->count() > 0)
                <hr>
                <h6 class="mb-3">{{ __('messages.recent_transactions') }}</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.document_number') }}</th>
                                <th>{{ __('messages.type') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.transaction_date') }}</th>
                                <th>{{ __('messages.created_by') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cashRegister->transactions->take(10) as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><code>{{ $transaction->document_number }}</code></td>
                                <td>
                                    @if($transaction->type == 'income')
                                        <span class="badge bg-success">{{ __('messages.income') }}</span>
                                    @elseif($transaction->type == 'expense')
                                        <span class="badge bg-danger">{{ __('messages.expense') }}</span>
                                    @else
                                        <span class="badge bg-info">{{ __('messages.opening_balance') }}</span>
                                    @endif
                                </td>
                                <td class="{{ $transaction->type == 'expense' ? 'text-danger' : 'text-success' }}">
                                    {{ $transaction->type == 'expense' ? '-' : '+' }}{{ number_format($transaction->amount, 2) }}
                                </td>
                                <td>{{ Str::limit($transaction->description, 30) ?? '-' }}</td>
                                <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                <td>{{ $transaction->user->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($cashRegister->transactions->count() > 10)
                    <a href="{{ route('transactions.index', ['cash_register' => $cashRegister->id]) }}" class="btn btn-outline-primary btn-sm">
                        {{ __('messages.view_all_transactions') }}
                    </a>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
