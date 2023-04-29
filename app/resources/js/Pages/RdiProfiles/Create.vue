<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  rdi_profile: Object,
  nutrient_categories: Array,
  can_create: Boolean,
  clone: Boolean
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
    <Head title="New RDI Profile" />

    <h1 class="text-xl font-semibold">New RDI Profile</h1>
    <p v-if="clone && rdi_profile" class="text-gray-700">(Cloned from {{rdi_profile.name}})</p>

    <CreateOrEdit
      :rdi_profile="rdi_profile"
      :nutrient_categories="nutrient_categories"
      :create="true"
    />

  </div>
</template>
