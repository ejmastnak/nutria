<script setup>
import { ref, computed } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import { roundNonZero } from '@/utils/GlobalFunctions.js'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import H1 from '@/Components/H1ForCrud.vue'
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  food_list: Object,
  nutrient_profiles: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
  units: Array,
  food_lists: Array,
  can_view: Boolean,
  can_create: Boolean,
  can_clone: Boolean,
  can_update: Boolean,
  can_delete: Boolean,
})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>

  <div>

    <Head :title="food_list.name" />
    <div>

      <H1 :text="food_list.name" />

      <!-- Food List name and mass pillbox labels -->
      <div class="flex mt-2">
        <div class="bg-blue-50 px-3 py-1 roundNonZeroed-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Food List
        </div>
        <div class="ml-2 bg-blue-50 px-3 py-1 roundNonZeroed-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{roundNonZero(Number(food_list.mass_in_grams))}} g
        </div>
      </div>

    </div>

    <!-- Table of food list ingredients and meals -->
    <div class="mt-8 text-gray-900">

      <!-- Table of food list ingredients -->
      <div v-if="food_list.food_list_ingredients.length" >
        <h2 class="text-lg">Food List Ingredients</h2>
        <table class="text-left w-full">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-3 bg-blue-50">
                Ingredient
              </th>
              <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
                Amount
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="fli in food_list.food_list_ingredients"
              class="border-t text-gray-600 font-medium"
            >
              <td scope="row" class="px-4 py-2">
                <MyLink
                  :href="route('ingredients.show', fli.ingredient_id)"
                  class="text-gray-800"
                >
                  {{fli.ingredient.name}}
                </MyLink>
              </td>
              <td class="px-3 py-2 text-right whitespace-nowrap">
                {{roundNonZero(Number(fli.amount, 1))}}
                {{fli.unit.name}}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table of food list meals -->
      <div v-if="food_list.food_list_meals.length" class="mt-2">
        <h2 class="text-lg">Food List Meals</h2>
        <table class="text-left w-full">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-3 bg-blue-50">
                Meal
              </th>
              <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
                Amount
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="flm in food_list.food_list_meals"
              class="border-t text-gray-600 font-medium"
            >
              <td scope="row" class="px-4 py-2">
                <MyLink
                  :href="route('meals.show', flm.meal_id)"
                  class="text-gray-800"
                >
                  {{flm.meal.name}}
                </MyLink>
              </td>
              <td class="px-3 py-2 text-right whitespace-nowrap">
                {{roundNonZero(Number(flm.amount, 1))}}
                {{flm.unit.name}}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="!food_list.food_list_ingredients.length && !food_list.food_list_meals.length" class="mt-1 text-gray-700">
        This food list has no meals or ingredients!
        Consider <MyLink :colored="true" :href="route('food-lists.edit', food_list.id)">adding some first</MyLink>.
      </div>

    </div>

    <NutrientProfile
      class="mt-6"
      :defaultUnit="food_list.food_list_unit"
      :defaultUnitAmount="1"
      :intakeGuidelines="intake_guidelines"
      :nutrientProfiles="nutrient_profiles"
      :nutrientCategories="nutrient_categories"
      :units="Array(food_list.food_list_unit).concat(units)"
      thing="foodList"
    />

  </div>
</template>
