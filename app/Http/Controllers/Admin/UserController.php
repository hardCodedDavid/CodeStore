<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->service->all();

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function show(User $user): JsonResponse
    {
        $data = $this->service->showUser($user);

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function action(User $user, Request $request): JsonResponse
    {
        $data = $this->service->userAction($user, $request);

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }
}
