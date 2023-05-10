<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarView from '@/Shared/CrudNavBarView.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarCloneButton from '@/Shared/CrudNavBarCloneButton.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  intake_guideline: Object,
  intake_guidelines: Array,
  nutrient_categories: Array,
  can_view: Boolean,
  can_create: Boolean,
  clone: Boolean
})

const searchDialog = ref(null)
const cloneExistingDialog = ref(null)

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head title="New Intake Guideline" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('intake-guidelines.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="intake guideline" />
      <div class="flex ml-auto">
        <CrudNavBarView v-if="clone" :enabled="can_view" text="View original" :href="route('intake-guidelines.show', intake_guideline.id)" />
        <CrudNavBarCreate v-if="clone" :enabled="can_create" text="New" :href="route('intake-guidelines.create')" />
        <CrudNavBarCloneButton v-if="!clone" :enabled="can_create" @wasClicked="cloneExistingDialog.open()" text="Clone" />
      </div>
    </CrudNavBar>

    <H1 class="mt-8" text="New Intake Guideline" />
    <p v-if="clone && intake_guideline" class="text-gray-700">(Cloned from {{intake_guideline.name}})</p>

    <CreateOrEdit
      :intake_guideline="intake_guideline"
      :nutrient_categories="nutrient_categories"
      :create="true"
    />

    <!-- Search for an intake guideline -->
    <SearchForThingAndGo
      ref="searchDialog"
      :things="intake_guidelines"
      goRoute="intake-guidelines.show"
      label="Search for another intake guideline"
      title=""
      action="Go"
    />

    <!-- Clone an existing intake guideline -->
    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="intake_guidelines"
      goRoute="intake-guidelines.clone"
      label="Search for an intake guideline to clone"
      title="Clone intake guideline"
      action="Clone"
    />

  </div>
</template>
