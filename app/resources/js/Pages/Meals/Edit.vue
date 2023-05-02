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

const props = defineProps({
  meal: Object,
  meals: Array,
  ingredients: Array,
  ingredient_categories: Array,
  units: Array,
  can_view: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const searchDialog = ref(null)
const deleteDialog = ref(null)

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

    <CrudNavBar>
      <CrudNavBarIndex :href="route('meals.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="meal" />
      <CrudNavBarCreate :enabled="can_create" text="New" :href="route('meals.create')" />
      <div class="flex ml-auto">
        <CrudNavBarView :enabled="can_view" text="View original" :href="route('meals.show', meal.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('meals.clone', meal.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(meal.id)" />
      </div>
    </CrudNavBar>

    <h1 class="mt-8 text-xl font-semibold">Edit {{meal.name}}</h1>

    <CreateOrEdit
      :meal="meal"
      :ingredients="ingredients"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="false"
    />

    <!-- Search for a meal -->
    <SearchForThingAndGo
      ref="searchDialog"
      :things="meals"
      goRoute="meals.show"
      label="Search for another meal"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="meals.destroy" thing="meal" />

  </div>
</template>
