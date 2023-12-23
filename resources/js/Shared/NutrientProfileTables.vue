<script setup>
import NutrientProfileTable from '@/Shared/NutrientProfileTable.vue'
const props = defineProps({
  nutrientProfile: Array,
  nutrientCategories: Array,
  scaleFactor: Number
})

</script>

<template>

  <div class="grid grid-cols-1 gap-4 lg:gap-0 lg:flex md:space-x-4">

    <div
      v-for="nc in nutrientCategories"
      :key="nc.id"
      class="col-span-1"
    >
      <h2 class="text-md text-gray-900">{{nc.name}}s</h2>
      <NutrientProfileTable
        :nutrientProfile="nutrientProfile.filter(nutrient => nutrient.nutrient_category_id === nc.id)"
        :scaleFactor="scaleFactor"
      />
    </div>

  </div>

</template>
