@extends('layouts.app')

@section('title', 'All Products - ShopHero')

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

<div class="products-page section">
    <div class="container">
        <div class="products-layout">
            <!-- Sidebar Filters -->
            <aside class="sidebar">
                <div class="filter-card">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <li>
                            <a href="{{ route('all.products') }}" class="{{ !request('category') ? 'active' : '' }}">
                                All Categories
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('all.products', ['category' => $category->id]) }}" class="{{ request('category') == $category->id ? 'active' : '' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <div class="search-bar">
                    <form action="{{ route('all.products') }}" method="GET" class="search-form">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                @if($products->count() > 0)
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
                    <div class="pagination">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="no-results">
                        <i class="fas fa-search-minus"></i>
                        <p>No products found matching your criteria.</p>
                        <a href="{{ route('all.products') }}" class="btn-clear">Clear All Filters</a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

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

    .section { padding: 4rem 0; }
    .products-layout { display: grid; grid-template-columns: 280px 1fr; gap: 3rem; margin-top: 2rem; }
    .sidebar .filter-card { background: var(--white); padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--border); position: sticky; top: 100px; }
    .sidebar h3 { font-size: 1.125rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }
    .category-list { list-style: none; }
    .category-list li { margin-bottom: 0.5rem; }
    .category-list a { text-decoration: none; color: var(--text-main); font-size: 0.9375rem; display: block; padding: 0.5rem 0.75rem; border-radius: 0.5rem; transition: all 0.2s; }
    .category-list a:hover, .category-list a.active { background: var(--primary); color: var(--white); }

    .search-bar { margin-bottom: 2rem; }
    .search-form { display: flex; gap: 0.5rem; }
    .search-form input { flex: 1; padding: 0.75rem 1.25rem; border-radius: 0.5rem; border: 1px solid var(--border); outline: none; font-size: 0.9375rem; }
    .search-form button { background: var(--primary); color: var(--white); border: none; padding: 0 1.25rem; border-radius: 0.5rem; cursor: pointer; }

    /* Product Grid */
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

    .pagination { margin-top: 3rem; display: flex; justify-content: center; }
    .pagination .page-link { padding: 0.5rem 1rem; border: 1px solid var(--border); background: var(--white); color: var(--text-main); text-decoration: none; border-radius: 0.375rem; margin: 0 2px; }
    .pagination .page-item.active .page-link { background: var(--primary); color: var(--white); border-color: var(--primary); }
    .pagination svg { width: 20px; height: 20px; }
    .pagination nav div:first-child { display: none; } /* Hide "Showing X to Y" on small screens if needed */

    .no-results { text-align: center; padding: 4rem 0; color: var(--text-muted); }
    .no-results i { font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.2; }
    .btn-clear { display: inline-block; margin-top: 1rem; color: var(--primary); font-weight: 700; text-decoration: none; }

    @media (max-width: 992px) {
        .products-layout { grid-template-columns: 1fr; }
        .sidebar { display: none; }
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
