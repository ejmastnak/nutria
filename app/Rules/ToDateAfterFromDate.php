<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Carbon\Carbon;

class ToDateAfterFromDate implements ValidationRule, DataAwareRule
{

    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // String-based date comparison is safe because dates must be ISO
        // format to pass validation>
        $cmp = strcmp($this->data['from_date_time_utc'], $this->data['to_date_time_utc']);
        if ($cmp > 0) $fail('The end date must be greater than or equal to the start date.');
    }
}
