<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DataAwareFoodListUnitIsConsistent;
use App\Rules\FoodListOwnedByUser;

class UpdateFoodListIntakeRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $foodListIntakeRecord = $this->route('food_list_intake_record');
        $user = $this->user();
        return $user && $user->can('update', $foodListIntakeRecord);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'food_list_id' => ['required', 'integer', 'exists:food_lists,id', new FoodListOwnedByUser],
            'amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'unit_id' => ['required', 'integer', 'exists:units,id', new DataAwareFoodListUnitIsConsistent],
            'date' => ['required', 'string', 'date_format:Y-m-d'],
            'time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
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
            'date_time_utc' => 'combined date and time',
        ];
    }

}
