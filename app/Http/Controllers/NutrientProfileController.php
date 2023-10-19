<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\FoodList;
use App\Models\Unit;
use App\Models\IntakeGuideline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NutrientProfileController extends Controller
{

    /**
     *  Returns an array of NutrientProfiles of the inputted Ingredient; one
     *  nutrient profile for each of the user's intake guidelines.
     */
    public static function getNutrientProfilesOfIngredient($ingredientID) {
        $user = Auth::user();

        $intakeGuidelines = IntakeGuideline::where('user_id', null)
        ->orWhere('user_id', $user ? $user->id : 0)
        ->orderBy('id', 'asc')
        ->get(['id']);

        $nutrientProfiles = array();
        foreach ($intakeGuidelines as $intakeGuideline) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuideline->id,
              'nutrient_profile' => self::profileIngredient($ingredientID, $intakeGuideline->id)
            ];
        }

        return $nutrientProfiles;
    }

    /**
     *  Computes a NutrientProfile for 100g of the specified Ingredient using
     *  the specified IntakeGuideline.
     */
    public static function profileIngredient($ingredientID, $intakeGuidelineID=1) {
        if (Ingredient::where('id', $ingredientID)->doesntExist()) return [];
        if (IntakeGuideline::where('id', $intakeGuidelineID)->doesntExist()) return [];

        $query = "
        select
          nutrients.id as nutrient_id,
          nutrients.display_name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          nutrients.precision as precision,
          round(ingredient_nutrients.amount_per_100g, 3) as amount,
          units.name as unit,
          round((ingredient_nutrients.amount_per_100g / nullif(intake_guideline_nutrients.rdi, 0)) * 100, 2) as pdv
        from ingredient_nutrients
        inner join nutrients
          on nutrients.id
          = ingredient_nutrients.nutrient_id
        inner join units
          on units.id
          = nutrients.unit_id
        inner join intake_guideline_nutrients
          on intake_guideline_nutrients.intake_guideline_id
          = :intake_guideline_id
          and intake_guideline_nutrients.nutrient_id
          = ingredient_nutrients.nutrient_id
        where ingredient_nutrients.ingredient_id=:ingredient_id
        order by nutrients.display_order_id;
        ";

        $result = DB::select($query, [
            'intake_guideline_id' => $intakeGuidelineID,
            'ingredient_id' => $ingredientID
        ]);

        return $result;
    }

    /**
     *  Returns an array of NutrientProfiles of the inputted Meal; one
     *  nutrient profile for each of the user's intake guidelines.
     */
    public static function getNutrientProfilesOfMeal($mealID) {
        $user = Auth::user();

        $intakeGuidelines = IntakeGuideline::where('user_id', null)
        ->orWhere('user_id', $user ? $user->id : 0)
        ->orderBy('id', 'asc')
        ->get(['id']);

        $nutrientProfiles = array();
        foreach ($intakeGuidelines as $intakeGuideline) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuideline->id,
              'nutrient_profile' => self::profileMeal($mealID, $intakeGuideline->id)
            ];
        }

        return $nutrientProfiles;
    }

    public static function profileMeal($mealID, $intakeGuidelineID=1, $returnAsSymbolTable=false) {
        if (Meal::where('id', $mealID)->doesntExist()) return [];
        if (IntakeGuideline::where('id', $intakeGuidelineID)->doesntExist()) return [];

        $query = "
        select
          nutrients.id as nutrient_id,
          nutrients.display_name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          nutrients.precision as precision,
          round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams), 3) as amount,
          units.name as unit,
          round(sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)), 2) as pdv
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
        inner join intake_guideline_nutrients
          on intake_guideline_nutrients.intake_guideline_id
          = :intake_guideline_id
          and intake_guideline_nutrients.nutrient_id
          = ingredient_nutrients.nutrient_id
        group by nutrients.id, units.name
        order by nutrients.display_order_id;
        ";

        $result = DB::select($query, [
            'meal_id' => $mealID,
            'intake_guideline_id' => $intakeGuidelineID
        ]);

        if ($returnAsSymbolTable) {
            // Create a symbol table mapping nutrient ids to results
            $symbolTable = [];
            foreach ($result as $nutrient) {
                $symbolTable[$nutrient->nutrient_id] = $nutrient;
            }
            return $symbolTable;
        } else {
            return $result;  // just return an array of results
        }
    }

    /**
     *  Returns an array of NutrientProfiles of the inputted Food List; one
     *  nutrient profile for each of the user's intake guidelines.
     */
    public static function getNutrientProfilesOfFoodList($mealID) {
        $user = Auth::user();

        $intakeGuidelines = IntakeGuideline::where('user_id', null)
        ->orWhere('user_id', $user ? $user->id : 0)
        ->orderBy('id', 'asc')
        ->get(['id']);

        $nutrientProfiles = array();
        foreach ($intakeGuidelines as $intakeGuideline) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuideline->id,
              'nutrient_profile' => self::profileFoodList($mealID, $intakeGuideline->id)
            ];
        }

        return $nutrientProfiles;
    }

    public static function profileFoodList($foodListID, $intakeGuidelineID=1) {
        if (FoodList::where('id', $foodListID)->doesntExist()) return [];
        if (IntakeGuideline::where('id', $intakeGuidelineID)->doesntExist()) return [];

        $query = "
        select
          nutrients.id as nutrient_id,
          nutrients.display_name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          nutrients.precision as precision,
          sum(result.amount) as amount,
          units.name as unit,
          sum(result.pdv) as pdv
        from (
          select
            nutrients.id as nutrient_id,
            round(sum((ingredient_nutrients.amount_per_100g / 100) * food_list_ingredients.mass_in_grams), 3) as amount,
            round(sum(ingredient_nutrients.amount_per_100g * food_list_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)), 2) as pdv
          from ingredient_nutrients
          inner join food_list_ingredients
            on ingredient_nutrients.ingredient_id
            = food_list_ingredients.ingredient_id
            and food_list_ingredients.food_list_id
            = :food_list_id
          inner join nutrients
            on nutrients.id
            = ingredient_nutrients.nutrient_id
          inner join intake_guideline_nutrients
            on intake_guideline_nutrients.intake_guideline_id
            = :intake_guideline_id
            and intake_guideline_nutrients.nutrient_id
            = ingredient_nutrients.nutrient_id
          group by nutrients.id
          union all
          select
            nutrients.id as nutrient_id,
            round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 3) as amount,
            round(sum(ingredient_nutrients.amount_per_100g * (meal_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)) * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 2) as pdv
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
          inner join intake_guideline_nutrients
            on intake_guideline_nutrients.intake_guideline_id
            = :intake_guideline_id
            and intake_guideline_nutrients.nutrient_id
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
            'intake_guideline_id' => $intakeGuidelineID
        ]);

        return $result;
    }
}
