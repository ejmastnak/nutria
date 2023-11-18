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
  meal: Object,
  ingredients: Array,
  units: Array,
  create: Boolean
})

const form = useForm({
  id: props.meal ? props.meal.id : null,
  name: props.meal ? props.meal.name : "",
  meal_ingredients: props.meal ? props.meal.meal_ingredients.map((meal_ingredient, idx) => ({
    id: idx,
    meal_ingredient: meal_ingredient,
  })) : []
})

const nameInput = ref(null)

const mealIngredientTableCellsRef = ref([])
var nextId = 1
function addMealIngredient() {
  form.meal_ingredients.push({
    id: nextId,
    meal_ingredient: {
      "id": null,
      "meal_id": null,
      "ingredient_id": null,
      "ingredient": {},
      "amount": null,
      "unit_id": props.units.find(unit => unit.name === 'g').id,
      "unit": props.units.find(unit => unit.name === 'g')
    }
  });

  // Focus text input for name of just-added empty meal ingredient
  // Use timeout to give time for new table row to be injected into DOM
  setTimeout(() => {
    const input = mealIngredientTableCellsRef.value[mealIngredientTableCellsRef.value.length - 1].querySelectorAll('input')[0];
    if (input) input.focus();
  }, 0)

  nextId += 1;
}

function updateMealIngredient(idx, newIngredient) {
  form.meal_ingredients[idx].meal_ingredient.ingredient_id = newIngredient.id
  form.meal_ingredients[idx].meal_ingredient.ingredient = newIngredient

  // Reset ingredient's unit (to avoid a lingering unit not supported by the
  // new ingredient) and amount (since we're already reseting unit, let's also
  // reset amount for consisten user experience).
  form.meal_ingredients[idx].meal_ingredient.unit_id = props.units.find(unit => unit.name === 'g').id
  form.meal_ingredients[idx].meal_ingredient.unit = props.units.find(unit => unit.name === 'g')
  form.meal_ingredients[idx].meal_ingredient.amount = null
}

function deleteMealIngredient(idx) {
  if (idx >= 0 && idx < form.meal_ingredients.length) form.meal_ingredients.splice(idx, 1)
}

function submit() {
  // Drop empty ingredients and unpack nested meal_ingredient object
  form.meal_ingredients = form.meal_ingredients.filter(mi => mi.meal_ingredient.ingredient).map(mi => mi.meal_ingredient)
  if (props.create) {
    form.post(route('meals.store'))
  } else {
    form.put(route('meals.update', props.meal.id))
  }
}

onMounted(() => {
  if (props.meal === null) {
    nameInput.value.focus()
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
            v-for="(meal_ingredient, idx) in form.meal_ingredients"
            :key="meal_ingredient.id"
            class="border-t text-gray-600"
          >
            <td ref="mealIngredientTableCellsRef" scope="row" class="px-5 py-2">
              <FuzzyCombobox
                :options="ingredients"
                :modelValue="meal_ingredient.meal_ingredient.ingredient"
                :showIcon="false"
                @update:modelValue="newValue => updateMealIngredient(idx, newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + idx + '.id']" />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + idx + '.ingredient_id']" />
            </td>
            <td class="px-4 py-2 text-right">
              <div class="w-fit ml-auto">
                <TextInput
                  type="number"
                  placeholder="0"
                  class="mt-1 block w-24 py-1 text-right"
                  v-model="meal_ingredient.meal_ingredient.amount"
                  required
                />
                <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + idx + '.amount']" />
              </div>
            </td>
            <td class="px-4 py-2 text-right">
              <SimpleCombobox
                :options="units.filter(unit => unit.g || (unit.ml && (meal_ingredient.meal_ingredient.ingredient && meal_ingredient.meal_ingredient.ingredient.density_g_ml))).concat(meal_ingredient.meal_ingredient.ingredient.custom_units)"
                :modelValue="meal_ingredient.meal_ingredient.unit"
                @update:modelValue="newValue => (meal_ingredient.meal_ingredient.unit_id = newValue.id, meal_ingredient.meal_ingredient.unit = newValue)"
              />
              <InputError class="mt-2 text-left" :message="form.errors['meal_ingredients.' + idx + '.unit_id']" />
            </td>
            <td class="px-4 py-2">
              <button
                type="button"
                @click="deleteMealIngredient(idx)"
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

      <pre>
        {{form}}
      </pre>

    </section>

  </form>

</template>
