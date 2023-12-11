<script setup>
import { ref, onMounted, computed } from 'vue'
import { useSortable } from '@vueuse/integrations/useSortable'
import { useForm } from '@inertiajs/vue3'
import { round, roundNonZero, gramAmountOfUnit, prepareUnitsForDisplay } from '@/utils/GlobalFunctions.js'
import { PlusCircleIcon, TrashIcon, Bars3Icon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import DensityDialog from './DensityDialog.vue'
import CustomUnitsDialog from './CustomUnitsDialog.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrients: Array,
  nutrient_categories: Array,
  units: Array,
  create: Boolean,
})

const form = useForm({
  id: props.ingredient ? props.ingredient.id : null,
  name: props.ingredient ? props.ingredient.name : "",
  ingredient_category_id: props.ingredient ? props.ingredient.ingredient_category_id : null,
  ingredient_category: props.ingredient ? props.ingredient.ingredient_category : null,
  density_mass_amount: props.ingredient ? props.ingredient.density_mass_amount : null,
  density_mass_unit_id: (props.ingredient && props.ingredient.density_mass_unit) ? props.ingredient.density_mass_unit.id : null,
  density_mass_unit: props.ingredient  ? props.ingredient.density_mass_unit : null,
  density_volume_amount: props.ingredient ? props.ingredient.density_volume_amount : null,
  density_volume_unit_id: (props.ingredient && props.ingredient.density_volume_unit) ? props.ingredient.density_volume_unit.id : null,
  density_volume_unit: props.ingredient ? props.ingredient.density_volume_unit : null,
  custom_units: props.ingredient ? props.ingredient.custom_units.map((custom_unit, idx) => ({
    id: idx + 1,
    custom_unit: custom_unit,
  })) : [],
  // Maps a custom unit's serial id to the unit's array index on last form
  // submit. This is to preserve association with errors sent from backend.
  custom_units_id_to_idx_mapping: {},
  nutrient_content_unit_amount: props.ingredient ? props.ingredient.nutrient_content_unit_amount : 100,
  nutrient_content_unit: props.ingredient ? props.ingredient.nutrient_content_unit : props.units.find(unit => unit.name === 'g'),
  // Reset zero amounts to null, to make it easier for user to fill values in.
  ingredient_nutrients: props.ingredient
    ? props.ingredient.ingredient_nutrients.map((ingredient_nutrient) => {
      if (ingredient_nutrient.amount == 0) ingredient_nutrient.amount = null;
      return ingredient_nutrient
    })
    : props.nutrients.map((nutrient) => ({
      id: null,
      ingredient_id: null,
      nutrient_id: nutrient.id,
      amount: null,
      nutrient: nutrient,
    })),
})

const nameInput = ref(null)

// Ingredient category
function updateSelectedIngredientCategory(newValue) {
  form.ingredient_category = newValue
  form.ingredient_category_id = newValue.id
}

// Density
const densityDialogRef = ref(null)
function updateDensity(updatedDensityObj) {
  form.density_mass_amount = updatedDensityObj.density_mass_amount
  form.density_mass_unit_id = updatedDensityObj.density_mass_unit_id
  form.density_mass_unit = updatedDensityObj.density_mass_unit
  form.density_volume_amount = updatedDensityObj.density_volume_amount
  form.density_volume_unit_id = updatedDensityObj.density_volume_unit_id
  form.density_volume_unit = updatedDensityObj.density_volume_unit
  updateAllowedNutrientContentUnits()
}
const density = computed(() => {
  return (Number(form.density_mass_amount) > 0 && Number(form.density_volume_amount) > 0)
    ? Number(form.density_mass_amount) / Number(form.density_volume_amount)
    : null
})
const densityGMl = computed(() => {
  return (density.value === null || isNaN(density.value))
    ? null
    : (Number(form.density_mass_amount) * form.density_mass_unit.g)  / (Number(form.density_volume_amount) * form.density_volume_unit.ml )
})

// Using a dedicated object instead of form.custom_units to allow for nested
// object format (used for adding items to list workflow)
const customUnits = ref(
  props.ingredient
    ? props.ingredient.custom_units.map((custom_unit, idx) => ({
      id: idx + 1,
      custom_unit: custom_unit,
    }))
    : []
)
const customUnitsDialogRef = ref(null)
function updateCustomUnits(updatedCustomUnits) {
  customUnits.value = updatedCustomUnits
  updateAllowedNutrientContentUnits()
}

const numCustomUnitErrors = computed(() => {
  const keys = Object.keys(form.errors)
  var n = 0
  for (var i = 0; i < keys.length; i++) {
    if (keys[i].startsWith('custom_units.')) n++;
  }
  return n
})

function getAllowedNutrientContentUnits() {
  // Begin with all mass units
  var allowedUnits = props.units.filter(unit => unit.g);
  // Add volume units for ingredients with a density
  if (form.density_mass_amount && form.density_mass_unit_id && form.density_volume_amount && form.density_volume_unit_id) {
    allowedUnits = allowedUnits.concat(props.units.filter(unit => unit.ml))
  }
  // Add any defined custom units
  allowedUnits = allowedUnits.concat(customUnits.value.map(custom_unit => custom_unit.custom_unit))
  return prepareUnitsForDisplay(allowedUnits, densityGMl.value)
}
const allowedNutrientContentUnits = ref(getAllowedNutrientContentUnits())

// Helper function to check if two units are equal when checking if user is
// using a disallowed nutrientContentUnit.
function unitsAreEqual(a, b) {
  // For existing units
  if (a.id && b.id) return a.id === b.id;
  // For newly created custom units
  else if (a.id === null && b.id === null) {
    return (a.name === b.name && a.custom_unit_amount === b.custom_unit_amount && a.custom_mass_amount === b.custom_mass_amount && a.custom_mass_unit_id === b.custom_mass_unit_id)
  }
  // Comparing a newly-created custom unit to an existing unit
  else return false;
}

function updateAllowedNutrientContentUnits() {
  const newAllowedNutrientContentUnits = getAllowedNutrientContentUnits()

  // Switch back to default 100 grams in the edge case when user had selected a
  // volume unit but just deleted the ingredient's density, or selected a
  // custom unit and then deleted it.
  var match = false
  for (var i = 0; i < newAllowedNutrientContentUnits.length; i++) {
    if (unitsAreEqual(newAllowedNutrientContentUnits[i], form.nutrient_content_unit)) {
      match = true
      break
    }
  }
  if (!match) {
    form.nutrient_content_unit_amount = 100
    form.nutrient_content_unit = props.units.find(unit => unit.name === 'g')
  }

  allowedNutrientContentUnits.value = newAllowedNutrientContentUnits
}

function submit() {
  // Only submit density fields if all density fields are present
  if (form.density_mass_amount === null || form.density_mass_amount === "" || form.density_mass_unit_id === null || form.density_volume_amount === null || form.density_volume_amount === "" || form.density_volume_unit_id === null) {
    form.density_mass_amount = null
    form.density_mass_unit_id = null
    form.density_mass_unit = null
    form.density_volume_amount = null
    form.density_volume_unit_id = null
    form.density_volume_unit = null
  }

  form.custom_units_id_to_idx_mapping = {}
  customUnits.value.forEach((customUnit, idx) => form.custom_units_id_to_idx_mapping[customUnit.id] = idx)
  form.custom_units = customUnits.value.map(custom_unit => custom_unit.custom_unit)

  if (props.create) {
    form.post(route('ingredients.store'))
  } else {
    form.put(route('ingredients.update', props.ingredient.id))
  }
}

onMounted(() => {
  if (props.ingredient === null) nameInput.value.focus()
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

    <InputError class="mt-1" :message="form.errors.id" />

    <section class="mt-4">
      <!-- Name -->
      <div class="w-full max-w-xl">
        <InputLabel for="name" value="Name" />
        <TextInput
          id="name"
          ref="nameInput"
          type="text"
          class="mt-1 inline-block w-full"
          v-model="form.name"
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <!-- Ingredient category -->
      <div class="mt-4">
        <SimpleCombobox
          :options="ingredient_categories"
          labelText="Ingredient category"
          :modelValue="form.ingredient_category"
          @update:modelValue="newValue => updateSelectedIngredientCategory(newValue)"
          inputClasses="w-80"
        />
        <InputError class="mt-2" :message="form.errors.ingredient_category_id" />
      </div>

      <!-- Density and custom units -->
      <div class="mt-6 flex flex-wrap gap-6">
        <!-- Density -->
        <div>
          <InputLabel for="edit-density-button" value="Ingredient density (optional)" />
          <div class="mt-0.5">
            <p v-if="density">
              <span class="font-medium">{{round(density, 2)}} {{form.density_mass_unit.name}}/{{form.density_volume_unit.name}}</span>
              <span v-if="form.density_mass_unit.name !== 'g' || form.density_volume_unit.name !== 'ml'">
                ({{round(densityGMl, 2)}} g/ml)
              </span>
            </p>
            <p v-else>
              No density is set for this ingredient.
            </p>
          </div>

          <PlainButton
            id="edit-density-button"
            @click="densityDialogRef.open()"
            class="inline-flex mt-1"
          >
            <PencilSquareIcon class="-ml-1 w-5 h-5 text-gray-600 shrink-0" />
            <p class="ml-1">Edit density</p>
          </PlainButton>
          <div class="max-w-[16rem]">
            <InputError class="mt-1" :message="form.errors.density_mass_amount" />
            <InputError :message="form.errors.density_mass_unit_id" />
            <InputError :message="form.errors.density_volume_amount" />
            <InputError :message="form.errors.density_volume_unit_id" />
          </div>

          <DensityDialog
            ref="densityDialogRef"
            @confirm="updateDensity"
            :density_mass_amount="form.density_mass_amount"
            :density_mass_unit_id="form.density_mass_unit_id"
            :density_mass_unit="form.density_mass_unit"
            :density_volume_amount="form.density_volume_amount"
            :density_volume_unit_id="form.density_volume_unit_id"
            :density_volume_unit="form.density_volume_unit"
            :units="units"
            :errors="{
              density_mass_amount: form.errors.density_mass_amount,
              density_mass_unit_id: form.errors.density_mass_unit_id,
              density_volume_amount: form.errors.density_volume_amount,
              density_volume_unit_id: form.errors.density_volume_unit_id,
            }"
          />
        </div>

        <!-- Custom units -->
        <div>
          <InputLabel for="edit-custom-units-button" value="Custom units (optional)" />
          <p v-if="customUnits.length === 0">
            No custom units.
          </p>
          <p v-else>
            ({{customUnits.length}} custom unit{{customUnits.length === 1 ? '' : 's'}})
          </p>
          <PlainButton
            id="edit-custom-units-button"
            @click="customUnitsDialogRef.open()"
            class="inline-flex mt-1"
          >
            <PencilSquareIcon class="-ml-1 w-5 h-5 text-gray-600 shrink-0" />
            <p class="ml-1">Edit custom units</p>
          </PlainButton>
          <div class="max-w-[16rem]">
            <InputError class="mt-1" :message="form.errors.custom_units" />
            <InputError v-if="numCustomUnitErrors" :message="'There ' + (numCustomUnitErrors > 1 ? 'are ' : 'is ') + numCustomUnitErrors + ' custom unit error' + (numCustomUnitErrors > 1 ? 's ' : '')  + ' that must be corrected.'" />
          </div>

          <CustomUnitsDialog
            ref="customUnitsDialogRef"
            @confirm="updateCustomUnits"
            :custom_units="customUnits"
            :units="units"
            :errors="Object.fromEntries(Object.entries(form.errors).filter(([key, value]) => key.startsWith('custom_unit')))"
            :id_idx_mapping="form.custom_units_id_to_idx_mapping"
          />

        </div>
      </div>

    </section>

    <!-- Ingredient nutrient table -->
    <section class="mt-10">

      <h2 class="text-xl">Nutrient content</h2>

      <InputError :message="form.errors.ingredient_nutrients" />

      <!-- Ingredient nutrient amount and unit -->
      <div class="mt-2 flex items-baseline">

        <p class="text-md text-gray-600">Per</p>

        <!-- Ingredient nutrient amount -->
        <div class="ml-2 w-24">
          <InputLabel class="sr-only" for="ingredient-nutrient-amount" value="Amount" />
          <TextInput
            id="ingredient-nutrient-amount"
            class="inline-block w-full py-1"
            type="number"
            step="any"
            v-model="form.nutrient_content_unit_amount"
          />
          <InputError class="mt-1" :message="form.errors.nutrient_content_unit_amount" />
        </div>

        <!-- Ingredient nutrient amount unit -->
        <div class="ml-2 w-24">
          <SimpleCombobox
            :options="allowedNutrientContentUnits"
            searchKey="name"
            displayKey="display_name"
            labelText="Unit"
            labelClasses="sr-only"
            inputClasses="py-1 w-24"
            optionsClasses="w-32"
            v-model="form.nutrient_content_unit"
          />
          <InputError class="mt-1" :message="form.errors.nutrient_content_unit" />
        </div>

        <p class="ml-8 pl-0.5 text-md text-gray-600">
          <span v-show="form.nutrient_content_unit && form.nutrient_content_unit.name !== 'g'" class="mr-1">({{roundNonZero(gramAmountOfUnit(form.nutrient_content_unit_amount, form.nutrient_content_unit, densityGMl), 1)}} g)</span>
          <span class="hidden sm:inline">of ingredient</span>
        </p>
      </div>

      <InputError class="mt-1" :message="form.errors.ingredient_nutrients" />

      <div class="mt-3 grid grid-cols-1 lg:flex md:gap-x-8 gap-y-3">
        <div
          v-for="nutrient_category in nutrient_categories"
          :key="nutrient_category.id"
          class="col-span-1"
        >

          <h3 class="text-md">{{nutrient_category.name}}s</h3>

          <div class="border border-gray-300 rounded-xl overflow-hidden w-fit">
            <table class="text-sm sm:text-base text-left">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-3 bg-blue-50">
                    Nutrient
                  </th>
                  <th scope="col" class="px-4 py-3 bg-blue-100">
                    Amount
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ingredient_nutrient in form.ingredient_nutrients.filter(ingredient_nutrient => ingredient_nutrient.nutrient.nutrient_category_id === nutrient_category.id)"
                  class="border-t text-gray-600"
                >
                  <td scope="row" class="px-5 py-2">
                    {{ingredient_nutrient.nutrient.display_name}}
                  </td>
                  <td class="px-4 py-2 text-right">
                    <div class="flex items-baseline">
                      <TextInput
                        type="number"
                        placeholder="0"
                        :min="0"
                        step="any"
                        class="mt-1 inline-block w-24 py-1 text-right"
                        v-model="ingredient_nutrient.amount"
                      />
                      <span class="ml-2">{{ingredient_nutrient.nutrient.unit.name}}</span>
                    </div>
                    <InputError class="mt-2 text-left" :message="form.errors['ingredient_nutrients.' + ingredient_nutrient.nutrient.seq_num + '.id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['ingredient_nutrients.' + ingredient_nutrient.nutrient.seq_num + '.nutrient_id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['ingredient_nutrients.' + ingredient_nutrient.nutrient.seq_num + '.amount']" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Submit buttons -->
    <section class="mt-4">
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        <span v-if="create">Create</span>
        <span v-else>Update</span>
      </PrimaryButton>
      <SecondaryLinkButton
        :href="route('ingredients.index')"
        class="ml-4"
      >
        Cancel
      </SecondaryLinkButton>
    </section>

  </form>

</template>
