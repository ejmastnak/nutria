<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = [
        'fdc_id',
        'name',
        'ingredient_category_id',
        'ingredient_nutrient_amount',
        'ingredient_nutrient_amount_unit_id',
        'density_mass_unit_id',
        'density_mass_amount',
        'density_volume_unit_id',
        'density_volume_amount',
        'density_g_ml',
        'meal_id',
        'user_id',
    ];

    public function withCategoryUnitsAndMeal() {
        $this->load(
            'ingredient_category:id,name',
            'custom_units:id,name,seq_num,ingredient_id,custom_unit_amount,custom_mass_amount,custom_mass_unit_id,custom_grams',
            'meal:id,name',
        );
        return $this->only([
            'id',
            'name',
            'ingredient_category_id',
            "density_mass_unit_id",
            "density_mass_amount",
            "density_volume_unit_id",
            "density_volume_amount",
            "density_g_ml",
            'meal_id',
        ]);
    }

    public function withCategoryUnitsNutrientsAndMeal() {
        // The long ingredient_nutrients query is to ensure
        // ingredient_nutrients are ordered by nutrients.seq_num
        $this->load([
            'ingredient_category:id,name',
            'ingredient_nutrient_amount_unit:id,name',
            'ingredient_nutrients' => function($query) {
                $query->select([
                    'ingredient_nutrients.id',
                    'ingredient_nutrients.ingredient_id',
                    'ingredient_nutrients.nutrient_id',
                    'ingredient_nutrients.amount',
                    'ingredient_nutrients.amount_per_100g',
                ])
                ->join('nutrients', 'ingredient_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.seq_num', 'asc');
            },
            'ingredient_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,seq_num',
            'ingredient_nutrients.nutrient.unit:id,name',
            'custom_units:id,name,seq_num,ingredient_id,custom_unit_amount,custom_mass_amount,custom_mass_unit_id,custom_grams',
            'meal:id,name',
        ]);

        return $this->only([
            'id',
            'name',
            'ingredient_category_id',
            'ingredient_category',
            'ingredient_nutrient_amount',
            'ingredient_nutrient_amount_unit_id',
            'ingredient_nutrient_amount_unit',
            'ingredient_nutrients',
            'density_mass_unit_id',
            'density_mass_amount',
            'density_volume_unit_id',
            'density_volume_amount',
            'density_g_ml',
            'custom_units',
            'meal_id',
            'meal',
        ]);
    }

    public static function getUsdaWithCategoryAndUnits() {
        return Cache::rememberForever('shared.usdaIngredients', function () {
            return self::where('user_id', null)
                ->with([
                    'ingredient_category:id,name',
                    'custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
                ])
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_ml', 'user_id']);
        });
    }

    public static function getForUserWithCategoryAndUnits(?int $userId) {
        if (is_null($userId)) return [];
        return self::where('user_id', $userId)
            ->with([
                'ingredient_category:id,name',
                'custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
            ])
            ->get(['id', 'name', 'ingredient_category_id', 'density_g_ml', 'user_id']);
    }

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function ingredient_category() {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id', 'id');
    }

    public function ingredient_nutrients() {
        return $this->hasMany(IngredientNutrient::class, 'ingredient_id', 'id');
    }

    public function ingredient_nutrient_amount_unit() {
        return $this->belongsTo(Unit::class, 'ingredient_nutrient_amount_unit_id', 'id');
    }

    public function density_mass_unit() {
        return $this->belongsTo(Unit::class, 'density_mass_unit_id', 'id');
    }

    public function density_volume_unit() {
        return $this->belongsTo(Unit::class, 'density_volume_unit_id', 'id');
    }

    public function custom_units() {
        return $this->hasMany(Unit::class, 'ingredient_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function meal_ingredients() {
        return $this->hasMany(MealIngredient::class, 'ingredient_id', 'id');
    }

    public function meals() {
        return $this->belongsToMany(Meal::class, 'meal_ingredients', 'ingredient_id', 'meal_id');
    }

    public function food_list_ingredients() {
        return $this->hasMany(FoodListIngredient::class, 'ingredient_id', 'id');
    }

    public function food_lists() {
        return $this->belongsToMany(FoodList::class, 'food_list_ingredients', 'ingredient_id', 'food_list_id');
    }

}
