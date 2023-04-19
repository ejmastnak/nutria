<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  ingredient: Object,
  nutrient_profile: Array,
  nutrient_categories: Array,
  can_edit: Boolean,
  can_delete: Boolean
})

const howManyGrams = ref("100");
const defaultMassInGrams = 100

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="w-fit">
    <Head title="Ingredient" />

    <div class="flex items-end">

      <div class="w-full">
        <h1 class="text-xl w-2/3">{{ingredient.name}}</h1>

        <!-- Ingredient category -->
        <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.ingredient_category.name}}
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
      class="mt-8 w-full"
      :nutrient_profile="nutrient_profile"
      :nutrient_categories="nutrient_categories"
      :howManyGrams="Number(howManyGrams)"
      :defaultMassInGrams="Number(defaultMassInGrams)" 
    />




    <!-- Buttons for Back, Edit, and Delete -->
    <div class="mt-4 flex">
      <PrimaryLinkButton
        class=""
        :href="route('ingredients.index')"
      >
        Back
      </PrimaryLinkButton>

      <SecondaryLinkButton
        v-if="can_edit"
        class=""
        :href="route('ingredients.edit', ingredient.id)"
      >
        Edit
      </SecondaryLinkButton>

      <SecondaryLinkButton
        v-if="can_delete"
        class=""
        :href="route('ingredients.destroy', ingredient.id)"
        as="button"
        method="delete"
      >
        Delete
      </SecondaryLinkButton>
    </div>


  </div>
  </template>
