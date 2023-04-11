<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\FoodList;
use App\Models\Unit;
use App\Models\RdiProfile;
use Illuminate\Support\Facades\DB;

class NutrientProfileController extends Controller
{
    /**
     *  Computes a NutrientProfile for 100g of the specified Ingredient using
     *  the specified RdiProfile.
     */
    public static function profileIngredient(Ingredient $ingredient, $rdiProfileID, $ingredient_mass_in_grams=100) {
        if (Ingredient::where('id', $ingredient->id)->doesntExist()) return [];
        if (RdiProfile::where('id', $rdiProfileID)->doesntExist()) return [];

        $query = "
        select
          nutrients.name as nutrient,
        round((:ingredient_mass_in_g * ingredient_nutrients.amount_per_100g / 100), 2) as amount,
          units.name as unit,
        round((:ingredient_mass_in_g * ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)), 1) as pdv
        from ingredient_nutrients
        inner join nutrients
          on nutrients.id
          = ingredient_nutrients.nutrient_id  
        inner join units
          on units.id
          = nutrients.unit_id
        inner join rdi_profile_nutrients
          on rdi_profile_nutrients.rdi_profile_id
          = :rdi_profile_id
          and rdi_profile_nutrients.nutrient_id
          = ingredient_nutrients.nutrient_id
        where ingredient_nutrients.ingredient_id=:ingredient_id
        order by nutrients.id;
        ";

        $result = DB::select($query, [
            'ingredient_mass_in_g' => $ingredient_mass_in_grams,
            'rdi_profile_id' => $rdiProfileID,
            'ingredient_id' => $ingredient->id
        ]);

        return $result;

    }

    public static function profileMeal(Meal $meal) {
        return;
    }

    public static function profileFoodList(FoodList $foodList) {
        return;
    }
}
