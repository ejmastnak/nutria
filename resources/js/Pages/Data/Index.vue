<script setup>
import { ref, onBeforeUnmount, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import BodyWeightRecords from './Partials/BodyWeightRecords.vue'
import FoodIntakeRecords from './Partials/FoodIntakeRecords.vue'
import NutrientProfileTrends from './Partials/NutrientProfileTrends.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  body_weight_records_paginator: Object,
  food_intake_records_paginator: Object,
  user_ingredients: Array,
  meals: Array,
  units: Array,
  intake_guidelines: Array,
  nutrient_categories: Array,
})

const ingredients = props.user_ingredients.concat(window.usdaIngredients ? window.usdaIngredients : [])

const selectedTab = ref(sessionStorage.getItem('dataIndexSelectedTab') ?? 0)
function changeTab(index) {
  selectedTab.value = index
}

// Preserve selected tab page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('dataIndexSelectedTab', selectedTab.value);
})
// Preserve search query on manual page reload
window.onbeforeunload = function() {
  sessionStorage.setItem('dataIndexSelectedTab', selectedTab.value);
}
</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <SidebarLayout
    page="data"
    :units="units"
    :ingredients="ingredients"
    :meals="meals"
  >
    <Head title="Data" />

    <h1 class="text-xl">Data</h1>
    <p class="mt-2 text-gray-500">
      Use this page to view and manage your body weight and food intake data.
    </p>

    <TabGroup @change="changeTab" :defaultIndex="Number(selectedTab)">

      <TabList class="mt-4 rounded w-fit border-b space-x-2 whitespace-nowrap flex">

        <!-- Food Intake -->
        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            Food Intake
          </button>
        </Tab>

        <!-- Body Weight -->
        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            Body Weight
          </button>
        </Tab>

        <!-- Nutrient Profile data -->
        <Tab as="template" v-slot="{ selected }">
          <button
            class="px-4 py-2 text-sm text-gray-600 focus:outline-none transition ease-in-out duration-150"
            :class="{
              'text-gray-800 font-semibold border-b-2 border-blue-500': selected,
              'hover:border-b-2 hover:border-gray-300': !selected
            }" >
            Nutrient Profile
          </button>
        </Tab>

      </TabList>

      <TabPanels class="mt-2">

        <!-- Food intake ingredients -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded w-fit p-1">
          <FoodIntakeRecords
            class="rounded-md w-fit"
            :food_intake_records_paginator="food_intake_records_paginator"
            :ingredients="ingredients"
            :meals="meals"
            :units="units"
          />
        </TabPanel>

        <!-- Body weight data-->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md w-fit p-1">
          <BodyWeightRecords
            class="rounded-md w-fit"
            :body_weight_records_paginator="body_weight_records_paginator"
            :units="units"
          />
        </TabPanel>

        <!-- Nutrient profile data -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded p-1 w-fit">
          <NutrientProfileTrends
            :intake_guidelines="intake_guidelines"
            :nutrient_categories="nutrient_categories"
          />
        </TabPanel>

      </TabPanels>
    </TabGroup>

  </SidebarLayout>
</template>
