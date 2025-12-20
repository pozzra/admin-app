@extends('layouts.admin')

@section('title', 'Settings')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="projects">
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.settings') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="name" style="display: block; margin-bottom: 5px;">{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="name" value="{{ $user->name }}" class="swal2-input" style="margin: 0; width: 100%;" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; margin-bottom: 5px;">{{ __('messages.email') }}</label>
                        <input type="email" name="email" id="email" value="{{ $user->email }}" class="swal2-input" style="margin: 0; width: 100%;" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="image" style="display: block; margin-bottom: 5px;">{{ __('messages.image') }}</label>
                        @if($user->image)
                            <div style="margin-bottom: 10px;">
                                <img src="{{ asset('user_images/' . $user->image) }}" alt="Profile" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="swal2-file" style="margin: 0; width: 100%;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password" style="display: block; margin-bottom: 5px;">Password (Leave blank to keep current)</label>
                        <input type="password" name="password" id="password" class="swal2-input" style="margin: 0; width: 100%;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password_confirmation" style="display: block; margin-bottom: 5px;">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="swal2-input" style="margin: 0; width: 100%;">
                    </div>

                    <button type="submit" style="background: var(--main-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem;">
                        {{ __('messages.update') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
