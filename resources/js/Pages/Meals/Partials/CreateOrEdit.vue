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
  meal_ingredients: [],  // filled in on submit
})

const mealIngredients = ref(props.meal
  ? props.meal.meal_ingredients.map((meal_ingredient, idx) => ({
    id: idx,
    meal_ingredient: meal_ingredient,
  }))
  : [])

const nameInput = ref(null)

const mealIngredientTableCellsRef = ref([])
var nextId = 1
function addMealIngredient() {
  mealIngredients.value.push({
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
  mealIngredients.value[idx].meal_ingredient.ingredient_id = newIngredient.id
  mealIngredients.value[idx].meal_ingredient.ingredient = newIngredient

  // Reset ingredient's unit (to avoid a lingering unit not supported by the
  // new ingredient) and amount (since we're already reseting unit, let's also
  // reset amount for consisten user experience).
  mealIngredients.value[idx].meal_ingredient.unit_id = props.units.find(unit => unit.name === 'g').id
  mealIngredients.value[idx].meal_ingredient.unit = props.units.find(unit => unit.name === 'g')
  mealIngredients.value[idx].meal_ingredient.amount = null
}

function deleteMealIngredient(idx) {
  if (idx >= 0 && idx < mealIngredients.value.length) mealIngredients.value.splice(idx, 1)
}

function submit() {
  // Drop empty ingredients and unpack nested meal_ingredient object
  form.meal_ingredients = mealIngredients.value.filter(mi => mi.meal_ingredient.ingredient).map(mi => mi.meal_ingredient)
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
      <div>
        <InputLabel for="name" value="Name" />
        <TextInput
          id="name"
          ref="nameInput"
          type="text"
          class="min-w-[28rem] mr-2"
          v-model="form.name"
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

    </section>

    <!-- Meal ingredients table -->
    <section class="mt-6">

      <h2 class="text-lg">Meal ingredients</h2>
      <InputError :message="form.errors.meal_ingredients" />

      <div v-if="mealIngredients.length" class="mt-1 grid grid-cols-16 min-w-[600px] gap-y-1.5 gap-x-1">

        <!-- Header -->
        <p class="col-span-9 px-3 py-3 -mx-1 bg-blue-50">Ingredient</p>
        <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-100 text-right">Amount</p>
        <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-50">Unit</p>
        <p class="col-span-1 px-3 py-3 -mx-1 bg-blue-100" />

        <!-- Meal ingredients -->
        <template
          v-for="(meal_ingredient, idx) in mealIngredients"
          :key="meal_ingredient.id"
        >
          <!-- Ingredient input -->
          <div ref="mealIngredientTableCellsRef" class="col-span-9">
            <FuzzyCombobox
              :options="ingredients"
              :modelValue="meal_ingredient.meal_ingredient.ingredient"
              :showIcon="false"
              @update:modelValue="newValue => updateMealIngredient(idx, newValue)"
            />
            <div class="text-left">
              <InputError :message="form.errors['meal_ingredients.' + idx + '.id']" />
              <InputError :message="form.errors['meal_ingredients.' + idx + '.ingredient_id']" />
            </div>
          </div>
          <!-- Amount input -->
          <div class="col-span-3 text-right">
            <div class="w-full ml-auto">
              <TextInput
                type="number"
                placeholder="0"
                class="w-full text-right"
                step="any"
                v-model="meal_ingredient.meal_ingredient.amount"
                required
              />
              <InputError class="text-left" :message="form.errors['meal_ingredients.' + idx + '.amount']" />
            </div>
          </div>
          <!-- Unit combobox -->
          <div class="col-span-3 text-right">
            <SimpleCombobox
              :options="units.filter(unit => unit.g || (unit.ml && (meal_ingredient.meal_ingredient.ingredient && meal_ingredient.meal_ingredient.ingredient.density_g_ml))).concat(meal_ingredient.meal_ingredient.ingredient.custom_units ? meal_ingredient.meal_ingredient.ingredient.custom_units : [])"
              :modelValue="meal_ingredient.meal_ingredient.unit"
              @update:modelValue="newValue => (meal_ingredient.meal_ingredient.unit_id = newValue.id, meal_ingredient.meal_ingredient.unit = newValue)"
              optionsClasses=""
            />
            <div class="text-left">
              <InputError :message="form.errors['meal_ingredients.' + idx + '.unit_id']" />
              <InputError :message="form.errors['meal_ingredients.' + idx]" />
            </div>
          </div>
          <div class="col-span-1 place-self-center">
            <button
              type="button"
              @click="deleteMealIngredient(idx)"
              class="text-gray-600 hover:text-red-700"
            >
              <TrashIcon class="w-6 h-6" />
            </button>
          </div>
        </template>
      </div>

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
