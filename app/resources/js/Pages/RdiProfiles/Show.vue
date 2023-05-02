<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, DocumentDuplicateIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import RdiProfile from './Partials/RdiProfile.vue'
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
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  rdi_profile: Object,
  rdi_profiles: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const howManyGrams = ref("100");
const defaultMassInGrams = 100

const searchDialog = ref(null)
const deleteDialog = ref(null)

const searchRdiProfile = ref({})
function search() {
  router.get(route('rdi-profiles.show', searchRdiProfile.value.id))
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
    <Head :title="rdi_profile.name" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('rdi-profiles.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="RDI profile" />
      <CrudNavBarCreate :enabled="can_create" :href="route('rdi-profiles.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('rdi-profiles.edit', rdi_profile.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('rdi-profiles.clone', rdi_profile.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(rdi_profile.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">
      <H1 :text="rdi_profile.name" />
      <!-- RDI profile pillbox label category -->
      <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
        RDI profile
      </div>
    </div>

    <section class="mt-8">
      <h2 class="text-lg">Recommended daily intakes</h2>
      <RdiProfile
        class="w-full mt-4"
        :rdi_profile_nutrients="rdi_profile.rdi_profile_nutrients"
        :nutrient_categories="nutrient_categories"
      />
    </section>

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
