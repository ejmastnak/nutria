<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
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
  meal: Object,
  meals: Array,
  ingredients: Array,
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
    <Head title="New meal" />

    <CrudNavBar>

      <!-- Desktop items -->
      <template v-slot:desktop-items>
        <CrudNavBarIndex :href="route('meals.index')" />
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="meal" />
        <div class="flex ml-auto">
          <CrudNavBarView v-if="clone" :enabled="can_view" text="View original" :href="route('meals.show', meal.id)" />
          <CrudNavBarCreate v-if="clone" :enabled="can_create" text="New" :href="route('meals.create')" />
          <CrudNavBarCloneButton v-if="!clone" :enabled="can_create" @wasClicked="cloneExistingDialog.open()" text="Clone" />
        </div>
      </template>

      <!-- Always-displayed mobile item -->
      <template v-slot:mobile-displayed>
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="meal" />
      </template>

      <!-- Mobile menu items -->
      <template v-slot:mobile-items>
        <CrudNavBarIndex :href="route('meals.index')" />
        <CrudNavBarView v-if="clone" :enabled="can_view" text="View original" :href="route('meals.show', meal.id)" />
        <CrudNavBarCreate v-if="clone" :enabled="can_create" text="New" :href="route('meals.create')" />
        <CrudNavBarCloneButton v-if="!clone" :enabled="can_create" @wasClicked="cloneExistingDialog.open()" text="Clone" />
      </template>

    </CrudNavBar>

    <H1 class="mt-8" text="New Meal" />
    <p v-if="clone && meal" class="text-gray-700">(Cloned from {{meal.name}})</p>

    <CreateOrEdit
      :meal="meal"
      :ingredients="ingredients"
      :ingredient_categories="ingredient_categories"
      :units="units"
      :create="true"
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

    <!-- Clone an existing meal -->
    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="meals"
      goRoute="meals.clone"
      label="Search for a meal to clone"
      title="Clone meal"
      action="Clone"
    />

  </div>
</template>
