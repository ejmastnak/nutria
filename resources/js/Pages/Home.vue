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
  <div class="mt-2">
    <Head title="Home" />

    Hello, world!

    <h1 class="text-2xl text-gray-900">Welcome to Nutria Nutrition!</h1>

    <p class="mt-4 text-gray-700 max-w-lg">
      This is the home page.
      It's bare for now, but you can use it to navigate to the important parts of the app.
    </p>

    <h2 class="mt-5 text-xl text-gray-900">Important Links</h2>
    <ul class="mt-2 ml-5 list-disc">
      <li><MyLink :colored="true" :href="route('ingredients.index')">Your ingredients</MyLink></li>
      <li><MyLink :colored="true" :href="route('meals.index')">Your meals</MyLink></li>
      <li><MyLink :colored="true" :href="route('food-lists.index')">Your food lists</MyLink></li>
      <li><MyLink :colored="true" :href="route('intake-guidelines.index')">Your intake guidelines</MyLink></li>
      <li><MyLink :colored="true" :href="route('data')">Log and view your data</MyLink></li>
    </ul>

  </div>
</template>
