@extends('layouts.admin')

@section('title', 'Account Settings')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h3><i class="fas fa-sliders-h" style="margin-right: 10px; color: var(--primary);"></i> {{ __('messages.settings') }}</h3>
        </div>
        <div class="card-body" style="padding: 2rem;">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="settings-layout">
                    <!-- Profile Image Section -->
                    <div class="profile-upload-section">
                        <div class="image-preview-container">
                            @if($user->image)
                                <img src="{{ asset('user_images/' . $user->image) }}" alt="Profile" id="profile-preview">
                            @else
                                <div class="placeholder-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <label for="image" class="upload-badge">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <input type="file" name="image" id="image" style="display: none;" onchange="previewImage(this)">
                        <p class="upload-hint">Click the camera icon to update photo</p>
                    </div>

                    <!-- Form Fields Section -->
                    <div class="form-fields-section">
                        <div class="form-group-custom">
                            <label for="name">{{ __('messages.name') }}</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="name" id="name" value="{{ $user->name }}" placeholder="Your full name" required>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label for="email">{{ __('messages.email') }}</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" id="email" value="{{ $user->email }}" placeholder="your@email.com" required>
                            </div>
                        </div>

                        <div class="password-divider">
                            <span>Security</span>
                        </div>

                        <div class="form-group-custom">
                            <label for="password">New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password" placeholder="Leave blank to keep current">
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label for="password_confirmation">Confirm New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-check-circle"></i>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your new password">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fas fa-save"></i> {{ __('messages.update') }} Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .settings-layout {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 3rem;
        align-items: start;
    }

    .profile-upload-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .image-preview-container {
        position: relative;
        width: 150px;
        height: 150px;
    }

    .image-preview-container img, .placeholder-avatar {
        width: 100%;
        height: 100%;
        border-radius: 1rem;
        object-fit: cover;
        border: 4px solid var(--bg-main);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .placeholder-avatar {
        background: var(--primary-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 3rem;
    }

    .upload-badge {
        position: absolute;
        bottom: -10px;
        right: -10px;
        background: var(--primary);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 4px solid var(--bg-card);
        transition: transform 0.2s;
    }

    .upload-badge:hover {
        transform: scale(1.1);
    }

    .upload-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: center;
    }

    .form-group-custom {
        margin-bottom: 1.5rem;
    }

    .form-group-custom label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .input-with-icon {
        position: relative;
    }

    .input-with-icon i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .input-with-icon input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 0.9375rem;
        background: var(--bg-main);
        color: var(--text-main);
        transition: all 0.2s;
        outline: none;
    }

    .input-with-icon input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-soft);
        background: var(--bg-card);
    }

    .password-divider {
        display: flex;
        align-items: center;
        margin: 2rem 0 1.5rem;
        gap: 1rem;
    }

    .password-divider::before, .password-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--border-color);
    }

    .password-divider span {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    @media (max-width: 640px) {
        .settings-layout {
            grid-template-columns: 100%;
            gap: 2rem;
        }
    }
</style>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-preview');
                if (preview) {
                    preview.src = e.target.result;
                } else {
                    // Handle case where there's no previous image (placeholder)
                    location.reload(); // Simple way to handle it for now
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
