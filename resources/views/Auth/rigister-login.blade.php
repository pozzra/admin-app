@extends('layouts.app')

@section('title', 'Login & Register')

@section('content')
<div class="auth-container">
    <div class="container" id="container">
        <!-- Sign Up Form -->
        <div class="form-container sign-up-container">
            <form action="/register" method="POST">
                @csrf
                <h1>Create Account</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>or use your email for registration</span>
                
                @if ($errors->any())
                    <div style="color: red; font-size: 12px; margin-bottom: 10px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <input type="text" name="name" placeholder="Name" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
                <button type="submit">Sign Up</button>
                <p class="mobile-toggle-text">Already have an account? <a href="#" id="signInMobile">Sign In</a></p>
            </form>
        </div>

        <!-- Sign In Form -->
        <div class="form-container sign-in-container">
            <form action="/login" method="POST">
                @csrf
                <h1>Sign in</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>or use your account</span>

                @if ($errors->any())
                    <div style="color: red; font-size: 12px; margin-bottom: 10px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <a href="#">Forgot your password?</a>
                <button type="submit">Sign In</button>
                <p class="mobile-toggle-text">Don't have an account? <a href="#" id="signUpMobile">Sign Up</a></p>
            </form>
        </div>

        <!-- Overlay -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Styling */
    :root {
        --primary-color: #FF4B2B;
        --secondary-color: #FF416C;
        --white: #FFFFFF;
        --gray: #333;
        --light-gray: #eee;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        font-family: 'Poppins', sans-serif;
        height: 100vh;
        margin: 0;
        background: #f6f5f7;
    }

    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        padding: 20px;
        box-sizing: border-box;
    }

    h1 {
        font-weight: bold;
        margin: 0;
    }

    p {
        font-size: 14px;
        font-weight: 100;
        line-height: 20px;
        letter-spacing: 0.5px;
        margin: 20px 0 30px;
    }

    span {
        font-size: 12px;
    }

    a {
        color: #333;
        font-size: 14px;
        text-decoration: none;
        margin: 15px 0;
    }

    button {
        border-radius: 20px;
        border: 1px solid var(--secondary-color);
        background-color: var(--secondary-color);
        color: var(--white);
        font-size: 12px;
        font-weight: bold;
        padding: 12px 45px;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: transform 80ms ease-in;
        cursor: pointer;
    }

    button:active {
        transform: scale(0.95);
    }

    button:focus {
        outline: none;
    }

    button.ghost {
        background-color: transparent;
        border-color: var(--white);
    }

    form {
        background-color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 50px;
        height: 100%;
        text-align: center;
    }

    input {
        background-color: var(--light-gray);
        border: none;
        padding: 12px 15px;
        margin: 8px 0;
        width: 100%;
        border-radius: 5px;
    }

    .container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                    0 10px 10px rgba(0,0,0,0.22);
        position: relative;
        overflow: hidden;
        width: 768px;
        max-width: 100%;
        min-height: 480px;
    }

    .form-container {
        position: absolute;
        top: 0;
        height: 100%;
        transition: all 0.6s ease-in-out;
    }

    .sign-in-container {
        left: 0;
        width: 50%;
        z-index: 2;
    }

    .container.right-panel-active .sign-in-container {
        transform: translateX(100%);
    }

    .sign-up-container {
        left: 0;
        width: 50%;
        opacity: 0;
        z-index: 1;
    }

    .container.right-panel-active .sign-up-container {
        transform: translateX(100%);
        opacity: 1;
        z-index: 5;
        animation: show 0.6s;
    }

    @keyframes show {
        0%, 49.99% {
            opacity: 0;
            z-index: 1;
        }
        50%, 100% {
            opacity: 1;
            z-index: 5;
        }
    }

    .overlay-container {
        position: absolute;
        top: 0;
        left: 50%;
        width: 50%;
        height: 100%;
        overflow: hidden;
        transition: transform 0.6s ease-in-out;
        z-index: 100;
    }

    .container.right-panel-active .overlay-container {
        transform: translateX(-100%);
    }

    .overlay {
        background: #FF416C;
        background: -webkit-linear-gradient(to right, #FF4B2B, #FF416C);
        background: linear-gradient(to right, #FF4B2B, #FF416C);
        background-repeat: no-repeat;
        background-size: cover;
        background-position: 0 0;
        color: #FFFFFF;
        position: relative;
        left: -100%;
        height: 100%;
        width: 200%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
    }

    .container.right-panel-active .overlay {
        transform: translateX(50%);
    }

    .overlay-panel {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 40px;
        text-align: center;
        top: 0;
        height: 100%;
        width: 50%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
    }

    .overlay-left {
        transform: translateX(-20%);
    }

    .container.right-panel-active .overlay-left {
        transform: translateX(0);
    }

    .overlay-right {
        right: 0;
        transform: translateX(0);
    }

    .container.right-panel-active .overlay-right {
        transform: translateX(20%);
    }

    .social-container {
        margin: 20px 0;
    }

    .social-container a {
        border: 1px solid #DDDDDD;
        border-radius: 50%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin: 0 5px;
        height: 40px;
        width: 40px;
    }

    /* Mobile Responsive Styles */
    .mobile-toggle-text {
        display: none;
    }

    @media (max-width: 768px) {
        .container {
            min-height: 600px; /* Increase height for mobile */
            width: 100%;
        }

        .form-container {
            width: 100%;
            padding: 0;
        }

        .sign-in-container {
            width: 100%;
        }

        .sign-up-container {
            width: 100%;
            opacity: 0;
            z-index: 1;
        }

        .overlay-container {
            display: none;
        }

        .mobile-toggle-text {
            display: block;
            margin-top: 20px;
        }
        
        form {
            padding: 0 20px;
        }

        /* Adjust animations for mobile since we occupy same space */
        .container.right-panel-active .sign-in-container {
            transform: translateX(100%); /* Move out of view */
            opacity: 0;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(0);
            opacity: 1;
            z-index: 5;
            animation: showMobile 0.6s;
        }
        
        @keyframes showMobile {
            0% { opacity: 0; z-index: 1; }
            100% { opacity: 1; z-index: 5; }
        }
    }
</style>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    // Mobile buttons
    const signUpMobile = document.getElementById('signUpMobile');
    const signInMobile = document.getElementById('signInMobile');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });

    if(signUpMobile) {
        signUpMobile.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.add("right-panel-active");
        });
    }

    if(signInMobile) {
        signInMobile.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove("right-panel-active");
        });
    }
</script>
@endsection