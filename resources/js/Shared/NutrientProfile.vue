<script setup>
import { ref, computed } from 'vue'
import NutrientProfileTables from '@/Shared/NutrientProfileTables.vue'
import NutrientProfileOptions from '@/Shared/NutrientProfileOptions.vue'
import { prepareUnitsForDisplay, gramAmountOfUnit } from '@/utils/GlobalFunctions.js'

const props = defineProps({
  defaultUnit: Object,
  defaultUnitAmount: Number,
  densityGMl: Number,  // nullable
  intakeGuidelines: Array,
  nutrientProfiles: Array,
  nutrientCategories: Array,
  units: Array,
})

const selectedUnit = ref(props.defaultUnit)
const selectedUnitAmount = ref(props.defaultUnitAmount)
const selectedIntakeGuideline = ref(props.intakeGuidelines[0])

// const selectedNutrientProfile = computed(() => {
//   const idx = props.nutrientProfiles.map(profile => profile.intake_guideline_id).indexOf(selectedIntakeGuideline.value.id)
//   return props.nutrientProfiles[0].nutrientProfile
//   // return props.nutrientProfiles[idx > 0 ? idx : 0].nutrientProfile
// })

const selectedNutrientProfile = computed(() => {
  const idx = props.nutrientProfiles.map(profile => profile.intake_guideline_id).indexOf(selectedIntakeGuideline.value.id)
  return props.nutrientProfiles[idx > 0 ? idx : 0]['nutrient_profile']
})

// For ingredients, nutrient profile is computed per 100 grams of ingredient.
// For meals/food lists, nutrient profile is computed per meal/food list. Thus
// each nutrient amount and RDI entry in the displayed nutrient profile is
// scaled by the gram equivalent of (selectedUnitAmount, selectedUnit) divided
// by 100 g (for ingredients) or meal/foodlist mass in grams.
const scaleFactor = computed(() => {
  return gramAmountOfUnit(selectedUnitAmount.value, selectedUnit.value, props.densityGMl) / gramAmountOfUnit(props.defaultUnitAmount, props.defaultUnit, props.densityGMl)
})

</script>

<template>
  <section>

    <h2 class="text-xl">Nutrient profile</h2>

    <NutrientProfileOptions
      class="mt-2"
      v-model:selected-unit="selectedUnit"
      v-model:selected-unit-amount="selectedUnitAmount"
      v-model:selected-intake-guideline="selectedIntakeGuideline"
      :densityGMl="densityGMl"
      :intakeGuidelines="intakeGuidelines"
      :units="prepareUnitsForDisplay(units, densityGMl)"
    />

    <NutrientProfileTables
      class="w-full mt-4"
      :nutrientProfile="selectedNutrientProfile"
      :nutrientCategories="nutrientCategories"
      :scaleFactor="scaleFactor"
    />

  </section>
</template>
