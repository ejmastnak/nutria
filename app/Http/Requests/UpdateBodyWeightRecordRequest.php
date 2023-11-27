<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsMassUnit;

class UpdateBodyWeightRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $bodyWeightRecord = $this->route('body_weight_record');
        $user = $this->user();
        return $user && $user->can('update', $bodyWeightRecord);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'unit_id' => ['required', 'integer', 'exists:units,id', new IsMassUnit],
            'date' => ['required', 'string', 'date_format:Y-m-d'],
            'time' => ['nullable', 'string', 'date_format:H:i:s,H:i'],
        ];
    }
}
