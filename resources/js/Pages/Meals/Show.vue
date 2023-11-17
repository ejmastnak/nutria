<script setup>
import { ref, computed } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import { ArchiveBoxArrowDownIcon } from '@heroicons/vue/24/outline'
import { round, roundNonZero } from '@/utils/GlobalFunctions.js'
import MyLink from '@/Components/MyLink.vue'
import H1 from '@/Components/H1ForCrud.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  meal: Object,
  nutrient_profiles: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
  meals: Array,
  can_view: Boolean,
  can_create: Boolean,
  can_clone: Boolean,
  can_update: Boolean,
  can_delete: Boolean,
  can_save_as_ingredient: Boolean,
})

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

    <!-- Meal name and descriptive pillboxes -->
    <div class="mt-8 flex">

      <div class="grow-[2] mr-4">
        <H1 :text="meal.name" />

        <!-- Meal name and mass pillbox labels -->
        <div class="flex mt-2">
          <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
            Meal
          </div>
          <div class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit whitespace-nowrap">
            {{roundNonZero(Number(meal.mass_in_grams))}} g
          </div>
        </div>
      </div>

      <SecondaryButton
        class="flex ml-auto items-center h-fit rounded-lg mt-1 normal-case"
        :class="{'!text-gray-300': !can_save_as_ingredient}"
        @click="saveAsIngredient"
      >
        <div class="flex items-center sm:whitespace-nowrap">
          <ArchiveBoxArrowDownIcon class="shrink-0 w-6 h-6" />
          <p class="ml-2 text-sm text-left">Save as ingredient</p>
        </div>
      </SecondaryButton>

    </div>

    <!-- Table of meal ingredients -->
    <div class="mt-8 text-gray-900">
      <h2 class="text-lg">Meal ingredients</h2>

      <table v-if="meal.meal_ingredients.length" class="text-left w-full">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-10/12">
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
              {{roundNonZero(Number(meal_ingredient.amount), 1)}}
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

  </div>
</template>
