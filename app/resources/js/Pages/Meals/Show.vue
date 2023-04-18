<script setup>
import { Head, Link } from '@inertiajs/vue3'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'

const props = defineProps({
  meal: Object,
  nutrient_profile: Array,
  can_edit: Boolean,
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
    <Head title="Meal" />

    <h1 class="text-xl font-semibold">{{meal.name}}</h1>

    <div class="">
      <PrimaryLinkButton
        class="ml-auto"
        :href="route('meals.index')"
      >
        Back
      </PrimaryLinkButton>

      <SecondaryLinkButton
        v-if="can_edit"
        class="ml-auto"
        :href="route('meals.edit', meal.id)"
      >
        Edit
      </SecondaryLinkButton>

      <SecondaryLinkButton
        v-if="can_delete"
        class="ml-auto"
        :href="route('meals.destroy', meal.id)"
        as="button"
        method="delete"
      >
        Delete
      </SecondaryLinkButton>


    </div>

    <table class="mt-8 sm:table-fixed w-3/4 text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 bg-blue-100">
            Ingredient
          </th>
          <th scope="col" class="px-6 py-3 w-2/12 bg-blue-200 text-right">
            Amount
          </th>
          <th scope="col" class="px-6 py-3  w-2/12 bg-blue-100">
            Unit
          </th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="meal_ingredient in meal.meal_ingredients"
          :key="meal_ingredient.id"
          class="bg-white border-b"
        >
          <td scope="row" class="px-5 py-2 font-medium text-gray-900">
            {{meal_ingredient.ingredient.name}}
          </td>
          <td class="px-6 py-2 text-right">
            {{meal_ingredient.amount}}
          </td>
          <td class="px-6 py-2">
            {{meal_ingredient.unit.name}}
          </td>
        </tr>
      </tbody>
    </table>

    <NutrientProfile :nutrient_profile="nutrient_profile" />

  </div>
  </template>
