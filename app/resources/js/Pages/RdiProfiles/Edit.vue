<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  rdi_profile: Object,
  nutrient_categories: Array,
  can_create: Boolean,
  can_clone: Boolean,
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
    <Head :title="'Edit ' + rdi_profile.name" />

    <h1 class="text-xl font-semibold">Edit {{rdi_profile.name}}</h1>

    <CreateOrEdit
      :rdi_profile="rdi_profile"
      :nutrient_categories="nutrient_categories"
      :create="false"
    />

  </div>
</template>
