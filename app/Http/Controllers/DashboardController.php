<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get stock count (total number of stock items)
        $stockCount = Stock::count();
        
        // Get total quantity of all stock items
        $totalStockQuantity = Stock::sum('quantity');
        
        // Get invoices count (count of unique invoice numbers)
        $invoiceCount = Invoice::distinct('invoice_number')->count('invoice_number');
        
        // Get invoices that have stock items
        // Since your Invoice model doesn't have a direct relationship with Stock,
        // we need to determine what "invoices with stock" means in your context
        // For now, let's count all invoices since we don't have a stock relationship
        $invoicesWithStock = $invoiceCount; // Or you can modify this based on your business logic
        
        // Get monthly invoice data for chart (count by month)
        $monthlyInvoices = Invoice::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(DISTINCT invoice_number) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Prepare chart data
        $chartData = [];
        $chartCategories = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyInvoices->where('month', $i)->first();
            $chartData[] = $monthData ? $monthData->count : 0;
            $chartCategories[] = date('M', mktime(0, 0, 0, $i, 1));
        }
        
        // Calculate growth percentages (based on unique invoices)
        $currentMonthInvoices = Invoice::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->distinct('invoice_number')
            ->count('invoice_number');
            
        $previousMonthInvoices = Invoice::whereMonth('created_at', date('m', strtotime('-1 month')))
            ->whereYear('created_at', date('Y'))
            ->distinct('invoice_number')
            ->count('invoice_number');
            
        $invoiceGrowth = $previousMonthInvoices > 0 
            ? round((($currentMonthInvoices - $previousMonthInvoices) / $previousMonthInvoices) * 100, 1)
            : 0;
        
        // Stock growth
        $currentMonthStock = Stock::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();
            
        $previousMonthStock = Stock::whereMonth('created_at', date('m', strtotime('-1 month')))
            ->whereYear('created_at', date('Y'))
            ->count();
            
        $stockGrowth = $previousMonthStock > 0 
            ? round((($currentMonthStock - $previousMonthStock) / $previousMonthStock) * 100, 1)
            : 25;
        
        // Calculate total revenue (sum of final_amount)
        $totalRevenue = Invoice::sum('final_amount');
        
        // Calculate average invoice value
        $averageInvoiceValue = $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0;
        
        return view('dashboard.index', compact(
            'stockCount',
            'totalStockQuantity',
            'invoiceCount',
            'invoicesWithStock',
            'chartData',
            'chartCategories',
            'invoiceGrowth',
            'stockGrowth',
            'totalRevenue',
            'averageInvoiceValue'
        ));
    }
}