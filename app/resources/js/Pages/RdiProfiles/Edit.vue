<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
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

const props = defineProps({
  rdi_profile: Object,
  rdi_profiles: Array,
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
    <Head :title="'Edit ' + rdi_profile.name" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('rdi-profiles.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="RDI profile" />
      <CrudNavBarCreate :enabled="can_create" text="New" :href="route('rdi-profiles.create')" />
      <div class="flex ml-auto">
        <CrudNavBarView :enabled="can_view" text="View original" :href="route('rdi-profiles.show', rdi_profile.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" text="Clone" :href="route('rdi-profiles.clone', rdi_profile.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(rdi_profile.id)" />
      </div>
    </CrudNavBar>

    <h1 class="mt-8 text-xl font-semibold">Edit {{rdi_profile.name}}</h1>

    <CreateOrEdit
      :rdi_profile="rdi_profile"
      :nutrient_categories="nutrient_categories"
      :create="false"
    />

    <!-- Search for an RDI profile -->
    <SearchForThingAndGo
      ref="searchDialog"
      :things="rdi_profiles"
      goRoute="rdi-profiles.show"
      label="Search for another RDI profile"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="rdi-profiles.destroy" thing="RDI profile" />

  </div>
</template>
