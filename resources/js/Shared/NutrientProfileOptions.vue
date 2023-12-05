<script setup>
import { ref, computed } from 'vue'
import { round, gramAmountOfUnit } from '@/utils/GlobalFunctions.js'
import { Popover, PopoverButton, PopoverPanel, PopoverOverlay } from '@headlessui/vue'
import { EllipsisHorizontalCircleIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import PlainButton from '@/Components/PlainButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  selectedUnit: Object,
  selectedUnitAmount: [Number, String],
  selectedIntakeGuideline: Object,
  defaultUnit: Object,
  defaultUnitAmount: Number,
  densityGMl: Number,
  intakeGuidelines: Array,
  units: Array,
  thing: String,  // "ingredient", "meal", or "foodList"
})

const emit = defineEmits([
  'update:selectedUnit',
  'update:selectedUnitAmount',
  'update:selectedIntakeGuideline',
])

const localSelectedUnit = ref(props.selectedUnit)
function updateSelectedUnit(newValue) {
  localSelectedUnit.value = newValue

  // Switch to unit amount if switching to a meal unit
  if (newValue.meal_id) updateSelectedUnitAmount(1);

  emit('update:selectedUnit', newValue)
}

const localSelectedUnitAmount = ref(props.selectedUnitAmount)
function updateSelectedUnitAmount(newValue) {
  localSelectedUnitAmount.value = newValue
  emit('update:selectedUnitAmount', newValue)
}

const localSelectedIntakeGuideline = ref(props.selectedIntakeGuideline)
function updateSelectedIntakeGuideline(newValue) {
  localSelectedIntakeGuideline.value = newValue
  emit('update:selectedIntakeGuideline', newValue)
}

function resetToDefaultAmountAndUnit() {
  if (props.thing === 'ingredient') {
    resetTo100G()
  } else {
    updateSelectedUnitAmount(props.defaultUnitAmount)
    updateSelectedUnit(props.defaultUnit)
  }
}

function resetTo100G() {
  const gram = props.units.find(unit => unit.name === 'g')
  if (gram) {
      updateSelectedUnitAmount(100)
      updateSelectedUnit(gram)
  }
}

</script>

<template>
  <div class="flex">

    <p class="w-80 text-gray-700 leading-snug">
      Displayed for <span class="font-bold">{{localSelectedUnitAmount}} {{localSelectedUnit.name}}</span>, using {{localSelectedIntakeGuideline.name}} to compute percent daily value.
    </p>

    <Popover class="relative z-50">
      <PopoverButton class="ml-1 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
        <EllipsisHorizontalCircleIcon class="-ml-1 w-6 h-6 text-gray-600 shrink-0" />
        <p class="ml-2">Adjust</p>
      </PopoverButton>
      <PopoverOverlay class="fixed inset-0 bg-black opacity-20" />

      <PopoverPanel v-slot="{close}" class="absolute right-0 translate-y-1 z-10 px-6 py-5 bg-white rounded-lg border border-gray-300 shadow">

        <p class="font-medium text-sm text-gray-600">Amount of ingredient</p>
        <div class="flex items-baseline">
          <div>
            <InputLabel for="selectedUnitAmountInput" value="Amount" class="sr-only" />
            <TextInput
              class="w-24 py-1.5"
              id="selectedUnitAmountInput"
              type="number"
              :value="localSelectedUnitAmount"
              @update:modelValue="newValue => updateSelectedUnitAmount(newValue)"
            />
          </div>

          <SimpleCombobox
            class="ml-2"
            inputClasses="py-1.5 !w-24"
            optionsClasses="w-32"
            labelText="Unit"
            labelClasses="sr-only"
            searchKey="name"
            displayKey="display_name"
            :options="units"
            :modelValue="localSelectedUnit"
            @update:modelValue="newValue => updateSelectedUnit(newValue)"
          />

          <!-- Gram equivalent of selected (amount, unit) combo -->
          <p v-if="localSelectedUnit && localSelectedUnit.name !== 'g'" class="ml-2 text-gray-600 whitespace-nowrap">
            ({{round(gramAmountOfUnit(localSelectedUnitAmount, localSelectedUnit, densityGMl))}} g total)
          </p>
        </div>

        <PlainButton class="mt-1 text-sm !text-gray-600" @click="resetToDefaultAmountAndUnit">
          <ArrowPathIcon class="-ml-1 w-5 h-5 text-gray-500 shrink-0"/>
          <p class="ml-1" v-if="thing === 'ingredient'">Reset to 100 g</p>
          <p class="ml-1" v-if="thing === 'meal'">Reset to 1 meal</p>
          <p class="ml-1" v-if="thing === 'foodList'">Reset to 1 food list</p>
        </PlainButton>

        <SimpleCombobox
          labelText="Intake Guideline for % DV"
          searchKey="name"
          class="mt-3 w-64"
          :options="intakeGuidelines"
          :modelValue="localSelectedIntakeGuideline"
          @update:modelValue="newValue => updateSelectedIntakeGuideline(newValue)"
        />

        <div class="flex">
          <SecondaryButton @click="close" class="mt-4">
            Close
          </SecondaryButton>
        </div>

      </PopoverPanel>

    </Popover>

  </div>
</template>



