<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  text: String,
  enabled: Boolean
})

const emit = defineEmits(['wasClicked'])

</script>

<template>
  <button
    type="button"
    :disabled="!enabled"
    @click="emit('wasClicked')"
    class="hover:underline px-2 sm:px-4 py-2 sm:py-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700"
    :class="{
      'text-gray-300': !enabled,
      'hover:bg-blue-100': enabled,
      'hover:bg-gray-100': !enabled
    }"
  >
    <div class="flex items-center whitespace-nowrap">
      <slot name="icon"></slot>
      <p class="ml-1">{{text}}</p>
    </div>
  </button>
</template>
