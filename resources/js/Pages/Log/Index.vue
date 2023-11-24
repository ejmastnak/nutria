<script setup>
import { Head } from '@inertiajs/vue3'
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  user_ingredients: Array,
  meals: Array,
  food_lists: Array,
})

const ingredients = props.user_ingredients.concat(window.usdaIngredients ? window.usdaIngredients : [])
</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>
    <Head title="Log Data" />

    <h1 class="text-xl">Log data</h1>
    <ul class="mt-4">
      <li>Body weight.</li>
      <li>Food.</li>
    </ul>

  </div>
</template>
