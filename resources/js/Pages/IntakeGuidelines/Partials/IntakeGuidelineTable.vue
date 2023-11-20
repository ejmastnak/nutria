<script setup>
import { roundNonZero } from '@/utils/GlobalFunctions.js'
const props = defineProps({intake_guideline_nutrients: Array})
</script>

<template>
  <div class="border border-gray-300 roundNonZeroed-xl overflow-hidden">
    <table class="text-left w-full">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-4 py-3 bg-blue-50 w-6/12">
            Nutrient
          </th>
          <th scope="col" class="px-4 py-3 bg-blue-100 text-right whitespace-nowrap">
            RDI
          </th>
          <th scope="col" class="bg-blue-100"></th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="intake_guideline_nutrient in intake_guideline_nutrients"
          class="border-t text-gray-600 font-medium text-sm"
        >
          <td scope="row" class="px-4 py-2 whitespace-nowrap">
            {{intake_guideline_nutrient.nutrient.display_name}}
          </td>
          <td class="pl-3 py-2 text-right">
            {{roundNonZero(Number(intake_guideline_nutrient.rdi), intake_guideline_nutrient.nutrient.precision)}}
          </td>
          <td class="pl-1 pr-3 py-2 text-left whitespace-nowrap">
            {{intake_guideline_nutrient.nutrient.unit.name}}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</template>
