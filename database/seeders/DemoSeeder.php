<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\ComfortCategory;
use App\Models\Driver;
use App\Models\Position;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        
        $economy = ComfortCategory::create(['name' => 'Economy', 'level' => 1]);
        $business = ComfortCategory::create(['name' => 'Business', 'level' => 2]);

        $managerPos = Position::create(['name' => 'Manager']);
        $managerPos->comfortCategories()->attach([$economy->id, $business->id]);

        $juniorPos = Position::create(['name' => 'Junior']);
        $juniorPos->comfortCategories()->attach([$economy->id]);

        $manager = User::create([
            'name' => 'Alice Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'position_id' => $managerPos->id,
        ]);

        $junior = User::create([
            'name' => 'Bob Junior',
            'email' => 'junior@example.com',
            'password' => Hash::make('password'),
            'position_id' => $juniorPos->id,
        ]);

        Car::create([
            'model' => 'Lada Granta (Eco)',
            'license_plate' => 'E001CO',
            'comfort_category_id' => $economy->id,
            'driver_id' => Driver::create(['name' => 'Driver Eco'])->id,
        ]);

        $bmw = Car::create([
            'model' => 'BMW 5 (Biz)',
            'license_plate' => 'B002IZ',
            'comfort_category_id' => $business->id,
            'driver_id' => Driver::create(['name' => 'Driver Biz'])->id,
        ]);

        Trip::create([
            'user_id' => $manager->id,
            'car_id' => $bmw->id,
            'start_time' => now()->setTime(14, 0),
            'end_time' => now()->setTime(16, 0),
        ]);
    }
}