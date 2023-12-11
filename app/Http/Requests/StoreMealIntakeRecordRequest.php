<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Meal;
use App\Rules\MealOwnedByUser;
use App\Rules\MealUnitIsConsistent;

class StoreMealIntakeRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', Meal::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'meal_intake_records' => ['required', 'array', 'min:1', config('validation.max_bulk_record_log_items')],
            'meal_intake_records.*' => ['required', 'array', 'required_array_keys:meal_id,amount,unit_id,date,time,date_time_utc', new MealUnitIsConsistent],
            'meal_intake_records.*.meal_id' => ['required', 'integer', 'exists:meals,id', new MealOwnedByUser],
            'meal_intake_records.*.amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'meal_intake_records.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'meal_intake_records.*.date' => ['required', 'string', 'date_format:Y-m-d'],
            'meal_intake_records.*.time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'meal_intake_records.*.date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'meal_intake_records' => 'meal intake records',
            'meal_intake_records.*' => 'meal intake record',
            'meal_intake_records.*.meal_id' => 'meal id',
            'meal_intake_records.*.amount' => 'amount',
            'meal_intake_records.*.unit_id' => 'unit id',
            'meal_intake_records.*.date' => 'date',
            'meal_intake_records.*.time' => 'time',
            'meal_intake_records.*.date_time_utc' => 'combined date and time',
        ];
    }

}
