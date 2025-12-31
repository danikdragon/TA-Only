<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\ComfortCategory;
use App\Models\Driver;
use App\Models\Position;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_available_cars_matching_their_position()
    {
        $category1 = ComfortCategory::create(['name' => 'Economy', 'level' => 1]);
        $category2 = ComfortCategory::create(['name' => 'Business', 'level' => 2]);

        $position = Position::create(['name' => 'Manager']);
        $position->comfortCategories()->attach($category1);

        $user = User::factory()->create(['position_id' => $position->id]);

        $driver1 = Driver::create(['name' => 'D1']);
        $driver2 = Driver::create(['name' => 'D2']);

        Car::create([
            'model' => 'Toyota Corolla', 
            'license_plate' => 'A123BC', 
            'comfort_category_id' => $category1->id,
            'driver_id' => $driver1->id
        ]);

        Car::create([
            'model' => 'Mercedes S-Class', 
            'license_plate' => 'B456CD', 
            'comfort_category_id' => $category2->id,
            'driver_id' => $driver2->id
        ]);

        $response = $this->actingAs($user)->getJson('/api/cars/available?start_time=2025-01-01 10:00:00&end_time=2025-01-01 12:00:00');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['model' => 'Toyota Corolla'])
            ->assertJsonMissing(['model' => 'Mercedes S-Class']);
    }

    public function test_car_is_unavailable_if_trip_overlaps()
    {
        $category = ComfortCategory::create(['name' => 'Economy', 'level' => 1]);
        $position = Position::create(['name' => 'Manager']);
        $position->comfortCategories()->attach($category);
        $user = User::factory()->create(['position_id' => $position->id]);
        $driver = Driver::create(['name' => 'D1']);
        $car = Car::create([
            'model' => 'Toyota Corolla', 
            'license_plate' => 'A123BC', 
            'comfort_category_id' => $category->id,
            'driver_id' => $driver->id
        ]);

        Trip::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_time' => '2025-01-01 10:00:00',
            'end_time' => '2025-01-01 11:00:00',
        ]);

        $this->actingAs($user)
            ->getJson('/api/cars/available?start_time=2025-01-01 10:30:00&end_time=2025-01-01 11:30:00')
            ->assertJsonCount(0, 'data');

        $this->actingAs($user)
            ->getJson('/api/cars/available?start_time=2025-01-01 09:00:00&end_time=2025-01-01 10:00:00')
            ->assertJsonCount(1, 'data');

        $this->actingAs($user)
            ->getJson('/api/cars/available?start_time=2025-01-01 11:00:00&end_time=2025-01-01 12:00:00')
            ->assertJsonCount(1, 'data');
    }
}