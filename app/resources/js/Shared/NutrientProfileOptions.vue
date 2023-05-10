<script setup>
import { ref, computed } from 'vue'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { EllipsisHorizontalCircleIcon } from '@heroicons/vue/24/outline'
import SimpleCombobox from '@/Shared/SimpleCombobox.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  intake_guidelines: Array,
  howManyGrams: String,
  selectedIntakeGuideline: Object,
  displayMassInput: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:howManyGrams', 'update:selectedIntakeGuideline'])

// Using a local selected intake guideline as a small hack to get around
// SimpleCombobox only supporting modelValue and not decoupled :value and
// @update like a simple <input/> component
const localSelectedIntakeGuideline = ref(props.selectedIntakeGuideline)
function updatedSelectedIntakeGuideline(newValue) {
  localSelectedIntakeGuideline.value = newValue
  emit('update:selectedIntakeGuideline', newValue)
}

</script>

<template>
  <Popover class="relative">

    <PopoverButton class="-ml-2 px-2 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700">
      <div class="flex items-center">
        <EllipsisHorizontalCircleIcon class="w-5 h-5 text-gray-600 mr-0.5" />
        <p class="text-gray-700">Options</p>
      </div>
    </PopoverButton>

    <PopoverPanel class="absolute z-10 p-4 border border-gray-200 shadow-sm rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-700">

      <div class="flex flex-col w-full whitespace-nowrap text-gray-900">

        <!-- Mass -->
        <div v-if="displayMassInput" class="flex items-baseline mb-4">
          <p class="mr-1">For</p>
          <div>
            <InputLabel for="howManyGramsInput" value="Mass" class="sr-only" />
            <input
              id="howManyGramsInput"
              type="number"
              min="0"
              class="text-right w-20 py-px pl-1 pr-px border-gray-300 focus:border-blue-500 focus:ring-blue-500 focus:border-1 focus:ring-0 rounded-md shadow-sm"
              :value="howManyGrams"
              @input="$emit('update:howManyGrams', $event.target.value)"
            />
          </div>
          <p class="ml-1">grams</p>
        </div>

        <!-- Intake Guideline -->
        <div class="w-fit">
          <SimpleCombobox
            comboboxInputClasses="py-px"
            labelText="Intake Guideline for % DV"
            :options="intake_guidelines"
            :modelValue="localSelectedIntakeGuideline"
            @update:modelValue="newValue => updatedSelectedIntakeGuideline(newValue)"
          />
        </div>

      </div>

    </PopoverPanel>
  </Popover>
</template>
