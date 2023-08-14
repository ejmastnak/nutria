<!-- A top-level heading for CRUD pages -->
<script setup>
const props = defineProps({
  text: String
})
</script>

<template>
  <h1 class="w-3/4 text-xl text-gray-900">{{text}}</h1>
</template>
