<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import IntakeGuideline from './Partials/IntakeGuideline.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarEdit from '@/Shared/CrudNavBarEdit.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  intake_guideline: Object,
  intake_guidelines: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
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
  <div>
    <Head :title="intake_guideline.name" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('intake-guidelines.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="intake guideline" />
      <CrudNavBarCreate :enabled="can_create" :href="route('intake-guidelines.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('intake-guidelines.edit', intake_guideline.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('intake-guidelines.clone', intake_guideline.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(intake_guideline.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">
      <H1 :text="intake_guideline.name" />
      <!-- intake guideline pillbox label category -->
      <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
        Intake Guideline
      </div>
    </div>

    <section class="mt-8">
      <h2 class="text-lg">Recommended daily intakes</h2>
      <IntakeGuideline
        class="w-full mt-4"
        :intake_guideline_nutrients="intake_guideline.intake_guideline_nutrients"
        :nutrient_categories="nutrient_categories"
      />
    </section>

    <SearchForThingAndGo
      ref="searchDialog"
      :things="intake_guidelines"
      goRoute="intake-guidelines.show"
      label="Search for another intake guideline"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="intake-guidelines.destroy" thing="intake guideline" />

  </div>
</template>
