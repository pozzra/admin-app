@extends('layouts.admin')

@section('title', 'Category Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.categories') }}</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <form method="GET" action="{{ route('categories.index') }}" style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                    <select name="per_page" class="per-page-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                @if(Auth::user()->role === 'Admin')
                <button class="btn-primary" onclick="createCategory()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_category') }}
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
                            <td>{{ __('messages.description') }}</td>
                            <td>Status</td>
                            @if(Auth::user()->role === 'Admin')
                            <td>{{ __('messages.action') }}</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset('category_images/' . $category->image) }}" alt="{{ $category->name }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid var(--border-color);" onclick="showImage('{{ asset('category_images/' . $category->image) }}')">
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">No Image</span>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $category->name }}</td>
                            <td style="color: var(--text-muted);">{{ Str::limit($category->description, 50) ?? '-' }}</td>
                            <td>
                                <span class="badge {{ strtolower($category->status) == 'active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $category->status ?? 'Active' }}
                                </span>
                            </td>
                            @if(Auth::user()->role === 'Admin')
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="edit-btn" onclick="editCategory({{ json_encode($category) }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteCategory({{ $category->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

<form id="delete-category-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function createCategory() {
        Swal.fire({
            title: '{{ __('messages.add_category') }}',
            html: `
                <form id="create-category-form" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Name</label>
                        <input type="text" name="name" class="swal2-input" placeholder="Category Name" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Description" style="margin: 0.5rem 0; width: 100%; height: 100px; border-radius: 0.5rem; font-size: 0.9375rem; padding: 0.75rem;"></textarea>
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Status</label>
                        <select name="status" class="swal2-input" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Image</label>
                        <input type="file" name="image" class="swal2-input" style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem; border: 1px dashed var(--border-color);">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
            customClass: {
                confirmButton: 'btn-primary',
                cancelButton: 'btn-secondary'
            },
            preConfirm: () => {
                const form = document.getElementById('create-category-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function editCategory(category) {
        Swal.fire({
            title: '{{ __('messages.edit') }}',
            html: `
                <form id="edit-category-form" action="/categories/${category.id}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Name</label>
                        <input type="text" name="name" value="${category.name}" class="swal2-input" placeholder="Category Name" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Description" style="margin: 0.5rem 0; width: 100%; height: 100px; border-radius: 0.5rem; font-size: 0.9375rem; padding: 0.75rem;">${category.description || ''}</textarea>
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Status</label>
                        <select name="status" class="swal2-input" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                            <option value="Active" ${category.status === 'Active' ? 'selected' : '' }>Active</option>
                            <option value="Inactive" ${category.status === 'Inactive' ? 'selected' : '' }>Inactive</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Image</label>
                        <input type="file" name="image" class="swal2-input" style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem; border: 1px dashed var(--border-color);">
                        ${category.image ? `<div style="margin-top: 10px;"><img src="/category_images/${category.image}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></div>` : ''}
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
            preConfirm: () => {
                const form = document.getElementById('edit-category-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function deleteCategory(id) {
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
                let form = document.getElementById('delete-category-form');
                form.action = '/categories/' + id;
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
