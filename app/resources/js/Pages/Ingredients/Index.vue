<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, MagnifyingGlassIcon, ArchiveBoxArrowDownIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
const props = defineProps({
  ingredients: Array,
  user_ingredients: Array,
  ingredient_categories: Array
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
    <Head title="Ingredients" />

    <div class="flex">
      <h1 class="text-xl font-semibold">Ingredients</h1>
      <PrimaryLinkButton
        :href="route('ingredients.create')"
        class="ml-auto"
      >
        New ingredient
      </PrimaryLinkButton>
    </div>

    <table class="mt-8 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 bg-blue-100">
            Name
          </th>
          <th scope="col" class="px-6 py-3  w-3/12 bg-blue-200">
            Type
          </th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="ingredient in ingredients" :key="ingredient.id"
          class="bg-white border-b"
        >
          <td scope="row" class="px-5 py-4 font-medium text-gray-900">
            <Link
              :href="route('ingredients.show', ingredient.id)"
              class="hover:underline hover:text-blue-700 rounded p-1"
            >
              {{ingredient.name}}
            </Link>
          </td>
          <td class="px-6 py-4">
            {{ingredient.ingredient_category.name}}
          </td>
        </tr>
      </tbody>
    </table>

  </div>
  </template>
