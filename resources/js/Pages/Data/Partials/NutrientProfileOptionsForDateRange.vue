<script setup>
import { ref, computed } from 'vue'
import { getHumanReadableDate } from '@/utils/GlobalFunctions.js'
import { Popover, PopoverButton, PopoverPanel, PopoverOverlay } from '@headlessui/vue'
import { EllipsisHorizontalCircleIcon } from '@heroicons/vue/24/outline'
import PlainButton from '@/Components/PlainButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'

const props = defineProps({
  selectedIntakeGuideline: Object,
  intakeGuidelines: Array,
  fromDate: String,
  toDate: String,
})

const emit = defineEmits([
  'update:selectedIntakeGuideline',
])

const localSelectedIntakeGuideline = ref(props.selectedIntakeGuideline)
function updateSelectedIntakeGuideline(newValue) {
  localSelectedIntakeGuideline.value = newValue
  emit('update:selectedIntakeGuideline', newValue)
}

</script>

<template>
  <div class="flex">

    <p class="max-w-sm md:max-w-md text-gray-700 leading-snug">
      Average daily nutrient profile for food consumed from <span class="font-semibold">{{getHumanReadableDate(fromDate)}}</span> to <span class="font-semibold">{{getHumanReadableDate(toDate)}}</span>, using <span class="font-semibold">{{localSelectedIntakeGuideline.name}}</span> to compute percent daily value.
    </p>

    <Popover class="relative z-50">
      <PopoverButton class="ml-1 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
        <EllipsisHorizontalCircleIcon class="-ml-1 w-6 h-6 text-gray-600 shrink-0" />
        <p class="ml-2">Adjust</p>
      </PopoverButton>
      <PopoverOverlay class="fixed inset-0 bg-black opacity-20" />

      <PopoverPanel v-slot="{close}" class="absolute right-0 translate-y-1 z-10 px-6 py-5 bg-white rounded-lg border border-gray-300 shadow">

        <SimpleCombobox
          labelText="Intake Guideline for % DV"
          searchKey="name"
          class="w-64 mr-4"
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
