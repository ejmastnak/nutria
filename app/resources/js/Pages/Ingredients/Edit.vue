<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrient_categories: Array,
  can_delete: Boolean,
  can_create: Boolean,
  can_clone: Boolean
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
    <Head :title="'Edit ' + ingredient.name"/>

    <h1 class="text-xl font-semibold">Edit Ingredient</h1>

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrient_categories="nutrient_categories"
      :create="false"
    />
  </div>
  </template>
