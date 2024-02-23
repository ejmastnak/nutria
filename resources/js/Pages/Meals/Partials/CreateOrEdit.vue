<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { round, currentLocalDate, currentLocalTime, localTimestampToUtcTimestamp } from '@/utils/GlobalFunctions.js'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import { PlusCircleIcon, PencilSquareIcon, TrashIcon, CalendarIcon, ClockIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  meal: Object,
  ingredients: Array,
  units: Array,
  create: Boolean,
  log: Boolean,
  editing_logged: Boolean,
})

const form = useForm({
  id: props.meal ? props.meal.id : null,
  name: props.meal ? props.meal.name : "",
  description: props.meal ? props.meal.description : null,
  meal_ingredients: [],  // filled in on submit
  // For createAndLog
  date: currentLocalDate(),
  time: currentLocalTime(),
  date_time_utc: null,  // filled in on submit
})

const showDescription = ref(props.meal ? (!!props.meal.description) : false)
const descriptionInputRef = ref(null)
function toggleDescription() {
  if (!showDescription.value) descriptionInputRef.value.focus();
  showDescription.value = !showDescription.value;
}

const mealIngredients = ref(props.meal
  ? props.meal.meal_ingredients.map((meal_ingredient, idx) => ({
    id: idx + 1,
    meal_ingredient: meal_ingredient,
  }))
  : [])

const nameInputRef = ref(null)

var nextId = props.meal ? props.meal.meal_ingredients.length + 1 : 1
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
  // Use timeout to give time for new element to be injected into DOM
  setTimeout(() => {
    const wrapper = document.getElementById('meal-ingredient-input-wrapper-' + nextId)
    if (wrapper) {
      const input = wrapper.querySelectorAll('input')[0];
      if (input) input.focus();
    }
    nextId += 1;
  }, 0)

}

function updateMealIngredient(idx, newIngredient) {
  mealIngredients.value[idx].meal_ingredient.ingredient_id = newIngredient.id
  mealIngredients.value[idx].meal_ingredient.ingredient = newIngredient

  // Reset ingredient's unit if old unit is not supported by new ingredient.
  const newUnits = props.units.filter(unit => unit.g || (unit.ml && newIngredient.density_g_ml)).concat(newIngredient.custom_units ? newIngredient.custom_units : [])
  if (!newUnits.map(unit => unit.id).includes(mealIngredients.value[idx].meal_ingredient.unit_id)) {
    mealIngredients.value[idx].meal_ingredient.unit_id = props.units.find(unit => unit.name === 'g').id
    mealIngredients.value[idx].meal_ingredient.unit = props.units.find(unit => unit.name === 'g')
    mealIngredients.value[idx].meal_ingredient.amount = null
  }

  // Focus ingredient amount input for convenience
  setTimeout(() => {
    const wrapper = document.getElementById('meal-ingredient-amount-wrapper-' + idx)
    if (wrapper) {
      const input = wrapper.querySelectorAll('input')[0];
      if (input) input.focus();
    }
  }, 0);

}

function deleteMealIngredient(idx) {
  if (idx >= 0 && idx < mealIngredients.value.length) mealIngredients.value.splice(idx, 1)
}

const mealNameBeginsWithDate = computed(() => {
  const regexp = new RegExp("^[0-9]{4}-[0-9]{2}-[0-9]{2}");
  return regexp.test(form.name.trim())
})

function prependDateToMealName() {
  if (mealNameBeginsWithDate.value) {
    const regexp = new RegExp("^[0-9]{4}-[0-9]{2}-[0-9]{2}");
    form.name = form.name.trim().replace(regexp, "").trim()
  } else {
    form.name = currentLocalDate() + " " + form.name.trim()
    nameInputRef.value.focus()
  }
}

function submit() {
  // Drop empty ingredients and unpack nested meal_ingredient object
  form.meal_ingredients = mealIngredients.value.filter(mi => mi.meal_ingredient.ingredient).map(mi => mi.meal_ingredient)
  form.date_time_utc = localTimestampToUtcTimestamp(form.date + " " + form.time)

  if (props.create) {
    if (props.log) form.post(route('meals.store-and-log'));
    else form.post(route('meals.store'));
  } else {
    if (props.editing_logged) form.put(route('meals.update-logged', props.meal.id));
    else form.put(route('meals.update', props.meal.id));
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
          <SecondaryButton @click="prependDateToMealName">
            <p v-if="!mealNameBeginsWithDate">Prepend today's date</p>
            <p v-else>Strip leading date</p>
          </SecondaryButton>
        </div>
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <div v-if="log" class="">

        <!-- Date -->
        <div class="mt-4 flex items-end">
          <div class="w-40">
            <InputLabel for="date" value="Date" />
            <TextInput
              id="date"
              class="w-full bg-white"
              type="date"
              v-model="form.date"
              required
            />
            <InputError :message="form.errors.date" />
          </div>
          <SecondaryButton @click="form.date = currentLocalDate()" class="ml-2 h-fit">
            <CalendarIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
            <p class="ml-1">Today</p>
          </SecondaryButton>
        </div>

        <!-- Time -->
        <div class="mt-3 flex items-end">
          <div class="w-40">
            <InputLabel for="time" value="Time" />
            <TextInput
              id="time"
              class="w-full bg-white"
              type="time"
              step="60"
              v-model="form.time"
            />
            <InputError :message="form.errors.time" />
          </div>
          <SecondaryButton @click="form.time = currentLocalTime()" class="ml-2 h-fit">
            <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
            <p class="ml-1">Now</p>
          </SecondaryButton>
        </div>

        <div class="mt-1">
          <InputError :message="form.errors.date_time_utc" />
        </div>
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

    <!-- Meal ingredients table -->
    <section class="mt-6">

      <h2 class="text-lg">Meal ingredients</h2>
      <InputError :message="form.errors.meal_ingredients" />
      <div v-if="mealIngredients.length" class="mt-1 sm:min-w-[600px] gap-y-6 sm:gap-y-1.5 grid">

        <!-- Header -->
        <div class="hidden sm:grid sm:grid-cols-16 sm:gap-x-1 border">
          <p class="col-span-9 px-3 py-3 -mx-1 bg-blue-50">Ingredient</p>
          <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-100 text-right">Amount</p>
          <p class="col-span-3 px-3 py-3 -mx-1 bg-blue-50">Unit</p>
          <p class="col-span-1 px-3 py-3 -mx-1 bg-blue-100" />
        </div>

        <!-- Meal ingredients -->
        <div
          v-for="(meal_ingredient, idx) in mealIngredients"
          :key="meal_ingredient.id"
          class="grid grid-cols-16 gap-x-1 bg-gray-50 -mx-1 px-2 py-1 rounded border border-gray-100"
        >
          <!-- Ingredient input -->
          <div :id="'meal-ingredient-input-wrapper-' + meal_ingredient.id" class="col-span-16 sm:col-span-9">
            <FuzzyCombobox
              :targets="ingredients"
              :modelValue="meal_ingredient.meal_ingredient.ingredient"
              :showIcon="false"
              :fuzzyLimit="6"
              labelText="Ingredient"
              @update:modelValue="newValue => updateMealIngredient(idx, newValue)"
            />
            <div class="text-left">
              <InputError :message="form.errors['meal_ingredients.' + idx + '.id']" />
              <InputError :message="form.errors['meal_ingredients.' + idx + '.ingredient_id']" />
            </div>
          </div>
          <!-- Amount input -->
          <div class="col-span-6 sm:col-span-3 text-right -mt-0.5 sm:mt-0">
            <!-- mt-1 needed to align labels and inputs with comboboxes -->
            <div :id="'meal-ingredient-amount-wrapper-' + idx" class="w-full ml-auto mt-1">
              <InputLabel :for="'meal-ingredient-amount-input-' + idx" value="Amount" />
              <TextInput
                type="number"
                :id="'meal-ingredient-amount-input-' + idx"
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
          <div class="col-span-8 sm:col-span-3 text-right -mt-0.5 sm:mt-0">
            <SimpleCombobox
              :options="units.filter(unit => unit.g || (unit.ml && (meal_ingredient.meal_ingredient.ingredient && meal_ingredient.meal_ingredient.ingredient.density_g_ml))).concat(meal_ingredient.meal_ingredient.ingredient.custom_units ? meal_ingredient.meal_ingredient.ingredient.custom_units : [])"
              :modelValue="meal_ingredient.meal_ingredient.unit"
              @update:modelValue="newValue => (meal_ingredient.meal_ingredient.unit_id = newValue.id, meal_ingredient.meal_ingredient.unit = newValue)"
              labelText="Unit"
              labelClasses="text-right w-full"
            />
            <div class="text-left">
              <InputError :message="form.errors['meal_ingredients.' + idx + '.unit_id']" />
              <InputError :message="form.errors['meal_ingredients.' + idx]" />
            </div>
          </div>
          <div class="col-span-2 sm:col-span-1 mt-0.5 sm:mt-1">
            <InputLabel :for="'meal-ingredient-delete-' + idx" class="sr-only" value="Delete" />
            <!-- mt-5 makes up for lack of visible label -->
            <PlainButton
              type="button"
              :id="'meal-ingredient-delete-' + idx"
              @click="deleteMealIngredient(idx)"
              class="!block mt-5 h-[2.6rem] text-gray-600 hover:text-red-700 !px-0 !w-full"
            >
              <TrashIcon class="w-5 h-5 mx-auto block" />
            </PlainButton>
          </div>
        </div>
      </div>

      <!-- New ingredient button -->
      <div :class="{
        'mt-1': !mealIngredients.length,
        'mt-5': !!mealIngredients.length,
        'sm:mt-4': !!mealIngredients.length,
      }" >
        <PlainButton
          @click="addMealIngredient"
          class="flex items-center mt-1 text-sm"
        >
          <PlusCircleIcon class="-ml-1 w-5 h-5 text-gray-500" />
          <p class="ml-1.5 whitespace-nowrap">Add ingredient</p>
        </PlainButton>
      </div>

    </section>

    <!-- Submit/cancel buttons -->
    <section class="mt-8">

      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        <span v-if="create">Create <span v-if="log">and log</span></span>
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
