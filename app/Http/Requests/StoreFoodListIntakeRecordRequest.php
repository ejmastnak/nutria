<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FoodList;
use App\Rules\FoodListOwnedByUser;
use App\Rules\FoodListUnitIsConsistent;

class StoreFoodListIntakeRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', FoodList::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'food_list_intake_records' => ['required', 'array', 'min:1', config('validation.max_bulk_record_log_items')],
            'food_list_intake_records.*' => ['required', 'array', 'required_array_keys:food_list_id,amount,unit_id,date,time,date_time_utc', new FoodListUnitIsConsistent],
            'food_list_intake_records.*.food_list_id' => ['required', 'integer', 'exists:food_lists,id', new FoodListOwnedByUser],
            'food_list_intake_records.*.amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'food_list_intake_records.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'food_list_intake_records.*.date' => ['required', 'string', 'date_format:Y-m-d'],
            'food_list_intake_records.*.time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'food_list_intake_records.*.date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
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
            'food_list_intake_records' => 'food list intake records',
            'food_list_intake_records.*' => 'food list intake record',
            'food_list_intake_records.*.food_list_id' => 'food list id',
            'food_list_intake_records.*.amount' => 'amount',
            'food_list_intake_records.*.unit_id' => 'unit id',
            'food_list_intake_records.*.date' => 'date',
            'food_list_intake_records.*.time' => 'time',
            'food_list_intake_records.*.date_time_utc' => 'combined date and time',
        ];
    }

}
