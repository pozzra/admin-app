@extends('layouts.app')

@section('title', 'Login - Admin Panel')

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <a href="{{ route('home') }}" class="brand">
                <i class="fas fa-shopping-bag"></i>
                <span>Shop<span>Hero</span></span>
            </a>
            <h1>Welcome Back</h1>
            <p>Please enter your credentials to access your account</p>
        </div>

        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="admin@example.com" required autofocus />
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label for="password">Password</label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required />
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <div class="login-footer">
            <p>Don't have an account? <a href="{{ route('register') }}">Create Account</a></p>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --bg-page: #f8fafc;
        --bg-card: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --danger: #ef4444;
    }

    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-page);
        padding: 1.5rem;
        background-image: 
            radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.05) 0px, transparent 50%);
    }

    .login-card {
        background: var(--bg-card);
        width: 100%;
        max-width: 440px;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-color);
        padding: 2.5rem;
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .brand {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .brand i {
        font-size: 2rem;
        color: var(--primary);
    }

    .brand span {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.025em;
    }

    .brand span span {
        color: var(--primary);
    }

    .login-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .login-header p {
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .alert-danger {
        background: #fef2f2;
        color: var(--danger);
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #fee2e2;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1rem;
    }

    .input-wrapper input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 0.9375rem;
        transition: all 0.2s;
        outline: none;
    }

    .input-wrapper input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .forgot-link {
        font-size: 0.75rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        background: var(--primary);
        color: #fff;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.2s;
        margin-top: 2rem;
    }

    .btn-login:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .login-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .login-footer p {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .login-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 480px) {
        .login-card {
            padding: 1.5rem;
        }
    }
</style>
@endsection
