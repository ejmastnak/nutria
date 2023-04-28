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
  can_clone: Boolean,
  can_delete: Boolean
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
    <Head :title="'Edit ' + food_list.name" />

    <h1 class="text-xl font-semibold">Edit {{food_list.name}}</h1>

    <CreateOrEdit
      :food_list="food_list"
      :ingredients="ingredients"
      :meals="meals"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="false"
    />

  </div>
</template>
