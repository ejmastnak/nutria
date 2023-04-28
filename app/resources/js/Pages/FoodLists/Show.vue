<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, DocumentDuplicateIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  food_list: Object,
  food_lists: Array,
  nutrient_profile: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean
})

const defaultMassInGrams = props.food_list.mass_in_grams
const howManyGrams = ref(props.food_list.mass_in_grams);

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

    <!-- Header bar with edit/clone/delete icons -->
    <div class="flex items-center space-x-4 -mt-2 border border-gray-300 p-1 px-4 rounded-xl">

      <Link
        v-if="can_edit"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('food-lists.edit', food_list.id)"
      >
      <div class="flex">
        <PencilSquareIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Edit</p>
      </div>
      </Link>

      <Link
        v-if="can_clone"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('food-lists.clone', food_list.id)"
      >
      <div class="flex">
        <DocumentDuplicateIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Clone</p>
      </div>
      </Link>

      <button
        v-if="can_delete"
        type="button"
        @click="deleteDialog.open(food_list.id)"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
      >
      <div class="flex">
        <TrashIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Delete</p>
      </div>
      </button>

      <form
        @submit.prevent="search"
        class="!ml-auto"
      >
        <FuzzyCombobox
          labelText="Search for another food list"
          :options="food_lists"
          v-model="searchFoodList"
        />
      </form>

    </div>

    <div class="mt-10">

      <h1 class="text-xl w-2/3">{{food_list.name}}</h1>

      <!-- Food list name and mass pillbox labels -->
      <div class="flex mt-2">
        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Food list
        </div>
        <div class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{food_list.mass_in_grams}} g
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
              <Link
                :href="route('ingredients.show', fli.ingredient_id)"
                class="text-gray-800 hover:text-blue-600 hover:underline"
              >
                {{fli.ingredient.name}}
              </Link>
            </td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              {{fli.amount}}
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
              <Link
                :href="route('meals.show', flm.meal_id)"
                class="text-gray-800 hover:text-blue-600 hover:underline"
              >
                {{flm.meal.name}}
              </Link>
            </td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              {{flm.amount}}
              {{flm.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>

      <p v-if="!food_list.food_list_ingredients.length && !food_list.food_list_meals.length" class="mt-1 text-gray-700">This food list has no meals or ingredients!</p>

    </div>

    <section v-if="food_list.food_list_ingredients.length || food_list.food_list_meals.length" class="mt-10">

      <h2 class="text-lg">Nutrient profile</h2>

      <NutrientProfile
        class="w-full mt-4"
        :nutrient_profile="nutrient_profile"
        :nutrient_categories="nutrient_categories"
        :howManyGrams="Number(howManyGrams)"
        :defaultMassInGrams="Number(defaultMassInGrams)"
      />

    </section>

    <DeleteDialog ref="deleteDialog" deleteRoute="food-lists.destroy" thing="food list" />

  </div>
</template>
