<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import H1 from '@/Components/H1ForCrud.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";

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
  <SidebarLayout
    page="edit"
    route_basename="meals"
    :id="meal.id"
    :things="meals"
    thing="meal"
    :can_view="can_view"
    :can_create="can_create"
    :can_clone="can_clone"
    :can_update="can_update"
    :can_delete="can_delete"
  >
    <Head :title="'Edit ' + meal.name" />
    <H1 :text="'Edit ' + meal.name" />
    <CreateOrEdit
      :meal="meal"
      :ingredients="ingredients"
      :units="units"
      :create="false"
    />
  </SidebarLayout>
</template>
