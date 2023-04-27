<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrient_categories: Array,
  can_create: Boolean,
  clone: {
    type: Boolean,
    default: false
  }
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
    <Head title="New Ingredient" />

    <h1 class="text-xl font-semibold">New Ingredient</h1>
    <p v-if="clone && ingredient" class="text-gray-700">(Cloned from {{ingredient.name}})</p>

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrient_categories="nutrient_categories"
      :create="true"
    />

  </div>
</template>
