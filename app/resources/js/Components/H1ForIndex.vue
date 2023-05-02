<!-- A top-level heading for Index pages -->
<script setup>
const props = defineProps({
  text: String
})
</script>

<template>
  <h1 class="font-medium text-2xl text-gray-900">{{text}}</h1>
</template>
