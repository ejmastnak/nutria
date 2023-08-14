<script setup>
import { Bars4Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'

function closePopover(close) { close() }
</script>

<!-- A navigation bar with Edit/Delete/etc links for use on CRUD model pages -->
<template>
  <!-- Desktop nav bar -->
  <header class="hidden sm:flex items-center border border-gray-300 rounded-xl">
    <slot name="desktop-items"></slot>
  </header>

  <!-- Mobile nav bar -->
  <header class="flex sm:hidden items-center border border-gray-300 rounded-xl text-gray-900">

    <!-- Icons on left that are always displayed in mobile layout -->
    <div class="flex mr-auto">
      <slot name="mobile-displayed"></slot>
    </div>

    <!-- Popover with hamburger button and mobile menu items -->
      <Popover v-slot="{open}" class="relative">
      <!-- Hamburger button for displaying full mobile menu -->
      <PopoverButton class="px-4 py-2 rounded-xl overflow-hidden focus:outline-none focus:ring-2 focus:ring-blue-700">
        <Bars4Icon v-if="!open" class="w-6 h-6 text-gray-500" />
        <XMarkIcon v-else class="w-6 h-6 text-gray-500" />
      </PopoverButton>
      <!-- Mobile menu items -->
      <PopoverPanel v-slot="{ close }" class="absolute right-0 z-10 px-2 py-1 border border-gray-200 shadow-sm rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-blue-700">
        <div class="flex flex-col">
          <slot name="mobile-items"></slot>
        </div>
      </PopoverPanel>
    </Popover>

  </header>

</template>


