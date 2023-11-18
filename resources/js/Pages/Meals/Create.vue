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
  can_create: Boolean,
  can_view: {type: Boolean, default: false},
  can_clone: {type: Boolean, default: false},
  can_update: {type: Boolean, default: false},
  can_delete: {type: Boolean, default: false},
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
    <Head title="New Meal" />
    <H1 class="mt-8" text="New Meal" />
    <p v-if="meal" class="text-gray-700">(Cloned from {{meal.name}})</p>
    <CreateOrEdit
      :meal="meal"
      :ingredients="ingredients"
      :units="units"
      :create="true"
    />
  </div>
</template>
