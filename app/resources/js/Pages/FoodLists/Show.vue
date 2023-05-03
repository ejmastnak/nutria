<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, DocumentDuplicateIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import NutrientProfileOptions from '@/Shared/NutrientProfileOptions.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarEdit from '@/Shared/CrudNavBarEdit.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import H1 from '@/Components/H1ForCrud.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  food_list: Object,
  food_lists: Array,
  nutrient_profiles: Array,
  rdi_profiles: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const howManyGrams = ref(props.food_list.mass_in_grams);
const defaultMassInGrams = props.food_list.mass_in_grams

const searchDialog = ref(null)
const deleteDialog = ref(null)

const searchFoodList = ref({})
function search() {
  router.get(route('food-lists.show', searchFoodList.value.id))
}

// Find index of nutrient profile with `rdi_profile_id` matching selectedRdiProfile
const selectedRdiProfile = ref(props.rdi_profiles[0])
const selectedNutrientProfile = computed(() => {
  const idx = props.nutrient_profiles.map(profile => profile.rdi_profile_id).indexOf(selectedRdiProfile.value.id)
  return props.nutrient_profiles[idx ?? 0].nutrient_profile
})
</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>

    <Head :title="food_list.name" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('food-lists.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="food list" />
      <CrudNavBarCreate :enabled="can_create" :href="route('food-lists.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit v-if="can_edit" :enabled="can_edit" :href="route('food-lists.edit', food_list.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('food-lists.clone', food_list.id)" />
        <CrudNavBarDelete v-if="can_delete" :enabled="can_delete" @wasClicked="deleteDialog.open(food_list.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">

      <H1 :text="food_list.name" />

      <!-- Food list name and mass pillbox labels -->
      <div class="flex mt-2">
        <div class="bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Food list
        </div>
        <div class="ml-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{food_list.mass_in_grams}} g
        </div>
      </div>

    </div>

    <!-- Table of food list ingredients and meals -->
    <div class="mt-8 text-gray-900">
      <h2 class="text-lg">Food list ingredients</h2>

      <!-- Table of food list ingredients -->
      <table v-if="food_list.food_list_ingredients.length" class="text-left w-full">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50">
              Ingredient
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
              Amount
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="fli in food_list.food_list_ingredients"
            class="border-t text-gray-600 font-medium"
          >
            <td scope="row" class="px-4 py-2">
              <Link
                :href="route('ingredients.show', fli.ingredient_id)"
                class="text-gray-800 hover:text-blue-600 hover:underline"
              >
                {{fli.ingredient.name}}
              </Link>
            </td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              {{fli.amount}}
              {{fli.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Table of food list meals -->
      <table v-if="food_list.food_list_meals.length" class="text-left w-full">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 bg-blue-50">
              Meal
            </th>
            <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
              Amount
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="flm in food_list.food_list_meals"
            class="border-t text-gray-600 font-medium"
          >
            <td scope="row" class="px-4 py-2">
              <Link
                :href="route('meals.show', flm.meal_id)"
                class="text-gray-800 hover:text-blue-600 hover:underline"
              >
                {{flm.meal.name}}
              </Link>
            </td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              {{flm.amount}}
              {{flm.unit.name}}
            </td>
          </tr>
        </tbody>
      </table>

      <p v-if="!food_list.food_list_ingredients.length && !food_list.food_list_meals.length" class="mt-1 text-gray-700">This food list has no meals or ingredients!</p>

    </div>

    <section v-if="food_list.food_list_ingredients.length || food_list.food_list_meals.length" class="mt-10">

      <h2 class="text-lg">Nutrient profile</h2>

      <NutrientProfileOptions
        :rdi_profiles="rdi_profiles"
        v-model:how-many-grams="howManyGrams"
        v-model:selected-rdi-profile="selectedRdiProfile"
        :showMassInput="false"
      />

      <NutrientProfile
        class="w-full mt-4"
        :nutrient_profile="selectedNutrientProfile"
        :nutrient_categories="nutrient_categories"
        :howManyGrams="Number(howManyGrams)"
        :defaultMassInGrams="Number(defaultMassInGrams)"
      />

    </section>

    <SearchForThingAndGo
      ref="searchDialog"
      :things="food_lists"
      goRoute="food-lists.show"
      label="Search for another food list"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="food-lists.destroy" thing="food list" />

  </div>
</template>
