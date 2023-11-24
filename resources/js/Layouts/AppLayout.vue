<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashMessage from '@/Shared/FlashMessage.vue'
import { Link } from '@inertiajs/vue3';
import FoodPopover from './Partials/FoodPopover.vue'

const showingNavigationDropdown = ref(false);

</script>

<template>
  <div class="bg-[#fefefe]">
    <div class="min-h-screen max-w-6xl md:px-4 lg:px-6 xl:px-12">
      <nav class="mx-auto">
        <!-- Desktop navigation menu -->
        <div class="fixed w-full top-0 inset-x-0 bg-[#fefefe] border-b border-gray-200 z-50">
          <div class="top-0 inset-x-0 max-w-6xl px-4 md:px-8 lg:px-10 xl:px-16 pr-4 sm:pr-6 lg:pr-8">
            <FlashMessage :flash="$page.props.flash" class="h-full" />

            <!-- Desktop navigation menu -->
            <div class="flex justify-between h-16">

              <div class="flex">
                <!-- Navigation Links -->
                <div class="space-x-8 flex">
                  <NavLink :href="route('home')" :active="route().current('home')" >
                    Home
                  </NavLink>
                  <div class="relative hidden sm:block lg:hidden">
                    <FoodPopover />
                  </div>
                  <NavLink class="hidden lg:inline-flex" :href="route('ingredients.index')" :active="route().current('ingredients.index')" >
                    Ingredients
                  </NavLink>
                  <NavLink class="hidden lg:inline-flex" :href="route('meals.index')" :active="route().current('meals.index')">
                    Meals
                  </NavLink>
                  <NavLink class="hidden lg:inline-flex" :href="route('food-lists.index')" :active="route().current('food-lists.index')">
                    Food Lists
                  </NavLink>
                  <NavLink class="hidden sm:inline-flex" :href="route('intake-guidelines.index')" :active="route().current('intake-guidelines.index')">
                    Intake Guidelines
                  </NavLink>
                  <NavLink class="hidden sm:inline-flex" :href="route('trends')" :active="route().current('trends')">
                    Trends
                  </NavLink>
                  <NavLink class="hidden sm:inline-flex" :href="route('log')" :active="route().current('log')">
                    Log Data
                  </NavLink>
                </div>

              </div>

              <!-- Dropdown with Profile/Log Out links -->
              <!-- Only show for authenticated users -->
              <div
                v-if="$page.props.auth.user"
                class="hidden sm:flex sm:items-center sm:ml-6"
              >
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                  <Dropdown align="right" width="48">
                    <template #trigger>
                      <span class="inline-flex rounded-md">
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                        >
                          {{ $page.props.auth.user.name }}

                          <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                      <DropdownLink :href="route('logout')" method="post" as="button">
                        Log Out
                      </DropdownLink>
                    </template>
                  </Dropdown>
                </div>
              </div>
              <!-- Log In for unauthenticated users -->
              <div
                v-else
                class="hidden sm:inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
              >
                <Link :href="route('login')">
                  Log In
                </Link>
              </div>

              <!-- Hamburger -->
              <div class="-mr-2 flex items-center sm:hidden">
                <button
                  @click="showingNavigationDropdown = !showingNavigationDropdown"
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                >
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path
                      :class="{
                        hidden: showingNavigationDropdown,
                        'inline-flex': !showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                    <path
                      :class="{
                        hidden: !showingNavigationDropdown,
                        'inline-flex': showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile navigation menu -->
        <div
          :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
          class="sm:hidden fixed top-[4rem] inset-x-0 bg-[#fefefe] border-b border-gray-300 shadow z-50"
        >

          <!-- Links in mobile navigation menu -->
          <div class="pt-2 pb-3 space-y-1">

            <ResponsiveNavLink :href="route('ingredients.index')" :active="route().current('ingredients.index')" >
              Ingredients
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('meals.index')" :active="route().current('meals.index')">
              Meals
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('food-lists.index')" :active="route().current('food-lists.index')">
              Food Lists
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('intake-guidelines.index')" :active="route().current('intake-guidelines.index')">
              Intake Guidelines
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('trends')" :active="route().current('trends')">
              Trends
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('log')" :active="route().current('log')">
              Log Data
            </ResponsiveNavLink>
            <!-- Display log in link for unauthenticated users -->
            <ResponsiveNavLink
              v-if="!$page.props.auth.user"
              :href="route('login')"
            >
              Log In
            </ResponsiveNavLink>
          </div>

          <!-- Responsive Settings Options -->
          <div
            v-if="$page.props.auth.user"
            class="pt-4 pb-1 border-t border-gray-200"
          >
            <div class="px-4">
              <div class="font-medium text-base text-gray-800">
                {{ $page.props.auth.user.name }}
              </div>
              <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
            </div>

            <div class="mt-3 space-y-1">
              <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
              <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                Log Out
              </ResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white shadow mx-auto" v-if="$slots.header">
        <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="mt-24 pb-6 px-4 sm:px-6">
        <slot />
      </main>
    </div>
  </div>
</template>
