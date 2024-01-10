<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IngredientIntakeRecord;
use App\Rules\IngredientOwnedByUser;
use App\Rules\IngredientUnitIsConsistent;

class StoreManyIngredientIntakeRecordsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', IngredientIntakeRecord::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'ingredient_intake_records' => ['required', 'array', 'min:1', config('validation.max_bulk_record_log_items')],
            'ingredient_intake_records.*' => ['required', 'array', 'required_array_keys:ingredient_id,amount,unit_id,date,time,date_time_utc', new IngredientUnitIsConsistent],
            'ingredient_intake_records.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id', new IngredientOwnedByUser],
            'ingredient_intake_records.*.amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'ingredient_intake_records.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'ingredient_intake_records.*.date' => ['required', 'string', 'date_format:Y-m-d'],
            'ingredient_intake_records.*.time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'ingredient_intake_records.*.date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
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
            'ingredient_intake_records' => 'ingredient intake records',
            'ingredient_intake_records.*' => 'ingredient intake record',
            'ingredient_intake_records.*.ingredient_id' => 'ingredient id',
            'ingredient_intake_records.*.amount' => 'amount',
            'ingredient_intake_records.*.unit_id' => 'unit id',
            'ingredient_intake_records.*.date' => 'date',
            'ingredient_intake_records.*.time' => 'time',
            'ingredient_intake_records.*.date_time_utc' => 'combined date and time',
        ];
    }

}
