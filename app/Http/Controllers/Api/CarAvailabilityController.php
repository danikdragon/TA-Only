<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetAvailableCarsRequest;
use App\Http\Resources\CarResource;
use App\Services\CarAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CarAvailabilityController extends Controller
{
    protected $carAvailabilityService;

    public function __construct(CarAvailabilityService $carAvailabilityService)
    {
        $this->carAvailabilityService = $carAvailabilityService;
    }

    public function index(GetAvailableCarsRequest $request)
    {
        
        if ($request->has('user_id')) {
            $user = \App\Models\User::find($request->input('user_id'));
        } 
        
        else {
            $user = $request->user() ?? \App\Models\User::first();
        }

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $cars = $this->carAvailabilityService->getAvailableCars(
            $user,
            $request->validated('start_time'),
            $request->validated('end_time'),
            $request->only(['model', 'comfort_category_id'])
        );

        return CarResource::collection($cars);
    }
}