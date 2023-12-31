<script setup>
import { round } from '@/utils/GlobalFunctions.js'

const props = defineProps({
  nutrientProfile: Array,
  scaleFactor: Number,
})

// Gradient of Tailwind color classes from red to green
const gradient = [
  'bg-red-300',
  'bg-red-200',
  'bg-red-100',
  'bg-orange-100',
  'bg-amber-100',
  'bg-yellow-100',
  'bg-lime-100',
  'bg-lime-200',
  'bg-lime-300',
  'bg-lime-400',
  'bg-lime-600 font-medium text-white',
]

function bg(pdv) {
  // Linearly maps the PDV range [0, 100] to the index range
  // [0, gradient.length - 2], reserving gradient[gradient.length - 1]  for PDV
  // values over 100.
  if (pdv < 0) return gradient[0];
  else if (pdv > 100) return gradient[gradient.length - 1];
  else {
    const idx = Math.floor((gradient.length - 1) * pdv/100)
    return gradient[idx]
  }
}

</script>

<template>
  <div class="border border-gray-300 rounded-xl w-full overflow-hidden">

    <table class="text-left w-full">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-4 py-3 bg-blue-50">
            Nutrient
          </th>
          <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
            Amount
          </th>
          <th scope="col" class="px-4 py-3 bg-blue-50 text-right whitespace-nowrap">
            % DV
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="nutrient in nutrientProfile"
          class="border-t text-gray-600 font-medium text-sm"
        >
          <td scope="row" class="px-4 py-2 whitespace-nowrap">
            {{nutrient.nutrient}}
          </td>
          <td class="px-3 py-2 text-right whitespace-nowrap">
            {{round(nutrient.amount * scaleFactor, precision=nutrient.precision)}}
            {{nutrient.unit}}
          </td>

          <td class="px-3 py-2 text-right font-semibold text-gray-600" :class="nutrient.pdv !== null ? bg(nutrient.pdv * scaleFactor) : ''">
            <p v-if="nutrient.pdv !== null">{{round(nutrient.pdv * scaleFactor, precision=0)}}<span class="ml-px">%</span></p>
            <p class="text-center" v-else>–</p>
          </td>
        </tr>
      </tbody>
    </table>

  </div>

</template>
