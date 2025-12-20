@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin-content')
<!-- Cards -->
<div class="cards">
    <div class="card-single">
        <div>
            <h1>{{ $userCount }}</h1>
            <span>{{ __('messages.users') }}</span>
        </div>
        <div>
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>{{ $productCount }}</h1>
            <span>{{ __('messages.products') }}</span>
        </div>
        <div>
            <i class="fas fa-box"></i>
        </div>
    </div>
    <div class="card-single">
        <div >
            <h1>{{ $categoryCount }}</h1>
            <span>{{ __('messages.categories') }}</span>
        </div>
        <div>
            <i class="fas fa-tags"></i>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>{{ $adminCount }}</h1>
            <span>Admins</span>
        </div>
        <div>
            <i class="fas fa-user-shield"></i>
        </div>
    </div>
</div>

<!-- Recent Grid -->
<div class="recent-grid">
    <div class="projects">
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.recent_products') }}</h3>
                <a  href="{{ route('products.index') }}"><button style="cursor: pointer;">{{ __('messages.view_all') }} <i class="fas fa-arrow-right"></i></button></a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>{{ __('messages.image') }}</td>
                                <td>{{ __('messages.name') }}</td>
                                <td>{{ __('messages.category') }}</td>
                                <td>{{ __('messages.price') }}</td>
                                <td>Status</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProducts as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('product_images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px; cursor: pointer;" onclick="showImage('{{ asset('product_images/' . $product->image) }}')">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td><span class="status {{ strtolower($product->status) == 'active' ? 'pink' : 'orange' }}"></span> {{ $product->status ?? 'Active' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection