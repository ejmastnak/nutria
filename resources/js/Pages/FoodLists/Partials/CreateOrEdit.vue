<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
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
  units: Array,
  create: Boolean
})

const form = useForm({
  id: props.food_list ? props.food_list.id : null,
  name: props.food_list ? props.food_list.name : "",
  food_list_ingredients: [],  // filled in on submit
  food_list_meals: [],  // filled in on submit
})

const foodListIngredients = ref(
  props.food_list
  ? props.food_list.food_list_ingredients.map((food_list_ingredient, idx) => ({
    id: idx,
    food_list_ingredient: food_list_ingredient,
  }))
  : [])

const foodListMeals = ref(
  props.food_list
  ? props.food_list.food_list_meals.map((food_list_meal, idx) => ({
    id: idx,
    food_list_meal: food_list_meal,
  }))
  : [])

const nameInput = ref(null)

const foodListIngredientTableCellsRef = ref([])
var nextIngredientID = 1
function addFoodListIngredient() {
  foodListIngredients.value.push({
    "id": nextIngredientID,
    food_list_ingredient: {
      "id": null,
      "food_list_id": null,
      "ingredient_id": null,
      "ingredient": {},
      "amount": null,
      "unit_id": props.units.find(unit => unit.name === 'g').id,
      "unit": props.units.find(unit => unit.name === 'g')
    }
  });

  // Focus text input for name of just-added empty food list ingredient
  // Use timeout to give time for new table row to be injected into DOM
  setTimeout(() => {
    const input = foodListIngredientTableCellsRef.value[foodListIngredientTableCellsRef.value.length - 1].querySelectorAll('input')[0];
    if (input) input.focus();
  }, 0)

  nextIngredientID += 1;
}

const foodListMealTableCellsRef = ref([])
var nextMealID = 1
function addFoodListMeal() {
  foodListMeals.value.push({
    "id": nextMealID,
    food_list_meal: {
      "id": null,
      "food_list_id": null,
      "meal_id": null,
      "meal": {},
      "amount": null,
      "unit_id": props.units.find(unit => unit.name === 'g').id,
      "unit": {}
    }
  });

  // Focus text input for name of just-added empty food list ingredient
  // Use timeout to give time for new table row to be injected into DOM
  setTimeout(() => {
    const input = foodListMealTableCellsRef.value[foodListMealTableCellsRef.value.length - 1].querySelectorAll('input')[0];
    if (input) input.focus();
  }, 0)

  nextMealID += 1;
}

function updateFoodListIngredient(idx, newIngredient) {
  foodListIngredients.value[idx].food_list_ingredient.ingredient_id = newIngredient.id
  foodListIngredients.value[idx].food_list_ingredient.ingredient = newIngredient

  // Reset ingredient's unit (to avoid a lingering unit not supported by the
  // new ingredient) and amount (since we're already reseting unit, let's also
  // reset amount for consisten user experience).
  foodListIngredients.value[idx].food_list_ingredient.unit_id = props.units.find(unit => unit.name === 'g').id
  foodListIngredients.value[idx].food_list_ingredient.unit = props.units.find(unit => unit.name === 'g')
  foodListIngredients.value[idx].food_list_ingredient.amount = null
}

function updateFoodListMeal(idx, newMeal) {
  foodListMeals.value[idx].food_list_meal.meal_id = newMeal.id
  foodListMeals.value[idx].food_list_meal.meal = newMeal

  // Reset meals's unit (to avoid a lingering unit not supported by the
  // new meal) and amount (since we're already reseting unit, let's also
  // reset amount for consisten user experience).
  foodListMeals.value[idx].food_list_meal.unit_id = newMeal.meal_unit.id
  foodListMeals.value[idx].food_list_meal.unit = newMeal.meal_unit
  foodListMeals.value[idx].food_list_meal.amount = 1
}

function deleteFoodListIngredient(idx) {
  if (idx >= 0 && idx < foodListIngredients.value.length) foodListIngredients.value.splice(idx, 1)
}

function deleteFoodListMeal(idx) {
  if (idx >= 0 && idx < foodListMeals.value.length) foodListMeals.value.splice(idx, 1)
}

function submit() {
  // Drop empty ingredients/meals and unpack nested food_list_ingredient and
  // food_list_meals object
  form.food_list_ingredients = foodListIngredients.value.filter(fli => fli.food_list_ingredient.ingredient).map(fli => fli.food_list_ingredient)
  form.food_list_meals = foodListMeals.value.filter(flm => flm.food_list_meal.meal).map(flm => flm.food_list_meal)

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

    <!-- Food list ingredients table -->
    <section class="mt-8 p-4 border border-gray-300 shadow-sm rounded-xl">

      <h2 class="text-lg">Food List Ingredients</h2>
      <InputError :message="form.errors.food_list_ingredients" />

      <table v-if="foodListIngredients.length" class="mt-2 text-sm sm:text-base text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-3/4">
              Ingredient
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
              Amount
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-50">
              Unit
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 w-10" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(food_list_ingredient, idx) in foodListIngredients"
            :key="food_list_ingredient.id"
            class="border-t text-gray-600 align-top"
          >
            <td ref="foodListIngredientTableCellsRef" scope="row" class="pr-2 py-2">
              <FuzzyCombobox
                :options="ingredients"
                :modelValue="food_list_ingredient.food_list_ingredient.ingredient"
                :showIcon="false"
                @update:modelValue="newValue => updateFoodListIngredient(idx, newValue)"
              />
              <div class="mt-2 text-left">
                <InputError :message="form.errors['food_list_ingredients.' + idx + '.id']" />
                <InputError :message="form.errors['food_list_ingredients.' + idx + '.ingredient_id']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <div class="w-fit ml-auto">
                <TextInput
                  type="number"
                  placeholder="0"
                  class="block w-24 text-right"
                  v-model="food_list_ingredient.food_list_ingredient.amount"
                  required
                />
                <InputError class="mt-2 text-left" :message="form.errors['food_list_ingredients.' + idx + '.amount']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="units.filter(unit => unit.g || (unit.ml && (food_list_ingredient.food_list_ingredient.ingredient && food_list_ingredient.food_list_ingredient.ingredient.density_g_ml))).concat(food_list_ingredient.food_list_ingredient.ingredient.custom_units ? food_list_ingredient.food_list_ingredient.ingredient.custom_units : [])"
                :modelValue="food_list_ingredient.food_list_ingredient.unit"
                @update:modelValue="newValue => (food_list_ingredient.food_list_ingredient.unit_id = newValue.id, food_list_ingredient.food_list_ingredient.unit = newValue)"
                inputClasses="w-28"
              />
              <div class="mt-2 text-left">
                <InputError :message="form.errors['food_list_ingredients.' + idx + '.unit_id']" />
                <InputError :message="form.errors['food_list_ingredients.' + idx]" />
              </div>
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                @click="deleteFoodListIngredient(idx)"
                class="ml-1 p-1 text-gray-600 hover:text-red-700 mt-1"
              >
                <TrashIcon class="w-6 h-6" />
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

      <table v-if="foodListMeals.length" class="mt-2 text-sm sm:text-base text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50 w-3/4">
              Meal
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
              Amount
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-50">
              Unit
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 w-10" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(food_list_meal, idx) in foodListMeals"
            :key="food_list_meal.id"
            class="border-t text-gray-600 align-top"
          >
            <td ref="foodListMealTableCellsRef" scope="row" class="pr-2 py-2">
              <FuzzyCombobox
                :options="meals"
                :modelValue="food_list_meal.food_list_meal.meal"
                :showIcon="false"
                @update:modelValue="newValue => updateFoodListMeal(idx, newValue)"
              />
              <div class="mt-2 text-left">
                <InputError :message="form.errors['food_list_meals.' + idx + '.id']" />
                <InputError :message="form.errors['food_list_meals.' + idx + '.meal_id']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <div class="w-fit ml-auto">
                <TextInput
                  type="number"
                  placeholder="0"
                  class="block w-24 text-right"
                  v-model="food_list_meal.food_list_meal.amount"
                  required
                />
                <InputError class="mt-2 text-left" :message="form.errors['food_list_meals.' + idx + '.amount']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="Array(food_list_meal.food_list_meal.meal.meal_unit).concat(units.filter(unit => unit.g))"
                :modelValue="food_list_meal.food_list_meal.unit"
                @update:modelValue="newValue => (food_list_meal.food_list_meal.unit_id = newValue.id, food_list_meal.food_list_meal.unit = newValue)"
                inputClasses="w-28"
              />
              <div class="mt-2 text-left">
                <InputError :message="form.errors['food_list_meals.' + idx + '.unit_id']" />
                <InputError :message="form.errors['food_list_meals.' + idx]" />
              </div>
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                @click="deleteFoodListMeal(idx)"
                class="ml-1 p-1 text-gray-600 hover:text-red-700 mt-1"
              >
                <TrashIcon class="w-6 h-6" />
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
