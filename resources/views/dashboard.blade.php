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
            <i class="fas fa-user-friends"></i>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>{{ $productCount }}</h1>
            <span>{{ __('messages.products') }}</span>
        </div>
        <div>
            <i class="fas fa-shopping-bag"></i>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>{{ $categoryCount }}</h1>
            <span>{{ __('messages.categories') }}</span>
        </div>
        <div>
            <i class="fas fa-layer-group"></i>
        </div>
    </div>
    <div class="card-single">
        <div>
            <h1>{{ $adminCount }}</h1>
            <span>Admins</span>
        </div>
        <div>
            <i class="fas fa-shield-alt"></i>
        </div>
    </div>
</div>

<!-- Dashboard Analytics -->
<div class="recent-grid" style="padding-top: 0;">
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600;">Sales Performance (Last 7 Days)</h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Real-time tracking</span>
        </div>
        <canvas id="dashboardSalesChart" height="80"></canvas>
    </div>
</div>

<!-- Recent Grid -->
<div class="recent-grid">
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.recent_products') }}</h3>
            <a href="{{ route('products.index') }}" class="btn-primary">
                {{ __('messages.view_all') }} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
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
                                    <img src="{{ asset('product_images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; cursor: pointer;" onclick="showImage('{{ asset('product_images/' . $product->image) }}')">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                <span class="badge {{ strtolower($product->status) == 'active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $product->status ?? 'Active' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dashboardSalesChart').getContext('2d');
    const salesData = @json($dailySales);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('en-US', { weekday: 'short' });
            }),
            datasets: [{
                label: 'Revenue ($)',
                data: salesData.map(d => d.total),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)' },
                    ticks: { font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
