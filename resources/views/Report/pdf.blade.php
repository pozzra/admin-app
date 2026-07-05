@extends('layouts.app')

@section('title', 'Monthly Report PDF')

@section('content')
<div style="padding: 2rem; font-family: 'Inter', sans-serif;">
    <div style="text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #4f46e5; padding-bottom: 1rem;">
        <h1 style="color: #1e293b; margin-bottom: 0.5rem;">Monthly Sales Report</h1>
        <p style="color: #64748b; font-size: 1.1rem;">{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>

    <div style="display: table; width: 100%; margin-bottom: 2rem;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 50%; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                <p style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem;">Total Revenue</p>
                <h2 style="color: #4f46e5; margin: 0;">${{ number_format($totalRevenue, 2) }}</h2>
            </div>
            <div style="display: table-cell; width: 4%;">&nbsp;</div>
            <div style="display: table-cell; width: 46%; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                <p style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem;">Total Orders</p>
                <h2 style="color: #1e293b; margin: 0;">{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>

    <h3 style="color: #1e293b; margin-bottom: 1rem; border-left: 4px solid #4f46e5; padding-left: 0.75rem;">Best Selling Products</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
        <thead>
            <tr style="background: #4f46e5; color: white;">
                <th style="padding: 0.75rem; text-align: left;">Product Name</th>
                <th style="padding: 0.75rem; text-align: right;">Quantity Sold</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bestSellingProducts as $item)
            <tr style="border-bottom: 1px solid #e2e8f0;">
                <td style="padding: 0.75rem;">{{ $item->product->name }}</td>
                <td style="padding: 0.75rem; text-align: right; font-weight: bold;">{{ $item->total_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="color: #1e293b; margin-bottom: 1rem; border-left: 4px solid #4f46e5; padding-left: 0.75rem;">Recent Completed Orders</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f1f5f9; color: #475569;">
                <th style="padding: 0.5rem; text-align: left; font-size: 0.8rem;">Order ID</th>
                <th style="padding: 0.5rem; text-align: left; font-size: 0.8rem;">Customer</th>
                <th style="padding: 0.5rem; text-align: right; font-size: 0.8rem;">Amount</th>
                <th style="padding: 0.5rem; text-align: right; font-size: 0.8rem;">Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
                $recentOrders = \App\Models\Order::with('user')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'Completed')
                    ->latest()
                    ->take(20)
                    ->get();
            @endphp
            @foreach($recentOrders as $order)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 0.5rem; font-size: 0.85rem;">#{{ $order->id }}</td>
                <td style="padding: 0.5rem; font-size: 0.85rem;">{{ $order->user->name }}</td>
                <td style="padding: 0.5rem; font-size: 0.85rem; text-align: right;">${{ number_format($order->total_amount, 2) }}</td>
                <td style="padding: 0.5rem; font-size: 0.85rem; text-align: right;">{{ $order->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 3rem; text-align: right; color: #94a3b8; font-size: 0.8rem;">
        Report generated on {{ now()->format('Y-m-d H:i') }}
    </div>
</div>
@endsection
