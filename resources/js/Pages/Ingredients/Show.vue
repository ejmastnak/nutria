<script setup>
import { round } from '@/utils/GlobalFunctions.js'
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import { EllipsisHorizontalCircleIcon } from '@heroicons/vue/24/outline'
import H1 from '@/Components/H1ForCrud.vue'
import MyLink from '@/Components/MyLink.vue'
import CustomUnitsShowDialog from './Partials/CustomUnitsShowDialog.vue'

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

const customUnitsShowDialogRef = ref(null)

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
      <div class="mt-2 flex flex-wrap gap-x-2 gap-y-1">
        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit h-fit">
          Ingredient
        </div>
        <div v-if="ingredient.ingredient_category" class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit h-fit">
          {{ingredient.ingredient_category.name}}
        </div>
      </div>

      <!-- Density -->
      <div class="mt-4 flex flex-wrap items-top gap-x-5 gap-y-2">
        <div>
          <p class="font-medium text-sm text-gray-600">
            Ingredient density
          </p>
          <p v-if="ingredient.density_g_ml">
            <span class="font-medium">{{round(Number(ingredient.density_g_ml), 2)}} {{ingredient.density_mass_unit.name}}/{{ingredient.density_volume_unit.name}}</span>
            <span v-if="ingredient.density_mass_unit.name !== 'g' || ingredient.density_volume_unit.name !== 'ml'">
              ({{round(ingredient.density_g_ml, 2)}} g/ml)
            </span>
          </p>
          <p class="leading-tight" v-else>
            No density is set for this ingredient.
          </p>
        </div>

        <!-- Custom units -->
        <div>
          <p class="font-medium text-sm text-gray-600">
            Custom units
          </p>
          <p v-if="ingredient.custom_units.length === 0">
            No custom units.
          </p>
          <button
            v-else
            type="button"
            class="rounded-md hover:text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700"
            @click="customUnitsShowDialogRef.open()"
          >
            {{ingredient.custom_units.length}} custom unit{{ingredient.custom_units.length === 1 ? '' : 's'}}
          </button>
        </div>
      </div>
    </div>

    <NutrientProfile
      class="mt-6"
      :defaultUnit="units.find((unit) => unit.name === 'g')"
      :defaultUnitAmount="100"
      :densityGMl="ingredient.density_g_ml"
      :intakeGuidelines="intake_guidelines"
      :nutrientProfiles="nutrient_profiles"
      :nutrientCategories="nutrient_categories"
      :units="units.concat(ingredient.custom_units)"
    />

    <CustomUnitsShowDialog ref="customUnitsShowDialogRef" :custom_units="ingredient.custom_units" />

  </div>
</template>
