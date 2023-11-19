<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  food_list: Object,
  user_ingredients: Array,
  meals: Array,
  units: Array,
  food_lists: Array,
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

  <div>
    <Head title="New Food List" />
    <H1 class="mt-8" text="New Food List" />
    <p v-if="food_list" class="text-gray-700">(Cloned from {{food_list.name}})</p>

    <CreateOrEdit
      :food_list="food_list"
      :ingredients="ingredients"
      :meals="meals"
      :units="units"
      :create="true"
    />

  </div>
</template>
