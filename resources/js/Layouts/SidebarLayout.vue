<script setup>
import { ref } from 'vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

</script>

<template>
  <div class="flex space-x-4">
    <nav class="-mt-8 -ml-6">

      <!-- Desktop navigation menu -->
      <div class="fixed hidden sm:block left-0 w-48 p-4 bg-[#fefefe] border-r border-gray-300 flex flex-col min-h-screen">
        <p>Item 1</p>
        <p>Item 2</p>
        <p>Item 3</p>
        <p>Item 4</p>
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
        class="fixed sm:hidden bg-white top-[6.5rem] inset-x-0 p-3 space-y-2 border border-gray-200 shadow rounded"
      >
        <p>Item 1</p>
        <p>Item 2</p>
        <p>Item 3</p>
        <p>Item 4</p>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="pl-2 sm:pl-60 mt-4 sm:mt-0">
      <slot />
    </div>
  </div>
</template>
