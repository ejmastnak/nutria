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
  <div>
    <Head title="Home" />

    <h1 class="text-xl">Nutria Nutrition</h1>

    <ul class="mt-4">
      <li><MyLink :colored="true" :href="route('ingredients.index')">Ingredients</MyLink></li>
      <li><MyLink :colored="true" :href="route('meals.index')">Meals</MyLink></li>
      <li><MyLink :colored="true" :href="route('food-lists.index')">Food Lists</MyLink></li>
      <li><MyLink :colored="true" :href="route('intake-guidelines.index')">Intake Guidelines</MyLink></li>
    </ul>

  </div>
</template>
