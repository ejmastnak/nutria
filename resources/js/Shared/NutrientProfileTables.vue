<script setup>
import NutrientProfileTable from '@/Shared/NutrientProfileTable.vue'
const props = defineProps({
  nutrientProfile: Array,
  nutrientCategories: Array,
  scaleFactor: Number
})

</script>

<template>

  <div class="w-full lg:flex lg:gap-x-4 space-y-4 lg:space-y-0">

    <div
      v-for="nc in nutrientCategories"
      :key="nc.id"
      class="w-full"
    >
      <h2 class="text-md text-gray-900">{{nc.name}}s</h2>
      <NutrientProfileTable
        :nutrientProfile="nutrientProfile.filter(nutrient => nutrient.nutrient_category_id === nc.id)"
        :scaleFactor="scaleFactor"
      />
    </div>

  </div>

</template>
