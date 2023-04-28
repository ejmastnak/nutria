<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  food_list: Object,
  ingredients: Array,
  meals: Array,
  ingredient_categories: Array,
  units: Array,
  can_create: Boolean,
  clone: Boolean
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
    <Head title="New food list" />

    <h1 class="text-xl font-semibold">New food list</h1>
    <p v-if="clone && food_list" class="text-gray-700">(Cloned from {{food_list.name}})</p>

    <CreateOrEdit
      :food_list="food_list"
      :ingredients="ingredients"
      :meals="meals"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="true"
    />

  </div>
</template>
