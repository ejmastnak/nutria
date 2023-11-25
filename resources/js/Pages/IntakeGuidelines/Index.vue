<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import debounce from "lodash/debounce";
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { TrashIcon, MagnifyingGlassIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import H1 from '@/Components/H1ForIndex.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import MyLink from '@/Components/MyLink.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import DeleteDialog from '@/Components/DeleteDialog.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  intake_guidelines: Array,
  can_create: Boolean
})

let idToDelete = ref(null)
const deleteDialog = ref(null)
function deleteIntakeGuideline() {
  if (idToDelete.value) {
    router.delete(route('intake-guidelines.destroy', idToDelete.value), {
      onSuccess: () => {
        // Updates filtered intake guidelines after deletion to ensure the
        // deleted intake guideline disappears from display.
        search(searchQuery.value)
      }
    });
  }
  idToDelete.value = null
}

// For fuzzy search over intake guideline names
const filteredIntakeGuidelines = ref([])
const fuzzysortOptions = {
  key: 'name',
  all: true,
  limit: 10,
  threshold: -10000
}

const searchQuery = ref("")
function search(query) {
  filteredIntakeGuidelines.value = fuzzysort.go(query.trim(), props.intake_guidelines, fuzzysortOptions)
}

watch(searchQuery, throttle(function (value) {
  search(value)
}, 300))

// Preserve search query between page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('intakeGuidelineIndexSearchQuery', searchQuery.value);
})

// Preserve search query on manual page reload
window.onbeforeunload = function() {
  sessionStorage.setItem('intakeGuidelineIndexSearchQuery', searchQuery.value);
}

const searchInputRef = ref(null)
function clearSearch() {
  searchQuery.value = ""
  searchInputRef.value.focus()
}

// Preserve search from previous visit to this page
onMounted(() => {
  const storedSearchQuery = sessionStorage.getItem('intakeGuidelineIndexSearchQuery')
  if (storedSearchQuery) {
      searchQuery.value = storedSearchQuery
  }
  search(searchQuery.value)
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
    page="index"
    route_basename="intake-guidelines"
    :id="null"
    :things="intake_guidelines"
    thing="intake guidelines"
    :can_create="can_create"
  >
    <Head title="Intake Guidelines" />

    <!-- Title -->
    <div class="mr-2 p-1">
      <H1 text="Intake Guidelines" />
      <p class="mt-2 max-w-md text-gray-500">
        Use this page to view and manage your intake guidelines.
      </p>
    </div>

    <!-- Intake guideline table -->
    <section class="mt-8 border border-gray-200 px-4 py-2 rounded-xl shadow-sm bg-white">

      <!-- Input for search -->
      <div class="px-2 py-4 flex flex-wrap items-end gap-x-2 gap-y-2">
        <div>
          <label for="intake-guideline-search" class="ml-1 text-sm text-gray-500">
            Search by intake guideline name
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
            </div>

            <input
              type="text"
              id="intake-guideline-search"
              ref="searchInputRef"
              class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
              v-model="searchQuery"
            />
          </div>
        </div>

        <!-- Clear search button -->
        <div>
          <label for="clear-search" class="sr-only">
            Clear filter
          </label>
          <SecondaryButton
            type="button"
            id="clear-search"
            class="normal-case font-normal !tracking-normal !text-sm !px-2 h-fit"
            @click="clearSearch"
          >
            <XMarkIcon class="w-5 h-5" />
            <span class="text-gray-600 font-normal ml-1.5">Clear search</span>
          </SecondaryButton>
        </div>
      </div>

      <table
        v-show="filteredIntakeGuidelines.length"
        class="mt-2 w-full text-sm sm:text-base text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-blue-100">
          <tr>
            <th scope="col" class="px-6 py-3">
              Name
            </th>
            <th scope="col" class="px-6 py-3 w-16 bg-blue-100" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="intake_guideline in filteredIntakeGuidelines.map(ig => ig.obj)" :key="intake_guideline.id"
            class="border-b"
          >
            <!-- Link to intake guideline show page -->
            <td scope="row" class="px-5 py-4 font-medium text-gray-900">
              <MyLink
                class="text-gray-800"
                :href="route('intake-guidelines.show', intake_guideline.id)"
              >
                {{intake_guideline.name}}
              </MyLink>
            </td>
            <!-- Edit and delete icons -->
            <td>
              <div class="flex items-center px-1.5">

                <MyLink
                  class="mx-auto"
                  v-if="intake_guideline.can_update"
                  :href="route('intake-guidelines.edit', intake_guideline.id)"
                >
                  <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
                </MyLink>

                <button
                  type="button"
                  v-if="intake_guideline.can_delete"
                  @click="idToDelete = intake_guideline.id; deleteDialog.open()"
                  class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
                >
                  <TrashIcon class="w-5 h-5 hover:text-red-700" />
                </button>

              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-show="intake_guidelines.length && filteredIntakeGuidelines.length === 0" class="px-6 py-4" >
        No results found. Try a less restrictive filter or search?
      </p>

    </section>

    <DeleteDialog
      ref="deleteDialog"
      description="intake guideline"
      @delete="deleteIntakeGuideline"
      @cancel="idToDelete = null"
    />

  </SidebarLayout>
</template>
