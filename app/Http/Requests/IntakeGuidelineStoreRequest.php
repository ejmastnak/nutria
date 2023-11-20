<?php

namespace App\Http\Requests;

use App\Models\IntakeGuideline;
use App\Models\Nutrient;
use Illuminate\Foundation\Http\FormRequest;

class IntakeGuidelineStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', IntakeGuideline::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $numNutrients = Nutrient::count();
        return [
            'name' => ['required', 'min:1', config('validation.max_name_length')],

            // Intake guideline nutrients
            'intake_guideline_nutrients' => ['required', 'array', 'min:' . $numNutrients, 'max:' . $numNutrients],
            'intake_guideline_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'intake_guideline_nutrients.*.rdi' => ['nullable', 'numeric', 'gte:0', config('validation.max_nutrient_amount')],
        ];
    }
}
