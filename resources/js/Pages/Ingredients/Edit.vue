<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarView from '@/Shared/CrudNavBarView.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
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

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>

    <Head :title="'Edit ' + ingredient.name"/>
    <H1 class="mt-8" :text="'Edit ' + ingredient.name " />

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrient_categories="nutrient_categories"
      :units="units"
      :create="false"
    />

  </div>
</template>
