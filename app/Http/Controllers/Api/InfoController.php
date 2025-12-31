<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class InfoController extends Controller
{
    
    public function getAllCars(): JsonResponse
    {
        $cars = Car::with(['comfortCategory', 'driver'])->get();
        return response()->json($cars);
    }

    public function getAllUsers(): JsonResponse
    {
        $users = User::with(['position.comfortCategories'])->get();
        return response()->json($users);
    }
}