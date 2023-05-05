<script setup>
import { Head } from '@inertiajs/vue3'
import MyLink from '@/Components/MyLink.vue'
</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head title="Home" />

    <h1 class="text-xl">Nutrient Profiler</h1>

    <ul class="mt-4">
      <li><MyLink class="text-blue-500" :href="route('ingredients.index')">Ingredients</MyLink></li>
      <li><MyLink class="text-blue-500" :href="route('meals.index')">Meals</MyLink></li>
      <li><MyLink class="text-blue-500" :href="route('food-lists.index')">Food Lists</MyLink></li>
      <li><MyLink class="text-blue-500" :href="route('intake-guidelines.index')">Intake Guidelines</MyLink></li>
    </ul>
  </div>
</template>
