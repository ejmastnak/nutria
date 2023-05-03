<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\FoodList;
use App\Models\Unit;
use App\Models\RdiProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NutrientProfileController extends Controller
{

    /**
     *  Returns an array of NutrientProfiles of the inputted Ingredient; one
     *  nutrient profile for each of the user's RDI profiles.
     */
    public static function getNutrientProfileOfIngredient($ingredientID) {
        $user = Auth::user();

        $rdi_profiles = RdiProfile::where('user_id', null)
        ->orWhere('user_id', $user ? $user->id : 0)
        ->orderBy('id', 'asc')
        ->get(['id']);

        $nutrientProfiles = array();
        foreach ($rdi_profiles as $rdi_profile) {
            $nutrientProfiles[] = [
              'rdi_profile_id' => $rdi_profile->id,
              'nutrient_profile' => self::profileIngredient($ingredientID, $rdi_profile->id)
            ];
        }

        return $nutrientProfiles;
    }

    /**
     *  Computes a NutrientProfile for 100g of the specified Ingredient using
     *  the specified RdiProfile.
     */
    public static function profileIngredient($ingredientID, $rdiProfileID=1) {
        if (Ingredient::where('id', $ingredientID)->doesntExist()) return [];
        if (RdiProfile::where('id', $rdiProfileID)->doesntExist()) return [];

        $query = "
        select
          nutrients.display_name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          round(ingredient_nutrients.amount_per_100g, 2) as amount,
          units.name as unit,
          round((ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)) * 100, 0) as pdv
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
        order by nutrients.display_order_id;
        ";

        $result = DB::select($query, [
            'rdi_profile_id' => $rdiProfileID,
            'ingredient_id' => $ingredientID
        ]);

        return $result;
    }

    public static function profileMeal($mealID, $rdiProfileID=1) {
        if (Meal::where('id', $mealID)->doesntExist()) return [];
        if (RdiProfile::where('id', $rdiProfileID)->doesntExist()) return [];

        $query = "
        select
          nutrients.display_name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams), 2) as amount,
          units.name as unit,
          round(sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / nullif(rdi_profile_nutrients.rdi, 0)), 0) as pdv
        from ingredient_nutrients
        inner join meal_ingredients
          on ingredient_nutrients.ingredient_id
          = meal_ingredients.ingredient_id
          and meal_ingredients.meal_id
          = :meal_id
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
        group by nutrients.id, units.name
        order by nutrients.display_order_id;
        ";

        $result = DB::select($query, [
            'meal_id' => $mealID,
            'rdi_profile_id' => $rdiProfileID
        ]);

        return $result;
    }

    public static function profileFoodList($foodListID, $rdiProfileID=1) {
        if (FoodList::where('id', $foodListID)->doesntExist()) return [];
        if (RdiProfile::where('id', $rdiProfileID)->doesntExist()) return [];

        $query = "
        select
          nutrients.display_name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          sum(result.amount) as amount,
          units.name as unit,
          sum(result.pdv) as pdv
        from (
          select
            nutrients.id as nutrient_id,
            round(sum((ingredient_nutrients.amount_per_100g / 100) * food_list_ingredients.mass_in_grams), 2) as amount,
            round(sum(ingredient_nutrients.amount_per_100g * food_list_ingredients.mass_in_grams / nullif(rdi_profile_nutrients.rdi, 0)), 0) as pdv
          from ingredient_nutrients
          inner join food_list_ingredients
            on ingredient_nutrients.ingredient_id
            = food_list_ingredients.ingredient_id
            and food_list_ingredients.food_list_id
            = :food_list_id
          inner join nutrients
            on nutrients.id
            = ingredient_nutrients.nutrient_id
          inner join rdi_profile_nutrients
            on rdi_profile_nutrients.rdi_profile_id
            = :rdi_profile_id
            and rdi_profile_nutrients.nutrient_id
            = ingredient_nutrients.nutrient_id
          group by nutrients.id
          union all
          select
            nutrients.id as nutrient_id,
            round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 2) as amount,
            round(sum(ingredient_nutrients.amount_per_100g * (meal_ingredients.mass_in_grams / nullif(rdi_profile_nutrients.rdi, 0)) * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 0) as pdv
          from ingredient_nutrients
          inner join food_list_meals
            on food_list_meals.food_list_id
            = :food_list_id
          inner join meals
            on food_list_meals.meal_id
            = meals.id
          inner join meal_ingredients
            on ingredient_nutrients.ingredient_id
            = meal_ingredients.ingredient_id
            and meal_ingredients.meal_id
            = food_list_meals.meal_id
          inner join nutrients
            on nutrients.id
            = ingredient_nutrients.nutrient_id
          inner join rdi_profile_nutrients
            on rdi_profile_nutrients.rdi_profile_id
            = :rdi_profile_id
            and rdi_profile_nutrients.nutrient_id
            = ingredient_nutrients.nutrient_id
          group by nutrients.id
        ) result
        inner join nutrients
          on nutrients.id
          = result.nutrient_id
        inner join units
          on units.id
          = nutrients.unit_id
        group by nutrients.id, units.name
        order by nutrients.display_order_id;
        ";

        $result = DB::select($query, [
            'food_list_id' => $foodListID,
            'rdi_profile_id' => $rdiProfileID
        ]);

        return $result;
    }
}
