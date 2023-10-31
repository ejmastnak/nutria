<script setup>
import fuzzysort from 'fuzzysort'
import throttle from "lodash/throttle";
import debounce from "lodash/debounce";

import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { TrashIcon, PlusCircleIcon, DocumentDuplicateIcon, MagnifyingGlassIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import MyLink from '@/Components/MyLink.vue'
import H1 from '@/Components/H1ForIndex.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import MultiSelect from '@/Components/MultiSelect.vue'

// import DeleteDialog from '@/Shared/DeleteDialog.vue'
import DeleteDialog from "@/Components/DeleteDialog.vue";

import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  user_ingredients: Array,
  ingredient_categories: Array,
  can_create: Boolean
})

const usdaIngredients = window.usdaIngredients ? window.usdaIngredients : []
const ingredients = usdaIngredients.concat(props.user_ingredients)

const cloneExistingDialog = ref(null)
const usdaSearchInput = ref(null)
const userSearchInput = ref(null)

// For filtering ingredients by category
const selectedUsdaCategories = ref([])
const selectedUsdaCategoryIds = computed(() => {
  return selectedUsdaCategories.value.map(category => category.id)
})
const selectedUserCategories = ref([])
const selectedUserCategoryIds = computed(() => {
  return selectedUserCategories.value.map(category => category.id)
})
function shouldDisplayUsdaIngredient(ingredient) {
  return (selectedUsdaCategories.value.length === 0) || selectedUsdaCategoryIds.value.includes(ingredient.ingredient_category_id)
}
function shouldDisplayUserIngredient(ingredient) {
  return (selectedUserCategories.value.length === 0) || selectedUserCategoryIds.value.includes(ingredient.ingredient_category_id)
}

// For fuzzy search over ingredient names
const filteredUsdaIngredients = ref([])
const filteredUserIngredients = ref([])
const fuzzysortUsdaOptions = {
  key: 'name',
  limit: 25,
  threshold: -10000
}
const fuzzysortUserOptions = {
  key: 'name',
  all: true,
  limit: 15,
  threshold: -10000
}

const usdaSearchQuery = ref(sessionStorage.getItem('ingredientsIndexUsdaSearchQuery') ?? "")
const userSearchQuery = ref(sessionStorage.getItem('ingredientsIndexUserSearchQuery') ?? "")

const numDisplayedUserIngredients = computed(() => {
  return filteredUserIngredients.value.filter(ingredient => shouldDisplayUserIngredient(ingredient.obj)).length
})

const selectedTab = ref(sessionStorage.getItem('ingredientsIndexSelectedTab') ?? 0)
function changeTab(index) {
  selectedTab.value = index
}

// Preserve ingredient search from previous visit to this page
onMounted(() => {
  if (usdaSearchQuery) {
    filteredUsdaIngredients.value = fuzzysort.go(usdaSearchQuery.value.trim(), usdaIngredients, fuzzysortUsdaOptions)
  }
  if (userSearchQuery) {
    filteredUserIngredients.value = fuzzysort.go(userSearchQuery.value.trim(), props.user_ingredients, fuzzysortUserOptions)
  }
})

watch(usdaSearchQuery, throttle(function (value) {
  filteredUsdaIngredients.value = fuzzysort.go(value.trim(), usdaIngredients, fuzzysortUsdaOptions)
}, 300))
watch(userSearchQuery, throttle(function (value) {
  filteredUserIngredients.value = fuzzysort.go(value.trim(), props.user_ingredients, fuzzysortUserOptions)
}, 300))

// Preserve search query between page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('ingredientsIndexUsdaSearchQuery', usdaSearchQuery.value);
  sessionStorage.setItem('ingredientsIndexUserSearchQuery', userSearchQuery.value);
  sessionStorage.setItem('ingredientsIndexSelectedTab', selectedTab.value);
})

// Preserve search query on manual page reload
window.onbeforeunload = function() {
  sessionStorage.setItem('ingredientsIndexUsdaSearchQuery', usdaSearchQuery.value);
  sessionStorage.setItem('ingredientsIndexUserSearchQuery', userSearchQuery.value);
  sessionStorage.setItem('ingredientsIndexSelectedTab', selectedTab.value);
}

function resetUsdaSearch() {
  usdaSearchQuery.value = ""
  selectedUsdaCategories.value = []
  usdaSearchInput.value.focus()
}

function resetUserSearch() {
  userSearchQuery.value = ""
  selectedUserCategories.value = []
  userSearchInput.value.focus()
}

let idToDelete = ref(null)
const deleteDialog = ref(null)
function deleteIngredient() {
  if (idToDelete.value) {
    router.delete(route('ingredients.destroy', idToDelete.value), {
      onSuccess: () => {
        // Updates filteredUserIngredients after deleting an ingredient to ensure then
        // ingredient disappears from display
        filteredUserIngredients.value = fuzzysort.go(userSearchQuery.value.trim(), props.user_ingredients, fuzzysortUserOptions)
      }
    });
  }
  idToDelete.value = null
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
        <H1 text="Ingredients" />
        <p class="mt-2 w-11/12 4 sm:w-2/3 text-gray-500">
          Use this page as an overview of ingredients from the USDA database—or ingredients you have created yourself.
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

    <TabGroup @change="changeTab" :defaultIndex="Number(selectedTab)">

      <TabList class="mt-4 rounded-xl w-fit border-b space-x-2">

        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            USDA Ingredients
          </button>
        </Tab>

        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            Your Ingredients
          </button>
        </Tab>

      </TabList>

      <TabPanels class="mt-2">

        <!-- Ingredients in USDA database -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-xl">
          <section class="border border-gray-200 p-4 rounded-xl shadow-sm bg-white">
            <h2 class="text-lg">Ingredients from the USDA database</h2>

            <div class="flex flex-col sm:flex-row items-start sm:items-end px-2 py-4">

              <!-- Input for search -->
              <div class="sm:mr-3">
                <label for="usda-search" class="ml-1 text-sm text-gray-500">
                  Search by ingredient name
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
                  </div>

                  <input
                    type="text"
                    id="usda-search"
                    ref="usdaSearchInput"
                    class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 sm:w-64 md:w-80 lg:w-96 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                    v-model="usdaSearchQuery"
                  />
                </div>
              </div>

              <!-- Select menu for ingredient category -->
              <div class="flex items-end">
                <div class="mt-2 sm:mt-0 sm:ml-2">

                  <MultiSelect
                    :options="ingredient_categories"
                    labelText="Filter by type"
                    v-model="selectedUsdaCategories"
                    width="full min-w-[6rem] max-w-full"
                  />

                </div>

                <div class="">
                  <label for="clear-usda-filters" class="sr-only">
                    Clear filter
                  </label>
                  <SecondaryButton
                    type="button"
                    id="clear-usda-filters"
                    class="normal-case font-normal !tracking-normal !text-sm !px-2 h-fit ml-2"
                    @click="resetUsdaSearch"
                  >
                    <XMarkIcon class="w-5 h-5" />
                    <span class="text-gray-600 font-normal ml-1.5">Clear filter</span>
                  </SecondaryButton>
                </div>
              </div>

            </div>

            <table
              v-show="filteredUsdaIngredients.length"
              class="mt-2 sm:table-fixed w-full text-sm sm:text-base text-left text-gray-500">
              <thead class="text-xs text-gray-700 uppercase bg-blue-100">
                <tr>
                  <th scope="col" class="px-6 py-3">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-3 w-4/12">
                    Type
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ingredient in filteredUsdaIngredients.map(fi => fi.obj)" :key="ingredient.id"
                  v-show="shouldDisplayUsdaIngredient(ingredient)"
                  class="border-b"
                >
                  <td scope="row" class="px-5 py-4 font-medium text-gray-900">
                    <MyLink
                      :href="route('ingredients.show', ingredient.id)"
                      class="text-gray-800"
                      preserve-state
                    >
                      {{ingredient.name}}
                    </MyLink>
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

                  <MultiSelect
                    :options="ingredient_categories"
                    labelText="Filter by type"
                    v-model="selectedUserCategories"
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
                  <th scope="col" class="px-6 py-3 w-4/12 bg-blue-200">
                    Type
                  </th>
                  <th scope="col" class="px-6 py-3 w-1/12 bg-blue-100" />
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ingredient in filteredUserIngredients.map(fi => fi.obj)" :key="ingredient.id"
                  v-show="shouldDisplayUserIngredient(ingredient)"
                  class="border-b"
                >
                  <td scope="row" class="px-5 py-4 font-medium text-gray-900">
                    <MyLink
                      class="text-gray-800"
                      :href="route('ingredients.show', ingredient.id)"
                    >
                      {{ingredient.name}}
                    </MyLink>
                  </td>
                  <td class="px-6 py-4">
                    {{ingredient.ingredient_category.name}}
                  </td>
                  <td>
                    <div class="flex items-center px-1.5">

                      <MyLink
                        class="mx-auto"
                        :href="route('ingredients.edit', ingredient.id)"
                      >
                        <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
                      </MyLink>

                      <button
                        type="button"
                        @click="idToDelete = ingredient.id; deleteDialog.open()"
                        class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
                      >
                        <TrashIcon class="w-5 h-5 hover:text-red-700" />
                      </button>

                    </div>
                  </td>
                </tr>
              </tbody>
              <p v-show="numDisplayedUserIngredients === 0" class="px-6 py-4" >
                No results found. Try a less restrictive filter or search?
              </p>
            </table>
          </section>
          <section v-else class="p-4 text-gray-800 max-w-lg">
            You haven't created any ingredients yet!
            <span v-if="can_create">
              Consider first
              <MyLink
                :href="route('ingredients.create')"
                class="text-blue-500"
              >
                creating a new ingredient
              </MyLink>
              or
              <button
                type="button"
                class="text-blue-500 hover:text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 p-px rounded-md"
                @click="cloneExistingDialog.open()"
              >
                cloning an existing ingredient.
              </button>
            </span>
            <span v-else>You need to <MyLink :href="route('login')" class="text-blue-500">log in</MyLink> to create ingredients.</span>
          </section>
        </TabPanel>

      </TabPanels>
    </TabGroup>

    <SearchForThingAndGo
      ref="cloneExistingDialog"
      :things="ingredients"
      goRoute="ingredients.clone"
      label="Search for an ingredient to clone"
      title="Clone ingredient"
      action="Clone"
    />

    <DeleteDialog
      ref="deleteDialog"
      description="ingredient"
      @delete="deleteIngredient"
      @cancel="idToDelete = null"
    />

  </div>
</template>
