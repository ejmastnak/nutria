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
import CrudNavBarSave from '@/Shared/CrudNavBarSave.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import MyLink from '@/Components/MyLink.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  meal: Object,
  meals: Array,
  nutrient_profiles: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean,
  can_create_ingredient: Boolean,
})

const deleteDialog = ref(null)
const searchDialog = ref(null)

const searchMeal = ref({})
function search() {
  router.get(route('meals.show', searchMeal.value.id))
}

function saveAsIngredient() {
  router.put(route('meals.save-as-ingredient', props.meal.id))
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
        <CrudNavBarSave :enabled="can_create_ingredient" text="Save as ingredient" @wasClicked="saveAsIngredient" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(meal.id, meal.ingredient ? {id: meal.ingredient.id, name: meal.ingredient.name} : null)" />
      </div>
    </CrudNavBar>

    <!-- Meal name and descriptive pillboxes -->
    <div class="mt-8">
      <H1 :text="meal.name" />

      <!-- Meal name and mass pillbox labels -->
      <div class="flex mt-2">
        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Meal
        </div>
        <div class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{round(Number(meal.mass_in_grams))}} g
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
              <MyLink
                :href="route('ingredients.show', meal_ingredient.ingredient_id)"
                class="text-gray-800"
              >
                {{meal_ingredient.ingredient.name}}
              </MyLink>
            </td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              {{round(Number(meal_ingredient.amount), 1)}}
              {{meal_ingredient.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="mt-1 text-gray-700">
        This meal has no ingredients!
        Consider <MyLink :colored="true" :href="route('meals.edit', meal.id)">adding some first</MyLink>.
      </div>
    </div>

    <NutrientProfile
      v-if="meal.meal_ingredients.length"
      class="mt-8"
      :intake_guidelines="intake_guidelines"
      :nutrient_profiles="nutrient_profiles"
      :nutrient_categories="nutrient_categories"
      :defaultMassInGrams="Number()"
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
