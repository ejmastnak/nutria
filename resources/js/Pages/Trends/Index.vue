<script setup>
import { Head } from '@inertiajs/vue3'
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  body_weight_records: Array,
  ingredient_intake_records: Array,
  meal_intake_records: Array,
  food_list_intake_records: Array,
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
    <Head title="Trends" />

    <h1 class="text-xl">Trends</h1>
    <ul class="mt-4">
      <li>Body weight.</li>
      <li>Food intake.</li>
      <li>Nutrient profile trends.</li>
    </ul>

    <pre>
      {{food_list_intake_records}}
    </pre>

  </div>
</template>
