<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntakeGuidelineUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $intake_guideline = $this->route('intake_guideline');
        $user = $this->user();
        return $intake_guideline && $user && $user->can('update', $intake_guideline);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:1', config('validation.max_name_length')],

            // Intake guideline nutrients
            'intake_guideline_nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients],
            'intake_guideline_nutrients.*.id' => ['required', 'integer', 'exists:intake_guideline_nutrients,id'],
            'intake_guideline_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'intake_guideline_nutrients.*.rdi' => ['required', 'numeric', 'gte:0', config('validation.max_nutrient_amount')],
        ];
    }
}
