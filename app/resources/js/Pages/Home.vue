<script setup>
import { Head, Link } from '@inertiajs/vue3'
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
      <li><Link class="text-blue-500 hover:text-blue-600 hover:underline" :href="route('ingredients.index')">Ingredients</Link></li>
      <li><Link class="text-blue-500 hover:text-blue-600 hover:underline" :href="route('meals.index')">Meals</Link></li>
      <li><Link class="text-blue-500 hover:text-blue-600 hover:underline" :href="route('food-lists.index')">Food Lists</Link></li>
      <li><Link class="text-blue-500 hover:text-blue-600 hover:underline" :href="route('rdi-profiles.index')">RDI Profiles</Link></li>
    </ul>
  </div>
</template>
