<script setup>
import NutrientProfileTable from '@/Shared/NutrientProfileTable.vue'
const props = defineProps({
  nutrient_profile: Array,
  nutrient_categories: Array,
  howManyGrams: Number,
  defaultMassInGrams: Number
})

</script>

<template>

  <div class="grid grid-cols-1 lg:flex md:space-x-4">

    <div
      v-for="nc in nutrient_categories"
      :key="nc.id"
      class="col-span-1 sm:w-3/4 md:2/3"
    >
      <h2 class="text-lg">{{nc.name}}s</h2>
      <NutrientProfileTable
        :nutrient_profile="nutrient_profile.filter(nutrient => nutrient.nutrient_category_id === nc.id)"
        :howManyGrams="howManyGrams"
        :defaultMassInGrams="defaultMassInGrams"
      />
    </div>

  </div>

  </template>
