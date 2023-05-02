<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import debounce from "lodash/debounce";

import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, DocumentDuplicateIcon, MagnifyingGlassIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import MultiselectListbox from '@/Shared/MultiselectListbox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  ingredients: Array,
  user_ingredients: Array,
  ingredient_categories: Array,
  can_create: Boolean
})

const cloneExistingDialog = ref(null)
const deleteDialog = ref(null)
const fdaSearchInput = ref(null)
const userSearchInput = ref(null)

// For filtering ingredients by category
const selectedFdaCategories = ref([])
const selectedUserCategories = ref([])

// For fuzzy search over ingredient names
const filteredIngredients = ref([])
const filteredUserIngredients = ref([])
const fuzzysortOptions = {
  key: 'name',
  limit: 25,        // don't return more results than this
  threshold: -10000    // don't return lower scores than this
}
const fuzzysortUserOptions = {
  key: 'name',
  all: true,
  limit: 15,
  threshold: -10000
}

const fdaSearchQuery = ref(sessionStorage.getItem('ingredientsIndexFdaSearchQuery') ?? "")
const userSearchQuery = ref(sessionStorage.getItem('ingredientsIndexUserSearchQuery') ?? "")

// Preserve ingredient search from previous visit to this page
onMounted(() => {
  if (fdaSearchQuery) {
    filteredIngredients.value = fuzzysort.go(fdaSearchQuery.value.trim(), props.ingredients, fuzzysortOptions)
  }
  if (userSearchQuery) {
    filteredUserIngredients.value = fuzzysort.go(userSearchQuery.value.trim(), props.user_ingredients, fuzzysortUserOptions)
  }
})

watch(fdaSearchQuery, throttle(function (value) {
  filteredIngredients.value = fuzzysort.go(value.trim(), props.ingredients, fuzzysortOptions)
}, 300))
watch(userSearchQuery, throttle(function (value) {
  filteredUserIngredients.value = fuzzysort.go(value.trim(), props.user_ingredients, fuzzysortUserOptions)
}, 300))

// Preserve search query between page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('ingredientsIndexFdaSearchQuery', fdaSearchQuery.value);
  sessionStorage.setItem('ingredientsIndexUserSearchQuery', userSearchQuery.value);
})

function resetFdaSearch() {
  fdaSearchQuery.value = ""
  selectedFdaCategories.value = []
  fdaSearchInput.value.focus()
}

function resetUserSearch() {
  userSearchQuery.value = ""
  selectedUserCategories.value = []
  userSearchInput.value.focus()
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
    <Head title="Ingredients" />

    <!-- Title and new ingredient top row -->
    <div class="flex">

      <div class="mr-2 p-1">
        <h1 class="font-medium text-2xl text-gray-900">Ingredients</h1>
        <p class="mt-2 w-11/12 4 sm:w-2/3 text-gray-500">
          Use this page as an overview of ingredients from the FDA database—or ingredients you have created yourself.
        </p>
      </div>

      <div class="flex flex-col ml-auto w-fit">

        <!-- New ingredient button -->
        <PrimaryLinkButton
          :href="route('ingredients.create')"
          class="flex ml-auto items-center py-2.0 sm:py-2.5 mt-1 normal-case"
          :class="{'!bg-blue-200': !can_create}"
        >
          <PlusCircleIcon class="w-6 h-6" />
          <p class="ml-2 font-semibold text-base whitespace-nowrap">New ingredient</p>
        </PrimaryLinkButton>

        <!-- Clone ingredient button -->
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

    <TabGroup :defaultIndex="0">

      <TabList class="mt-4 rounded-xl w-fit border-b space-x-2">


        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none focus:border-b-2 focus:border-blue-500 transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            FDA Ingredients
          </button>
        </Tab>

        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none focus:border-b-2 focus:border-blue-500 transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            Your Ingredients
          </button>
        </Tab>

      </TabList>

      <TabPanels class="mt-2">

        <!-- Ingredients in FDA database -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl">
          <section class="border border-gray-200 p-4 rounded-xl shadow-sm bg-white">
            <h2 class="text-lg">Ingredients from the FDA database</h2>

            <div class="flex flex-col sm:flex-row items-start sm:items-end px-2 py-4">

              <!-- Input for search -->
              <div class="sm:mr-3">
                <label for="fda-search" class="ml-1 text-sm text-gray-500">
                  Search by ingredient name
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
                  </div>

                  <input
                    type="text"
                    id="fda-search"
                    ref="fdaSearchInput"
                    class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                    v-model="fdaSearchQuery"
                  />
                </div>
              </div>

              <!-- Select menu for ingredient category -->
              <div class="flex items-end">
                <div class="mt-2 sm:mt-0 sm:ml-2">
                  <MultiselectListbox
                    :options="ingredient_categories"
                    labelText="Filter by type"
                    :modelValue="selectedFdaCategories"
                    @update:modelValue="newValue => selectedFdaCategories = newValue"
                    width="full min-w-[6rem] max-w-full"
                  />
                </div>

                <div class="">
                  <label for="clear-fda-filters" class="sr-only">
                    Clear filter
                  </label>
                  <SecondaryButton
                    type="button"
                    id="clear-fda-filters"
                    class="normal-case font-normal !tracking-normal !text-sm !px-2 h-fit ml-2"
                    @click="resetFdaSearch"
                  >
                    <XMarkIcon class="w-5 h-5" />
                    <span class="text-gray-600 font-normal ml-1.5">Clear filter</span>
                  </SecondaryButton>
                </div>
              </div>

            </div>

            <table
              v-show="filteredIngredients.length"
              class="mt-2 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
              <thead class="text-xs text-gray-700 uppercase bg-blue-100">
                <tr>
                  <th scope="col" class="px-6 py-3">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-3  w-4/12">
                    Type
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ingredient in filteredIngredients.map(fi => fi.obj)" :key="ingredient.id"
                  v-show="selectedFdaCategories.length === 0 || selectedFdaCategories.includes(ingredient.ingredient_category_id)"
                  class="border-b"
                >
                  <td scope="row" class="px-5 py-4 font-medium text-gray-900">
                    <Link
                      :href="route('ingredients.show', ingredient.id)"
                      class="text-gray-800 hover:text-blue-600 hover:underline"
                      preserve-state
                    >
                      {{ingredient.name}}
                    </Link>
                  </td>
                  <td class="px-6 py-4">
                    {{ingredient.ingredient_category.name}}
                  </td>
                </tr>
              </tbody>
            </table>
          </section>
        </TabPanel>

        <!-- User ingredients -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl">
          <section v-if="user_ingredients.length" class="border border-gray-200 shadow-sm p-4 rounded-xl bg-white" >
            <h2 class="text-lg">Your ingredients</h2>

            <div class="p-4 flex">
              <div>

                <label for="user-search" class="ml-1 text-sm text-gray-500">
                  Search by ingredient name
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
                  </div>

                  <input
                    type="text"
                    id="user-search"
                    ref="userSearchInput"
                    class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                    v-model="userSearchQuery"
                  />
                </div>
              </div>

              <div class="flex items-end">
                <div class="mt-2 sm:mt-0 sm:ml-2">
                  <MultiselectListbox
                    :options="ingredient_categories"
                    labelText="Filter by type"
                    :modelValue="selectedUserCategories"
                    @update:modelValue="newValue => selectedUserCategories = newValue"
                    width="full min-w-[6rem] max-w-full"
                  />
                </div>

                <div class="">
                  <label for="clear-user-filters" class="sr-only">
                    Clear filter
                  </label>
                  <SecondaryButton
                    type="button"
                    id="clear-user-filters"
                    class="normal-case font-normal !tracking-normal !text-sm !px-2 h-fit ml-2"
                    @click="resetUserSearch"
                  >
                    <XMarkIcon class="w-5 h-5" />
                    <span class="text-gray-600 font-normal ml-1.5">Clear filter</span>
                  </SecondaryButton>
                </div>
              </div>
            </div>

            <table class="mt-2 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 bg-blue-100">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-3  w-3/12 bg-blue-200">
                    Type
                  </th>
                  <th scope="col" class="px-6 py-3  w-1/12 bg-blue-100" />
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ingredient in filteredUserIngredients.map(fi => fi.obj)" :key="ingredient.id"
                  v-show="selectedUserCategories.length === 0 || selectedUserCategories.includes(ingredient.ingredient_category_id)"
                  class="border-b"
                >
                  <td scope="row" class="px-5 py-4 font-medium text-gray-900">
                    <Link
                      class="text-gray-800 hover:text-blue-600 hover:underline"
                      :href="route('ingredients.show', ingredient.id)"
                    >
                      {{ingredient.name}}
                    </Link>
                  </td>
                  <td class="px-6 py-4">
                    {{ingredient.ingredient_category.name}}
                  </td>
                  <td>
                    <div class="flex items-center px-1.5">

                      <Link
                        class="mx-auto"
                        :href="route('ingredients.edit', ingredient.id)"
                      >
                      <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
                      </Link>

                      <button
                        type="button"
                        @click="deleteDialog.open(ingredient.id)"
                        class="mx-auto"
                      >
                        <TrashIcon class="w-5 h-5 hover:text-red-700" />
                      </button>

                    </div>
                  </td>
                </tr>
              </tbody>
              <p
                v-show="filteredUserIngredients.filter((ing) => selectedUserCategories.length === 0 || selectedUserCategories.includes(ing.obj.ingredient_category_id)).length === 0"
                class="px-6 py-4" >
                No results found. Try a less restrictive filter or search?
              </p>
            </table>
          </section>
          <section v-else class="p-4 text-gray-800 max-w-lg">
            You haven't created any ingredients yet!
            <span v-if="can_create">
              Consider first
              <Link :href="route('ingredients.create')" class="text-blue-500 hover:text-blue-600 hover:underline">creating a new ingredient</Link>
              or
              <button
                type="button"
                class="text-blue-500 hover:text-blue-600 hover:underline"
                @click="cloneExistingDialog.open()"
              >
                cloning an existing ingredient.
              </button>
            </span>
            <span v-else>You need to <Link :href="route('login')" class="text-blue-500 hover:text-blue-600 hover:underline">log in</Link> to create ingredients.</span>
          </section>
        </TabPanel>

      </TabPanels>
    </TabGroup>

    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="ingredients.concat(user_ingredients)"
      goRoute="ingredients.clone"
      label="Search for an ingredient to clone"
      title="Clone ingredient"
      action="Clone"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="ingredients.destroy" thing="ingredient" />

  </div>
</template>
