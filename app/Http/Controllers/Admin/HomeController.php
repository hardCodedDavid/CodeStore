<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Admin;
use App\Models\Purchase;
use App\Services\HomeService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Setting\BankRequest;
use App\Http\Requests\Setting\BusinessRequest;
use App\Http\Requests\Setting\LocationRequest;

class HomeController extends Controller
{
    public function __construct(protected HomeService $service)
    {
        //
    }

    public function index()
    {
        $data = $this->service->all();

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function updateBusiness(BusinessRequest $request): JsonResponse
    {
        $data = $this->service->business($request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }

    public function updateLocation(LocationRequest $request): JsonResponse
    {
        $data = $this->service->location($request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }

    public function updateBank(BankRequest $request): JsonResponse
    {
        $data = $this->service->bank($request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }

    public function updateProfile(): JsonResponse
    {
        // Validate request
        $this->validate(request(), [
            'name' => ['required'],
            'phone' => ['required']
        ]);

        Admin::find(auth('admin')->id())->update([
            'name' => request('name'),
            'phone' => request('phone')
        ]);

        $data = Admin::find(auth('admin')->id());

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }

    public function changePassword(): JsonResponse
    {
        // Validate request
        $this->validate(request(), [
            'old_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8']
        ]);

        if (!Hash::check(request('old_password'), auth()->user()['password'])) {
            return back()->with('error', 'Old password is incorrect');
        }

        Admin::find(auth('admin')->id())->update([
            'password' => Hash::make(request('new_password')),
        ]);

        $data = Admin::find(auth('admin')->id());

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }
    public function sendInvoiceLinkToMail($type, $code): JsonResponse
    {
        if ($type == "sales") {
            $sale = Sale::query()->where('code', $code)->first();
            $email = $sale['customer_email'];
        }
        else if ($type == "purchases") {
            $purchase = Purchase::query()->where('code', $code)->first();
            $email = $purchase['supplier']['email'];
        }
        else return $this->failure(message: 'Invoice type not Supported');
        // \App\Http\Controllers\NotificationController::sendInvoiceLinkNotification($email, route('invoice.get', ['type' => $type, 'code' => $code]));
        return $this->success(message: 'Invoice sent successfully');
    }
}
