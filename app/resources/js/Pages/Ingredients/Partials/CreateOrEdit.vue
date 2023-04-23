<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3';
import SimpleCombobox from '@/Shared/SimpleCombobox.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue';
import IngredientNutrientTable from './IngredientNutrientTable.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrient_categories: Array,
  create: Boolean
})

const form = useForm({
  name: props.create ? "" : props.ingredient.name,
  density_g_per_ml: props.create ? "" : (props.ingredient.density_g_per_ml ? props.ingredient.density_g_per_ml.toFixed(2) : ""),
  ingredient_nutrients: props.ingredient.ingredient_nutrients.map(nutrient => ({
    nutrient_id: nutrient.nutrient_id,
    nutrient_category_id: nutrient.nutrient_category_id,
    name: nutrient.nutrient.display_name,
    unit: nutrient.nutrient.unit.name,
    amount_per_100g: nutrient.amount_per_100g.toString()
  }))
});

const selectedCategory = ref({})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div class="">

    <section class="mt-4">

      <!-- Name -->
      <div class="w-full max-w-[22rem]">
        <InputLabel for="name" value="Name" />
        <TextInput
          id="name"
          type="text"
          class="mt-1 block w-full"
          v-model="form.name"
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <!-- Ingredient category -->
      <div class="w-full max-w-[22rem] mt-4">
        <SimpleCombobox
          :options="ingredient_categories"
          labelText="Ingredient category"
          :modelValue="selectedCategory"
          @update:modelValue="newValue => selectedCategory = newValue"
        />
      </div>

      <!-- Density -->
      <div class="mt-4 w-full max-w-[22rem]">
        <InputLabel for="density" value="Density in grams per milliliter (optional)" />
        <TextInput
          id="density"
          type="text"
          class="mt-1 block w-full"
          v-model="form.density_g_per_ml"
          required
        />
        <InputError class="mt-2" :message="form.errors.density_g_per_ml" />
      </div>

    </section>

    <!-- Ingredient nutrient table -->
    <section class="mt-8 grid grid-cols-1 lg:flex md:gap-x-8">

      <div
        v-for="nc in nutrient_categories"
        :key="nc.id"
        class="col-span-1"
      >

        <h2 class="text-lg">{{nc.name}}s</h2>

        <div class="border border-gray-300 rounded-xl overflow-hidden w-fit">
          <table class="text-sm sm:text-base text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
                <th scope="col" class="px-4 py-3 bg-blue-50">
                  Nutrient
                </th>
                <th scope="col" class="px-4 py-3 bg-blue-100">
                  Amount
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="ingredient_nutrient in form.ingredient_nutrients.filter(nutrient => nutrient.nutrient_category_id === nc.id)"
                class="border-t text-gray-600"
              >
                <td scope="row" class="px-5 py-2">
                  {{ingredient_nutrient.name}}
                </td>
                <td class="px-4 py-2 text-right flex items-baseline">
                  <TextInput
                    type="text"
                    class="mt-1 block w-20 py-1"
                    v-model="ingredient_nutrient.amount_per_100g"
                    required
                  />
                  <span class="ml-2">{{ingredient_nutrient.unit}}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </div>
</template>
