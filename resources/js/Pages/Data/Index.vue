<script setup>
import { ref, onBeforeUnmount, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import BodyWeightRecords from './Partials/BodyWeightRecords.vue'
import FoodIntakeRecords from './Partials/FoodIntakeRecords.vue'
import NutrientProfileTrends from './Partials/NutrientProfileTrends.vue'
import LogBodyWeightDialog from './Partials/LogBodyWeightDialog.vue'
import LogIngredientIntakeDialog from './Partials/LogIngredientIntakeDialog.vue'
import LogMealIntakeDialog from './Partials/LogMealIntakeDialog.vue'
import LogFoodListIntakeDialog from './Partials/LogFoodListIntakeDialog.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SidebarLayout from "@/Layouts/SidebarLayout.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
  body_weight_records: Array,
  ingredient_intake_records: Array,
  meal_intake_records: Array,
  food_list_intake_records: Array,
  user_ingredients: Array,
  meals: Array,
  food_lists: Array,
  units: Array,
})

const ingredients = props.user_ingredients.concat(window.usdaIngredients ? window.usdaIngredients : [])

const selectedTab = ref(sessionStorage.getItem('dataIndexSelectedTab') ?? 0)
function changeTab(index) {
  selectedTab.value = index
}

const logBodyWeightDialogRef = ref(null)
const logIngredientIntakeDialogRef = ref(null)
const logMealIntakeDialogRef = ref(null)
const logFoodListIntakeDialogRef = ref(null)

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
    :food_lists="food_lists"
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
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded w-fit">
          <div class="flex gap-x-1.5">
            <SecondaryButton @click="logIngredientIntakeDialogRef.open(null)" >
              Log Ingredient
            </SecondaryButton>
            <SecondaryButton @click="logMealIntakeDialogRef.open(null)" >
              Log Meal
            </SecondaryButton>
            <SecondaryButton @click="logFoodListIntakeDialogRef.open(null)" >
              Log Food List
            </SecondaryButton>
          </div>
          <FoodIntakeRecords
            class="mt-2 overflow-hidden rounded-md w-fit"
            :ingredient_intake_records="ingredient_intake_records"
            :meal_intake_records="meal_intake_records"
            :food_list_intake_records="food_list_intake_records"
            :ingredients="ingredients"
            :meals="meals"
            :food_lists="food_lists"
            :units="units"
          />
          <LogIngredientIntakeDialog
            :ingredients="ingredients"
            :units="units"
            ref="logIngredientIntakeDialogRef"
          />
          <LogMealIntakeDialog
            :meals="meals"
            :units="units"
            ref="logMealIntakeDialogRef"
          />
          <LogFoodListIntakeDialog
            :food_lists="food_lists"
            :units="units"
            ref="logFoodListIntakeDialogRef"
          />
        </TabPanel>

        <!-- Body weight data-->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md w-fit">
          <SecondaryButton @click="logBodyWeightDialogRef.open(null)" >
            Log body weight
          </SecondaryButton>
          <BodyWeightRecords
            class="mt-2 overflow-hidden rounded-md w-fit"
            :body_weight_records="body_weight_records"
            :units="units"
          />
          <LogBodyWeightDialog :units="units" ref="logBodyWeightDialogRef" />
        </TabPanel>

        <!-- Nutrient profile data -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded">
          <NutrientProfileTrends class="overflow-hidden rounded-md" />
        </TabPanel>

      </TabPanels>
    </TabGroup>

  </SidebarLayout>
</template>
