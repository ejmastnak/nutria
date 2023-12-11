<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BodyWeightRecord;
use App\Rules\IsMassUnit;

class StoreBodyWeightRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', BodyWeightRecord::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'body_weight_records' => ['required', 'array', 'min:1', config('validation.max_bulk_record_log_items')],
            'body_weight_records.*' => ['required', 'array', 'required_array_keys:amount,unit_id,date,time,date_time_utc'],
            'body_weight_records.*.amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'body_weight_records.*.unit_id' => ['required', 'integer', 'exists:units,id', new IsMassUnit],
            'body_weight_records.*.date' => ['required', 'string', 'date_format:Y-m-d'],
            'body_weight_records.*.time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'body_weight_records.*.date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
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
            'body_weight_records' => 'body weight records',
            'body_weight_records.*' => 'body weight record',
            'body_weight_records.*.amount' => 'amount',
            'body_weight_records.*.unit_id' => 'unit id',
            'body_weight_records.*.date' => 'date',
            'body_weight_records.*.time' => 'time',
            'body_weight_records.*.date_time_utc' => 'combined date and time',
        ];
    }

}
