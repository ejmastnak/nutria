<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrients: Array,
  nutrient_categories: Array,
  units: Array,
  user_ingredients: Array,
  can_create: Boolean,
  can_view: {type: Boolean, default: false},
  can_clone: {type: Boolean, default: false},
  can_update: {type: Boolean, default: false},
  can_delete: {type: Boolean, default: false},
})

const searchDialog = ref(null)
const cloneExistingDialog = ref(null)

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
    :page="ingredient ? 'clone' : 'create'"
    route_basename="ingredients"
    :id="ingredient ? ingredient.id : null"
    :things="ingredients"
    thing="ingredient"
    :can_view="can_view"
    :can_create="can_create"
    :can_clone="can_clone"
    :can_update="can_update"
    :can_delete="can_delete"
  >
    <Head title="New Ingredient" />
    <H1 text="New Ingredient" />
    <p v-if="ingredient" class="text-gray-700">(Cloned from {{ingredient.name}})</p>

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrients="nutrients"
      :nutrient_categories="nutrient_categories"
      :units="units"
      :create="true"
    />

  </SidebarLayout>
</template>
