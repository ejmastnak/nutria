<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import SimpleCombobox from '@/Shared/SimpleCombobox.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import { PlusCircleIcon, TrashIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import IngredientNutrientTable from './IngredientNutrientTable.vue'

const props = defineProps({
  meal: Object,
  ingredients: Array,
  ingredient_categories: Array,
  units: Array,
  create: Boolean
})

const gramIdx = props.units.map(unit => unit.name).indexOf("g")
const gram = props.units[gramIdx]
const searchIngredient = ref({})
const form = useForm({
  name: props.meal ? props.meal.name : "",
  meal_ingredients: props.meal ? props.meal.meal_ingredients : []
})

var nextID = -1
function addIngredient() {
  form.meal_ingredients.push({
        "id": nextID,
        "meal_id": 0,
        "ingredient_id": 0,
        "amount": "0.0",
        "unit_id": 0,
        "ingredient": {
          "id": 0,
          "name": "",
          "density_g_per_ml": null
        },
        "unit": gram
  });
  nextID -= 1;
}

function updateMealIngredient(mealIngredient, newIngredient) {
  // If switching from an ingredient with well-defined density and amount
  // specified in units of volume to an ingredient that does not have density,
  // reset ingredient's amount and unit to non-volume units.
  if (mealIngredient.unit.is_volume && mealIngredient.ingredient.density_g_per_ml && !newIngredient.density_g_per_ml) {
    mealIngredient.ingredient = newIngredient
    mealIngredient.amount = '0.0'
    mealIngredient.unit = gram
  } else {
    mealIngredient.ingredient = newIngredient
  }
}

function deleteMealIngredient(id) {
  const idx = form.meal_ingredients.map(mi => mi.id).indexOf(id);
  form.meal_ingredients.splice(idx, 1);
}

function submit() {
  if (props.create) {
    alert("Create!");
    // form.post(route('meals.store'))
  } else {
    // form.put(route('meals.update', props.meal.id))
    alert("Update!");
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
            </td>
            <td class="px-4 py-2 text-right">
              <TextInput
                type="text"
                class="mt-1 block w-24 py-1 text-right"
                v-model="meal_ingredient.amount"
                required
              />
              <!-- TODO: input errors for individual meal ingredients -->
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="units.filter(unit => unit.is_mass || (meal_ingredient.ingredient.density_g_per_ml && unit.is_volume))"
                :modelValue="meal_ingredient.unit"
                @update:modelValue="newValue => meal_ingredient.unit = newValue"
              />
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
          @click="addIngredient"
          class="flex items-center mt-1 normal-case !font-normal !text-sm !px-2"
        >
          <PlusCircleIcon class="w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add ingredient</p>
        </SecondaryButton>

      </div>

    </section>

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

    <pre>
      {{form.name}}
    </pre>
    <pre>
      {{form.meal_ingredients}}
    </pre>

  </form>


</template>
