<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAvailableCarsRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'model' => ['nullable', 'string'],
            'comfort_category_id' => ['nullable', 'exists:comfort_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'], 
        ];
    }
}