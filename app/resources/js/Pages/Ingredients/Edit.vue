<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
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

const searchDialog = ref(null)
const deleteDialog = ref(null)

const props = defineProps({
  ingredient: Object,
  ingredients: Array,
  ingredient_categories: Array,
  nutrient_categories: Array,
  can_view: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
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

    <Head :title="'Edit ' + ingredient.name"/>

    <CrudNavBar>
      <CrudNavBarIndex :href="route('ingredients.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="ingredient" />
      <CrudNavBarCreate :enabled="can_create" text="New" :href="route('ingredients.create')" />
      <div class="flex ml-auto">
        <CrudNavBarView :enabled="can_view" text="View original" :href="route('ingredients.show', ingredient.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('ingredients.clone', ingredient.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(ingredient.id)" />
      </div>
    </CrudNavBar>

    <h1 class="mt-8 text-xl font-semibold">Edit Ingredient</h1>

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrient_categories="nutrient_categories"
      :create="false"
    />

    <!-- Search for an ingredient -->
    <SearchForThingAndGo
      ref="searchDialog"
      :things="ingredients"
      goRoute="ingredients.show"
      label="Search for another ingredient"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="ingredients.destroy" thing="ingredient" />

  </div>
  </template>
