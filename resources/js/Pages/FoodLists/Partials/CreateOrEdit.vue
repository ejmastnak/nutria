<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { round, currentLocalDate } from '@/utils/GlobalFunctions.js'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import { PlusCircleIcon, TrashIcon, PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
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
  description: props.food_list ? props.food_list.description : null,
  food_list_ingredients: [],  // filled in on submit
  food_list_meals: [],  // filled in on submit
})

const showDescription = ref(props.food_list ? (!!props.food_list.description) : false)
const descriptionInputRef = ref(null)
function toggleDescription() {
  if (!showDescription.value) descriptionInputRef.value.focus();
  showDescription.value = !showDescription.value;
}

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

const nameInputRef = ref(null)

var nextIngredientId = props.food_list ? props.food_list.food_list_ingredients.length + 1 : 1
function addFoodListIngredient() {
  foodListIngredients.value.push({
    "id": nextIngredientId,
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
  // Use timeout to give time for new element to be injected into DOM
  setTimeout(() => {
    const wrapper = document.getElementById('food-list-ingredient-input-wrapper-' + nextIngredientId)
    if (wrapper) {
      const input = wrapper.querySelectorAll('input')[0];
      if (input) input.focus();
    }
    nextIngredientId += 1;
  }, 0)

}

var nextMealId = props.food_list ? props.food_list.food_list_meals.length + 1 : 1
function addFoodListMeal() {
  foodListMeals.value.push({
    "id": nextMealId,
    food_list_meal: {
      "id": null,
      "food_list_id": null,
      "meal_id": null,
      "meal": {},
      "amount": null,
      "unit_id": null,
      "unit": {}
    }
  });

  // Focus text input for name of just-added empty food list meal
  // Use timeout to give time for new element to be injected into DOM
  setTimeout(() => {
    const wrapper = document.getElementById('food-list-meal-input-wrapper-' + nextMealId)
    if (wrapper) {
      const input = wrapper.querySelectorAll('input')[0];
      if (input) input.focus();
    }
    nextMealId += 1;
  }, 0)

}

function updateFoodListIngredient(idx, newIngredient) {
  foodListIngredients.value[idx].food_list_ingredient.ingredient_id = newIngredient.id
  foodListIngredients.value[idx].food_list_ingredient.ingredient = newIngredient

  // Reset ingredient's unit if old unit is not supported by new ingredient.
  const newUnits = props.units.filter(unit => unit.g || (unit.ml && newIngredient.density_g_ml)).concat(newIngredient.custom_units ? newIngredient.custom_units : [])
  if (!newUnits.map(unit => unit.id).includes(foodListIngredients.value[idx].food_list_ingredient.unit_id)) {
    foodListIngredients.value[idx].food_list_ingredient.unit_id = props.units.find(unit => unit.name === 'g').id
    foodListIngredients.value[idx].food_list_ingredient.unit = props.units.find(unit => unit.name === 'g')
    foodListIngredients.value[idx].food_list_ingredient.amount = null
  }

  // Focus ingredient amount input for convenience
  setTimeout(() => {
    const wrapper = document.getElementById('food-list-ingredient-amount-wrapper-' + idx)
    if (wrapper) {
      const input = wrapper.querySelectorAll('input')[0];
      if (input) input.focus();
    }
  }, 0);

}

function updateFoodListMeal(idx, newMeal) {
  foodListMeals.value[idx].food_list_meal.meal_id = newMeal.id
  foodListMeals.value[idx].food_list_meal.meal = newMeal

  // Reset meal's unit to 1 meal if a unit has not yet been selected or if old
  // unit is not supported by new meal (mass units will always be supported,
  // but each meal's natural unit is different).
  if (foodListMeals.value[idx].food_list_meal.unit_id === null || foodListMeals.value[idx].food_list_meal.unit.g === null) {
    foodListMeals.value[idx].food_list_meal.unit_id = newMeal.meal_unit.id
    foodListMeals.value[idx].food_list_meal.unit = newMeal.meal_unit
    if (foodListMeals.value[idx].food_list_meal.amount === null) foodListMeals.value[idx].food_list_meal.amount = 1;
  }

  // Focus meal amount input for convenience
  setTimeout(() => {
    const wrapper = document.getElementById('food-list-meal-amount-wrapper-' + idx)
    if (wrapper) {
      const input = wrapper.querySelectorAll('input')[0];
      if (input) input.focus();
    }
  }, 0);

}

function deleteFoodListIngredient(idx) {
  if (idx >= 0 && idx < foodListIngredients.value.length) foodListIngredients.value.splice(idx, 1)
}

function deleteFoodListMeal(idx) {
  if (idx >= 0 && idx < foodListMeals.value.length) foodListMeals.value.splice(idx, 1)
}

const foodListNameBeginsWithDate = computed(() => {
  const regexp = new RegExp("^[0-9]{4}-[0-9]{2}-[0-9]{2}");
  return regexp.test(form.name.trim())
})

function prependDateToFoodListName() {
  if (foodListNameBeginsWithDate.value) {
    const regexp = new RegExp("^[0-9]{4}-[0-9]{2}-[0-9]{2}");
    form.name = form.name.trim().replace(regexp, "").trim()
  } else {
    form.name = currentLocalDate() + " " + form.name.trim()
    nameInputRef.value.focus()
  }
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

onMounted(() => {
  if (props.meal === null) {
    nameInputRef.value.focus()
  }
})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue"
export default {
  layout: AppLayout,
}
</script>

<template>

  <form @submit.prevent="submit" class="mb-56 sm:mb-16">

    <section class="mt-4">
      <!-- Name -->
      <div>
        <InputLabel for="name" value="Name" />
        <div class="flex flex-wrap gap-1">
          <TextInput
            id="name"
            ref="nameInputRef"
            type="text"
            class="min-w-[28rem] mr-2"
            v-model="form.name"
            required
          />
          <SecondaryButton @click="prependDateToFoodListName">
            <p v-if="!foodListNameBeginsWithDate">Prepend today's date</p>
            <p v-else>Strip leading date</p>
          </SecondaryButton>
        </div>
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <!-- Description -->
      <div class="mt-4 w-full">
        <InputLabel for="description" value="Description (optional)" />
        <PlainButton @click="toggleDescription" class="mt-0.5 flex items-center text-sm">
          <PencilSquareIcon v-if="!showDescription" class="-ml-1 w-5 h-5 text-gray-500" />
          <XMarkIcon v-else class="-ml-1 w-5 h-5 text-gray-600" />
          <p class="ml-1.5 whitespace-nowrap">
            {{showDescription ? "Hide description" : (form.description ? "Edit" : "Add") + " description"}}
          </p>
        </PlainButton>
        <TextArea
          v-show="showDescription" 
          id="description"
          ref="descriptionInputRef"
          class="mt-1 block w-full h-36 sm:h-44 md:h-48 max-w-xl"
          v-model="form.description"
        />
        <InputError class="mt-2" :message="form.errors.description" />
      </div>

    </section>

    <!-- Food list ingredients table -->
    <section class="mt-6">

      <h2 class="text-lg">Food List Ingredients</h2>
      <InputError :message="form.errors.food_list_ingredients" />
      <div v-if="foodListIngredients.length" class="mt-1 sm:min-w-[600px] gap-y-6 sm:gap-y-1.5 grid">

        <!-- Header -->
        <div class="hidden sm:grid sm:grid-cols-16 sm:gap-x-1 w-full border">
          <p class="col-span-9 px-3 py-3 -mx-1 bg-blue-50">Ingredient</p>
          <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-100 text-right">Amount</p>
          <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-50">Unit</p>
          <p class="col-span-1 px-3 py-3 -mx-1 bg-blue-100" />
        </div>

        <!-- Food list ingredients -->
        <div
          v-for="(food_list_ingredient, idx) in foodListIngredients"
          :key="food_list_ingredient.id"
          class="grid grid-cols-16 gap-x-1 bg-gray-50 -mx-1 px-2 py-1 rounded border border-gray-100"
        >
          <!-- Ingredient input -->
          <div :id="'food-list-ingredient-input-wrapper-' + food_list_ingredient.id" class="col-span-16 sm:col-span-9">
            <FuzzyCombobox
              :targets="ingredients"
              :modelValue="food_list_ingredient.food_list_ingredient.ingredient"
              :showIcon="false"
              :fuzzyLimit="6"
              labelText="Ingredient"
              @update:modelValue="newValue => updateFoodListIngredient(idx, newValue)"
            />
            <div class="text-left">
              <InputError :message="form.errors['food_list_ingredients.' + idx + '.id']" />
              <InputError :message="form.errors['food_list_ingredients.' + idx + '.ingredient_id']" />
            </div>
          </div>
          <!-- Amount input -->
          <div class="col-span-6 sm:col-span-3 text-right -mt-0.5 sm:mt-0">
            <!-- mt-1 needed to align labels and inputs with comboboxes -->
            <div :id="'food-list-ingredient-amount-wrapper-' + idx" class="w-full ml-auto mt-1">
              <InputLabel :for="'food-list-ingredient-amount-input-' + idx" value="Amount" />
              <TextInput
                type="number"
                :id="'food-list-ingredient-amount-input-' + idx"
                placeholder="0"
                class="w-full text-right"
                step="any"
                v-model="food_list_ingredient.food_list_ingredient.amount"
                required
              />
              <InputError class="text-left" :message="form.errors['food_list_ingredients.' + idx + '.amount']" />
            </div>
          </div>
          <!-- Unit combobox -->
          <div class="col-span-8 sm:col-span-3 text-right -mt-0.5 sm:mt-0">
            <SimpleCombobox
              :options="units.filter(unit => unit.g || (unit.ml && (food_list_ingredient.food_list_ingredient.ingredient && food_list_ingredient.food_list_ingredient.ingredient.density_g_ml))).concat(food_list_ingredient.food_list_ingredient.ingredient.custom_units ? food_list_ingredient.food_list_ingredient.ingredient.custom_units : [])"
              :modelValue="food_list_ingredient.food_list_ingredient.unit"
              @update:modelValue="newValue => (food_list_ingredient.food_list_ingredient.unit_id = newValue.id, food_list_ingredient.food_list_ingredient.unit = newValue)"
              labelText="Unit"
              labelClasses="text-right w-full"
            />
            <div class="text-left">
              <InputError :message="form.errors['food_list_ingredients.' + idx + '.unit_id']" />
              <InputError :message="form.errors['food_list_ingredients.' + idx]" />
            </div>
          </div>
          <div class="col-span-2 sm:col-span-1 mt-0.5 sm:mt-1">
            <InputLabel :for="'food-list-ingredient-delete-' + idx" class="sr-only" value="Delete" />
            <!-- mt-5 makes up for lack of visible label -->
            <PlainButton
              type="button"
              :id="'food-list-ingredient-delete-' + idx"
              @click="deleteFoodListIngredient(idx)"
              class="!block mt-5 h-[2.6rem] text-gray-600 hover:text-red-700 !px-0 !w-full"
            >
              <TrashIcon class="w-5 h-5 mx-auto block" />
            </PlainButton>
          </div>
        </div>
      </div>

      <!-- New ingredient button -->
      <div :class="{
        'mt-1': !foodListIngredients.length,
        'mt-5': !!foodListIngredients.length,
        'sm:mt-4': !!foodListIngredients.length,
      }" >
        <PlainButton
          @click="addFoodListIngredient"
          class="flex items-center mt-1 text-sm"
        >
          <PlusCircleIcon class="-ml-1 w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add ingredient</p>
        </PlainButton>
      </div>

    </section>

    <!-- Food List meals table -->
    <section class="mt-6">

      <h2 class="text-lg">Food List Meals</h2>
      <InputError :message="form.errors.food_list_meals" />
      <div v-if="foodListMeals.length" class="mt-1 sm:min-w-[600px] gap-y-6 sm:gap-y-1.5 grid">

        <!-- Header -->
        <div class="hidden sm:grid sm:grid-cols-16 sm:gap-x-1 w-full border">
          <p class="col-span-9 px-3 py-3 -mx-1 bg-blue-50">Ingredient</p>
          <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-100 text-right">Amount</p>
          <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-50">Unit</p>
          <p class="col-span-1 px-3 py-3 -mx-1 bg-blue-100" />
        </div>

        <!-- Food list meals -->
        <div
          v-for="(food_list_meal, idx) in foodListMeals"
          :key="food_list_meal.id"
          class="grid grid-cols-16 gap-x-1 bg-gray-50 -mx-1 px-2 py-1 rounded border border-gray-100"
        >
          <!-- Meal input -->
          <div :id="'food-list-meal-input-wrapper-' + food_list_meal.id" class="col-span-16 sm:col-span-9">
            <FuzzyCombobox
              :targets="meals"
              :modelValue="food_list_meal.food_list_meal.meal"
              :showIcon="false"
              :fuzzyLimit="6"
              labelText="Meal"
              @update:modelValue="newValue => updateFoodListMeal(idx, newValue)"
            />
            <div class="text-left">
              <InputError :message="form.errors['food_list_meals.' + idx + '.id']" />
              <InputError :message="form.errors['food_list_meals.' + idx + '.meal_id']" />
            </div>
          </div>
          <!-- Amount input -->
          <div class="col-span-6 sm:col-span-3 text-right -mt-0.5 sm:mt-0">
            <!-- mt-1 needed to align labels and inputs with comboboxes -->
            <div :id="'food-list-meal-amount-wrapper-' + idx" class="w-full ml-auto mt-1">
              <InputLabel :for="'food-list-meal-amount-wrapper-' + idx" value="Amount" />
              <TextInput
                type="number"
                :id="'food-list-meal-amount-wrapper-' + idx"
                placeholder="0"
                class="w-full text-right"
                step="any"
                v-model="food_list_meal.food_list_meal.amount"
                required
              />
              <InputError class="text-left" :message="form.errors['food_list_meals.' + idx + '.amount']" />
            </div>
          </div>
          <!-- Unit combobox -->
          <div class="col-span-8 sm:col-span-3 text-right -mt-0.5 sm:mt-0">
            <SimpleCombobox
              :options="food_list_meal.food_list_meal.meal_id ? Array(food_list_meal.food_list_meal.meal.meal_unit).concat(units.filter(unit => unit.g)) : []"
              :modelValue="food_list_meal.food_list_meal.unit"
              @update:modelValue="newValue => (food_list_meal.food_list_meal.unit_id = newValue.id, food_list_meal.food_list_meal.unit = newValue)"
              labelText="Unit"
              labelClasses="text-right w-full"
            />
            <div class="text-left">
              <InputError :message="form.errors['food_list_meals.' + idx + '.unit_id']" />
              <InputError :message="form.errors['food_list_meals.' + idx]" />
            </div>
          </div>
          <div class="col-span-2 sm:col-span-1 mt-0.5 sm:mt-1">
            <InputLabel :for="'food-list-meal-delete-' + idx" class="sr-only" value="Delete" />
            <!-- mt-5 makes up for lack of visible label -->
            <PlainButton
              type="button"
              :id="'food-list-meal-delete-' + idx"
              @click="deleteFoodListMeal(idx)"
              class="!block mt-5 h-[2.6rem] text-gray-600 hover:text-red-700 !px-0 !w-full"
            >
              <TrashIcon class="w-5 h-5 mx-auto block" />
            </PlainButton>
          </div>
        </div>
      </div>

      <!-- New meal button -->
      <div :class="{
        'mt-1': !foodListMeals.length,
        'mt-5': !!foodListMeals.length,
        'sm:mt-4': !!foodListMeals.length,
      }" >
        <PlainButton
          @click="addFoodListMeal"
          class="flex items-center mt-1 text-sm"
        >
          <PlusCircleIcon class="-ml-1 w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add meal</p>
        </PlainButton>
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
