<script setup>
import { Link } from '@inertiajs/vue3'
const props = defineProps({
  href: String,
  enabled: {
    type: Boolean,
    default: true,
  },
})
</script>

<template>
  <Link
    :href="href"
    class="flex w-full items-start text-left leading-tight px-4 py-2 hover:bg-blue-50 rounded-md"
    :class="{
      'text-gray-400': !enabled,
      'hover:bg-gray-50': !enabled
    }"
  >
  <slot/>
  </Link>
</template>

