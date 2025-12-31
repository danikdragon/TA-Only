<?php

namespace App\Services;

use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CarAvailabilityService
{
    
    public function getAvailableCars(User $user, $startTime, $endTime, array $filters = []): Collection
    {
        $startTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);

        $user->load('position.comfortCategories');
        
        if (!$user->position) {
            return new Collection(); 
        }

        $allowedCategoryIds = $user->position->comfortCategories->pluck('id')->toArray();

        $query = Car::query()
            ->with(['comfortCategory', 'driver'])
            ->whereIn('comfort_category_id', $allowedCategoryIds);

        $query->whereDoesntHave('trips', function (Builder $q) use ($startTime, $endTime) {
            $q->where(function ($subQ) use ($startTime, $endTime) {
                $subQ->where('start_time', '<', $endTime)
                     ->where('end_time', '>', $startTime);
            });
        });

        if (!empty($filters['model'])) {
            $query->where('model', 'like', '%' . $filters['model'] . '%');
        }

        if (!empty($filters['comfort_category_id'])) {
            
            if (in_array($filters['comfort_category_id'], $allowedCategoryIds)) {
                $query->where('comfort_category_id', $filters['comfort_category_id']);
            } else {
                
                return new Collection(); 
            }
        }

        return $query->get();
    }
}
