<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  ingredient: Object,
  can_delete: Boolean
})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head title="Edit Ingredient" />

    <h1 class="text-xl font-semibold">Edit Ingredient</h1>

    <table class="mt-8 sm:table-fixed w-3/4 text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 bg-blue-100">
            Nutrient
          </th>
          <th scope="col" class="px-6 py-3  w-2/12 bg-blue-200">
            Amount
          </th>
          <th scope="col" class="px-6 py-3  w-2/12 bg-blue-200">
            Unit
          </th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="ingredient_nutrient in ingredient.ingredient_nutrients" :key="ingredient_nutrient.nutrient_id"
          class="bg-white border-b"
        >
          <td scope="row" class="px-5 py-4 font-medium text-gray-900">
              {{ingredient_nutrient.nutrient.display_name}}
          </td>
          <td class="px-6 py-4">
              {{ingredient_nutrient.amount_per_100g}}
          </td>
          <td class="px-6 py-4">
              {{ingredient_nutrient.nutrient.unit.name}}
          </td>
        </tr>
      </tbody>
    </table>

  </div>
  </template>
