<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['id', 'name'];

    public static function getWithName() {
        return self::all(['id', 'name']);
    }

    public static function getWithNameSorted() {
        return self::orderBy('name')->get(['id', 'name']);
    }

    public static function otherCategory() {
        return self::where('name', 'Other')->first();
    }
}
