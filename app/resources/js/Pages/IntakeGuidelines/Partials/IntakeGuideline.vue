<script setup>
import IntakeGuidelineTable from './IntakeGuidelineTable.vue'
const props = defineProps({
  intake_guideline_nutrients: Array,
  nutrient_categories: Array
})

</script>

<template>

  <div class="grid grid-cols-1 lg:flex md:space-x-4">

    <div
      v-for="nc in nutrient_categories"
      :key="nc.id"
      class="col-span-1 sm:w-3/4 md:2/3"
    >
      <h2 class="text-md text-gray-900">{{nc.name}}s</h2>
      <IntakeGuidelineTable :intake_guideline_nutrients="intake_guideline_nutrients.filter(intake_guideline_nutrient => intake_guideline_nutrient.nutrient.nutrient_category_id === nc.id)" />
    </div>

  </div>

</template>
