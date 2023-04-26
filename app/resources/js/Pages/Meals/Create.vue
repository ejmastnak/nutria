<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  ingredients: Array,
  ingredient_categories: Array,
  units: Array,
  can_create: Boolean
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
    <Head title="New meal" />

    <h1 class="text-xl font-semibold">New meal</h1>

    <CreateOrEdit
      :meal="null"
      :ingredients="ingredients"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="true"
    />

  </div>
</template>
