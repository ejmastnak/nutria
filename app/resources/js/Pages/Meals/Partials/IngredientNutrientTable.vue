<script setup>
const props = defineProps({
  ingredient_nutrients: Array,
})
</script>

<template>
  <div class="border border-gray-300 rounded-xl overflow-hidden">
    <table class="text-sm sm:text-base text-left w-full">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-4 py-3 bg-blue-50">
            Nutrient
          </th>
          <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
            Amount
          </th>
          <th scope="col" class="px-4 py-3 bg-blue-50 text-right">
            Unit
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="ingredient_nutrient in ingredient_nutrients"
          class="border-t text-gray-600"
        >
          <td scope="row" class="px-5 py-2">
            {{ingredient_nutrient.nutrient.display_name}}
          </td>
          <td class="px-4 py-2 text-right">
            {{ingredient_nutrient.amount_per_100g}}
          </td>
          <td class="px-4 py-2 text-right">
            {{ingredient_nutrient.nutrient.unit.name}}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  </template>
