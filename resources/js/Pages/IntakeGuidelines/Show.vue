<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import H1 from '@/Components/H1ForCrud.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import IntakeGuideline from './Partials/IntakeGuideline.vue'

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

const searchDialog = ref(null)
const deleteDialog = ref(null)

const searchIntakeGuideline = ref({})
function search() {
  router.get(route('intake-guidelines.show', searchIntakeGuideline.value.id))
}

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <SidebarLayout
    page="show"
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
    <Head :title="intake_guideline.name" />

    <div>
      <H1 :text="intake_guideline.name" />
      <div class="flex items-center mt-2">
        <!-- Intake guideline label and priority pillboxes -->
        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Intake Guideline
        </div>
        <div v-if="intake_guideline.priority !== null" class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Priority: {{intake_guideline.priority}}
        </div>
      </div>
    </div>

    <section class="mt-4">
      <p class="max-w-md text-gray-700 text-sm">
        This page shows the recommended daily intakes (RDIs) for macronutrients, minerals, and vitamins for this intake guideline.
      </p>

      <!-- Description -->
      <div v-if="intake_guideline.description" class="mt-4">
        <p class="font-medium text-gray-600">
          Description
        </p>
        {{intake_guideline.description}}
      </div>

      <IntakeGuideline
        class="w-full mt-6"
        :intake_guideline_nutrients="intake_guideline.intake_guideline_nutrients"
        :nutrient_categories="nutrient_categories"
      />
    </section>

  </SidebarLayout>
</template>
