<script setup>
import { ref, computed } from 'vue'
import { round, gramAmountOfUnit } from '@/utils/GlobalFunctions.js'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { EllipsisHorizontalCircleIcon } from '@heroicons/vue/24/outline'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  selectedUnit: Object,
  selectedUnitAmount: [Number, String],
  selectedIntakeGuideline: Object,
  densityGMl: Number,
  intakeGuidelines: Array,
  units: Array,
})

const emit = defineEmits([
  'update:selectedUnit',
  'update:selectedUnitAmount',
  'update:selectedIntakeGuideline',
])

const localSelectedUnit = ref(props.selectedUnit)
function updateSelectedUnit(newValue) {
  localSelectedUnit.value = newValue
  emit('update:selectedUnit', newValue)
}

const localSelectedIntakeGuideline = ref(props.selectedIntakeGuideline)
function updateSelectedIntakeGuideline(newValue) {
  localSelectedIntakeGuideline.value = newValue
  emit('update:selectedIntakeGuideline', newValue)
}

</script>

<template>
  <div class="">

    <div class="flex items-baseline mb-4">
      <div>
        <InputLabel for="selectedUnitAmountInput" value="Amount" class="sr-only" />
        <TextInput
          class="w-24 py-1.5"
          id="selectedUnitAmountInput"
          type="number"
          :value="selectedUnitAmount"
          @input="$emit('update:selectedUnitAmount', $event.target.value)"
        />
      </div>

      <SimpleCombobox
        class="ml-2 w-32"
        inputClasses="py-1.5"
        labelText="Unit"
        labelClasses="sr-only"
        searchKey="name"
        displayKey="display_name"
        :options="units"
        :modelValue="localSelectedUnit"
        @update:modelValue="newValue => updateSelectedUnit(newValue)"
      />

      <!-- Gram equivalent of selected (amount, unit) combo -->
      <p v-if="localSelectedUnit && localSelectedUnit.name !== 'g'" class="ml-2 text-gray-600">
        ({{round(gramAmountOfUnit(selectedUnitAmount, localSelectedUnit, densityGMl))}} g total)
      </p>

    </div>

    <SimpleCombobox
      labelText="Intake Guideline for % DV"
      searchKey="name"
      class="w-64"
      :options="intakeGuidelines"
      :modelValue="localSelectedIntakeGuideline"
      @update:modelValue="newValue => updateSelectedIntakeGuideline(newValue)"
    />

  </div>
</template>



