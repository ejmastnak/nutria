<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Ingredient' => 'App\Policies\IngredientPolicy',
        'App\Models\Meal' => 'App\Policies\MealPolicy',
        'App\Models\FoodList' => 'App\Policies\FoodListPolicy',
        'App\Models\RdiProfile' => 'App\Policies\RdiProfilePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
