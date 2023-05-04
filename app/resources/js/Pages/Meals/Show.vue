<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import NutrientProfileMain from '@/Shared/NutrientProfileMain.vue'
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

const props = defineProps({
  meal: Object,
  meals: Array,
  nutrient_profiles: Array,
  rdi_profiles: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean,
})

const deleteDialog = ref(null)
const searchDialog = ref(null)

const searchMeal = ref({})
function search() {
  router.get(route('meals.show', searchMeal.value.id))
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

    <Head :title="meal.name" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('meals.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="meal" />
      <CrudNavBarCreate :enabled="can_create" :href="route('meals.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('meals.edit', meal.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('meals.clone', meal.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(meal.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">
      <H1 :text="meal.name" />

      <!-- Meal name and mass pillbox labels -->
      <div class="flex mt-2">

        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Meal
        </div>

        <div class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{meal.mass_in_grams}} g
        </div>

      </div>
    </div>

    <!-- Table of meal ingredients -->
    <div class="mt-8 text-gray-900">
      <h2 class="text-lg">Meal ingredients</h2>

      <table v-if="meal.meal_ingredients.length" class="text-left w-full">
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
            v-for="meal_ingredient in meal.meal_ingredients"
            class="border-t text-gray-600 font-medium"
          >
            <td scope="row" class="px-4 py-2">
              <Link
                :href="route('ingredients.show', meal_ingredient.ingredient_id)"
                class="text-gray-800 hover:text-blue-600 hover:underline"
              >
                {{meal_ingredient.ingredient.name}}
              </Link>
            </td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              {{meal_ingredient.amount}}
              {{meal_ingredient.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="mt-1 text-gray-700">This meal has no ingredients!</p>
    </div>

    <NutrientProfileMain
      v-if="meal.meal_ingredients.length"
      class="mt-8"
      :rdi_profiles="rdi_profiles"
      :nutrient_profiles="nutrient_profiles"
      :nutrient_categories="nutrient_categories"
      :defaultMassInGrams="Number(meal.mass_in_grams)"
      :displayMassInput="true"
    />

    <SearchForThingAndGo
      ref="searchDialog"
      :things="meals"
      goRoute="meals.show"
      label="Search for another meal"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="meals.destroy" thing="meal" />

  </div>
</template>
