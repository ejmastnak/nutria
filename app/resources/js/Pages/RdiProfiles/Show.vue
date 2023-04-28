<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, DocumentDuplicateIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import RdiProfile from './Partials/RdiProfile.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  rdi_profile: Object,
  rdi_profiles: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean
})

const howManyGrams = ref("100");
const defaultMassInGrams = 100

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

    <div class="flex items-center space-x-4 -mt-2 border border-gray-300 p-1 px-4 rounded-xl">

      <Link
        v-if="can_edit"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('rdi-profiles.edit', rdi_profile.id)"
      >
      <div class="flex">
        <PencilSquareIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Edit</p>
      </div>
      </Link>

      <Link
        v-if="can_clone"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('rdi-profiles.clone', rdi_profile.id)"
      >
      <div class="flex">
        <DocumentDuplicateIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Clone</p>
      </div>
      </Link>

      <button
        v-if="can_delete"
        type="button"
        @click="deleteDialog.open(rdi_profile.id)"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
      >
      <div class="flex">
        <TrashIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Delete</p>
      </div>
      </button>

      <form
        @submit.prevent="search"
        class="!ml-auto"
      >
        <FuzzyCombobox
          labelText="Search for another RDI profile"
          :options="rdi_profiles"
          v-model="searchRdiProfile"
        />
      </form>

    </div>

    <div class="mt-10">
      <h1 class="text-xl w-2/3">{{rdi_profile.name}}</h1>
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

    <DeleteDialog ref="deleteDialog" deleteRoute="rdi-profiles.destroy" thing="RDI profile" />

  </div>
</template>
