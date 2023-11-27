<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IngredientOwnedByUser;
use App\Rules\DataAwareIngredientUnitIsConsistent;

class UpdateIngredientIntakeRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ingredientIntakeRecord = $this->route('ingredient_intake_record');
        $user = $this->user();
        return $user && $user->can('update', $ingredientIntakeRecord);
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
            'time' => ['nullable', 'string', 'date_format:H:i:s,H:i'],
        ];
    }
}
