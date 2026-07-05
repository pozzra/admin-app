@extends('layouts.admin')

@section('title', 'Product Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-shopping-bag" style="margin-right: 10px; color: var(--primary);"></i> {{ __('messages.products') }}</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <form method="GET" action="{{ route('products.index') }}" style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                    <select name="per_page" class="per-page-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                @if(Auth::user()->role === 'Admin')
                <button class="btn-primary" onclick="createProduct()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_product') }}
                </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <td>{{ __('messages.image') }}</td>
                            <td>{{ __('messages.name') }}</td>
                            <td>{{ __('messages.category') }}</td>
                            <td>{{ __('messages.price') }}</td>
                            <td>{{ __('messages.stock') }}</td>
                            <td>Status</td>
                            @if(Auth::user()->role === 'Admin')
                            <td>{{ __('messages.action') }}</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('product_images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid var(--border-color);" onclick="showImage('{{ asset('product_images/' . $product->image) }}')">
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">No Image</span>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                            <td style="font-weight: 600;">${{ number_format($product->price, 2) }}</td>
                            <td>
                                <span style="color: {{ $product->stock <= 5 ? 'var(--danger)' : 'inherit' }}; font-weight: {{ $product->stock <= 5 ? 'bold' : 'normal' }};">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ strtolower($product->status) == 'active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $product->status ?? 'Active' }}
                                </span>
                            </td>
                            @if(Auth::user()->role === 'Admin')
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="edit-btn" onclick="editProduct({{ json_encode($product) }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteProduct({{ $product->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<form id="delete-product-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function createProduct() {
        Swal.fire({
            title: '{{ __('messages.add_product') }}',
            html: `
                <form id="create-product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Name</label>
                            <input type="text" name="name" class="swal2-input" placeholder="Product Name" required style="width: 100%; margin: 0.5rem 0;">
                        </div>
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Category</label>
                            <select name="category_id" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="swal2-input" placeholder="0.00" required style="width: 100%; margin: 0.5rem 0;">
                        </div>
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Stock</label>
                            <input type="number" name="stock" class="swal2-input" placeholder="0" required style="width: 100%; margin: 0.5rem 0;">
                        </div>
                    </div>
                    <div style="text-align: left; margin-bottom: 1rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Status</label>
                        <select name="status" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div style="text-align: left; margin-bottom: 1rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Product Description" style="width: 100%; height: 80px; margin: 0.5rem 0; padding: 0.75rem;"></textarea>
                    </div>
                    <div style="text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Image</label>
                        <input type="file" name="image" class="swal2-input" style="width: 100%; margin: 0.5rem 0; border: 1px dashed var(--border-color);">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            width: '600px',
            preConfirm: () => {
                const form = document.getElementById('create-product-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function editProduct(product) {
        Swal.fire({
            title: '{{ __('messages.edit') }} Product',
            html: `
                <form id="edit-product-form" action="/products/${product.id}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Name</label>
                            <input type="text" name="name" value="${product.name}" class="swal2-input" placeholder="Product Name" required style="width: 100%; margin: 0.5rem 0;">
                        </div>
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Category</label>
                            <select name="category_id" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" ${product.category_id == {{ $category->id }} ? 'selected' : ''}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Price ($)</label>
                            <input type="number" step="0.01" name="price" value="${product.price}" class="swal2-input" placeholder="0.00" required style="width: 100%; margin: 0.5rem 0;">
                        </div>
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 600;">Stock</label>
                            <input type="number" name="stock" value="${product.stock}" class="swal2-input" placeholder="0" required style="width: 100%; margin: 0.5rem 0;">
                        </div>
                    </div>
                    <div style="text-align: left; margin-bottom: 1rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Status</label>
                        <select name="status" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            <option value="Active" ${product.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Inactive" ${product.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    <div style="text-align: left; margin-bottom: 1rem;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Product Description" style="width: 100%; height: 80px; margin: 0.5rem 0; padding: 0.75rem;">${product.description || ''}</textarea>
                    </div>
                    <div style="text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Image</label>
                        <input type="file" name="image" class="swal2-input" style="width: 100%; margin: 0.5rem 0; border: 1px dashed var(--border-color);">
                        ${product.image ? `<div style="margin-top: 10px;"><img src="/product_images/${product.image}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></div>` : ''}
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            width: '600px',
            preConfirm: () => {
                const form = document.getElementById('edit-product-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function deleteProduct(id) {
        Swal.fire({
            title: '{{ __('messages.confirm_delete') }}',
            text: "{{ __('messages.wont_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: '{{ __('messages.yes_delete') }}',
            cancelButtonText: '{{ __('messages.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('delete-product-form');
                form.action = '/products/' + id;
                form.submit();
            }
        })
    }
</script>

<style>
    .delete-btn {
        background: var(--danger-soft);
        border: none;
        color: var(--danger);
        cursor: pointer;
        font-size: 0.875rem;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .delete-btn:hover {
        background: var(--danger);
        color: #fff;
    }
    .edit-btn {
        background: var(--primary-soft);
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 0.875rem;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .edit-btn:hover {
        background: var(--primary);
        color: #fff;
    }
    
    .search-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        outline: none;
        background: var(--bg-main);
        color: var(--text-main);
    }
    
    .per-page-select {
        padding: 0.5rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        font-size: 0.875rem;
        outline: none;
        background: var(--bg-main);
        color: var(--text-main);
        cursor: pointer;
    }
</style>
@endsection
