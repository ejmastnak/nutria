<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import debounce from "lodash/debounce";
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, DocumentDuplicateIcon, MagnifyingGlassIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import MyLink from '@/Components/MyLink.vue'
import H1 from '@/Components/H1ForIndex.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  food_lists: Array,
  can_create: Boolean
})

const cloneExistingDialog = ref(null)
const deleteDialog = ref(null)
const searchInput = ref(null)

// For fuzzy search over meal names
const filteredFoodLists = ref([])
const fuzzysortOptions = {
  key: 'name',
  all: true,
  limit: 10,
  threshold: -10000
}

const search = ref(sessionStorage.getItem('foodListsIndexSearchQuery') ?? "")

// Preserve food list search from previous visit to this page
onMounted(() => {
  if (search) {
    filteredFoodLists.value = fuzzysort.go(search.value.trim(), props.food_lists, fuzzysortOptions)
  }
})

watch(search, throttle(function (value) {
  filteredFoodLists.value = fuzzysort.go(value.trim(), props.food_lists, fuzzysortOptions)
}, 300))

// Preserve search query between page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('foodListsIndexSearchQuery', search.value);
})

// Preserve search query on manual page reload
window.onbeforeunload = function() {
  sessionStorage.setItem('foodListsIndexSearchQuery', search.value);
}

function resetSearch() {
  search.value = ""
  searchInput.value.focus()
}

// Updates filteredFoodLists after deleting a food list to ensure the meal
// disappears from display
function updateFuzzySearchOnDeletion(id) {
  filteredFoodLists.value = fuzzysort.go(search.value.trim(), props.food_lists, fuzzysortOptions)
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
    <Head title="Food Lists" />

    <!-- Title and new food list top row -->
    <div class="flex">

      <div class="mr-2 p-1">
        <H1 text="Food Lists" />
        <p class="mt-2 w-11/12 4 sm:w-2/3 text-gray-500">
          Use this page as an overview of food lists you have created.
        </p>
      </div>

      <div class="flex flex-col ml-auto w-fit">

        <!-- New food list button -->
        <PrimaryLinkButton
          :href="route('food-lists.create')"
          class="flex ml-auto items-center py-2.0 sm:py-2.5 mt-1 normal-case"
          :class="{'!bg-blue-200': !can_create}"
        >
          <PlusCircleIcon class="w-6 h-6" />
          <p class="ml-2 font-semibold text-base whitespace-nowrap">New food list</p>
        </PrimaryLinkButton>

        <!-- Clone food list button -->
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

    <!-- Food list table -->
    <section class="mt-8 border border-gray-200 px-4 py-2 rounded-xl shadow-sm bg-white">

      <!-- Input for search -->
      <div class="px-2 py-4">

        <label for="food-list-search" class="ml-1 text-sm text-gray-500">
          Search by food list name
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
          </div>

          <input
            type="text"
            id="food-list-search"
            ref="searchInput"
            class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
            v-model="search"
          />
        </div>
      </div>

      <table
        v-show="filteredFoodLists.length"
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
            v-for="foodList in filteredFoodLists.map(fl => fl.obj)" :key="foodList.id"
            class="border-b"
          >
            <!-- Link to food list show page -->
            <td scope="row" class="px-5 py-4 font-medium text-gray-900">
              <MyLink
                class="text-gray-800"
                :href="route('food-lists.show', foodList.id)"
              >
                {{foodList.name}}
              </MyLink>
            </td>
            <!-- Edit and delete icons -->
            <td>
              <div class="flex items-center px-1.5">

                <MyLink
                  class="mx-auto"
                  :href="route('food-lists.edit', foodList.id)"
                >
                  <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
                </MyLink>

                <button
                  type="button"
                  @click="deleteDialog.open(foodList.id)"
                  class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
                >
                  <TrashIcon class="w-5 h-5 hover:text-red-700" />
                </button>

              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-show="food_lists.length && filteredFoodLists.length === 0" class="px-6 py-4" >
        No results found. Try a less restrictive filter or search?
      </p>

      <div v-show="!food_lists.length" class="p-4 text-gray-800 max-w-lg">
        You haven't created any food lists yet!
        <span v-if="can_create">
          Consider first
          <MyLink
            :href="route('food-lists.create')"
            class="text-blue-500"
          >
            creating a new food list
          </MyLink>
          or
          <button
            type="button"
            class="text-blue-500 hover:text-blue-600 hover:underline p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
            @click="cloneExistingDialog.open()"
          >
            cloning an existing food list.
          </button>
        </span>
        <span v-else>You need to <MyLink :href="route('login')" class="text-blue-500">log in</MyLink> to create food lists.</span>
      </div>

    </section>

    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="food_lists"
      goRoute="food-lists.clone"
      label="Search for a food list to clone"
      title="Clone food list"
      action="Clone"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="food-lists.destroy" thing="food list" @delete="updateFuzzySearchOnDeletion" />

  </div>
</template>
