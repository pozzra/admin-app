<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyReportExport;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // 1. Total Revenue
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->sum('total_amount');

        // 2. Total Orders
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // 3. Best Selling Products
        $bestSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'Completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // 4. Daily Sales Data for Chart
        $dailySales = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 5. Order Status Distribution
        $orderStatuses = Order::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return view('Report.index', compact(
            'totalRevenue',
            'totalOrders',
            'bestSellingProducts',
            'dailySales',
            'orderStatuses',
            'month',
            'year'
        ));
    }

    public function downloadExcel(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $fileName = 'monthly_report_'.$month.'_'.$year.'.xlsx';

        return Excel::download(new MonthlyReportExport($month, $year), $fileName);
    }

    public function downloadPdf(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->sum('total_amount');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        $bestSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'Completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        $pdf = Pdf::loadView('Report.pdf', compact(
            'totalRevenue',
            'totalOrders',
            'bestSellingProducts',
            'month',
            'year'
        ));

        return $pdf->download('monthly_report_'.$month.'_'.$year.'.pdf');
    }
}
