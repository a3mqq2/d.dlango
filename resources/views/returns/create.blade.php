@extends('layouts.app')

@section('title', __('messages.new_return'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('returns.index') }}">{{ __('messages.sales_returns') }}</a></li>
    <li class="breadcrumb-item active">{{ __('messages.new_return') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.new_return') }}</h5>
            </div>
            <div class="card-body">
                {{-- Search Sale Section --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.search_by_invoice') }}</label>
                        <div class="input-group">
                            <input type="text" id="invoiceNumber" class="form-control"
                                   placeholder="{{ __('messages.enter_invoice_number') }}"
                                   autofocus>
                            <button type="button" id="searchSaleBtn" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i>
                                {{ __('messages.search') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Alert Messages --}}
                <div id="alertMessage" class="alert d-none"></div>

                {{-- Sale Details Section (Hidden by default) --}}
                <div id="saleDetails" class="d-none">
                    <hr>
                    <h5 class="mb-3">{{ __('messages.sale_details') }}</h5>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted">{{ __('messages.invoice_number') }}</small>
                                    <h6 id="saleInvoiceNumber" class="mb-0"></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted">{{ __('messages.customer') }}</small>
                                    <h6 id="saleCustomer" class="mb-0"></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted">{{ __('messages.sale_date') }}</small>
                                    <h6 id="saleDate" class="mb-0"></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted">{{ __('messages.total_amount') }}</small>
                                    <h6 id="saleTotal" class="mb-0"></h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Return Form --}}
                    <form id="returnForm">
                        @csrf
                        <input type="hidden" id="saleId" name="sale_id">

                        {{-- Items Table --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>{{ __('messages.product') }}</th>
                                        <th>{{ __('messages.code') }}</th>
                                        <th class="text-center">{{ __('messages.quantity') }}</th>
                                        <th class="text-center">{{ __('messages.returned_before') }}</th>
                                        <th class="text-center">{{ __('messages.returnable') }}</th>
                                        <th class="text-center">{{ __('messages.return_quantity') }}</th>
                                        <th class="text-end">{{ __('messages.unit_price') }}</th>
                                        <th class="text-end">{{ __('messages.subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8" class="text-end fw-bold">{{ __('messages.return_total') }}:</td>
                                        <td class="text-end fw-bold">
                                            <span id="returnTotal">0.00</span> {{ __('messages.currency') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Return Options --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.refund_method') }} <span class="text-danger">*</span></label>
                                <select name="refund_method" id="refundMethod" class="form-select" required>
                                    <option value="cash">{{ __('messages.cash') }}</option>
                                    <option value="credit">{{ __('messages.credit') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="cashboxContainer">
                                <label class="form-label">{{ __('messages.cashbox') }} <span class="text-danger">*</span></label>
                                <select name="cashbox_id" id="cashboxId" class="form-select">
                                    @foreach($cashboxes as $cashbox)
                                        <option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.reason') }}</label>
                                <input type="text" name="reason" class="form-control"
                                       placeholder="{{ __('messages.return_reason_placeholder') }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">{{ __('messages.notes') }}</label>
                                <textarea name="notes" class="form-control" rows="2"
                                          placeholder="{{ __('messages.enter_notes') }}"></textarea>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                                {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                                <i class="ti ti-check me-1"></i>
                                {{ __('messages.process_return') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal --}}
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="ti ti-circle-check text-success" style="font-size: 80px;"></i>
                </div>
                <h4 class="mb-3">{{ __('messages.return_completed') }}</h4>
                <p class="text-muted mb-4">
                    {{ __('messages.return_number') }}: <strong id="returnNumberDisplay"></strong>
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                        {{ __('messages.back_to_returns') }}
                    </a>
                    <a href="#" id="printReturnBtn" class="btn btn-primary" target="_blank">
                        <i class="ti ti-printer me-1"></i>
                        {{ __('messages.print_receipt') }}
                    </a>
                    <a href="{{ route('returns.create') }}" class="btn btn-success">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('messages.new_return') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchSaleBtn = document.getElementById('searchSaleBtn');
    const invoiceNumberInput = document.getElementById('invoiceNumber');
    const saleDetailsSection = document.getElementById('saleDetails');
    const alertMessage = document.getElementById('alertMessage');
    const itemsTableBody = document.getElementById('itemsTableBody');
    const returnForm = document.getElementById('returnForm');
    const submitBtn = document.getElementById('submitBtn');
    const refundMethod = document.getElementById('refundMethod');
    const cashboxContainer = document.getElementById('cashboxContainer');

    let saleItems = [];

    // Search sale on button click or Enter key
    searchSaleBtn.addEventListener('click', searchSale);
    invoiceNumberInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchSale();
        }
    });

    // Toggle cashbox visibility based on refund method
    refundMethod.addEventListener('change', function() {
        if (this.value === 'cash') {
            cashboxContainer.classList.remove('d-none');
            document.getElementById('cashboxId').required = true;
        } else {
            cashboxContainer.classList.add('d-none');
            document.getElementById('cashboxId').required = false;
        }
    });

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            cb.dispatchEvent(new Event('change'));
        });
    });

    function searchSale() {
        const invoiceNumber = invoiceNumberInput.value.trim();
        if (!invoiceNumber) {
            showAlert('warning', '{{ __("messages.enter_invoice_number") }}');
            return;
        }

        searchSaleBtn.disabled = true;
        searchSaleBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("messages.searching") }}';

        fetch('{{ route("returns.search-sale") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ invoice_number: invoiceNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySaleDetails(data.sale, data.items);
                hideAlert();
            } else {
                showAlert('danger', data.message);
                saleDetailsSection.classList.add('d-none');
            }
        })
        .catch(error => {
            showAlert('danger', '{{ __("messages.error_occurred") }}');
        })
        .finally(() => {
            searchSaleBtn.disabled = false;
            searchSaleBtn.innerHTML = '<i class="ti ti-search me-1"></i>{{ __("messages.search") }}';
        });
    }

    function displaySaleDetails(sale, items) {
        saleItems = items;

        document.getElementById('saleId').value = sale.id;
        document.getElementById('saleInvoiceNumber').textContent = '#' + sale.invoice_number;
        document.getElementById('saleCustomer').textContent = sale.customer;
        document.getElementById('saleDate').textContent = sale.sale_date;
        document.getElementById('saleTotal').textContent = parseFloat(sale.total_amount).toFixed(2) + ' {{ __("messages.currency") }}';

        // Build items table
        itemsTableBody.innerHTML = '';
        items.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="checkbox" class="form-check-input item-checkbox" data-index="${index}">
                </td>
                <td>${item.name}</td>
                <td><code>${item.code}</code></td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-center">${item.returned_quantity}</td>
                <td class="text-center">${item.returnable_quantity}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center return-qty"
                           data-index="${index}"
                           min="0" max="${item.returnable_quantity}"
                           value="0" style="width: 80px; margin: 0 auto;"
                           disabled>
                    <input type="hidden" name="items[${index}][sale_item_id]" value="${item.id}" disabled>
                    <input type="hidden" name="items[${index}][quantity]" class="hidden-qty-${index}" value="0" disabled>
                </td>
                <td class="text-end">${parseFloat(item.unit_price).toFixed(2)}</td>
                <td class="text-end item-subtotal" data-index="${index}">0.00</td>
            `;
            itemsTableBody.appendChild(row);
        });

        // Add event listeners for checkboxes and quantity inputs
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const index = this.dataset.index;
                const qtyInput = document.querySelector(`.return-qty[data-index="${index}"]`);
                const hiddenSaleItemId = document.querySelector(`input[name="items[${index}][sale_item_id]"]`);
                const hiddenQty = document.querySelector(`.hidden-qty-${index}`);

                if (this.checked) {
                    qtyInput.disabled = false;
                    qtyInput.value = saleItems[index].returnable_quantity;
                    hiddenSaleItemId.disabled = false;
                    hiddenQty.disabled = false;
                    hiddenQty.value = qtyInput.value;
                } else {
                    qtyInput.disabled = true;
                    qtyInput.value = 0;
                    hiddenSaleItemId.disabled = true;
                    hiddenQty.disabled = true;
                    hiddenQty.value = 0;
                }
                updateSubtotal(index);
                updateTotal();
            });
        });

        document.querySelectorAll('.return-qty').forEach(input => {
            input.addEventListener('input', function() {
                const index = this.dataset.index;
                const maxQty = saleItems[index].returnable_quantity;
                if (parseInt(this.value) > maxQty) {
                    this.value = maxQty;
                }
                if (parseInt(this.value) < 0) {
                    this.value = 0;
                }
                document.querySelector(`.hidden-qty-${index}`).value = this.value;
                updateSubtotal(index);
                updateTotal();
            });
        });

        saleDetailsSection.classList.remove('d-none');
    }

    function updateSubtotal(index) {
        const qty = parseInt(document.querySelector(`.return-qty[data-index="${index}"]`).value) || 0;
        const price = parseFloat(saleItems[index].unit_price);
        const subtotal = qty * price;
        document.querySelector(`.item-subtotal[data-index="${index}"]`).textContent = subtotal.toFixed(2);
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.item-subtotal').forEach(cell => {
            total += parseFloat(cell.textContent) || 0;
        });
        document.getElementById('returnTotal').textContent = total.toFixed(2);

        // Enable/disable submit button
        submitBtn.disabled = total <= 0;
    }

    function showAlert(type, message) {
        alertMessage.className = `alert alert-${type}`;
        alertMessage.textContent = message;
        alertMessage.classList.remove('d-none');
    }

    function hideAlert() {
        alertMessage.classList.add('d-none');
    }

    // Form submission
    returnForm.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("messages.processing") }}';

        const formData = new FormData(this);

        fetch('{{ route("returns.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('returnNumberDisplay').textContent = data.return_number;
                document.getElementById('printReturnBtn').href = '{{ url("returns") }}/' + data.return.id + '/receipt';

                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            } else {
                showAlert('danger', data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-check me-1"></i>{{ __("messages.process_return") }}';
            }
        })
        .catch(error => {
            showAlert('danger', '{{ __("messages.error_occurred") }}');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ti ti-check me-1"></i>{{ __("messages.process_return") }}';
        });
    });
});
</script>
@endpush
