<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ToDateAfterFromDate;

class NutrientProfileForDateRangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->is_registered;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'from_date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i:s'],
            'to_date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i:s', new ToDateAfterFromDate],
            'time_zone' => ['required', 'string', 'timezone:all'],
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
            'from_date_time_utc' => 'start date',
            'to_date_time_utc' => 'end date',
        ];
    }

}
