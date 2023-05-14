<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: String,
  text: String,
  enabled: Boolean
})
</script>

<template>
  <Link
    class="hover:underline px-2 sm:px-4 py-2 sm:py-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700"
    :class="{
      'text-gray-300': !enabled,
      'hover:bg-blue-100': enabled,
      'hover:bg-gray-100': !enabled
    }"
    :href="href"
  >
  <div class="flex items-center whitespace-nowrap">
    <slot name="icon"></slot>
    <p class="ml-1">{{text}}</p>
  </div>
  </Link>
</template>
