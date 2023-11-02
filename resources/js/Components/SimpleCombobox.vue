<script setup>
import throttle from "lodash/throttle";
import { ref, computed, watch } from 'vue'
import { MagnifyingGlassIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'
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
  searchKey: {
    type: String,
    default: 'name',
  },
  displayKey: {
    type: String,
    default: 'name',
  },
  throttlems: {  // ms amount used to throttle fuzzy search
    type: Number,
    default: 100,
  },
  labelClasses: String,
  inputClasses: String,
})

const emit = defineEmits([ 'update:modelValue' ])
const selectedOption = computed({
  get() { return props.modelValue },
  set(option) { emit('update:modelValue', option) }
})

// For search over option names
const query = ref("")
const filteredOptions = ref(props.options)
watch(query, throttle(function (value) {
  filteredOptions.value = query.value.trim() === ''
    ? props.options
    : props.options.filter((option) => {
      return option[props.searchKey].toLowerCase().includes(query.value.toLowerCase())
    })
}, props.throttlems))

</script>

<template>

  <Combobox v-model="selectedOption">
    <div class="relative">

      <div>
        <ComboboxLabel class="text-sm font-medium text-gray-700" :class="labelClasses">
          {{labelText}}
        </ComboboxLabel>

        <div class="relative">
          <ComboboxInput
            class="w-full border border-gray-300 rounded-md shadow-sm focus:border focus:border-blue-500"
            :class="inputClasses"
            @change="query = $event.target.value"
            :displayValue="(option) => option ? option[searchKey] : ''"
          />
          <ComboboxButton tabindex="0" class="absolute right-0 px-4 rounded-md h-full focus:outline-none focus:border-2 focus:border-blue-500 active:border-0" >
            <ChevronDownIcon class="w-5 h-5 text-gray-600 shrink-0"/>
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
            {{ option[displayKey] }}
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>