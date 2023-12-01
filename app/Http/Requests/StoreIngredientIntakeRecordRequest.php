<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IngredientIntakeRecord;
use App\Rules\IngredientOwnedByUser;
use App\Rules\DataAwareIngredientUnitIsConsistent;

class StoreIngredientIntakeRecordRequest extends FormRequest
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
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id', new IngredientOwnedByUser],
            'amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'unit_id' => ['required', 'integer', 'exists:units,id', new DataAwareIngredientUnitIsConsistent],
            'date' => ['required', 'string', 'date_format:Y-m-d'],
            'time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
        ];
    }
}
