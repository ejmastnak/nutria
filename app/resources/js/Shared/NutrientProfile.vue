<script setup>
const props = defineProps({
  nutrient_profile: Array
})
</script>

<template>
  <div class="">

    <table class="mt-8 sm:table-fixed w-3/4 text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 bg-blue-100">
            Nutrient
          </th>
          <th scope="col" class="px-6 py-3 w-2/12 bg-blue-200 text-right">
            Amount
          </th>
          <th scope="col" class="px-6 py-3  w-2/12 bg-blue-100">
            Unit
          </th>
          <th scope="col" class="px-6 py-3  w-2/12 bg-blue-200 text-right">
            PDV
          </th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="nutrient in nutrient_profile"
          class="bg-white border-b"
        >
          <td scope="row" class="px-5 py-2 font-medium text-gray-900">
              {{nutrient.nutrient}}
          </td>
          <td class="px-6 py-2 text-right">
              {{nutrient.amount}}
          </td>
          <td class="px-6 py-2">
              {{nutrient.unit}}
          </td>
          <td class="px-6 py-2 text-right">
              {{nutrient.pdv}}%
          </td>
        </tr>
      </tbody>
    </table>

  </div>
  </template>
