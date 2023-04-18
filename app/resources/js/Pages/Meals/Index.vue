<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, MagnifyingGlassIcon, ArchiveBoxArrowDownIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
const props = defineProps({
  meals: Array
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
    <Head title="Meals" />

    <div class="flex">
      <h1 class="text-xl font-semibold">Meals</h1>
      <PrimaryLinkButton
        :href="route('meals.create')"
        class="ml-auto"
      >
        New meal
      </PrimaryLinkButton>
    </div>

    <table v-if="meals.length" class="mt-8 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
      <tbody>
        <tr 
          v-for="meal in meals" :key="meal.id"
          class="bg-white border-b"
        >
          <td scope="row" class="px-5 py-4 font-medium text-gray-900">
            <Link
              :href="route('meals.show', meal.id)"
              class="hover:underline hover:text-blue-700 rounded p-1"
            >
              {{meal.name}}
            </Link>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else class="mt-8">
      You haven't made any meals yet!
    </div>

  </div>
  </template>
