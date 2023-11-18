<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  meal: Object,
  user_ingredients: Array,
  units: Array,
  meals: Array,
  can_view: Boolean,
  can_create: Boolean,
  can_clone: Boolean,
  can_update: Boolean,
  can_delete: Boolean,
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
  <div class="">
    <Head :title="'Edit ' + meal.name" />
    <H1 class="mt-8" :text="'Edit ' + meal.name" />
    <CreateOrEdit
      :meal="meal"
      :ingredients="ingredients"
      :units="units"
      :create="false"
    />
  </div>
</template>
