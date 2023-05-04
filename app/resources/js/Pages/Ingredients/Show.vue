<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
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

const props = defineProps({
  ingredient: Object,
  nutrient_profiles: Array,
  rdi_profiles: Array,
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
      <CrudNavBarIndex :href="route('ingredients.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="ingredient" />
      <CrudNavBarCreate :enabled="can_create" :href="route('ingredients.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('ingredients.edit', ingredient.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('ingredients.clone', ingredient.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(ingredient.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">

      <H1 :text="ingredient.name" />

      <!-- Ingredient category -->
      <div class="flex">
        <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Ingredient
        </div>

        <div class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.ingredient_category.name}}
        </div>

        <div v-if="ingredient.density_g_per_ml" class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.density_g_per_ml}} g/ml
        </div>
      </div>

    </div>

    <NutrientProfile
      class="mt-8"
      :rdi_profiles="rdi_profiles"
      :nutrient_profiles="nutrient_profiles"
      :nutrient_categories="nutrient_categories"
      :defaultMassInGrams="100"
      :displayMassInput="true"
    />

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
