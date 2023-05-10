<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarView from '@/Shared/CrudNavBarView.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarCloneButton from '@/Shared/CrudNavBarCloneButton.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  food_list: Object,
  food_lists: Array,
  ingredients: Array,
  meals: Array,
  ingredient_categories: Array,
  units: Array,
  can_view: Boolean,
  can_create: Boolean,
  clone: Boolean
})

const searchDialog = ref(null)
const cloneExistingDialog = ref(null)

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head title="New Food List" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('food-lists.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="food list" />
      <div class="flex ml-auto">
        <CrudNavBarView v-if="clone" :enabled="can_view" text="View original" :href="route('food-lists.show', food_list.id)" />
        <CrudNavBarCreate v-if="clone" :enabled="can_create" text="New" :href="route('food-lists.create')" />
        <CrudNavBarCloneButton v-if="!clone" :enabled="can_create" @wasClicked="cloneExistingDialog.open()" text="Clone" />
      </div>
    </CrudNavBar>

    <H1 class="mt-8" text="New Food List" />
    <p v-if="clone && food_list" class="text-gray-700">(Cloned from {{food_list.name}})</p>

    <CreateOrEdit
      :food_list="food_list"
      :ingredients="ingredients"
      :meals="meals"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="true"
    />

    <!-- Search for an food list -->
    <SearchForThingAndGo
      ref="searchDialog"
      :things="food_lists"
      goRoute="food-lists.show"
      label="Search for another food list"
      title=""
      action="Go"
    />

    <!-- Clone an existing food list -->
    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="food_lists"
      goRoute="food-lists.clone"
      label="Search for a food list to clone"
      title="Clone food list"
      action="Clone"
    />

  </div>
</template>
