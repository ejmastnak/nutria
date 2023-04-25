<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, DocumentDuplicateIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  meal: Object,
  meals: Array,
  nutrient_profile: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_create: Boolean,
  can_delete: Boolean
})

const howManyGrams = ref("100");
const defaultMassInGrams = props.meal.mass_in_grams

const deleteDialog = ref(null)

const searchMeal = ref({})
function search() {
  router.get(route('meals.show', searchMeal.value.id))
}

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="w-fit">

    <Head :title="meal.name" />

    <div class="flex items-center space-x-4 -mt-2 border border-gray-300 p-1 px-4 rounded-xl">

      <Link
        v-if="can_edit"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('meals.edit', meal.id)"
      >
      <div class="flex">
        <PencilSquareIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Edit</p>
      </div>
      </Link>

      <Link
        v-if="can_edit"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('meals.clone', meal.id)"
      >
      <div class="flex">
        <DocumentDuplicateIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Clone</p>
      </div>
      </Link>

      <button
        v-if="can_delete"
        type="button"
        @click="deleteDialog.open(meal.id)"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
      >
      <div class="flex">
        <TrashIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Delete</p>
      </div>
      </button>

      <form
        @submit.prevent="search"
        class="!ml-auto"
      >
        <FuzzyCombobox
          labelText="Search for another meal"
          :options="meals"
          v-model="searchMeal"
        />
      </form>

    </div>

    <div class="mt-10 flex items-end">

      <div class="w-full">

        <h1 class="text-xl w-2/3">{{meal.name}}</h1>

        <!-- Meal pillbox label -->
        <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Meal
        </div>
      </div>

      <!-- How many grams text input -->
      <div class="ml-auto flex items-baseline text-gray-500 text-md">
        <div class="">
          <InputLabel for="howManyGrams" value="Meal mass" class="sr-only" />
          <TextInput
            id="howManyGrams"
            type="number"
            min="0"
            class="mt-1 mx-1.5 pl-1 pr-0 text-right text-lg w-20 font-bold block py-px"
            v-model="howManyGrams"
          />
        </div>
        <p class="">grams</p>
      </div>

    </div>

    <NutrientProfile
      class="mt-10 w-full"
      :nutrient_profile="nutrient_profile"
      :nutrient_categories="nutrient_categories"
      :howManyGrams="Number(howManyGrams)"
      :defaultMassInGrams="Number(defaultMassInGrams)"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="meals.destroy" thing="meal" />

  </div>
</template>
