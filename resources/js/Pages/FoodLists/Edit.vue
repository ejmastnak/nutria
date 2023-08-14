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
  food_list: Object,
  food_lists: Array,
  ingredients: Array,
  meals: Array,
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
    <Head :title="'Edit ' + food_list.name" />

    <CrudNavBar>

      <!-- Desktop items -->
      <template v-slot:desktop-items>
        <CrudNavBarIndex :href="route('food-lists.index')" />
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="food list" />
        <CrudNavBarCreate :enabled="can_create" text="New" :href="route('food-lists.create')" />
        <div class="flex ml-auto">
          <CrudNavBarView :enabled="can_view" text="View" :href="route('food-lists.show', food_list.id)" />
          <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('food-lists.clone', food_list.id)" />
          <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(food_list.id)" />
        </div>
      </template>

      <!-- Always-displayed mobile item -->
      <template v-slot:mobile-displayed>
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="food list" />
      </template>

      <!-- Mobile menu items -->
      <template v-slot:mobile-items>
        <CrudNavBarIndex :href="route('food-lists.index')" />
        <CrudNavBarCreate :enabled="can_create" text="New" :href="route('food-lists.create')" />
        <CrudNavBarView :enabled="can_view" text="View" :href="route('food-lists.show', food_list.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('food-lists.clone', food_list.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(food_list.id)" />
      </template>

    </CrudNavBar>

    <H1 class="mt-8" :text="'Edit ' + food_list.name" />

    <CreateOrEdit
      :food_list="food_list"
      :ingredients="ingredients"
      :meals="meals"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="false"
    />

    <!-- Search for a food list -->
    <SearchForThingAndGo
      ref="searchDialog"
      :things="food_lists"
      goRoute="food-lists.show"
      label="Search for another food list"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="food-lists.destroy" thing="food list" />

  </div>
</template>
