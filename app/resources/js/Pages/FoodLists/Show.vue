<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarEdit from '@/Shared/CrudNavBarEdit.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import H1 from '@/Components/H1ForCrud.vue'
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  food_list: Object,
  food_lists: Array,
  nutrient_profiles: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const searchDialog = ref(null)
const deleteDialog = ref(null)

const searchFoodList = ref({})
function search() {
  router.get(route('food-lists.show', searchFoodList.value.id))
}

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

    <CrudNavBar>
      <CrudNavBarIndex :href="route('food-lists.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="food list" />
      <CrudNavBarCreate :enabled="can_create" :href="route('food-lists.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('food-lists.edit', food_list.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('food-lists.clone', food_list.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(food_list.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">

      <H1 :text="food_list.name" />

      <!-- Food list name and mass pillbox labels -->
      <div class="flex mt-2">
        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Food list
        </div>
        <div class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{round(Number(food_list.mass_in_grams))}} g
        </div>
      </div>

    </div>

    <!-- Table of food list ingredients and meals -->
    <div class="mt-8 text-gray-900">
      <h2 class="text-lg">Food list ingredients</h2>

      <!-- Table of food list ingredients -->
      <table v-if="food_list.food_list_ingredients.length" class="text-left w-full">
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
              {{round(Number(fli.amount, 1))}}
              {{fli.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Table of food list meals -->
      <table v-if="food_list.food_list_meals.length" class="text-left w-full">
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
              {{round(Number(flm.amount, 1))}}
              {{flm.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>

      <p v-if="!food_list.food_list_ingredients.length && !food_list.food_list_meals.length" class="mt-1 text-gray-700">This food list has no meals or ingredients!</p>

    </div>

    <NutrientProfile
      v-if="food_list.food_list_ingredients.length || food_list.food_list_meals.length"
      class="mt-8"
      :intake_guidelines="intake_guidelines"
      :nutrient_profiles="nutrient_profiles"
      :nutrient_categories="nutrient_categories"
      :defaultMassInGrams="Number(props.food_list.mass_in_grams)"
      :displayMassInput="false"
    />

    <SearchForThingAndGo
      ref="searchDialog"
      :things="food_lists"
      goRoute="food-lists.show"
      label="Search for another food list"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="food-lists.destroy" thing="food list" />

  </div>
</template>
