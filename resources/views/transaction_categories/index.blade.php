@extends('layouts.app')

@section('title', __('messages.transaction_categories'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.home') }}</a></li>
<li class="breadcrumb-item"><a href="#">{{ __('messages.finance') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('messages.transaction_categories') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Add Category Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-plus me-2"></i>
                    {{ __('messages.add_category') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('transaction-categories.store') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            {{ __('messages.category_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-tag"></i>
                            </span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="{{ __('messages.enter_category_name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ __('messages.add') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Categories List Card --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ti ti-tag me-2"></i>
                    {{ __('messages.transaction_categories') }}
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">#</th>
                                <th width="50%">{{ __('messages.category_name') }}</th>
                                <th width="20%">{{ __('messages.transactions_count') }}</th>
                                <th width="20%" class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr id="category-row-{{ $category->id }}">
                                    <td class="fw-semibold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-light-primary rounded d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="ti ti-tag text-primary fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0" id="category-name-{{ $category->id }}">{{ $category->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-6 px-3 py-2">
                                            <i class="ti ti-arrows-exchange me-1"></i>
                                            {{ $category->transactions_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="editCategory({{ $category->id }}, '{{ $category->name }}')"
                                                    title="{{ __('messages.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <form action="{{ route('transaction-categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirmDelete({{ $category->transactions_count }})">
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
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-tag-off" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5 class="mt-3">{{ __('messages.no_categories') }}</h5>
                                            <p>{{ __('messages.no_categories_desc') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editCategoryModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    {{ __('messages.edit_category') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ __('messages.category_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-tag"></i>
                            </span>
                            <input type="text" id="edit_category_name" name="name" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ __('messages.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let editModal = null;

document.addEventListener('DOMContentLoaded', function() {
    editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
});

function editCategory(id, name) {
    document.getElementById('edit_category_name').value = name;
    document.getElementById('editCategoryForm').action = `/transaction-categories/${id}`;
    editModal.show();
}

function confirmDelete(transactionsCount) {
    if (transactionsCount > 0) {
        return confirm('{{ __('messages.confirm_delete_category_with_transactions') }}'.replace(':count', transactionsCount));
    }
    return confirm('{{ __('messages.confirm_delete_category') }}');
}
</script>
@endpush

<style>
.bg-light-primary {
    background-color: rgba(41, 26, 107, 0.1) !important;
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
