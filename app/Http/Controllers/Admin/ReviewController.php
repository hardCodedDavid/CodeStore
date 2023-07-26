<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = Review::latest()->get();

        return $this->success(message: 'Data fetched successfully', data: compact('reviews'));
    }

    public function action(Review $review, $action): JsonResponse
    {
        if ($review['status'] == $action)
            return $this->failure('Review already ' .$action);
        $review->update(['status' => $action]);
        return $this->success('Review '.$action);
    }
}
