<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import H1 from '@/Components/H1ForCrud.vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  intake_guideline: Object,
  nutrient_categories: Array,
  intake_guidelines: Array,
  can_view: Boolean,
  can_create: Boolean,
  can_clone: Boolean,
  can_update: Boolean,
  can_delete: Boolean,
})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <SidebarLayout
    page="edit"
    route_basename="intake-guidelines"
    :id="intake_guideline.id"
    :things="intake_guidelines"
    thing="intake guideline"
    :can_view="can_view"
    :can_create="can_create"
    :can_clone="can_clone"
    :can_update="can_update"
    :can_delete="can_delete"
  >
    <Head :title="'Edit ' + intake_guideline.name" />
    <H1 class="mt-8" :text="'Edit ' + intake_guideline.name" />

    <CreateOrEdit
      :intake_guideline="intake_guideline"
      :nutrient_categories="nutrient_categories"
      :create="false"
    />

  </SidebarLayout>
</template>
