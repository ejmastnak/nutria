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
  <div class="">
    <Head title="Ingredient" />

    <h1 class="text-2xl">{{ingredient.name}}</h1>

    <!-- Category and density information about ingredient -->
    <section class="mt-1">
      <div class="">
        <span class="bg-blue-100 px-3 py-1 rounded-xl font-medium border border-gray-300 text-sm">{{ingredient.ingredient_category.name}}</span>
      </div>
      <!-- <div v-if="ingredient.density_g_per_ml" class=""> -->
      <!--   Density: {{ingredient.density_g_per_ml}} -->
      <!-- </div> -->
    </section>

    <section class="mt-8 w-fit">
      <div class="flex text-baseline">
        <h2 class="text-xl">
          Nutrient profile
        </h2>
        <div class="ml-auto flex items-baseline text-gray-500 text-sm">
          <p class="">For</p>
          <div class="">
            <InputLabel for="howManyGrams" value="Ingredient quantity" class="sr-only" />
            <TextInput
              id="howManyGrams"
              type="number"
              class="mt-1 mx-1 px-1 text-right text-sm w-16 block py-px"
              v-model="howManyGrams"
              required
            />
          </div>
          <p class="">grams</p>
        </div>
      </div>

      <NutrientProfile 
        :nutrient_profile="nutrient_profile"
        :howManyGrams="Number(howManyGrams)"
        :defaultMassInGrams="Number(defaultMassInGrams)" 
        class="mt-3"
      />
    </section>


    <!-- Buttons for Back, Edit, and Delete -->
    <!-- <div class="mt-4"> -->
    <!--   <PrimaryLinkButton -->
    <!--     class="ml-auto" -->
    <!--     :href="route('ingredients.index')" -->
    <!--   > -->
    <!--     Back -->
    <!--   </PrimaryLinkButton> -->
    <!---->
    <!--   <SecondaryLinkButton -->
    <!--     v-if="can_edit" -->
    <!--     class="ml-auto" -->
    <!--     :href="route('ingredients.edit', ingredient.id)" -->
    <!--   > -->
    <!--     Edit -->
    <!--   </SecondaryLinkButton> -->
    <!---->
    <!--   <SecondaryLinkButton -->
    <!--     v-if="can_delete" -->
    <!--     class="ml-auto" -->
    <!--     :href="route('ingredients.destroy', ingredient.id)" -->
    <!--     as="button" -->
    <!--     method="delete" -->
    <!--   > -->
    <!--     Delete -->
    <!--   </SecondaryLinkButton> -->
    <!---->
    <!-- </div> -->

  </div>
  </template>
