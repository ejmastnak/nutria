<script setup>

/*
A simplified version of NutrientProfile used to show a nutrient profile for
all food consumed over a given date range---it's simpler than NutrientProfile
because there is no need to support recomputing the nutrient profile for
different amounts of ingredient/meal/food list.
*/

import { ref, computed } from 'vue'
import NutrientProfileTables from '@/Shared/NutrientProfileTables.vue'
import NutrientProfileOptionsForDateRange from './NutrientProfileOptionsForDateRange.vue'

const props = defineProps({
  intakeGuidelines: Array,
  nutrientProfiles: Array,
  daysWithRecords: Number,
  nutrientCategories: Array,
  fromDate: String,
  toDate: String,
})

const selectedIntakeGuideline = ref(props.intakeGuidelines[0])
const selectedNutrientProfile = computed(() => {
  const idx = props.nutrientProfiles.map(profile => profile.intake_guideline_id).indexOf(selectedIntakeGuideline.value.id)
  return props.nutrientProfiles[idx > 0 ? idx : 0]['nutrient_profile']
})

const nutrientProfileIsEmpty = computed(() => {
  return !(props.nutrientProfiles.length && props.nutrientProfiles[0].nutrient_profile && props.nutrientProfiles[0].nutrient_profile.length)
})

const daysInRange = computed(() => {
  const fromDate = new Date(props.fromDate)
  const toDate = new Date(props.toDate)
  return Math.round((toDate.getTime() - fromDate.getTime())/86400000) + 1
})

</script>

<template>
  <section>

    <h2 class="text-xl">Nutrient profile</h2>

    <p 
      v-if="nutrientProfileIsEmpty"
      class="mt-px"
    >
      No food was consumed over this time period, so there is no nutrient profile to display.
    </p>

    <NutrientProfileOptionsForDateRange
      class="mt-2"
      v-if="!nutrientProfileIsEmpty"
      v-model:selected-intake-guideline="selectedIntakeGuideline"
      :intakeGuidelines="intakeGuidelines"
      :fromDate="fromDate"
      :toDate="toDate"
      :daysInRange="daysInRange"
      :daysWithRecords="daysWithRecords"
    />

    <NutrientProfileTables
      class="w-full mt-4"
      v-if="!nutrientProfileIsEmpty"
      :nutrientProfile="selectedNutrientProfile"
      :nutrientCategories="nutrientCategories"
      :scaleFactor="1/daysWithRecords"
    />

  </section>
</template>
