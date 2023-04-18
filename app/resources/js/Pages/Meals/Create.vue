<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  ingredients: Array,
  user_ingredients: Array,
  ingredient_categories: Array,
  units: Array
})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head title="New Meal" />

    <h1 class="text-xl font-semibold">New Meal</h1>

  </div>
  </template>
