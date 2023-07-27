<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\Supplier;
use Illuminate\Support\Str;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\ProductController;

class HomeService 
{
    public function all() : array 
    {
        $sales = Sale::with('items')->get();
        $purchases = Purchase::with('items')->get();
        $totalSales = 0;
        $totalProfit = 0;
        $totalPurchases = 0;
        $yearPurchases = [];
        $yearSales = [];
        $monthPurchases = [];
        $monthSales = [];
        $yearProfit = [];

        // Compute total sales and profit
        foreach ($sales as $sale) {
            $totalSales += $sale->getSubTotal();
            $totalProfit += $sale->getProfit();
        }

        // Compute total sales
        foreach ($purchases as $purchase) {
            $totalPurchases += $purchase->getSubTotal();
        }

        // Generate current month data
        for ($day = 1; $day <= date('t'); $day++){
            $monthPurchases[] = round(PurchaseItem::query()
                ->whereHas('purchase', function ($q) use($day) {
                    $q->whereDate('date', date('Y-m') . '-' . $day);
                })
                ->select(DB::raw('sum(price * quantity) as total'))->first()['total']);
            $monthSales[] = round(SaleItem::query()
                ->whereHas('sale', function ($q) use($day) {
                    $q->whereDate('date', date('Y-m') . '-' . $day);
                })
                ->select(DB::raw('sum(price * quantity) as total'))->first()['total']);
        }

        //  Generate current year data
        for ($month = 1; $month <= 12; $month++){
            $yearPurchases[] = round(PurchaseItem::query()
                ->whereHas('purchase', function ($q) use($month) {
                    $q->whereYear('date', date('Y'))
                        ->whereMonth('date', $month);
                })
                ->select(DB::raw('sum(price * quantity) as total'))->first()['total']);
            $yearSales[] = round(SaleItem::query()
                ->whereHas('sale', function ($q) use($month) {
                    $q->whereYear('date', date('Y'))
                        ->whereMonth('date', $month);
                })
                ->select(DB::raw('sum(price * quantity) as total'))->first()['total']);
            $yearProfit[] = round(SaleItem::query()
                ->whereHas('sale', function ($q) use($month) {
                    $q->whereYear('date', date('Y'))
                        ->whereMonth('date', $month);
                })
                ->sum('profit'));
        }

        return  [
            'products' => Product::query()->count(),
            'listed_products' => Product::query()->where('is_listed', 1)->count(),
            'admins' => Admin::query()->where('email', '!=', 'softwebdigital@gmail.com')->count(),
            // 'orders' => Order::query()->count(),
            'sales' => $totalSales,
            'purchases' => $totalPurchases,
            'profit' => $totalProfit,
            'users' => User::query()->count(),
            'suppliers' => Supplier::query()->count(),
            'sales_count' => Sale::query()->count(),
            'purchases_count' => Purchase::query()->count(),
            'top_selling' => Product::with('saleItems')->get()->sortBy(function ($q) { $q->saleItems->count(); })->take(5),
            'chart_data' => [
                'year_transactions' => [
                    'sales' => $yearSales,
                    'purchases' => $yearPurchases,
                    'profit' => $yearProfit
                ],
                'month_transactions' => [
                    'sales' => $monthSales,
                    'purchases' => $monthPurchases
                ]
            ]
        ];
    }

    public function business(array $data) : object
    {
        $settings = Setting::query()->first();
        if (request('logo')) {
            $path1 = ProductController::saveFileAndReturnPath(request('logo'), Str::random(8));
            if ($settings && $settings['logo']) unlink($settings['logo']);
        }
        if (request('icon')) {
            $icon = ProductController::resizeImageAndReturnPath(request('icon'), Str::random(8), 100, 100, 'img');
            if ($settings && $settings['icon']) unlink($settings['icon']);
        }
        if (request('dashboard_logo')) {
            $path2 = ProductController::saveFileAndReturnPath(request('dashboard_logo'), Str::random(8));
            if ($settings && $settings['dashboard_logo']) unlink($settings['dashboard_logo']);
        }
        if (request('store_logo')) {
            $path3 = ProductController::saveFileAndReturnPath(request('store_logo'), Str::random(8));
            if ($settings && $settings['store_logo']) unlink($settings['store_logo']);
        }
        if (request('email_logo')) {
            $path4 = ProductController::saveFileAndReturnPath(request('email_logo'), Str::random(8));
            if ($settings && $settings['email_logo']) unlink($settings['email_logo']);
        }
        $data = request()->only('name', 'email', 'phone_1', 'phone_2', 'address', 'motto', 'facebook', 'instagram', 'twitter', 'youtube', 'linkedin');
        if (isset($path1)) $data['logo'] = $path1;
        if (isset($icon)) $data['icon'] = $icon;
        if (isset($path2)) $data['dashboard_logo'] = $path2;
        if (isset($path3)) $data['store_logo'] = $path3;
        if (isset($path4)) $data['email_logo'] = $path4;
        if ($settings) $settings->update($data);
        else Setting::query()->create($data);

        return $settings;
    }
    public function location(array $data) : object
    {
        $settings = Setting::query()->first();
        $data = [];
        foreach (request('locations') as $location) $data[] = array_values($location)[0];
        $settings->update(['pickup_locations' => json_encode($data)]);

        return $settings;
    }

    public function bank(array $data) : object
    {
        $data = request()->only('account_name', 'account_number', 'bank_name');
        if ($settings = Setting::query()->first()) $settings->update($data);
        else Setting::query()->create($data);

        return $settings;
    }
}