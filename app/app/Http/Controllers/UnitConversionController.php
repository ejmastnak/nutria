<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitConversionController extends Controller
{
    public function to_grams_for_ingredient($amount, $unit_id, $ingredient_id) {
        return 0.0;
    }

    public function to_grams($amount, $unit_id) {
        return 0.0;
    }
}
