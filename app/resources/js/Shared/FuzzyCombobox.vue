<!--
https://headlessui.com/vue/combobox
A combobox with fuzzy search over the combobox options, which are
in practice either ingredients or meals.
Input: an array of objects with `id` and `name` keys.
Output: the selected object.
-->

<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import { ref, computed, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
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
  fuzzyLimit: {
    type: Number,
    default: 15
  },
  fuzzyThreshold: {
    type: Number,
    default: -1000
  },
  throttlems: {
    type: Number,
    default: 300
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
    emit('update:modelValue', option.obj)
  }
})

// For fuzzy search over option names
const query = ref("")
const filteredOptions = ref([])
const fuzzyOptions = {
  key: 'name',
  limit: props.fuzzyLimit,
  threshold: props.fuzzyThreshold
}

watch(query, throttle(function (value) {
  filteredOptions.value = fuzzysort.go(value.trim(), props.options, fuzzyOptions)
}, props.throttlems))

</script>

<template>
  <Combobox v-model="selectedValue">
    <div class="relative">

      <div>
        <ComboboxLabel class="text-sm text-gray-500">
          {{labelText}}
        </ComboboxLabel>

        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
          </div>

          <ComboboxInput
            class="block p-1 pl-10 border border-gray-300 rounded-md shadow-sm focus:border focus:border-blue-500 w-full"
            @change="query = $event.target.value"
            :displayValue="(option) => option.name"
          />

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
            {{ option.obj.name }}
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>
