<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import H1 from '@/Components/H1ForCrud.vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import CreateOrEdit from './Partials/CreateOrEdit.vue'

const props = defineProps({
  intake_guideline: Object,
  nutrients: Array,
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
    :page="intake_guideline ? 'clone' : 'create'"
    route_basename="intake-guidelines"
    :id="intake_guideline ? intake_guideline.id : null"
    :things="intake_guidelines"
    thing="intake guidelines"
    :can_view="can_view"
    :can_create="can_create"
    :can_clone="can_clone"
    :can_update="can_update"
    :can_delete="can_delete"
  >
    <Head title="New Intake Guideline" />

    <H1 text="New Intake Guideline" />
    <p v-if="intake_guideline" class="text-gray-700">(Cloned from {{intake_guideline.name}})</p>

    <CreateOrEdit
      :intake_guideline="intake_guideline"
      :nutrient_categories="nutrient_categories"
      :nutrients="nutrients"
      :create="true"
    />

  </SidebarLayout>
</template>
