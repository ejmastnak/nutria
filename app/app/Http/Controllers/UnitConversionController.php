<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitConversionController extends Controller
{

    private static $mass_unit_names = ['g', 'oz'];
    private static $volume_unit_names = ['ml', 'l', 'fl oz', 'cup', 'qt'];

    // Specifies how many grams are in one unit of key unit
    private static $how_many_grams = [
        'g' => 1.0,
        'oz' => 28.349523125
    ];

    // Specifies how many ml are in one unit of key unit
    private static $how_many_ml = [
        'ml' => 1.0,
        'l' => 1000.0,
        'fl oz' => 29.5735295625,
        'cup' => 236.5882365,
        'qt' => 946.352946
    ];

    public static function to_grams_for_ingredient($amount, $unit_id, $ingredient_id) {
        if (self::is_volume($unit_id)) {
            $density_g_per_ml = Ingredient::find($ingredient_id)->density_g_per_ml;
            if (is_null($density_g_per_ml)) return 0.0;
            $volume_in_ml = self::volume_to_ml($amount, $unit_id);
            return $volume_in_ml*$density_g_per_ml;
        } else {
            return self::mass_to_grams($amount, $unit_id);
        }
    }

    /**
     *  Returns the specified $amount, passed in units of $unit_id, to the
     *  equivalent amount in grams.
     *  Returns 0.0 if $unit_id is not a supported unit of mass.
     *  E.g. returns 1000 for input of kilogram.
     */
    public static function mass_to_grams($amount, $unit_id) {
        $unit_name = Unit::find($unit_id)->name;
        if (!array_key_exists($unit_name, self::$how_many_grams)) return 0.0;
        return $amount * self::$how_many_grams[$unit_name];
    }

    private static function is_volume($unit_id) {
        return in_array(Unit::find($unit_id)->name, self::$volume_unit_names);
    }

    /**
     *  Returns the specified $amount, passed in units of $unit_id, to the
     *  equivalent amount in milliliters.
     *  E.g. returns 1000 for input of 1 liter.
     */
    private static function volume_to_ml($amount, $unit_id) {
        $unit_name = Unit::find($unit_id)->name;
        if (!array_key_exists($unit_name, self::$how_many_ml)) return 0.0;
        return $amount * self::$how_many_ml[$unit_name];
    }

}
