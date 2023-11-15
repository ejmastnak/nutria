<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrient_categories: Array,
  units: Array,
  user_ingredients: Array,
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
    route_basename="ingredients"
    :id="ingredient.id"
    :things="ingredients"
    thing="ingredient"
    :can_view="can_view"
    :can_create="can_create"
    :can_clone="can_clone"
    :can_update="can_update"
    :can_delete="can_delete"
  >
    <Head :title="'Edit ' + ingredient.name"/>
    <H1 :text="'Edit ' + ingredient.name " />

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrient_categories="nutrient_categories"
      :units="units"
      :create="false"
    />

  </SidebarLayout>
</template>
