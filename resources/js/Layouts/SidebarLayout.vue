<script setup>
import { ref } from 'vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import All from './SidebarPartials/All.vue'
import FindAnother from './SidebarPartials/FindAnother.vue'
import CloneAnother from './SidebarPartials/CloneAnother.vue'
import New from './SidebarPartials/New.vue'
import ShowOriginal from './SidebarPartials/ShowOriginal.vue'
import CloneOriginal from './SidebarPartials/CloneOriginal.vue'
import EditOriginal from './SidebarPartials/EditOriginal.vue'
import DeleteOriginal from './SidebarPartials/DeleteOriginal.vue'
import LogBodyWeight from './SidebarPartials/LogBodyWeight.vue'
import LogFoodIntake from './SidebarPartials/LogFoodIntake.vue'

const props = defineProps({
  page: String,  // "index", "show", "create", "clone", "edit", or "trends"
  route_basename: {type: String, default: ""},  // "ingredients", "meals", "food-lists", or "intake-guidelines"
  id: {type: Number, default: null},  // id of this thing
  things: {type: Array, default: []},  // list of all things
  thing: {type: String, default: ""},  // e.g. "ingredient", "meal", etc.
  can_create: {type: Boolean, default: false},
  can_view: {type: Boolean, default: false},
  can_clone: {type: Boolean, default: false},
  can_update: {type: Boolean, default: false},
  can_delete: {type: Boolean, default: false},
  // For trends
  units: {type: Array, default: []},
  ingredients: {type: Array, default: []},
  meals: {type: Array, default: []},
  food_lists: {type: Array, default: []},
})

const showingNavigationDropdown = ref(false);

</script>

<template>
  <div class="flex space-x-4">

    <nav class="-mt-8 -ml-6">

      <!-- Desktop navigation menu -->
      <div class="fixed hidden sm:block left-0 w-48 p-2 bg-[#fefefe] border-r border-gray-300 flex flex-col min-h-screen whitespace-nowrap z-40">

        <!-- For all CRUD pages -->
        <div v-if="(page === 'index' || page === 'create' || page === 'show' || page === 'edit' || page === 'clone')" >
          <All :href="route(route_basename + '.index')" />
          <FindAnother :things="things" :thing="thing" :route_basename="route_basename" />
          <CloneAnother :things="things" :thing="thing" :route_basename="route_basename" :enabled="can_create" />
          <New :href="route(route_basename + '.create')" :enabled="can_create" />
        </div>

        <!-- For Show, Edit, Clone pages -->
        <div
          v-if="(page === 'show' || page === 'edit' || page === 'clone')"
          class="border-t border-gray-200 mt-3 pt-3"
        >
          <ShowOriginal
            :href="route(route_basename + '.show', id)"
            :enabled="can_view"
          />
          <CloneOriginal
            :href="route(route_basename + '.clone', id)"
            :enabled="can_clone"
          />
          <EditOriginal
            v-if="can_update"
            :href="route(route_basename + '.edit', id)"
            :enabled="can_update"
          />
          <DeleteOriginal
            v-if="can_delete"
            :href="route(route_basename + '.destroy', id)"
            :thing="thing"
            :enabled="can_delete"
          />
        </div>

        <!-- For Trends page -->
        <div v-if="page === 'trends'">
          <LogFoodIntake
            :units="units"
            :ingredients="ingredients"
            :meals="meals"
            :food_lists="food_lists"
          />
          <LogBodyWeight :units="units" />
        </div>

      </div>

      <!-- Hamburger -->
      <div class="fixed left-0 sm:hidden bg-white w-full border-b border-gray-100">
        <button
          @click="showingNavigationDropdown = !showingNavigationDropdown"
          class="inline-flex items-center justify-center pl-3 p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
        >
          <Bars3Icon class="h-6 w-6" :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" />
          <XMarkIcon class="h-6 w-6" :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" />
          <p class="ml-1">Options</p>
        </button>
      </div>

      <!-- Mobile navigation menu -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="fixed sm:hidden bg-white top-[6.5rem] inset-x-0 p-3 space-y-2 border border-gray-200 shadow rounded z-50"
      >
        <!-- For all CRUD pages -->
        <div v-if="(page === 'index' || page === 'create' || page === 'show' || page === 'edit' || page === 'clone')" >
          <All :href="route(route_basename + '.index')" />
          <FindAnother :things="things" :thing="thing" :route_basename="route_basename" />
          <CloneAnother :things="things" :thing="thing" :route_basename="route_basename" :enabled="can_create" />
          <New :href="route(route_basename + '.create')" :enabled="can_create" />
        </div>

        <!-- For Show, Edit, Clone pages -->
        <div
          v-if="page === 'show' || page === 'edit' || page === 'clone'"
          class="border-t border-gray-200 mt-3 pt-3"
        >
          <ShowOriginal
            :href="route(route_basename + '.show', id)"
            :enabled="can_view"
          />
          <CloneOriginal
            :href="route(route_basename + '.clone', id)"
            :enabled="can_clone"
          />
          <EditOriginal
            v-if="can_update"
            :href="route(route_basename + '.edit', id)"
            :enabled="can_update"
          />
          <DeleteOriginal
            v-if="can_delete"
            :href="route(route_basename + '.destroy', id)"
            :thing="thing"
            :enabled="can_delete"
          />
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="pl-2 sm:pl-60 mt-4 sm:mt-0 w-full">
      <slot />
    </div>
  </div>
</template>
