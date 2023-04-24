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
  ingredient: Object,
  nutrient_profile: Array,
  nutrient_categories: Array,
  ingredients: Array,
  can_edit: Boolean,
  can_create: Boolean,
  can_delete: Boolean
})

const howManyGrams = ref("100");
const defaultMassInGrams = 100

const deleteDialog = ref(null)

const searchIngredient = ref({})
function search() {
  router.get(route('ingredients.show', searchIngredient.value.id))
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
    <Head :title="ingredient.name" />

    <div class="flex items-center space-x-4 -mt-2 border border-gray-300 p-1 px-4 rounded-xl">

      <Link
        v-if="can_edit"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('ingredients.edit', ingredient.id)"
      >
      <div class="flex">
        <PencilSquareIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Edit</p>
      </div>
      </Link>

      <Link
        v-if="can_edit"
        class="hover:underline hover:bg-blue-100 p-2 rounded-lg"
        :href="route('ingredients.clone', ingredient.id)"
      >
      <div class="flex">
        <DocumentDuplicateIcon class="h-5 w-5 text-baseline text-gray-700" />
        <p class="ml-1">Clone</p>
      </div>
      </Link>

      <button
        v-if="can_edit"
        type="button"
        @click="deleteDialog.open(ingredient.id)"
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
          labelText="Search for another ingredient"
          :options="ingredients"
          v-model="searchIngredient"
        />
      </form>

    </div>

    <div class="mt-10 flex items-end">

      <div class="w-full">

        <h1 class="text-xl w-2/3">{{ingredient.name}}</h1>

        <!-- Ingredient category -->
        <div class="flex">
          <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
            Ingredient
          </div>

          <div class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
            {{ingredient.ingredient_category.name}}
          </div>

          <div v-if="ingredient.density_g_per_ml" class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
            {{ingredient.density_g_per_ml}} g/ml
          </div>

        </div>
      </div>

      <div class="ml-auto flex items-baseline text-gray-500 text-md">
        <div class="">
          <InputLabel for="howManyGrams" value="Ingredient quantity" class="sr-only" />
          <TextInput
            id="howManyGrams"
            type="number"
            min="0"
            class="mt-1 mx-1.5 pl-1 pr-0 text-right text-lg w-20 font-bold block py-px"
            v-model="howManyGrams"
            required
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

    <DeleteDialog ref="deleteDialog" deleteRoute="ingredients.destroy" thing="ingredient" />

  </div>
</template>
