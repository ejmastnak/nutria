<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  meal: Object,
  ingredients: Array,
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
    <Head :title="'Edit ' + meal.name" />

    <h1 class="text-xl font-semibold">Edit {{meal.name}}</h1>

    <CreateOrEdit
      :meal="meal"
      :ingredients="ingredients"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="false"
    />

  </div>
</template>
