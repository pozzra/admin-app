@extends('layouts.admin')

@section('title', 'Slider Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-photo-video" style="margin-right: 10px; color: var(--primary);"></i> {{ __('messages.sliders') }}</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <form method="GET" action="{{ route('sliders.index') }}" style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                    <select name="per_page" class="per-page-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                @if(Auth::user()->role === 'Admin')
                <button class="btn-primary" onclick="createSlider()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_slider') }}
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
                            <td>{{ __('messages.title') }}</td>
                            <td>{{ __('messages.description') }}</td>
                            <td>Status</td>
                            @if(Auth::user()->role === 'Admin')
                            <td>{{ __('messages.action') }}</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sliders as $slider)
                        <tr>
                            <td>
                                @if($slider->image)
                                    <img src="{{ asset('images/sliders/' . $slider->image) }}" alt="{{ $slider->title }}" style="width: 120px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid var(--border-color);" onclick="showImage('{{ asset('images/sliders/' . $slider->image) }}')">
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">No Image</span>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $slider->title }}</td>
                            <td style="color: var(--text-muted);">{{ Str::limit($slider->description, 50) ?? '-' }}</td>
                            <td>
                                <span class="badge {{ strtolower($slider->status) == 'active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $slider->status ?? 'Active' }}
                                </span>
                            </td>
                            @if(Auth::user()->role === 'Admin')
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="edit-btn" onclick="editSlider({{ json_encode($slider) }})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn" onclick="deleteSlider({{ $slider->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                {{ $sliders->links() }}
            </div>
        </div>
    </div>
</div>

<form id="delete-slider-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function createSlider() {
        Swal.fire({
            title: '{{ __('messages.create_slider') }}',
            html: `
                <form id="create-slider-form" action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Title</label>
                        <input type="text" name="title" class="swal2-input" placeholder="Slider Title" required style="width: 100%; margin: 0.5rem 0;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Description" style="width: 100%; height: 80px; margin: 0.5rem 0; padding: 0.75rem;"></textarea>
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Status</label>
                        <select name="status" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div style="text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Slider Image</label>
                        <input type="file" name="image" class="swal2-input" required style="width: 100%; margin: 0.5rem 0; border: 1px dashed var(--border-color);">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            preConfirm: () => {
                const form = document.getElementById('create-slider-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function editSlider(slider) {
        Swal.fire({
            title: '{{ __('messages.edit_slider') }}',
            html: `
                <form id="edit-slider-form" action="/sliders/${slider.id}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Title</label>
                        <input type="text" name="title" value="${slider.title}" class="swal2-input" placeholder="Slider Title" required style="width: 100%; margin: 0.5rem 0;">
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Description</label>
                        <textarea name="description" class="swal2-input" placeholder="Description" style="width: 100%; height: 80px; margin: 0.5rem 0; padding: 0.75rem;">${slider.description || ''}</textarea>
                    </div>
                    <div style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">Status</label>
                        <select name="status" class="swal2-input" required style="width: 100%; margin: 0.5rem 0;">
                            <option value="Active" ${slider.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Inactive" ${slider.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    <div style="text-align: left;">
                        <label style="font-size: 0.875rem; font-weight: 600;">{{ __('messages.change_image') }}</label>
                        <input type="file" name="image" class="swal2-input" style="width: 100%; margin: 0.5rem 0; border: 1px dashed var(--border-color);">
                        ${slider.image ? `<div style="margin-top: 10px;"><img src="/images/sliders/${slider.image}" style="width: 100px; height: 50px; object-fit: cover; border-radius: 4px;"></div>` : ''}
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            preConfirm: () => {
                const form = document.getElementById('edit-slider-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                form.submit();
            }
        });
    }

    function deleteSlider(id) {
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
                let form = document.getElementById('delete-slider-form');
                form.action = '/sliders/' + id;
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
