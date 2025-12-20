@extends('layouts.admin')

@section('title', 'Category Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="projects">
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.categories') }}</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form method="GET" action="{{ route('categories.index') }}" style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                        <select name="per_page" class="per-page-select">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    @if(Auth::user()->role === 'Admin')
                    <button style="cursor: pointer;" onclick="createCategory()">{{ __('messages.add_category') }} <i class="fas fa-plus"></i></button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table width="100%">
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
                                        <img src="{{ asset('category_images/' . $category->image) }}" alt="{{ $category->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; cursor: pointer;" onclick="showImage('{{ asset('category_images/' . $category->image) }}')">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td><span class="status {{ strtolower($category->status) == 'active' ? 'pink' : 'orange' }}"></span> {{ $category->status ?? 'Active' }}</td>
                                @if(Auth::user()->role === 'Admin')
                                <td>
                                    <button class="edit-btn" onclick="editCategory({{ json_encode($category) }})"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteCategory({{ $category->id }})"><i class="fas fa-trash"></i></button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    {{ $categories->links() }}
                </div>
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
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Name</label>
                        <input type="text" name="name" class="swal2-input" placeholder="Category Name" required style="margin: 5px 0; width: 100%;">
                    </div>
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Description" style="margin: 5px 0; width: 100%; height: 100px;"></textarea>
                    </div>
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Status</label>
                        <select name="status" class="swal2-input" required style="margin: 5px 0; width: 100%;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Image</label>
                        <input type="file" name="image" class="swal2-input" style="margin: 5px 0; width: 100%;">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
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
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Name</label>
                        <input type="text" name="name" value="${category.name}" class="swal2-input" placeholder="Category Name" required style="margin: 5px 0; width: 100%;">
                    </div>
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Description" style="margin: 5px 0; width: 100%; height: 100px;">${category.description || ''}</textarea>
                    </div>
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Status</label>
                        <select name="status" class="swal2-input" required style="margin: 5px 0; width: 100%;">
                            <option value="Active" ${category.status === 'Active' ? 'selected' : '' }>Active</option>
                            <option value="Inactive" ${category.status === 'Inactive' ? 'selected' : '' }>Inactive</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label>Image</label>
                        <input type="file" name="image" class="swal2-input" style="margin: 5px 0; width: 100%;">
                        ${category.image ? `<div style="margin-top: 10px;"><img src="/category_images/${category.image}" style="width: 50px; height: 50px; object-fit: cover;"></div>` : ''}
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
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
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
    
    function showImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Category Image',
            showConfirmButton: false,
            showCloseButton: true,
            background: 'transparent',
            backdrop: 'rgba(0,0,0,0.8)'
        });
    }
</script>

<style>
    .delete-btn {
        background: none;
        border: none;
        color: #ff416c;
        cursor: pointer;
        font-size: 1.1rem;
        margin-left: 10px;
    }
    .delete-btn:hover {
        color: #d33;
    }
    .edit-btn {
        background: none;
        border: none;
        color: #38bdf8;
        cursor: pointer;
        font-size: 1.1rem;
    }
    .edit-btn:hover {
        color: #0ea5e9;
    }
</style>
@endsection
