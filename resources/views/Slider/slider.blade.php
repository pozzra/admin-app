@extends('layouts.admin')
@section('title', __('messages.slider_management'))
@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
<div class="projects">
    <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.slider_management') }}</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form method="GET" action="{{ route('sliders.index') }}" style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                        <select name="per_page" class="per-page-select">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    @if(strtolower(Auth::user()->role) === 'admin')
                    <button style="cursor: pointer;" onclick="createSlider()">{{ __('messages.add_slider') }} <i class="fas fa-plus"></i></button>
                    @endif
                </div>
            </div>
        <div class="card-body">
            <div class="table-responsive">
                <table width="100%">
                    <thead>
                        <tr>
                            <td>{{ __('messages.image') }}</td>
                            <td>{{ __('messages.title') }}</td>
                            <td>{{ __('messages.description') }}</td>
                            <td>{{ __('messages.status') }}</td>
                            @if(strtolower(Auth::user()->role) === 'admin')
                            <td>{{ __('messages.action') }}</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sliders as $slider)
                        <tr>
                            <td>
                                <img src="{{ asset($slider->image) }}" alt="{{ $slider->title }}" style="width: 80px; height: 50px; object-fit: cover; border-radius: 5px; cursor: pointer;" onclick="showImage('{{ asset($slider->image) }}')">
                            </td>
                            <td>{{ $slider->title }}</td>
                            <td>{{ Str::limit($slider->description, 50) }}</td>
                            <td>
                                @if($slider->status)
                                    <span class="status purple"></span> Active
                                @else
                                    <span class="status orange"></span> Inactive
                                @endif
                            </td>
                            @if(strtolower(Auth::user()->role) === 'admin')
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <button class="action-btn edit-btn" onclick='editSlider(@json($slider))'  >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('sliders.destroy', $slider->id) }}" method="POST" id="delete-slider-form-{{ $slider->id }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"  class="delete-btn" onclick="deleteSlider({{ $slider->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">
                {{ $sliders->links() }}
            </div>
        </div>
    </div>
</div>
</div>
<script>
    function createSlider() {
        Swal.fire({
            title: '{{ __('messages.create_slider') }}',
            html: `
                <form id="create-slider-form" action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label style="display:block; text-align:left; margin-top:5px;">{{ __('messages.title') }}:</label>
                    <input type="text" name="title" class="swal2-input" placeholder="{{ __('messages.title') }}" required>
                    <label style="display:block; text-align:left; margin-top:5px;">{{ __('messages.description') }}:</label>
                    <textarea name="description" class="swal2-textarea" placeholder="{{ __('messages.description') }}"></textarea>
                    <label style="display:block; text-align:left; margin-top:5px;">{{ __('messages.image') }}:</label>
                    <input type="file" name="image" class="swal2-file" accept="image/*" required>
                    <div style="margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <input type="checkbox" name="status" id="status-create" checked>
                        <label for="status-create">{{ __('messages.active') }}</label>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.create') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
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
        let isChecked = slider.status ? 'checked' : '';
        Swal.fire({
            title: '{{ __('messages.edit_slider') }}',
            html: `
                <form id="edit-slider-form" action="/sliders/${slider.id}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <label style="display:block; text-align:left; margin-top:5px;">{{ __('messages.title') }}:</label>
                    <input type="text" name="title" class="swal2-input" placeholder="{{ __('messages.title') }}" value="${slider.title}" required>
                    
                    <label style="display:block; text-align:left; margin-top:5px;">{{ __('messages.description') }}:</label>
                    <textarea name="description" class="swal2-textarea" placeholder="{{ __('messages.description') }}">${slider.description || ''}</textarea>
                    
                    <label style="display:block; text-align:left; margin-top:5px;">{{ __('messages.change_image') }}:</label>
                    <input type="file" name="image" class="swal2-file" accept="image/*">
                    
                    <div style="margin-top: 10px; display: flex; align-items: center; justify-content: flex-start; gap: 10px;">
                        <input type="checkbox" name="status" id="status-edit" ${isChecked}>
                        <label for="status-edit">{{ __('messages.active') }}</label>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __('messages.update') }}',
            cancelButtonText: '{{ __('messages.cancel') }}',
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
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __('messages.yes_delete') }}',
            cancelButtonText: '{{ __('messages.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('delete-slider-form-' + id);
                form.submit();
            }
        })
    }

    function showImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Slider Image',
            showCloseButton: true,
            showConfirmButton: false,
            background: 'transparent',
            backdrop: 'rgba(0,0,0,0.8)',
            customClass: {
                popup: 'image-preview-popup'
            }
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
