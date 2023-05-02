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
    class="hover:underline px-4 py-4 rounded-lg"
    :class="{
      'text-gray-300': !enabled,
      'hover:bg-blue-100': enabled,
      'hover:bg-gray-100': !enabled
    }"
    :href="enabled ? href : '#'"
  >
  <div class="flex items-center">
    <slot name="icon"></slot>
    <p class="ml-1">{{text}}</p>
  </div>
  </Link>
</template>
