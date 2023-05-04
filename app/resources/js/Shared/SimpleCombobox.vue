<!--
https://headlessui.com/vue/combobox
A simple combobox with single select and a dropdown ComboboxButton.
Intended for use with a small list of options, e.g. to select ingredient
category when creating ingredients.
Supports basic case-insensitive search over options by name.
Input: an array of objects with `id` and `name` keys.
Output: the selected object via modelValue.
-->

<script setup>
import { ref, computed } from 'vue'
import InputLabel from '@/Components/InputLabel.vue'
import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/24/outline'
import {
  Combobox,
  ComboboxLabel,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
} from '@headlessui/vue'

const props = defineProps({
  options: Array,
  labelText: String,
  modelValue: Object,
  comboboxInputClasses: {
    type: String,
    default: ""
  }
})

const emit = defineEmits([
  'update:modelValue',
])

const selectedValue = computed({
  get() {
    return props.modelValue;
  },
  set(option) {
    emit('update:modelValue', option)
  }
})

const query = ref('')

const filteredOptions = computed(() =>
  query.value.trim() === ''
    ? props.options
    : props.options.filter((option) => {
      return option.name.toLowerCase().includes(query.value.toLowerCase())
    })
)

</script>

<template>
  <Combobox v-model="selectedValue">

    <div>
      <ComboboxLabel>
        <InputLabel>
          {{labelText}}
        </InputLabel>
      </ComboboxLabel>
      <div class="mt-1 relative">
        <ComboboxInput
          class="w-full border border-gray-300 rounded-md shadow-sm focus:border focus:border-blue-500"
          :class="comboboxInputClasses"
          @change="query = $event.target.value"
          :displayValue="(option) => option.name"
        />
        <ComboboxButton tabindex="0" class="absolute right-0 px-4 rounded-md h-full focus:outline-none focus:border-2 focus:border-blue-500 active:border-0" >
          <ChevronDownIcon class="w-5 h-5 text-gray-500"/>
        </ComboboxButton>
      </div>
    </div>

    <ComboboxOptions class="absolute z-50 overflow-hidden mt-0.5 bg-white border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
      <!-- Options passed as props -->
      <ComboboxOption
        v-for="option in filteredOptions"
        :key="option.id"
        :value="option"
        class="text-left cursor-pointer"
        v-slot="{ active, selected }"
      >
        <li :class="{
          'px-4': true,
          'py-1.5': true,
          'bg-blue-500 text-white': active,
          'text-gray-500': !selected,
          'font-bold': selected,
        }"
        >
          {{ option.name }}
        </li>
      </ComboboxOption>
    </ComboboxOptions>
  </Combobox>
</template>
