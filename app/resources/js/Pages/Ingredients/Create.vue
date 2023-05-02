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

const props = defineProps({
  ingredient: Object,
  ingredients: Array,
  ingredient_categories: Array,
  nutrient_categories: Array,
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
    <Head title="New Ingredient" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('ingredients.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="ingredient" />
      <div class="flex ml-auto">
        <CrudNavBarView v-if="clone" :enabled="can_view" text="View original" :href="route('ingredients.show', ingredient.id)" />
        <CrudNavBarCreate v-if="clone" :enabled="can_create" text="New" :href="route('ingredients.create')" />
        <CrudNavBarCloneButton v-if="!clone" :enabled="can_create" @wasClicked="cloneExistingDialog.open()" text="Clone" />
      </div>
    </CrudNavBar>

    <h1 class="mt-8 text-xl font-semibold">New Ingredient</h1>
    <p v-if="clone && ingredient" class="text-gray-700">(Cloned from {{ingredient.name}})</p>

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrient_categories="nutrient_categories"
      :create="true"
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

    <!-- Clone an existing ingredient -->
    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="ingredients"
      goRoute="ingredients.clone"
      label="Search for an ingredient to clone"
      title="Clone ingredient"
      action="Clone"
    />

  </div>
</template>
