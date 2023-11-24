<script setup>
import { computed, ref } from 'vue'
import { TrashIcon, PencilSquareIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'
import LogIngredientIntakeDialog from './LogIngredientIntakeDialog.vue'
import LogMealIntakeDialog from './LogMealIntakeDialog.vue'
import MyLink from '@/Components/MyLink.vue'
import { roundNonZero } from '@/utils/GlobalFunctions.js'
const props = defineProps({
  ingredient_intake_records: Array,
  meal_intake_records: Array,
  food_list_intake_records: Array,
  ingredients: Array,
  meals: Array,
  food_lists: Array,
  units: Array,
})

const INGREDIENT=0
const MEAL=1
const FOOD_LIST=2

const foodItems = computed(() => {
  return props.ingredient_intake_records.map((record) => {record['type'] = INGREDIENT; return record}).concat(props.meal_intake_records.map((record) => {record['type'] = MEAL; return record}), props.food_list_intake_records.map((record) => {record['type'] = FOOD_LIST; return record})).sort((a, b) => {
    if (a.date > b.date) return 1;
    if (a.date < b.date) return -1;
    if (a.time > b.time) return 1;
    if (a.time < b.time) return -1;
    return 0;
  })
})

const logIngredientIntakeDialogRef = ref(null)
const logMealIntakeDialogRef = ref(null)
const logFoodListIntakeDialogRef = ref(null)

function openUpdateDialog(foodItem) {
  if (foodItem.type === INGREDIENT) {
    logIngredientIntakeDialogRef.value.open(foodItem)
  } else if (foodItem.type === MEAL) {
    logMealIntakeDialogRef.value.open(foodItem)
  } else if (foodItem.type === FOOD_LIST) {
    // logFoodListIntakeDialogRef.value.open(foodItem)
    alert("Not yet");
  }
}

</script>

<template>
  <div>
    <table v-if="foodItems.length" class="text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-8 py-3 bg-blue-100">Food</th>
          <!-- Amount and unit food -->
          <th scope="col" class="px-8 py-3 bg-blue-200">Amount</th>
          <th scope="col" class="px-8 py-3 bg-blue-100">Date</th>
          <th scope="col" class="px-8 py-3 bg-blue-200" />
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="foodItem in foodItems" :key="foodItem.id + '-' + foodItem.type"
          class="border-b"
        >
          <td class="px-8 py-4">
            <p v-if="foodItem.type === INGREDIENT" class="flex items-center">
              <button
                type="button"
                @click="logIngredientIntakeDialogRef.open(foodItem)"
                class="inline-block px-px rounded-md hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 whitespace-nowrap"
              >
                {{foodItem.ingredient.name}}
              </button>
              <MyLink :href="route('ingredients.show', foodItem.ingredient.id)">
                <ArrowTopRightOnSquareIcon class="ml-0.5 w-5 h-5 text-gray-500" />
              </MyLink>
            </p>
            <p v-if="foodItem.type === MEAL" class="flex items-center">
              <button
                type="button"
                @click="logMealIntakeDialogRef.open(foodItem)"
                class="inline-block px-px rounded-md hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 whitespace-nowrap"
              >
                {{foodItem.meal.name}}
              </button>
              <MyLink :href="route('meals.show', foodItem.meal.id)">
                <ArrowTopRightOnSquareIcon class="ml-0.5 w-5 h-5 text-gray-500" />
              </MyLink>
            </p>
            <p v-if="foodItem.type === FOOD_LIST" class="flex items-center">
              <button
                type="button"
                @click="logFoodListIntakeDialogRef.open(foodItem)"
                class="inline-block px-px rounded-md hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 whitespace-nowrap"
              >
                {{foodItem.food_list.name}}
              </button>
              <MyLink :href="route('food-lists.show', foodItem.food_list.id)">
                <ArrowTopRightOnSquareIcon class="ml-0.5 w-5 h-5 text-gray-500" />
              </MyLink>
            </p>
          </td>
          <td scope="row" class="pr-1 py-4 text-gray-900 text-right">
            {{roundNonZero(Number(foodItem.amount))}}
            {{foodItem.unit.name}}
          </td>
          <td class="px-8 py-4">
            {{(new Date(foodItem.date)).toLocaleDateString('en-GB', {
              day: 'numeric', month: 'long', year: 'numeric'
            })}}
          </td>
          <td>
            <div class="flex items-center px-8">
              <button
                type="button"
                @click="openUpdateDialog(foodItem)"
                class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
              >
                <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
              </button>


              <TrashIcon class="ml-1 w-5 h-5 hover:text-red-700" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else>
      You haven't created any food intake records yet!
    </p>

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
  </div>
</template>
