<?php
namespace App\Services;

use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\FoodList;
use App\Models\IntakeGuideline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 *  Used to compute the nutrient profiles of ingredients, meals, and food
 *  lists. API is:
 *
 *  - getNutrientProfilesOfIngredient(int $ingredientId, ?int $userId)
 *  - getNutrientProfilesOfMeal(int $mealId, ?int $userId)
 *  - getNutrientProfilesOfFoodList(int $foodListId, ?int $userId)
 *
 *  Each function returns an array of nutrient profiles, with one nutrient
 *  profile for each intake guideline applicable to the user making the
 *  request.
 *
 *  A given nutrient profile is an array of arrays with the structure:
 *
 *  array(
 *      array(
 *          "nutrient_id" => 1,
 *          "nutrient" => "Protein",
 *          "amount" => 25.0,
 *          "precision" => 0,
 *          "unit" => "g",
 *          "pdv" => 50,
 *          "nutrient_category_id" => 0
 *      ),
 *      array(
 *          "nutrient_id" => 2,
 *          "nutrient" => "Energy",
 *          "amount" => 2000,
 *          "precision" => 0,
 *          "unit" => "kcal",
 *          "pdv" => 100,
 *          "nutrient_category_id" => 0
 *      ),
 *  );
 *
 */
class NutrientProfileService
{
    public function getNutrientProfilesOfIngredient(int $ingredientId, ?int $userId) {
        $intakeGuidelineIds = IntakeGuideline::getIdsForUser($userId);
        $nutrientProfiles = array();
        foreach ($intakeGuidelineIds as $intakeGuidelineId) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuidelineId,
              'nutrient_profile' => $this->profileIngredient($ingredientId, $intakeGuidelineId)
            ];
        }

        return $nutrientProfiles;
    }

    /**
     *  Computes a NutrientProfile for 100g of the specified Ingredient using
     *  the specified IntakeGuideline.
     */
    private function profileIngredient($ingredientId, $intakeGuidelineId) {
        if (is_null(Ingredient::find($ingredientId))) return [];
        if (is_null(IntakeGuideline::find($intakeGuidelineId))) return [];

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
        order by nutrients.seq_num;
        ";

        $result = DB::select($query, [
            'intake_guideline_id' => $intakeGuidelineId,
            'ingredient_id' => $ingredientId
        ]);

        return $result;
    }

    /**
     *  Returns an array of nutrient profiles for the given meal, with
     *  one nutrient profile for each of the user's intake guidelines.
     */
    public function getNutrientProfilesOfMeal(int $mealId, ?int $userId) {
        $intakeGuidelineIds = IntakeGuideline::getIdsForUser($userId);
        $nutrientProfiles = array();
        foreach ($intakeGuidelineIds as $intakeGuidelineId) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuidelineId,
              'nutrient_profile' => $this->profileMeal($mealId, $intakeGuidelineId)
            ];
        }

        return $nutrientProfiles;
    }

    private function profileMeal($mealId, $intakeGuidelineId) {
        if (is_null(Meal::find($mealId))) return [];
        if (is_null(IntakeGuideline::find($intakeGuidelineId))) return [];

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
        order by nutrients.seq_num;
        ";

        $result = DB::select($query, [
            'meal_id' => $mealId,
            'intake_guideline_id' => $intakeGuidelineId
        ]);

        return $result;
    }

    /**
     *  Instead of returning an array of nutrient data, returns a symbol table
     *  mapping nutrient ids to nutrient data. This is used when saving meals
     *  as ingredients.
     */
    public function getNutrientIndexedMealProfile($mealId, $intakeGuidelineId=1) {

        $symbolTable = [];
        $result = $this->profileMeal($mealId, $intakeGuidelineId);
        foreach ($result as $nutrient) $symbolTable[$nutrient->nutrient_id] = $nutrient;
        return $symbolTable;
    }

    /**
     *  Returns an array of nutrient profiles for the given food list, with one
     *  nutrient profile for each of the user's intake guidelines.
     */
    public function getNutrientProfilesOfFoodList(int $foodListId, ?int $userId) {
        $intakeGuidelineIds = IntakeGuideline::getIdsForUser($userId);
        $nutrientProfiles = array();
        foreach ($intakeGuidelineIds as $intakeGuidelineId) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuidelineId,
              'nutrient_profile' => $this->profileFoodList($foodListId, $intakeGuidelineId)
            ];
        }

        return $nutrientProfiles;
    }

    private function profileFoodList($foodListId, $intakeGuidelineId) {
        if (is_null(FoodList::find($foodListId))) return [];
        if (is_null(IntakeGuideline::find($intakeGuidelineId))) return [];

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
        order by nutrients.seq_num;
        ";

        $result = DB::select($query, [
            'food_list_id' => $foodListId,
            'intake_guideline_id' => $intakeGuidelineId
        ]);

        return $result;
    }


    /**
     *  Returns an array of nutrient profiles for all food consumed by the user
     *  over the inputted date range (including both start and end date), with
     *  one nutrient profile for each of the user's intake guidelines.
     *
     * Input: an array containing keys from_date_time_utc and to_date_time_utc.
     */
    public function getNutrientProfilesForDateRange(array $data, int $userId) {
        $fromDate = $data['from_date_time_utc'];
        $toDate = $data['to_date_time_utc'];
        $intakeGuidelineIds = IntakeGuideline::getIdsForUser($userId);
        $nutrientProfiles = array();
        foreach ($intakeGuidelineIds as $intakeGuidelineId) {
            $nutrientProfiles[] = [
              'intake_guideline_id' => $intakeGuidelineId,
              'nutrient_profile' => $this->getNutrientProfileForDateRange($fromDate, $toDate, $intakeGuidelineId, $userId)
            ];
        }

        return $nutrientProfiles;
    }

    /**
     *  Computes a NutrientProfile for 100g of the specified Ingredient using
     *  the specified IntakeGuideline.
     */
    private function getNutrientProfileForDateRange(string $fromDate, string $toDate, int $intakeGuidelineId, int $userId) {
        $query = "
        select
          nutrients.id as nutrient_id,
          nutrients.name as nutrient,
          nutrients.nutrient_category_id as nutrient_category_id,
          nutrients.precision as precision,
          round(sum((ingredient_nutrients.amount_per_100g / 100) * unioned_ingredients.mass_in_grams), 3) as amount,
          units.name as unit,
          round(sum(ingredient_nutrients.amount_per_100g * unioned_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)), 2) as pdv
        from (

          select
            ingredients.id as ingredient_id,
            food_intake_records.mass_in_grams
          from food_intake_records
          inner join ingredients
            on ingredients.id
            = food_intake_records.ingredient_id
          where
            food_intake_records.date_time_utc >= :from_date
            and
            food_intake_records.date_time_utc <= :to_date
            and
            food_intake_records.ingredient_id is not null
            and
            food_intake_records.user_id = :user_id

          union all

          select
            ingredients.id as ingredient_id,
            round(meal_ingredients.mass_in_grams * (food_intake_records.mass_in_grams / meals.mass_in_grams), 2) as mass_in_grams
          from food_intake_records
          inner join meals
            on meals.id
            = food_intake_records.meal_id
          inner join meal_ingredients
            on meal_ingredients.meal_id
            = meals.id
          inner join ingredients
            on ingredients.id
            = meal_ingredients.ingredient_id
          where
            food_intake_records.date_time_utc >= :from_date
            and
            food_intake_records.date_time_utc <= :to_date
            and
            food_intake_records.meal_id is not null
            and
            food_intake_records.user_id = :user_id

        )
        as unioned_ingredients
        inner join ingredient_nutrients
          on ingredient_nutrients.ingredient_id
          = unioned_ingredients.ingredient_id
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
        order by nutrients.seq_num;
        ";

        $result = DB::select($query, [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'intake_guideline_id' => $intakeGuidelineId,
            'user_id' => $userId,
        ]);

        return $result;
    }

    /**
     *  Used to check how many days in an inclusive date range have food intake
     *  records. Motivation: when computing average daily nutrient profile over
     *  a given date range, only count days with food intake records, because
     *  the user might not have logged food intake on every day in the date
     *  range.
     */
    public function getDaysWithFoodIntakeRecordsInDateRange(array $data, int $userId): int {
        $fromDate = $data['from_date_time_utc'];
        $toDate = $data['to_date_time_utc'];

        $query = "
        SELECT COUNT(DISTINCT DATE(date_time_utc))
        FROM food_intake_records
        WHERE DATE(date_time_utc) BETWEEN DATE(:from_date) AND DATE(:to_date)
        AND user_id = :user_id;
        ";

        $result = DB::select($query, [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'user_id' => $userId,
        ]);

        return $result[0]->count;
    }

}
