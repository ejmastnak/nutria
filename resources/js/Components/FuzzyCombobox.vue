<script setup>
import fuzzysort from 'fuzzysort'
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

defineExpose({ focus })
const inputWrapperRef = ref(null)
function focus() {
  inputWrapperRef.value.querySelectorAll('input')[0].focus()
}

const props = defineProps({
  targets: Array,
  labelText: String,
  modelValue: Object,
  searchKey: {
    type: String,
    default: 'name',
  },
  fuzzyLimit: {  // max number of search results
    type: Number,
    default: 15,
  },
  fuzzyThreshold: {  // omit results with scores below threshold
    type: Number,
    default: -1000,
  },
  throttlems: {  // ms amount used to throttle fuzzy search
    type: Number,
    default: 300,
  },
  showIcon: {
    type: Boolean,
    default: true,
  }
})

const emit = defineEmits([
  'update:modelValue',
])

const selectedValue = computed({
  get() {
    return props.modelValue;
  },
  set(result) {
    emit('update:modelValue', result)
  }
})

// For fuzzy search over targets
const query = ref("")
const filteredTargets = ref([])
const fuzzyOptions = {
  key: props.searchKey,
  limit: props.fuzzyLimit,
  threshold: props.fuzzyThreshold
}

watch(query, throttle(function (value) {
  filteredTargets.value = fuzzysort.go(value.trim(), props.targets, fuzzyOptions)
}, props.throttlems))

</script>

<template>
  <Combobox v-model="selectedValue">
    <div class="relative">

      <div>
        <ComboboxLabel class="text-sm font-medium text-gray-700">
          {{labelText}}
        </ComboboxLabel>

        <div class="relative" ref="inputWrapperRef">
          <ComboboxInput
            class="w-full border border-gray-300 rounded-md shadow-sm focus:border focus:border-blue-500 text-ellipsis"
            @change="query = $event.target.value"
            :displayValue="(result) => result ? result[searchKey] : ''"
          />
          <ComboboxButton v-if="showIcon" tabindex="0" class="absolute right-0 px-4 rounded-md h-full focus:outline-none focus:border-2 focus:border-blue-500 active:border-0" >
            <ChevronDownIcon class="w-5 h-5 text-gray-600 shrink-0"/>
          </ComboboxButton>
        </div>

      </div>

      <ComboboxOptions class="absolute z-50 overflow-hidden mt-0.5 bg-white border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <!-- Options passed as props -->
        <ComboboxOption
          v-for="target in filteredTargets"
          :key="target.obj.id"
          :value="target.obj"
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
            {{ target.obj[searchKey] }}
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>
