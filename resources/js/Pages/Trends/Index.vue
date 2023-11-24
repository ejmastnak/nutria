<script setup>
import { ref, onBeforeUnmount, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import MyLink from '@/Components/MyLink.vue'
import BodyWeightRecords from './Partials/BodyWeightRecords.vue'
import FoodIntakeRecords from './Partials/FoodIntakeRecords.vue'
import NutrientProfileTrends from './Partials/NutrientProfileTrends.vue'
import LogBodyWeightDialog from './Partials/LogBodyWeightDialog.vue'
import LogIngredientIntakeDialog from './Partials/LogIngredientIntakeDialog.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
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

const selectedTab = ref(sessionStorage.getItem('trendsIndexSelectedTab') ?? 0)
function changeTab(index) {
  selectedTab.value = index
}

const logBodyWeightDialogRef = ref(null)
const logIngredientIntakeDialogRef = ref(null)

// Preserve selected tab page visits
onBeforeUnmount(() => {
  sessionStorage.setItem('trendsIndexSelectedTab', selectedTab.value);
})
// Preserve search query on manual page reload
window.onbeforeunload = function() {
  sessionStorage.setItem('trendsIndexSelectedTab', selectedTab.value);
}
</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>
    <Head title="Trends" />

    <h1 class="text-xl">Trends</h1>
    <p class="text-gray-500">
      This page shows your body weight and food intake data.
    </p>

    <TabGroup @change="changeTab" :defaultIndex="Number(selectedTab)">

      <TabList class="mt-4 rounded w-fit border-b space-x-2">

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

        <!-- Body weight trends-->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md w-fit">
          <SecondaryButton @click="logBodyWeightDialogRef.open(null)" >
            Log body weight
          </SecondaryButton>
          <BodyWeightRecords
            class="mt-1 overflow-hidden rounded-md w-fit"
            :body_weight_records="body_weight_records"
            :units="units"
          />
          <LogBodyWeightDialog :units="units" ref="logBodyWeightDialogRef" />
        </TabPanel>

        <!-- Food intake ingredients -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded">
          <SecondaryButton @click="logIngredientIntakeDialogRef.open(null)" >
            Log Ingredient
          </SecondaryButton>
          <FoodIntakeRecords
            class="mt-1 overflow-hidden rounded-md"
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
        </TabPanel>

        <!-- Nutrient profile trends -->
        <TabPanel class="focus:outline-none focus:ring-1 focus:ring-blue-500 rounded">
          <NutrientProfileTrends class="overflow-hidden rounded-md" />
        </TabPanel>

      </TabPanels>
    </TabGroup>

  </div>
</template>
