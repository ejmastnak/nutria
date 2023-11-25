<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import debounce from "lodash/debounce";
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, DocumentDuplicateIcon, MagnifyingGlassIcon, PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import H1 from '@/Components/H1ForIndex.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import MyLink from '@/Components/MyLink.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
  meals: Array,
  can_create: Boolean
})

let idToDelete = ref(null)
const deleteDialog = ref(null)
function deleteMeal() {
  if (idToDelete.value) {
    router.delete(route('meals.destroy', idToDelete.value), {
      onSuccess: () => {
        // Updates filtered meals after deleting an meal to ensure the deleted
        // meal disappears from display.
        search(searchQuery.value)
      }
    });
  }
  idToDelete.value = null
}

// For fuzzy search over meal names
const filteredMeals = ref([])
const fuzzysortOptions = {
  key: 'name',
  all: true,
  limit: 10,
  threshold: -10000
}

const searchQuery = ref("")
function search(query) {
  filteredMeals.value = fuzzysort.go(query.trim(), props.meals, fuzzysortOptions)
}

watch(searchQuery, throttle(function (value) {
  search(value)
}, 300))

// Preserve search query between page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('mealsIndexSearchQuery', searchQuery.value);
})

// Preserve search query on manual page reload
window.onbeforeunload = function() {
  sessionStorage.setItem('mealsIndexSearchQuery', searchQuery.value);
}

const searchInputRef = ref(null)
function clearSearch() {
  searchQuery.value = ""
  searchInputRef.value.focus()
}

// Preserve meal search from previous visit to this page
onMounted(() => {
  const storedSearchQuery = sessionStorage.getItem('mealsIndexSearchQuery')
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
    route_basename="meals"
    :id="null"
    :things="meals"
    thing="meal"
    :can_create="can_create"
  >
    <Head title="Meals" />

    <!-- Title -->
    <div class="mr-2 p-1">
      <H1 text="Meals" />
      <p class="mt-2 w-11/12 4 sm:w-2/3 text-gray-500">
        Use this page as an overview of meals you have created.
      </p>
    </div>

    <!-- Meal table -->
    <section class="mt-8 border border-gray-200 px-4 py-2 rounded-xl shadow-sm bg-white">

      <!-- Input for search -->
      <div class="px-2 py-4 flex flex-wrap items-end gap-x-2 gap-y-2">
        <div>
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
              ref="searchInputRef"
              class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
              v-model="searchQuery"
            />
          </div>
        </div>

        <!-- Clear search button -->
        <div>
          <label for="clear-search" class="sr-only">
            Clear search
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
        v-show="filteredMeals.length"
        class="mt-2 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-blue-100">
          <tr>
            <th scope="col" class="px-6 py-3 w-11/12">
              Name
            </th>
            <th scope="col" class="px-6 py-3 w-1/12 bg-blue-100" />
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="meal in filteredMeals.map(fm => fm.obj)" :key="meal.id"
            class="border-b"
          >
            <!-- Link to meal show page -->
            <td scope="row" class="px-5 py-4 font-medium text-gray-900">
              <MyLink
                class="text-gray-800"
                :href="route('meals.show', meal.id)"
              >
                {{meal.name}}
              </MyLink>
            </td>
            <!-- Edit and delete icons -->
            <td>
              <div class="flex items-center px-1.5">

                <MyLink
                  class="mx-auto"
                  :href="route('meals.edit', meal.id)"
                >
                  <PencilSquareIcon class="w-5 h-5" />
                </MyLink>

                <button
                  type="button"
                  @click="idToDelete = meal.id; deleteDialog.open()"
                  class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
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
          <MyLink :href="route('meals.create')" class="text-blue-500">
            creating a new meal.
          </MyLink>
        </span>
        <span v-else>You need to <MyLink :href="route('login')" class="text-blue-500">log in</MyLink> to create meals.</span>
      </div>

    </section>

    <DeleteDialog
      ref="deleteDialog"
      description="meal"
      @delete="deleteMeal"
      @cancel="idToDelete = null"
    />

  </SidebarLayout>
</template>
