<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MealOwnedByUser;
use App\Rules\DataAwareMealUnitIsConsistent;

class UpdateMealIntakeRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $mealIntakeRecord = $this->route('meal_intake_record');
        $user = $this->user();
        return $user && $user->can('update', $mealIntakeRecord);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'meal_id' => ['required', 'integer', 'exists:meals,id', new MealOwnedByUser],
            'amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'unit_id' => ['required', 'integer', 'exists:units,id', new DataAwareMealUnitIsConsistent],
            'date' => ['required', 'string', 'date_format:Y-m-d'],
            'time' => ['nullable', 'string', 'date_format:H:i'],
        ];
    }
}
