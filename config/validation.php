<?php

/**
 *  Rules, some admittedly arbitrary, to use for validation
 */

return [
    'max_name_length' => "max:5000",
    'max_description_length' => "max:10000",
    // Quantity of nutrient (in nutrient units) in an ingredient
    'max_ingredient_nutrient_amount' => "max:100000000",
    // Amount of an ingredient in mass or volume (in whatever unit the user
    // used) to specify an ingredient density or mass amount of a custom unit.
    'max_ingredient_amount' => "max:100000000",
    // Max custom units in an ingredient store/update request
    'max_custom_units' => "max:1000",
];
