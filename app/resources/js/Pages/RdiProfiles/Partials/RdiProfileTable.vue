<script setup>
import { round } from '@/utils/GlobalFunctions.js'
const props = defineProps({rdi_profile_nutrients: Array})
</script>

<template>
  <div class="border border-gray-300 rounded-xl overflow-hidden">
    <table class="text-left w-full">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-4 py-3 bg-blue-50">
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
          v-for="rdi_profile_nutrient in rdi_profile_nutrients"
          class="border-t text-gray-600 font-medium text-sm"
        >
          <td scope="row" class="px-4 py-2">
            {{rdi_profile_nutrient.nutrient.display_name}}
          </td>
          <td class="pl-3 py-2 text-right">
            {{round(Number(rdi_profile_nutrient.rdi), rdi_profile_nutrient.nutrient.precision)}}
          </td>
          <td class="px-1 py-2 text-left whitespace-nowrap">
            {{rdi_profile_nutrient.nutrient.unit.name}}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</template>
