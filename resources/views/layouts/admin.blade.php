@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<input type="checkbox" id="sidebar-toggle" style="display: none;">
<div class="dashboard-container">
    <label for="sidebar-toggle" class="body-overlay"></label>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>{{ Auth::user()->role }}<span> Panel</span></h2>
        </div>

        <!-- Sidebar Profile Removed -->

        <ul class="nav-links">
            <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ url('/') }}"><i class="fas fa-th-large"></i> {{ __('messages.dashboard') }}</a></li>
            <li class="{{ Request::routeIs('user') ? 'active' : '' }}"><a href="{{ route('user') }}"><i class="fas fa-users"></i> {{ __('messages.users') }}</a></li>
            <li class="{{ Request::routeIs('categories.index') ? 'active' : '' }}"><a href="{{ route('categories.index') }}"><i class="fas fa-list"></i> {{ __('messages.categories') }}</a></li>
            <li class="{{ Request::routeIs('products.index') ? 'active' : '' }}"><a href="{{ route('products.index') }}"><i class="fas fa-boxes"></i> {{ __('messages.products') }}</a></li>
            <li class="{{ Request::routeIs('orders.index') ? 'active' : '' }}"><a href="{{ route('orders.index') }}"><i class="fas fa-shopping-cart"></i> {{ __('messages.orders') }}</a></li>
            <li class="{{ Request::routeIs('sliders.index') ? 'active' : '' }}"><a href="{{ route('sliders.index') }}"><i class="fas fa-images"></i> {{ __('messages.sliders') }}</a></li>
            <li class="{{ Request::routeIs('settings.index') ? 'active' : '' }}"><a href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> {{ __('messages.settings') }}</a></li>
            
            <!-- Removed Mobile Only Controls -->

            <li>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); confirmLogout();"></a>
                </form>
            </li>

        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header>
            <div class="menu-toggle">
                <label for="sidebar-toggle">
                    <span class="fas fa-bars"></span>
                </label>
            </div>
            <div  style="display: flex; align-items: center; margin-left: 10px; ">
                <!-- <i class="fas fa-search"></i>
                <input type="search" id="searchInput" placeholder="{{ __('messages.search_placeholder') }}" /> -->
            </div>
            <div class="user-wrapper" style="display: flex; align-items: center; margin-left: 10px; ">
                <div class="user-info" onclick="toggleUserDropdown(event)" style="cursor: pointer; position: relative;">
                    @if(Auth::user()->image)
                        <img src="{{ asset('user_images/' . Auth::user()->image) }}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover;">
                    @else
                        <i class="fas fa-user-circle" style="font-size: 40px; margin-right: 10px; color: var(--text-color);"></i>
                    @endif
                    <div style="display: inline-block; vertical-align: middle;">
                         <h4>{{ Auth::user()->name }}</h4> 
                         <small>{{ Auth::user()->role }}</small> 
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> {{ __('messages.settings') }}</a>
                        
                        <!-- Theme Toggle inside Dropdown -->
                        <a href="#" onclick="event.preventDefault(); toggleTheme()">
                            <i class="fas fa-moon" id="dropdown-theme-icon"></i> Theme
                        </a>

                        <!-- Language Switcher inside Dropdown -->
                        @if(App::getLocale() == 'en')
                        <a href="{{ route('lang.switch', 'kh') }}">
                            <img src="https://flagcdn.com/w40/kh.png" alt="Khmer" style="width: 20px; height: auto; border-radius: 2px;"> Khmer
                        </a>
                        @else
                        <a href="{{ route('lang.switch', 'en') }}">
                            <img src="https://flagcdn.com/w40/gb.png" alt="English" style="width: 20px; height: auto; border-radius: 2px;"> English
                        </a>
                        @endif

                        <a href="#" onclick="event.preventDefault(); confirmLogout()"><i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        @yield('admin-content')
        
    </main>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Theme Switcher Logic
    const themeIcon = document.getElementById('dropdown-theme-icon');
    const body = document.body;

    // Check LocalStorage
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        if (themeIcon) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }

    function toggleTheme() {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            if (themeIcon) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        } else {
            localStorage.setItem('theme', 'light');
            if (themeIcon) {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `
                <ul style="text-align: left;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            `
        });
    @endif

    function confirmLogout() {
        Swal.fire({
            title: '{{ __('messages.confirm_logout') }}',
            text: "{{ __('messages.confirm_logout') }}", // Or a specific logout message key if distinct
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __('messages.yes_logout') }}',
            cancelButtonText: '{{ __('messages.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        })
    }

    // Old Search Logic Removed to prevent crash (element is commented out)

    function toggleUserDropdown(event) {
        if(event) event.stopPropagation(); // Stop click from reaching window.onclick
        const dropdown = document.getElementById('userDropdown');
        if(dropdown) {
             // If we are clicking the toggle and it's already showing, this toggles it off.
            dropdown.classList.toggle('show');
        }
    }

    // Close dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('.user-dropdown') && !event.target.closest('.user-info')) {
             var dropdowns = document.getElementsByClassName("user-dropdown");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    // AJAX Auto-Search & Pagination
    document.addEventListener('DOMContentLoaded', function() {
        const debounce = (func, delay) => {
            let timeoutId;
            const debounced = (...args) => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(null, args);
                }, delay);
            };
            debounced.cancel = () => clearTimeout(timeoutId);
            return debounced;
        };

        if (typeof showLoading !== 'function') {
            window.showLoading = function() {
                const cardBody = document.querySelector('.card-body');
                if(cardBody) {
                    cardBody.style.opacity = '0.5';
                    cardBody.style.pointerEvents = 'none';
                }
            };
        }

        if (typeof hideLoading !== 'function') {
            window.hideLoading = function() {
                const cardBody = document.querySelector('.card-body');
                if(cardBody) {
                    cardBody.style.opacity = '1';
                    cardBody.style.pointerEvents = 'auto';
                }
            };
        }
        
        const updateTable = (html) => {
             const parser = new DOMParser();
             const doc = parser.parseFromString(html, 'text/html');
             const newBody = doc.querySelector('.card-body');
             const currentBody = document.querySelector('.card-body');
             if (newBody && currentBody) {
                 currentBody.innerHTML = newBody.innerHTML;
             }
        };

        const performFetch = (url) => {
            window.showLoading();
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                updateTable(html);
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                window.hideLoading();
            });
        };

        const handleSearchInput = (event) => {
            const input = event.target;
            const form = input.closest('form');
            if (!form) return;
            const url = new URL(form.action);
            const params = new URLSearchParams(new FormData(form));
            // Ensure current input value is used
            params.set(input.name, input.value);
            performFetch(url.toString() + '?' + params.toString());
        };

        const debouncedSearch = debounce(handleSearchInput, 500); // Increased delay slightly

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('search-input')) {
                debouncedSearch(e);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('search-input')) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    debouncedSearch.cancel();
                    handleSearchInput(e);
                }
            }
        });

        document.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                performFetch(link.href);
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('per-page-select')) {
                 const form = e.target.closest('form');
                 if(form) {
                    const url = new URL(form.action);
                    const params = new URLSearchParams(new FormData(form));
                    performFetch(url.toString() + '?' + params.toString());
                 }
            }
        });
    });

    function showImage(url) {
        // Use custom modal instead of SweetAlert to prevent closing existing SweetAlerts
        const modal = document.getElementById('customImageModal');
        const modalImg = document.getElementById('customImagePreview');
        if(modal && modalImg) {
            modal.style.display = "block";
            modalImg.src = url;
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('customImageModal');
        if(modal) {
            modal.style.display = "none";
        }
    }

    // Close modal on outside click
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('customImageModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
</script>

<!-- Custom Image Modal Structure -->
<div id="customImageModal" class="custom-modal">
    <span class="custom-modal-close" onclick="closeImageModal()">&times;</span>
    <img class="custom-modal-content" id="customImagePreview">
</div>

<style>
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 10000; /* Higher than SweetAlert (usually 1060) */
        padding-top: 50px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
    }
    
    .custom-modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        max-height: 90vh;
        object-fit: contain;
        animation-name: zoom;
        animation-duration: 0.6s;
    }
    
    .custom-modal-close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }
    
    .custom-modal-close:hover,
    .custom-modal-close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
    
    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }
</style>
@endsection
