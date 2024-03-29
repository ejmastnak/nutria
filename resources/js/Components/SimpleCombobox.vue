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
  throttlems: {  // ms amount used to throttle search
    type: Number,
    default: 100,
  },
  labelClasses: String,
  inputClasses: String,
  optionsClasses: String,
})

const emit = defineEmits([ 'update:modelValue' ])
const selectedOption = computed({
  get() { return props.modelValue },
  set(option) { emit('update:modelValue', option) }
})

// This feels like a disgusting hack; the goal is to watch when props.options
// changes and updated filteredOptions accordingly. The obvious choice would be
// to watch props.options, but for whatever this watcher doesn't seem to run
// when options change. But watching computedOptions does (see watcher below).
const computedOptions = computed(() => {
  return props.options.map(option => option)
})

// For search over option names
const query = ref("")
const filteredOptions = ref(computedOptions.value)
function search() {
  filteredOptions.value = query.value.trim() === ''
    ? computedOptions.value
    : computedOptions.value.filter((option) => {
      return option[props.searchKey].toLowerCase().includes(query.value.toLowerCase())
    })
}

watch(query, throttle(function (value) { search() }, props.throttlems))
watch(computedOptions, () => { search() })


</script>

<template>

  <Combobox v-model="selectedOption">
    <div class="relative">

      <div>
        <ComboboxLabel class="text-sm font-medium text-gray-700 w-full" :class="labelClasses">
          {{labelText}}
        </ComboboxLabel>

        <div class="flex">
          <ComboboxInput
            class="w-full border border-gray-300 rounded-bl-md rounded-tl-md shadow-sm focus:border focus:border-blue-500 text-ellipsis"
            :class="inputClasses"
            @change="query = $event.target.value"
            :displayValue="(option) => option ? option[searchKey] : ''"
          />
          <ComboboxButton class="px-1 focus:outline-none focus:border-2 focus:border-blue-500 active:border-0 bg-white rounded-br-md rounded-tr-md border-y border-r border-gray-300" >
            <ChevronDownIcon class="w-5 h-5 text-gray-600 shrink-0"/>
          </ComboboxButton>
        </div>

      </div>

      <ComboboxOptions class="absolute z-50 overflow-hidden mt-0.5 bg-white border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" :class="optionsClasses">
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
