<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarView from '@/Shared/CrudNavBarView.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  intake_guideline: Object,
  intake_guidelines: Array,
  nutrient_categories: Array,
  can_view: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const searchDialog = ref(null)
const deleteDialog = ref(null)

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head :title="'Edit ' + intake_guideline.name" />

    <CrudNavBar>

      <!-- Desktop items -->
      <template v-slot:desktop-items>
        <CrudNavBarIndex :href="route('intake-guidelines.index')" />
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="intake guideline" />
        <CrudNavBarCreate :enabled="can_create" text="New" :href="route('intake-guidelines.create')" />
        <div class="flex ml-auto">
          <CrudNavBarView :enabled="can_view" text="View" :href="route('intake-guidelines.show', intake_guideline.id)" />
          <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('intake-guidelines.clone', intake_guideline.id)" />
          <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(intake_guideline.id)" />
        </div>
      </template>

      <!-- Always-displayed mobile item -->
      <template v-slot:mobile-displayed>
        <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="intake guideline" />
      </template>

      <!-- Mobile menu items -->
      <template v-slot:mobile-items>
        <CrudNavBarIndex :href="route('intake-guidelines.index')" />
        <CrudNavBarCreate :enabled="can_create" text="New" :href="route('intake-guidelines.create')" />
        <CrudNavBarView :enabled="can_view" text="View" :href="route('intake-guidelines.show', intake_guideline.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('intake-guidelines.clone', intake_guideline.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(intake_guideline.id)" />
      </template>

    </CrudNavBar>

    <H1 class="mt-8" text="Edit Intake Guideline" />

    <CreateOrEdit
      :intake_guideline="intake_guideline"
      :nutrient_categories="nutrient_categories"
      :create="false"
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

    <DeleteDialog ref="deleteDialog" deleteRoute="intake-guidelines.destroy" thing="intake guideline" />

  </div>
</template>
