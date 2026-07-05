@extends('layouts.app')

@section('title', 'Home - Buy Products')

@section('content')
<nav class="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="brand">
            <i class="fas fa-shopping-bag"></i>
            <span>Shop<span>Hero</span></span>
        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('all.products') }}">Products</a>
            @auth
                @if(Auth::user()->role === 'Admin')
                    <a href="{{ route('dashboard') }}" class="btn-dashboard">Dashboard</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="btn-register">Register</a>
            @endauth
        </div>
    </div>
</nav>

<header class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Discover Amazing Products</h1>
            <p>Shop the latest trends and get the best deals on your favorite items. Register today and start shopping!</p>
            <div class="hero-btns">
                <a href="{{ route('all.products') }}" class="btn-primary">Shop Now</a>
                @guest
                    <a href="{{ route('register') }}" class="btn-secondary">Join Us</a>
                @endguest
            </div>
        </div>
    </div>
</header>

<section class="categories section">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="category-grid">
            @foreach($categories as $category)
                <a href="{{ route('all.products', ['category' => $category->id]) }}" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3>{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="products section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Latest Products</h2>
            <a href="{{ route('all.products') }}" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <div class="product-image">
                        @if($product->image)
                            <img src="{{ asset('product_images/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="image-placeholder">
                                <i class="fas fa-box-open"></i>
                            </div>
                        @endif
                        <span class="badge-price">${{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="product-info">
                        <h3>{{ $product->name }}</h3>
                        <p>{{ Str::limit($product->description, 60) }}</p>
                        <button class="btn-buy" onclick="buyNow({{ $product->id }})">Buy Now</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="brand">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Shop<span>Hero</span></span>
                </div>
                <p>Your one-stop shop for everything you need. Quality products at the best prices.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('all.products') }}">Products</a>
                <a href="{{ route('register') }}">Register</a>
            </div>
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <p><i class="fas fa-envelope"></i> support@shophero.com</p>
                <p><i class="fas fa-phone"></i> +855 123 456 78</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} ShopHero. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --bg-main: #f8fafc;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --white: #ffffff;
        --border: #e2e8f0;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-main); line-height: 1.6; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
    .section { padding: 4rem 0; }
    .section-title { font-size: 2rem; font-weight: 800; margin-bottom: 2.5rem; text-align: center; }

    /* Navbar */
    .navbar { background: var(--white); padding: 1.25rem 0; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; }
    .navbar .container { display: flex; justify-content: space-between; align-items: center; }
    .brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
    .brand i { font-size: 1.75rem; color: var(--primary); }
    .brand span { font-size: 1.25rem; font-weight: 800; color: var(--text-main); }
    .brand span span { color: var(--primary); }
    .nav-links { display: flex; align-items: center; gap: 2rem; }
    .nav-links a { text-decoration: none; color: var(--text-main); font-weight: 600; font-size: 0.9375rem; transition: color 0.2s; }
    .nav-links a:hover { color: var(--primary); }
    .btn-register, .btn-dashboard { background: var(--primary); color: var(--white) !important; padding: 0.5rem 1.25rem; border-radius: 0.5rem; }
    .btn-logout { background: transparent; border: 1px solid var(--border); padding: 0.5rem 1.25rem; border-radius: 0.5rem; cursor: pointer; font-weight: 600; }

    /* Hero */
    .hero { background: linear-gradient(rgba(79, 70, 229, 0.9), rgba(79, 70, 229, 0.9)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=2070'); background-size: cover; background-position: center; color: var(--white); padding: 6rem 0; text-align: center; }
    .hero-content h1 { font-size: 3.5rem; font-weight: 900; margin-bottom: 1.5rem; }
    .hero-content p { font-size: 1.25rem; max-width: 700px; margin: 0 auto 2.5rem; opacity: 0.9; }
    .hero-btns { display: flex; gap: 1rem; justify-content: center; }
    .btn-primary { background: var(--white); color: var(--primary); padding: 0.875rem 2rem; border-radius: 0.5rem; font-weight: 700; text-decoration: none; transition: transform 0.2s; }
    .btn-secondary { background: transparent; color: var(--white); border: 2px solid var(--white); padding: 0.875rem 2rem; border-radius: 0.5rem; font-weight: 700; text-decoration: none; }
    .btn-primary:hover { transform: translateY(-2px); }

    /* Category Grid */
    .category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
    .category-card { background: var(--white); padding: 2rem; border-radius: 1rem; text-align: center; text-decoration: none; color: var(--text-main); border: 1px solid var(--border); transition: all 0.2s; }
    .category-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .category-icon { width: 60px; height: 60px; background: var(--bg-main); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; color: var(--primary); font-size: 1.5rem; }

    /* Product Grid */
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
    .view-all { color: var(--primary); text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem; }
    .product-card { background: var(--white); border-radius: 1rem; border: 1px solid var(--border); overflow: hidden; transition: all 0.2s; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .product-image { height: 200px; position: relative; background: #f1f5f9; }
    .product-image img { width: 100%; height: 100%; object-fit: cover; }
    .image-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 3rem; }
    .badge-price { position: absolute; top: 1rem; right: 1rem; background: var(--white); padding: 0.25rem 0.75rem; border-radius: 2rem; font-weight: 800; color: var(--primary); font-size: 0.875rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .product-info { padding: 1.5rem; }
    .product-info h3 { font-size: 1.125rem; margin-bottom: 0.5rem; }
    .product-info p { color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem; }
    .btn-buy { width: 100%; background: var(--primary); color: var(--white); border: none; padding: 0.75rem; border-radius: 0.5rem; font-weight: 700; cursor: pointer; transition: background 0.2s; }
    .btn-buy:hover { background: var(--primary-hover); }

    /* Footer */
    .footer { background: var(--white); border-top: 1px solid var(--border); padding: 4rem 0 2rem; margin-top: 4rem; }
    .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 4rem; margin-bottom: 3rem; }
    .footer-brand p { color: var(--text-muted); margin-top: 1rem; max-width: 300px; }
    .footer h4 { margin-bottom: 1.5rem; font-size: 1.125rem; }
    .footer-links a { display: block; text-decoration: none; color: var(--text-muted); margin-bottom: 0.75rem; }
    .footer-contact p { color: var(--text-muted); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.75rem; }
    .footer-bottom { border-top: 1px solid var(--border); padding-top: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; }

    @media (max-width: 768px) {
        .footer-grid { grid-template-columns: 1fr; gap: 2.5rem; }
        .hero-content h1 { font-size: 2.5rem; }
        .nav-links { display: none; }
    }
</style>

<form id="quick-buy-form" action="{{ route('quick.buy') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="product_id" id="quick-buy-product-id">
    <input type="hidden" name="quantity" id="quick-buy-quantity" value="1">
</form>

<script>
    function buyNow(productId) {
        @auth
            Swal.fire({
                title: 'Order Product',
                html: `
                    <div style="text-align: left; margin-bottom: 1rem;">
                        <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Quantity</label>
                        <input type="number" id="swal-qty" class="swal2-input" value="1" min="1" style="width: 100%; margin: 0;">
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Confirm Purchase',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5',
                preConfirm: () => {
                    return document.getElementById('swal-qty').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('quick-buy-product-id').value = productId;
                    document.getElementById('quick-buy-quantity').value = result.value;
                    document.getElementById('quick-buy-form').submit();
                }
            });
        @else
            Swal.fire({
                title: 'Please Login',
                text: "You need to be logged in to purchase products.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Login Now',
                cancelButtonText: 'Register',
                reverseButtons: true,
                confirmButtonColor: '#4f46e5',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}";
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = "{{ route('register') }}";
                }
            });
        @endauth
    }
</script>
@endsection
