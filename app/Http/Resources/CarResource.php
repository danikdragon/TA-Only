<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'license_plate' => $this->license_plate,
            'comfort_category' => [
                'id' => $this->comfortCategory->id,
                'name' => $this->comfortCategory->name,
                'level' => $this->comfortCategory->level,
            ],
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
            ],
        ];
    }
}