<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { TrashIcon, PencilSquareIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'
import LogIngredientIntakeRecordDialog from '@/Shared/LogIngredientIntakeRecordDialog.vue'
import LogIngredientIntakeRecordsDialog from '@/Shared/LogIngredientIntakeRecordsDialog.vue'
import LogMealIntakeRecordDialog from '@/Shared/LogMealIntakeRecordDialog.vue'
import LogMealIntakeRecordsDialog from '@/Shared/LogMealIntakeRecordsDialog.vue'
import DeleteDialog from "@/Components/DeleteDialog.vue";
import MyLink from '@/Components/MyLink.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { roundNonZero, getHumanReadableLocalDate } from '@/utils/GlobalFunctions.js'

import { ingredientIntakeRecordsForm } from '@/Shared/store.js'
import { mealIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  ingredient_intake_records: Array,
  meal_intake_records: Array,
  ingredients: Array,
  meals: Array,
  units: Array,
})

const INGREDIENT=0
const MEAL=1

const foodItems = computed(() => {
  return props.ingredient_intake_records.map((record) => {record['type'] = INGREDIENT; return record}).concat(props.meal_intake_records.map((record) => {record['type'] = MEAL; return record})).sort((a, b) => {
    if (a.date_time_utc > b.date_time_utc) return -1;
    if (a.date_time_utc < b.date_time_utc) return 1;
    return 0;
  })
})

const logIngredientIntakeRecordDialogRef = ref(null)
const logIngredientIntakeRecordsDialogRef = ref(null)
const logMealIntakeRecordDialogRef = ref(null)
const logMealIntakeRecordsDialogRef = ref(null)

function openUpdateDialog(foodItem) {
  if (foodItem.type === INGREDIENT) {
    logIngredientIntakeRecordDialogRef.value.open(foodItem)
  } else if (foodItem.type === MEAL) {
    logMealIntakeRecordDialogRef.value.open(foodItem)
  }
}

const idToDelete = ref(null)
const deleteRouteName = ref(null)
const thingToDelete = ref(null)
const deleteDialogRef = ref(null)

function logIngredientIntake() {
  if (ingredientIntakeRecordsForm.ingredientIntakeRecords.length >= 1) {
    logIngredientIntakeRecordsDialogRef.value.open()
  } else {
    logIngredientIntakeRecordDialogRef.value.open(null)
  }
}

function logMealIntake() {
  if (mealIntakeRecordsForm.mealIntakeRecords.length >= 1) {
    logMealIntakeRecordsDialogRef.value.open()
  } else {
    logMealIntakeRecordDialogRef.value.open(null)
  }
}

function openDeleteDialog(foodItem) {
  idToDelete.value = foodItem.id
  if (foodItem.type === INGREDIENT) {
    deleteRouteName.value = "ingredient-intake-records.destroy"
    thingToDelete.value = "ingredient intake record"
    deleteDialogRef.value.open(foodItem)
  } else if (foodItem.type === MEAL) {
    deleteRouteName.value = "meal-intake-records.destroy"
    thingToDelete.value = "meal intake record"
    deleteDialogRef.value.open(foodItem)
  }
}

function deleteBodyWeightRecord() {
  if (idToDelete.value && deleteRouteName.value) {
      router.delete(route(deleteRouteName.value, idToDelete.value));
  }
  idToDelete.value = null
  deleteRouteName.value = null
  thingToDelete.value = null
}

// Used to color different days with different background colors to better
// distinguish different days.
const bgColors = ['bg-gray-50', 'bg-white']
var bgColorCounter = 0
function getBgColorForFoodItemRow(idx) {
  if (idx === 0) return bgColors[0];
  const prevItem = foodItems.value[idx - 1]
  const thisItem = foodItems.value[idx]
  if (thisItem.date_time_utc !== prevItem.date_time_utc) bgColorCounter += 1;
  return bgColors[bgColorCounter % bgColors.length];
}

</script>

<template>
  <div>

    <p v-if="foodItems.length === 0" class="mt-1 mb-2">
      You haven't created any food intake records yet!
    </p>

    <div class="flex gap-x-1.5">
      <SecondaryButton @click="logMealIntake" >
        Log Meals
      </SecondaryButton>
      <SecondaryButton @click="logIngredientIntake" >
        Log Ingredients
      </SecondaryButton>
    </div>

    <table v-if="foodItems.length" class="mt-2 text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-8 py-3 bg-blue-100">Food</th>
          <th scope="col" class="px-8 py-3 bg-blue-200">Amount</th>
          <th scope="col" class="px-8 py-3 bg-blue-100">Date</th>
          <th scope="col" class="px-8 py-3 bg-blue-200">Type</th>
          <th scope="col" class="px-8 py-3 bg-blue-100" />
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(foodItem, idx) in foodItems" :key="foodItem.id + '-' + foodItem.type"
          class="border-b hover:bg-gray-100 cursor-pointer"
          :class="getBgColorForFoodItemRow(idx)"
          @click="openUpdateDialog(foodItem)"
        >
          <td class="px-8 py-4">
            <p v-if="foodItem.type === INGREDIENT" class="flex items-center whitespace-nowrap">
              {{foodItem.ingredient.name}}
              <MyLink @click.stop :href="route('ingredients.show', foodItem.ingredient.id)">
                <ArrowTopRightOnSquareIcon class="ml-0.5 w-5 h-5 text-gray-500" />
              </MyLink>
            </p>
            <p v-if="foodItem.type === MEAL" class="flex items-center whitespace-nowrap">
              {{foodItem.meal.name}}
              <MyLink @click.stop :href="route('meals.show', foodItem.meal.id)">
                <ArrowTopRightOnSquareIcon class="ml-0.5 w-5 h-5 text-gray-500" />
              </MyLink>
            </p>
          </td>
          <td scope="row" class="pr-1 py-4 text-gray-900 text-right">
            {{roundNonZero(Number(foodItem.amount), 2)}}
            {{foodItem.unit.name}}
          </td>
          <td class="px-8 py-4 whitespace-nowrap">
            <span class="hidden md:inline">
              {{getHumanReadableLocalDate(foodItem.date_time_utc)}}
            </span>
            <span class="md:hidden">
              {{getHumanReadableLocalDate(foodItem.date_time_utc, shortMonth=true)}}
            </span>
          </td>
          <td class="px-4 py-4">
            <p v-if="foodItem.type === INGREDIENT">Ingredient</p>
            <p v-if="foodItem.type === MEAL">Meal</p>
          </td>
          <td class="flex items-center px-8 py-4 h-full">
            <button
              type="button"
              @click.stop="openUpdateDialog(foodItem)"
              class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
            >
              <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
            </button>
            <button
              type="button"
              @click.stop="openDeleteDialog(foodItem)"
              class="ml-1 mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
            >
              <TrashIcon class="w-5 h-5 hover:text-red-700" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <LogIngredientIntakeRecordDialog
      :ingredients="ingredients"
      :units="units"
      ref="logIngredientIntakeRecordDialogRef"
    />
    <LogIngredientIntakeRecordsDialog
      :ingredients="ingredients"
      :units="units"
      ref="logIngredientIntakeRecordsDialogRef"
    />

    <LogMealIntakeRecordDialog
      :meals="meals"
      :units="units"
      ref="logMealIntakeRecordDialogRef"
    />
    <LogMealIntakeRecordsDialog
      :meals="meals"
      :units="units"
      ref="logMealIntakeRecordsDialogRef"
    />

    <DeleteDialog
      ref="deleteDialogRef"
      :description="thingToDelete"
      @delete="deleteBodyWeightRecord"
      @cancel="idToDelete = null; typeToDelete = null"
    />

  </div>
</template>
