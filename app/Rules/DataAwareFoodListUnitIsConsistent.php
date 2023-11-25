<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\FoodList;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

/**
 *  Ensures the unit used to specify the amount of a food list is itself a food
 *  list unit. Intended for use with FoodListIntakeRecordRequests.
 */
class DataAwareFoodListUnitIsConsistent implements ValidationRule, DataAwareRule
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
        $unit = Unit::find($this->data['unit_id']);
        if (is_null($unit)) {
            $fail("The :attribute's unit was not recognized.");
            return;
        }

        $foodList = FoodList::find($this->data['food_list_id']);
        if (is_null($foodList)) {
            $fail("The :attribute's food list was not recognized.");
            return;
        }

        // Allow only the food list unit for the matching food list
        if (!is_null($unit->food_list_id)) {
            if ($unit->food_list_id === $foodList->id) return;
            else {
                $fail("The food list does not support the unit " . $unit->name . " .");
                return;
            }
        }

        // Fail all other cases
        else $fail("The food list does not support the unit " . $unit->name . " .");
    }
}
