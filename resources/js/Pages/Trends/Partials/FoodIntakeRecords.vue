<script setup>
import { computed } from 'vue'
import { TrashIcon } from '@heroicons/vue/24/outline'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import MyLink from '@/Components/MyLink.vue'
import { roundNonZero } from '@/utils/GlobalFunctions.js'
const props = defineProps({
  ingredient_intake_records: Array,
  meal_intake_records: Array,
  food_list_intake_records: Array,
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
            <p v-if="foodItem.type === INGREDIENT">
              <MyLink :href="route('ingredients.show', foodItem.ingredient.id)">
                {{foodItem.ingredient.name}}
              </MyLink>
            </p>
            <p v-if="foodItem.type === MEAL">
              <MyLink :href="route('meals.show', foodItem.meal.id)">
                {{foodItem.meal.name}}
              </MyLink>
            </p>
            <p v-if="foodItem.type === FOOD_LIST">
              <MyLink :href="route('food-lists.show', foodItem.food_list.id)">
                {{foodItem.food_list.name}}
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
              <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
              <TrashIcon class="ml-1 w-5 h-5 hover:text-red-700" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else>
      You haven't created any food intake records yet!
    </p>
  </div>
</template>
