<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: String
})
</script>

<template>
  <Link
    :href="href"
    class="hover:text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 rounded-md p-px"
  >
    <slot />
  </Link>
</template>
