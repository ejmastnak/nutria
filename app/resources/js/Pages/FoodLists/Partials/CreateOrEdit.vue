<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import SimpleCombobox from '@/Shared/SimpleCombobox.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import { PlusCircleIcon, TrashIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  food_list: Object,
  ingredients: Array,
  meals: Array,
  ingredient_categories: Array,
  units: Array,
  create: Boolean
})

const gramIdx = props.units.map(unit => unit.name).indexOf("g")
const gram = props.units[gramIdx]
const form = useForm({
  name: props.create ? "" : props.food_list.name,
  food_list_ingredients: props.food_list ? props.food_list.food_list_ingredients.map((fli, index) => ({
    idx: index,
    id: fli.id,
    food_list_id: fli.food_list_id,
    ingredient_id: fli.ingredient_id,
    amount: round(Number(fli.amount), 1).toString(),
    unit_id: fli.unit_id,
    ingredient: fli.ingredient,
    unit: fli.unit
  })) : [],
  food_list_meals: props.food_list ? props.food_list.food_list_meals.map((flm, index) => ({
    idx: index,
    id: flm.id,
    food_list_id: flm.food_list_id,
    meal_id: flm.meal_id,
    amount: round(Number(flm.amount), 1).toString(),
    unit_id: flm.unit_id,
    meal: flm.meal,
    unit: flm.unit
  })) : []
})

const nameInput = ref(null)
onMounted(() => {
  if (props.create) {
    nameInput.value.focus()
  }
})

const foodListIngredientTableCells = ref([])
var nextIngredientID = -1
function addFoodListIngredient() {
  form.food_list_ingredients.push({
    "idx": form.food_list_ingredients.length,
    "id": nextIngredientID,
    "food_list_id": 0,
    "ingredient_id": 0,
    "amount": "0.0",
    "unit_id": gram.id,
    "ingredient": {
      "id": 0,
      "name": "",
      "density_g_per_ml": null
    },
    "unit": gram
  });

  // Focus text input for name of just-added empty food list ingredient
  // Use timeout to give time for new table row to be injected into DOM
  setTimeout(() => {
    const input = foodListIngredientTableCells.value[foodListIngredientTableCells.value.length - 1].querySelectorAll('input')[0];
    if (input) input.focus();
  }, 0)

  nextIngredientID -= 1;
}

const foodListMealTableCells = ref([])
var nextMealID = -1
function addFoodListMeal() {
  form.food_list_meals.push({
    "idx": form.food_list_meals.length,
    "id": nextMealID,
    "food_list_id": 0,
    "meal_id": 0,
    "amount": "0.0",
    "unit_id": gram.id,
    "meal": {
      "id": 0,
      "name": ""
    },
    "unit": gram
  });

  // Focus text input for name of just-added empty food list meal
  // Use timeout to give time for new table row to be injected into DOM
  setTimeout(() => {
    const input = foodListMealTableCells.value[foodListMealTableCells.value.length - 1].querySelectorAll('input')[0];
    if (input) input.focus();
  }, 0)

  nextMealID -= 1;
}

function updateFoodListIngredient(foodListIngredient, newIngredient) {
  // If switching from an ingredient with well-defined density and amount
  // specified in units of volume to an ingredient that does not have density,
  // reset ingredient's amount and unit to non-volume units.
  if (foodListIngredient.unit.is_volume && foodListIngredient.ingredient.density_g_per_ml && !newIngredient.density_g_per_ml) {
    foodListIngredient.ingredient_id = newIngredient.id
    foodListIngredient.ingredient = newIngredient
    foodListIngredient.amount = '0.0'
    foodListIngredient.unit = gram
  } else {
    foodListIngredient.ingredient_id = newIngredient.id
    foodListIngredient.ingredient = newIngredient
  }
}

function updateFoodListMeal(foodListMeal, newMeal) {
  foodListMeal.meal_id = newMeal.id
  foodListMeal.meal = newMeal
  foodListMeal.amount = round(round(Number(newMeal.mass_in_grams), 1)).toString()
}

function deleteFoodListIngredient(id) {
  const idx = form.food_list_ingredients.map(fli => fli.id).indexOf(id);
  form.food_list_ingredients.splice(idx, 1);
  // Eager update of food list ingredient indices
  form.food_list_ingredients.forEach((fli, idx) => fli.idx = idx);
}

function deleteFoodListMeal(id) {
  const idx = form.food_list_meals.map(flm => flm.id).indexOf(id);
  form.food_list_meals.splice(idx, 1);
  // Eager update of food list ingredient indices
  form.food_list_meals.forEach((flm, idx) => flm.idx = idx);
}

function submit() {
  form.food_list_ingredients = form.food_list_ingredients.filter(fli => fli.ingredient.id > 0)
  form.food_list_meals = form.food_list_meals.filter(flm => flm.meal.id > 0)

  if (props.create) {
    form.post(route('food-lists.store'))
  } else {
    form.put(route('food-lists.update', props.food_list.id))
  }
}

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue"
export default {
  layout: AppLayout,
}
</script>

<template>

  <form @submit.prevent="submit">

    <!-- Name -->
    <section class="mt-4">
      <div class="w-96">
        <InputLabel for="name" value="Name" />
        <TextInput
          id="name"
          ref="nameInput"
          type="text"
          class="mt-1 block w-full"
          v-model="form.name"
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>
    </section>

    <!-- Food List ingredients table -->
    <section class="mt-8 p-4 border border-gray-300 shadow-sm rounded-xl">

      <h2 class="text-lg">Food List Ingredients</h2>

      <InputError :message="form.errors.food_list_ingredients" />

      <table v-if="form.food_list_ingredients.length" class="mt-2 text-sm sm:text-base text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-7/12">
              Ingredient
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right w-2/12">
              Amount
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-2/12">
              Unit
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 w-1/12" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="fli in form.food_list_ingredients"
            :key="fli.id"
            class="border-t text-gray-600"
          >
            <td ref="foodListIngredientTableCells" scope="row" class="px-5 py-2">
              <FuzzyCombobox
                :options="ingredients"
                :modelValue="fli.ingredient"
                @update:modelValue="newValue => updateFoodListIngredient(fli, newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['food_list_ingredients.' + fli.idx + '.ingredient_id']" />
              <InputError class="mt-2 text-left" :message="form.errors['food_list_ingredients.' + fli.idx + '.id']" />
            </td>
            <td class="px-4 py-2 text-right">
              <div class="w-fit ml-auto">
                <TextInput
                  type="text"
                  class="mt-1 block w-24 py-1 text-right"
                  v-model="fli.amount"
                  required
                />
                <InputError class="mt-2 text-left" :message="form.errors['food_list_ingredients.' + fli.idx + '.amount']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="units.filter(unit => unit.is_mass || (fli.ingredient.density_g_per_ml && unit.is_volume))"
                :modelValue="fli.unit"
                @update:modelValue="newValue => (fli.unit_id = newValue.id, fli.unit = newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['food_list_ingredients.' + fli.idx + '.unit_id']" />
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                @click="deleteFoodListIngredient(fli.id)"
                class="ml-1 p-1 text-gray-700 hover:text-red-700"
              >
                <TrashIcon class="w-6 h-6 text-gray-600" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- New ingredient button -->
      <div class="mt-2">
        <SecondaryButton
          @click="addFoodListIngredient"
          class="flex items-center mt-1 normal-case !font-normal !text-sm !px-2"
        >
          <PlusCircleIcon class="w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add ingredient</p>
        </SecondaryButton>
      </div>

    </section>

    <!-- Food List meals table -->
    <section class="mt-8 p-4 border border-gray-300 shadow-sm rounded-xl">

      <h2 class="text-lg">Food List Meals</h2>

      <InputError :message="form.errors.food_list_meals" />

      <table v-if="form.food_list_meals.length" class="mt-2 text-sm sm:text-base text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-7/12">
              Meal
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right w-2/12">
              Amount
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-2/12">
              Unit
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 w-1/12" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="flm in form.food_list_meals"
            :key="flm.id"
            class="border-t text-gray-600"
          >
            <td ref="foodListMealTableCells" scope="row" class="px-5 py-2">
              <FuzzyCombobox
                :options="meals"
                :modelValue="flm.meal"
                @update:modelValue="newValue => updateFoodListMeal(flm, newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['food_list_meals.' + flm.idx + '.meal_id']" />
              <InputError class="mt-2 text-left" :message="form.errors['food_list_meals.' + flm.idx + '.id']" />
            </td>
            <td class="px-4 py-2 text-right">
              <div class="w-fit ml-auto">
                <TextInput
                  type="text"
                  class="mt-1 block w-24 py-1 text-right"
                  v-model="flm.amount"
                  required
                />
                <InputError class="mt-2 text-left" :message="form.errors['food_list_meals.' + flm.idx + '.amount']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="units.filter(unit => unit.is_mass)"
                :modelValue="flm.unit"
                @update:modelValue="newValue => (flm.unit_id = newValue.id, flm.unit = newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['food_list_meals.' + flm.idx + '.unit_id']" />
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                @click="deleteFoodListMeal(flm.id)"
                class="ml-1 p-1 text-gray-700 hover:text-red-700"
              >
                <TrashIcon class="w-6 h-6 text-gray-600" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- New meal button -->
      <div class="mt-2">
        <SecondaryButton
          @click="addFoodListMeal"
          class="flex items-center mt-1 normal-case !font-normal !text-sm !px-2"
        >
          <PlusCircleIcon class="w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add meal</p>
        </SecondaryButton>
      </div>

    </section>

    <!-- Submit/cancel buttons -->
    <section class="mt-8">

      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        <span v-if="create">Create</span>
        <span v-else>Update</span>
      </PrimaryButton>

      <SecondaryLinkButton
        :href="route('food-lists.index')"
        class="ml-4"
      >
        Cancel
      </SecondaryLinkButton>

    </section>

  </form>

</template>
