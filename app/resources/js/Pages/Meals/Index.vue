<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import debounce from "lodash/debounce";

import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, DocumentDuplicateIcon, MagnifyingGlassIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import H1 from '@/Components/H1ForIndex.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  meals: Array,
  can_create: Boolean
})

const cloneExistingDialog = ref(null)
const deleteDialog = ref(null)
const searchInput = ref(null)

// For fuzzy search over meal names
const filteredMeals = ref([])
const fuzzysortOptions = {
  key: 'name',
  all: true,
  limit: 10,        // don't return more results than this
  threshold: -10000    // don't return lower scores than this
}

const search = ref(sessionStorage.getItem('mealsIndexSearchQuery') ?? "")

// Preserve meal search from previous visit to this page
onMounted(() => {
  if (search) {
    filteredMeals.value = fuzzysort.go(search.value.trim(), props.meals, fuzzysortOptions)
  }
})

watch(search, throttle(function (value) {
  filteredMeals.value = fuzzysort.go(value.trim(), props.meals, fuzzysortOptions)
}, 300))

// Preserve search query between page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('mealsIndexSearchQuery', search.value);
})

function resetSearch() {
  search.value = ""
  searchInput.value.focus()
}

// Updates filteredMeals after deleting a meal to ensure the meal disappears
// from display
function updateFuzzySearchOnDeletion(id) {
  filteredMeals.value = fuzzysort.go(search.value.trim(), props.meals, fuzzysortOptions)
}

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">
    <Head title="Meals" />

    <!-- Title and new meal top row -->
    <div class="flex">

      <div class="mr-2 p-1">
        <H1 text="Meals" />
        <p class="mt-2 w-11/12 4 sm:w-2/3 text-gray-500">
          Use this page as an overview of meals you have created.
        </p>
      </div>

      <div class="flex flex-col ml-auto w-fit">

        <!-- New meal button -->
        <PrimaryLinkButton
          :href="route('meals.create')"
          class="flex ml-auto items-center py-2.0 sm:py-2.5 mt-1 normal-case"
          :class="{'!bg-blue-200': !can_create}"
        >
          <PlusCircleIcon class="w-6 h-6" />
          <p class="ml-2 font-semibold text-base whitespace-nowrap">New meal</p>
        </PrimaryLinkButton>

        <!-- Clone meal button -->
        <SecondaryButton
          class="flex ml-auto items-center mt-1 normal-case"
          :class="{'!text-gray-300': !can_create}"
          @click="cloneExistingDialog.open()"
        >
          <DocumentDuplicateIcon class="w-6 h-6" />
          <p class="ml-2 font-semibold text-base whitespace-nowrap">Clone existing</p>
        </SecondaryButton>

      </div>
    </div>

    <!-- Meal table -->
    <section class="mt-8 border border-gray-200 px-4 py-2 rounded-xl shadow-sm bg-white">

      <!-- Input for search -->
      <div class="px-2 py-4">

        <label for="meal-search" class="ml-1 text-sm text-gray-500">
          Search by meal name
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
          </div>

          <input
            type="text"
            id="meal-search"
            ref="searchInput"
            class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
            v-model="search"
          />
        </div>
      </div>

      <table
        v-show="filteredMeals.length"
        class="mt-2 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-blue-100">
          <tr>
            <th scope="col" class="px-6 py-3">
              Name
            </th>
            <th scope="col" class="px-6 py-3  w-1/12 bg-blue-100" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="meal in filteredMeals.map(fm => fm.obj)" :key="meal.id"
            class="border-b"
          >
            <!-- Link to meal show page -->
            <td scope="row" class="px-5 py-4 font-medium text-gray-900">
              <Link
                class="text-gray-800 hover:text-blue-600 hover:underline"
                :href="route('meals.show', meal.id)"
              >
                {{meal.name}}
              </Link>
            </td>
            <!-- Edit and delete icons -->
            <td>
              <div class="flex items-center px-1.5">

                <Link
                  class="mx-auto"
                  :href="route('meals.edit', meal.id)"
                >
                <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
                </Link>

                <button
                  type="button"
                  @click="deleteDialog.open(meal.id)"
                  class="mx-auto"
                >
                  <TrashIcon class="w-5 h-5 hover:text-red-700" />
                </button>

              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-show="meals.length && filteredMeals.length === 0" class="px-6 py-4" >
        No results found. Try a less restrictive filter or search?
      </p>

      <div v-show="!meals.length" class="p-4 text-gray-800 max-w-lg">
        You haven't created any meals yet!
        <span v-if="can_create">
          Consider first
          <Link :href="route('meals.create')" class="text-blue-500 hover:text-blue-600 hover:underline">creating a new meal</Link>
          or
          <button
            type="button"
            class="text-blue-500 hover:text-blue-600 hover:underline"
            @click="cloneExistingDialog.open()"
          >
            cloning an existing meal.
          </button>
        </span>
        <span v-else>You need to <Link :href="route('login')" class="text-blue-500 hover:text-blue-600 hover:underline">log in</Link> to create meals.</span>
      </div>

    </section>

    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="meals"
      goRoute="meals.clone"
      label="Search for a meal to clone"
      title="Clone meal"
      action="Clone"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="meals.destroy" thing="meal" @delete="updateFuzzySearchOnDeletion" />

  </div>
</template>
