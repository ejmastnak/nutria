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

const props = defineProps({
  page: String,  // "index", "show", "create", "clone", or "edit"
  route_basename: String,  // "ingredients", "meals", "food-lists", or "intake-guidelines"
  id: Number,  // id of this thing
  things: Array,  // list of all things
  thing: String,  // e.g. "ingredient", "meal", etc.
  can_create: Boolean,
  can_view: {type: Boolean, default: false},
  can_clone: {type: Boolean, default: false},
  can_update: {type: Boolean, default: false},
  can_delete: {type: Boolean, default: false},
})

const showingNavigationDropdown = ref(false);

</script>

<template>
  <div class="flex space-x-4">

    <nav class="-mt-8 -ml-6">

      <!-- Desktop navigation menu -->
      <div class="fixed hidden sm:block left-0 w-48 p-2 bg-[#fefefe] border-r border-gray-300 flex flex-col min-h-screen whitespace-nowrap z-50">
        <div>
          <All :href="route(route_basename + '.index')" />
          <FindAnother :things="things" :route_basename="route_basename" />
          <CloneAnother :things="things" :route_basename="route_basename" :enabled="can_create" />
          <New :href="route(route_basename + '.create')" :enabled="can_create" />
        </div>

        <div class="border-t border-gray-200 mt-3 pt-3">
          <ShowOriginal
            v-if="page === 'show' || page === 'edit' || page === 'clone'"
            :href="route(route_basename + '.show', id)"
            :enabled="can_view"
          />
          <CloneOriginal
            v-if="page === 'show' || page === 'edit' || page === 'clone'"
            :href="route(route_basename + '.clone', id)"
            :enabled="can_clone"
          />
          <EditOriginal
            v-if="can_update && (page === 'show' || page === 'edit' || page === 'clone')"
            :href="route(route_basename + '.edit', id)"
            :enabled="can_update"
          />
          <DeleteOriginal
            v-if="can_delete && (page === 'show' || page === 'edit' || page === 'clone')"
            :href="route(route_basename + '.destroy', id)"
            :thing="thing"
            :enabled="can_delete"
          />
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
        <div>
          <All :href="route(route_basename + '.index')" />
          <FindAnother :things="things" :route_basename="route_basename" />
          <CloneAnother :things="things" :route_basename="route_basename" :enabled="can_create" />
          <New :href="route(route_basename + '.create')" :enabled="can_create" />
        </div>

        <div class="border-t border-gray-200 mt-3 pt-3">
          <ShowOriginal
            v-if="page === 'show' || page === 'edit' || page === 'clone'"
            :href="route(route_basename + '.show', id)"
            :enabled="can_view"
          />
          <CloneOriginal
            v-if="page === 'show' || page === 'edit' || page === 'clone'"
            :href="route(route_basename + '.clone', id)"
            :enabled="can_clone"
          />
          <EditOriginal
            v-if="can_update && (page === 'show' || page === 'edit' || page === 'clone')"
            :href="route(route_basename + '.edit', id)"
            :enabled="can_update"
          />
          <DeleteOriginal
            v-if="can_delete && (page === 'show' || page === 'edit' || page === 'clone')"
            :href="route(route_basename + '.destroy', id)"
            :thing="thing"
            :enabled="can_delete"
          />
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="pl-2 sm:pl-60 mt-4 sm:mt-0">
      <slot />
    </div>
  </div>
</template>
