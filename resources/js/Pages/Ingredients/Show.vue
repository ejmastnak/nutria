<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarEdit from '@/Shared/CrudNavBarEdit.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import H1 from '@/Components/H1ForCrud.vue'
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  ingredient: Object,
  has_ingredient_nutrients: Boolean,
  nutrient_profiles: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
  ingredients: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const searchDialog = ref(null)
const deleteDialog = ref(null)

const searchIngredient = ref({})
function search() {
  router.get(route('ingredients.show', searchIngredient.value.id))
}

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>
    <Head :title="ingredient.name" />

    <CrudNavBar>

      <!-- Desktop items -->
      <template v-slot:desktop-items>
        <CrudNavBarIndex :href="route('ingredients.index')" />
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="ingredient" />
        <CrudNavBarCreate :enabled="can_create" :href="route('ingredients.create')" />
        <div class="flex ml-auto">
          <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('ingredients.edit', ingredient.id)" />
          <CrudNavBarCloneLink :enabled="can_clone" :href="route('ingredients.clone', ingredient.id)" />
          <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(ingredient.id)" />
        </div>
      </template>

      <!-- Always-displayed mobile item -->
      <template v-slot:mobile-displayed>
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="ingredient" />
      </template>

      <!-- Mobile menu items -->
      <template v-slot:mobile-items>
        <CrudNavBarIndex :href="route('ingredients.index')" />
        <CrudNavBarCreate :enabled="can_create" :href="route('ingredients.create')" />
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('ingredients.edit', ingredient.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('ingredients.clone', ingredient.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(ingredient.id)" />
      </template>

    </CrudNavBar>

    <!-- Ingredient name and descriptive pillboxes -->
    <div class="mt-8">
      <H1 :text="ingredient.name" />
      <div v-if="ingredient.meal" class="text-gray-700">
        (Created from
        <MyLink :colored="true" :href="route('meals.show', ingredient.meal.id)">
          {{ingredient.meal.name}}
        </MyLink>)
      </div>

      <!-- Ingredient category -->
      <div class="flex">
        <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Ingredient
        </div>
        <div class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.ingredient_category.name}}
        </div>
        <div v-if="ingredient.density_g_per_ml" class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{round(Number(ingredient.density_g_per_ml), 2)}} g/ml
        </div>
      </div>
    </div>

    <NutrientProfile
      v-if="has_ingredient_nutrients"
      class="mt-8"
      :intake_guidelines="intake_guidelines"
      :nutrient_profiles="nutrient_profiles"
      :nutrient_categories="nutrient_categories"
      :defaultMassInGrams="100"
      :displayMassInput="true"
    />
    <div v-else class="mt-4 max-w-xl text-gray-700">
      This ingredient has no nutrients.
      There is likely from an error when creating the ingredient—you should probably
      <button class="p-px rounded-md text-blue-500 hover:text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700" type="button" @click="deleteDialog.open(ingredient.id)">
        delete this ingredient
      </button>,
      then
      create a new one.
    </div>

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