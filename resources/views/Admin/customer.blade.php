@extends('layouts.admin')

@section('title', 'Customer Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-friends" style="margin-right: 10px; color: var(--primary);"></i> {{ __('messages.customers') }} (អតិថិជន)</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <form method="GET" action="{{ route('customers.index') }}" style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                    <select name="per_page" class="per-page-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <button class="btn-primary" onclick="createCustomer()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_customer') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <td>{{ __('messages.image') }}</td>
                            <td>{{ __('messages.name') }}</td>
                            <td>{{ __('messages.email') }}</td>
                            <td>Status</td>
                            <td>{{ __('messages.action') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                @if($user->image)
                                    <img src="{{ asset('user_images/' . $user->image) }}" alt="{{ $user->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; cursor: pointer; border: 2px solid var(--border-color);" onclick="showImage('{{ asset('user_images/' . $user->image) }}')">
                                @else
                                    <div style="width: 40px; height: 40px; background: var(--primary-soft); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $user->name }}</td>
                            <td style="color: var(--text-muted);">{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ strtolower($user->status) == 'active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->status ?? 'Active' }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="edit-btn" onclick="editCustomer({{ json_encode($user) }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteCustomer({{ $user->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<form id="delete-user-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function createCustomer() {
        Swal.fire({
            title: '{{ __('messages.add_customer') }}',
            html: `
                <form id="create-user-form" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="role" value="User">
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Name</label>
                        <input type="text" name="name" class="swal2-input" placeholder="Customer Name" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Email</label>
                        <input type="email" name="email" class="swal2-input" placeholder="Email Address" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Password</label>
                        <input type="password" name="password" class="swal2-input" placeholder="Password" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Status</label>
                        <select name="status" class="swal2-input" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Profile Image</label>
                        <input type="file" name="image" class="swal2-input" style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem; border: 1px dashed var(--border-color);">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
            preConfirm: () => {
                const form = document.getElementById('create-user-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function editCustomer(user) {
        Swal.fire({
            title: '{{ __('messages.edit') }} {{ __('messages.customers') }}',
            html: `
                <form id="edit-user-form" action="/users/${user.id}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="role" value="User">
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Name</label>
                        <input type="text" name="name" value="${user.name}" class="swal2-input" placeholder="Customer Name" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Email</label>
                        <input type="email" name="email" value="${user.email}" class="swal2-input" placeholder="Email Address" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Status</label>
                        <select name="status" class="swal2-input" required style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                            <option value="Active" ${user.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Inactive" ${user.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="swal2-input" placeholder="New Password" style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem;">
                    </div>
                    <div style="margin-bottom: 1rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Profile Image</label>
                        <input type="file" name="image" class="swal2-input" style="margin: 0.5rem 0; width: 100%; border-radius: 0.5rem; font-size: 0.9375rem; border: 1px dashed var(--border-color);">
                        ${user.image ? `<div style="margin-top: 10px;"><img src="/user_images/${user.image}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 1px solid var(--border-color);"></div>` : ''}
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
            preConfirm: () => {
                const form = document.getElementById('edit-user-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function deleteCustomer(id) {
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
                let form = document.getElementById('delete-user-form');
                form.action = '/users/' + id;
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
