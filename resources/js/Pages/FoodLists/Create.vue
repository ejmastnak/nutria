<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import H1 from '@/Components/H1ForCrud.vue'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";

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
  <SidebarLayout
    :page="food_list ? 'clone' : 'create'"
    route_basename="food-lists"
    :id="food_list ? food_list.id : null"
    :things="food_lists"
    thing="food list"
    :can_view="can_view"
    :can_create="can_create"
    :can_clone="can_clone"
    :can_update="can_update"
    :can_delete="can_delete"
  >
    <Head title="New Food List" />
    <H1 text="New Food List" />
    <p v-if="food_list" class="text-gray-700">(Cloned from {{food_list.name}})</p>

    <CreateOrEdit
      :food_list="food_list"
      :ingredients="ingredients"
      :meals="meals"
      :units="units"
      :create="true"
    />

  </SidebarLayout>
</template>
