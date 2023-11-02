<script setup>
import { round } from '@/utils/GlobalFunctions.js'
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import H1 from '@/Components/H1ForCrud.vue'
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  ingredient: Object,
  nutrient_profiles: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
  units: Array,
  can_edit: Boolean,
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

  <div>
    <Head :title="ingredient.name" />

    <!-- Ingredient name and descriptive pillboxes -->
    <div>
      <H1 :text="ingredient.name" />
      <div v-if="ingredient.meal" class="text-gray-700">
        (Created from
        <MyLink :colored="true" :href="route('meals.show', ingredient.meal.id)">
          {{ingredient.meal.name}}
        </MyLink>)
      </div>

      <!-- Descriptive pillboxes -->
      <div class="flex">
        <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Ingredient
        </div>
        <div v-if="ingredient.ingredient_category" class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.ingredient_category.name}}
        </div>
        <div v-if="ingredient.density_g_ml" class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Density: {{round(Number(ingredient.density_g_ml), 2)}} g/ml
        </div>
      </div>
    </div>

    <NutrientProfile
      class="mt-8"
      :defaultUnit="units.find((unit) => unit.name === 'g')"
      :defaultUnitAmount="100"
      :densityGMl="ingredient.density_g_ml"
      :intakeGuidelines="intake_guidelines"
      :nutrientProfiles="nutrient_profiles"
      :nutrientCategories="nutrient_categories"
      :units="units.concat(ingredient.custom_units)"
    />

  </div>
</template>
