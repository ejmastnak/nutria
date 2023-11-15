<script setup>
const props = defineProps({
  enabled: {
    type: Boolean,
    default: true,
  },
})
</script>

<template>
  <div
    class="-ml-1 w-6 h-6 text-gray-700 shrink-0"
    :class="{ '!text-gray-400': !enabled }"
  >
    <slot/>
  </div>
</template>

