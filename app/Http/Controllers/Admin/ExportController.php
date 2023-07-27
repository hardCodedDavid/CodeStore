<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SaleExport;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Exports\PurchaseExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportUsers()
    {
        try {
            $range = $this->getDateRange(request('range'));
        }catch (\Exception $e) {
            return back()->with('error', 'Invalid range selected');
        }
        return Excel::download(new UserExport(request('type'), $range[0], $range[1]), 'users.xlsx');
    }

    public function exportProducts()
    {
        try {
            $range = $this->getDateRange(request('range'));
        }catch (\Exception $e) {
            return back()->with('error', 'Invalid range selected');
        }
        return Excel::download(new ProductExport(request('type'), $range[0], $range[1]), 'products.xlsx');
    }

    public function exportPurchases()
    {
        try {
            $range = $this->getDateRange(request('range'));
        }catch (\Exception $e) {
            return back()->with('error', 'Invalid range selected');
        }
        return Excel::download(new PurchaseExport($range[0], $range[1]), 'purchases.xlsx');
    }

    public function exportSales()
    {
        try {
            $range = $this->getDateRange(request('range'));
        }catch (\Exception $e) {
            return back()->with('error', 'Invalid range selected');
        }
        return Excel::download(new SaleExport(request('type'), $range[0], $range[1]), 'sales.xlsx');
    }

    private function getDateRange($str)
    {
        $range = explode('-', $str);
        $from = trim($range[0]);
        $to = trim($range[1]);
        return [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))];
    }
}