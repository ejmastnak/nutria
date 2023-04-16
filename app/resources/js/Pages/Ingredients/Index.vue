<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, MagnifyingGlassIcon, ArchiveBoxArrowDownIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
const props = defineProps({
  ingredients: Array,
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

    <h2 class="text-xl font-semibold">Ingredients</h2>
    <pre>
      {{ingredients}}
    </pre>

  </div>
  </template>
