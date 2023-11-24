<script setup>
import { Popover, PopoverButton, PopoverPanel, PopoverOverlay } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import MyLink from '@/Components/MyLink.vue'
</script>

<template>
  <div>
    <Popover v-slot="{ open }" class="relative">
      <PopoverButton
        class="h-16 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
      >
        <span>Food</span>
        <ChevronDownIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
      </PopoverButton>
      <PopoverOverlay class="fixed inset-0 bg-black opacity-20 z-50" />

      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-1 opacity-0"
      >
        <PopoverPanel
          class="absolute z-50 mt-1 rounded shadow-lg bg-white py-1"
        >
          <MyLink
            :href="route('ingredients.index')"
            class="block rounded-none w-full px-5 py-2 font-medium text-left text-sm leading-5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
            :class="route().current('ingredients.index') ? '!text-gray-900' : ''"
          >
            Ingredients
          </MyLink>
          <MyLink
            :href="route('meals.index')"
            class="block rounded-none w-full px-5 py-2 font-medium text-left text-sm leading-5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
            :class="route().current('meals.index') ? '!text-gray-900' : ''"
          >
            Meals
          </MyLink>
          <MyLink
            :href="route('food-lists.index')"
            class="block rounded-none w-full px-5 py-2 font-medium text-left text-sm leading-5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
            :class="route().current('food-lists.index') ? '!text-gray-900' : ''"
          >
            Food Lists
          </MyLink>
        </PopoverPanel>
      </transition>
    </Popover>
  </div>
</template>
