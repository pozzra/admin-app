@extends('layouts.admin')

@section('title', 'Monthly Report')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-invoice-dollar" style="margin-right: 10px; color: var(--primary);"></i> {{ __('messages.monthly_report') }}</h3>
            <form method="GET" action="{{ route('reports.index') }}" style="display: flex; gap: 10px; align-items: center;">
                <select name="month" class="per-page-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="per-page-select">
                    @foreach(range(date('Y') - 2, date('Y')) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button type="button" class="btn-primary" onclick="window.print()" style="background: var(--text-muted);">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('reports.excel', ['month' => $month, 'year' => $year]) }}" class="btn-primary" style="background: #10b981; text-decoration: none;">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('reports.pdf', ['month' => $month, 'year' => $year]) }}" class="btn-primary" style="background: #ef4444; text-decoration: none;">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </form>
        </div>
        
        <div class="card-body">
            <!-- Stats Cards -->
            <div class="cards" style="padding: 0; margin-bottom: 2rem; margin-top: 0; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="card-single" style="border: 1px solid var(--border-color);">
                    <div>
                        <h1>${{ number_format($totalRevenue, 2) }}</h1>
                        <span>Total Revenue</span>
                    </div>
                    <div>
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="card-single" style="border: 1px solid var(--border-color);">
                    <div>
                        <h1>{{ $totalOrders }}</h1>
                        <span>Total Orders</span>
                    </div>
                    <div>
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                </div>
                <div class="card-single" style="border: 1px solid var(--border-color);">
                    <div>
                        <h1>{{ $orderStatuses->where('status', 'Completed')->first()->count ?? 0 }}</h1>
                        <span>Completed</span>
                    </div>
                    <div>
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                    </div>
                </div>
                <div class="card-single" style="border: 1px solid var(--border-color);">
                    <div>
                        <h1>{{ $orderStatuses->where('status', 'Pending')->first()->count ?? 0 }}</h1>
                        <span>Pending</span>
                    </div>
                    <div>
                        <i class="fas fa-clock" style="color: var(--warning);"></i>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- Chart Section -->
                <div class="card" style="padding: 1.5rem;">
                    <h4 style="margin-bottom: 1.5rem;">Sales Performance</h4>
                    <canvas id="salesChart" height="200"></canvas>
                </div>

                <!-- Best Selling Products -->
                <div class="card" style="padding: 1.5rem;">
                    <h4 style="margin-bottom: 1.5rem;">Best Sellers</h4>
                    <div class="table-responsive">
                        <table style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <td>Product</td>
                                    <td>Qty</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSellingProducts as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td style="font-weight: 600;">{{ $item->total_quantity }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" style="text-align: center;">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($dailySales);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(d => d.date),
            datasets: [{
                label: 'Daily Revenue ($)',
                data: salesData.map(d => d.total),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
