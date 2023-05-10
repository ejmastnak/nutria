<script setup>
import { ref, onMounted } from 'vue'
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
  meal: Object,
  ingredients: Array,
  ingredient_categories: Array,
  units: Array,
  create: Boolean
})

const gramIdx = props.units.map(unit => unit.name).indexOf("g")
const gram = props.units[gramIdx]
const form = useForm({
  name: props.create ? "" : props.meal.name,
  meal_ingredients: props.meal ? props.meal.meal_ingredients.map((meal_ingredient, index) => ({
    idx: index,
    id: meal_ingredient.id,
    meal_id: meal_ingredient.meal_id,
    ingredient_id: meal_ingredient.ingredient_id,
    amount: round(Number(meal_ingredient.amount), 1).toString(),
    unit_id: meal_ingredient.unit_id,
    ingredient: meal_ingredient.ingredient,
    unit: meal_ingredient.unit
  })) : []
})

const nameInput = ref(null)
onMounted(() => {
  if (props.create) {
    nameInput.value.focus()
  }
})


var nextMealIngredientID = -1
function addMealIngredient() {
  form.meal_ingredients.push({
    "idx": form.meal_ingredients.length,
    "id": nextMealIngredientID,
    "meal_id": 0,
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
  nextMealIngredientID -= 1;
}

function updateMealIngredient(mealIngredient, newIngredient) {
  // If switching from an ingredient with well-defined density and amount
  // specified in units of volume to an ingredient that does not have density,
  // reset ingredient's amount and unit to non-volume units.
  if (mealIngredient.unit.is_volume && mealIngredient.ingredient.density_g_per_ml && !newIngredient.density_g_per_ml) {
    mealIngredient.ingredient_id = newIngredient.id
    mealIngredient.ingredient = newIngredient
    mealIngredient.amount = '0.0'
    mealIngredient.unit = gram
  } else {
    mealIngredient.ingredient_id = newIngredient.id
    mealIngredient.ingredient = newIngredient
  }
}

function deleteMealIngredient(id) {
  const idx = form.meal_ingredients.map(mi => mi.id).indexOf(id);
  form.meal_ingredients.splice(idx, 1);
  // Eager update of meal ingredient indices (which are needed to associate
  // individual meal ingredients with validation error messages)
  form.meal_ingredients.forEach((meal_ingredient, idx) => meal_ingredient.idx = idx);
}

function submit() {
  if (props.create) {
    form.post(route('meals.store'))
  } else {
    form.put(route('meals.update', props.meal.id))
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

    <section class="mt-4">
      <!-- Name -->
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

    <!-- Meal ingredients table -->
    <section class="mt-8 p-4 border border-gray-300 shadow-sm rounded-xl">

      <h2 class="text-lg">Meal ingredients</h2>

      <InputError :message="form.errors.meal_ingredients" />

      <table v-if="form.meal_ingredients.length" class="mt-2 text-sm sm:text-base text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50">
              Ingredient
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
              Amount
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-50">
              Unit
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="meal_ingredient in form.meal_ingredients"
            class="border-t text-gray-600"
          >
            <td scope="row" class="px-5 py-2">
              <FuzzyCombobox
                :options="ingredients"
                :modelValue="meal_ingredient.ingredient"
                @update:modelValue="newValue => updateMealIngredient(meal_ingredient, newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + meal_ingredient.idx + '.ingredient_id']" />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + meal_ingredient.idx + '.id']" />
            </td>
            <td class="px-4 py-2 text-right">
              <TextInput
                type="text"
                class="mt-1 block w-24 py-1 text-right"
                v-model="meal_ingredient.amount"
                required
              />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + meal_ingredient.idx + '.amount']" />
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="units.filter(unit => unit.is_mass || (meal_ingredient.ingredient.density_g_per_ml && unit.is_volume))"
                :modelValue="meal_ingredient.unit"
                @update:modelValue="newValue => (meal_ingredient.unit_id = newValue.id, meal_ingredient.unit = newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + meal_ingredient.idx + '.unit_id']" />
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                @click="deleteMealIngredient(meal_ingredient.id)"
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
          @click="addMealIngredient"
          class="flex items-center mt-1 normal-case !font-normal !text-sm !px-2"
        >
          <PlusCircleIcon class="w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add ingredient</p>
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
        :href="route('meals.index')"
        class="ml-4"
      >
        Cancel
      </SecondaryLinkButton>

    </section>

  </form>

</template>
