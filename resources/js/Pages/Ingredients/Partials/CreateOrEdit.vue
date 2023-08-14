<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import SimpleCombobox from '@/Shared/SimpleCombobox.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrient_categories: Array,
  create: Boolean,
})

const form = useForm({
  name: props.create ? "" : props.ingredient.name,
  ingredient_category_id: props.ingredient.ingredient_category_id ?? 0,
  density_g_per_ml: props.ingredient.density_g_per_ml ? round(Number(props.ingredient.density_g_per_ml), 2).toString() : "",
  ingredient_nutrients: props.ingredient.ingredient_nutrients.map((ingredient_nutrient, index) => ({
    id: props.create ? 0 : ingredient_nutrient.id,
    idx: index,
    nutrient_id: ingredient_nutrient.nutrient_id,
    nutrient_category_id: ingredient_nutrient.nutrient.nutrient_category_id,
    name: ingredient_nutrient.nutrient.display_name,
    unit: ingredient_nutrient.nutrient.unit.name,
    amount_per_100g: round(Number(ingredient_nutrient.amount_per_100g), ingredient_nutrient.nutrient.precision).toString()
  }))
})

const nameInput = ref(null)
const selectedCategory = ref({})

function submit() {
  form.ingredient_category_id = selectedCategory.value.id
  if (props.create) {
    form.post(route('ingredients.store'))
  } else {
    form.put(route('ingredients.update', props.ingredient.id))
  }
}

onMounted(() => {
  if (form.ingredient_category_id) {
    // Set selectedCategory to the item in props.nutrient_categories whose id
    // equals props.ingredient.ingredient_category_id
    const idx = props.ingredient_categories.map(ic => ic.id).indexOf(props.ingredient.ingredient_category_id)
    selectedCategory.value = props.ingredient_categories[idx]
  }
  if (props.create) {
    nameInput.value.focus()
  }
})

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue"
export default {
  layout: AppLayout,
}
</script>

<template>

  <form @submit.prevent="submit">

    <section class="mt-4">
      <!-- Name -->
      <div class="w-full max-w-[40rem]">
        <InputLabel for="name" value="Name" />
        <TextInput
          id="name"
          ref="nameInput"
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
        <InputError class="mt-2" :message="form.errors.ingredient_category_id" />
      </div>

      <!-- Density -->
      <div class="mt-4 w-full max-w-[22rem]">
        <InputLabel for="density" value="Density in grams per milliliter (optional)" />
        <div class="flex items-baseline">
          <TextInput
            id="density"
            type="text"
            class="mt-1 block w-20 text-right"
            v-model="form.density_g_per_ml"
          />
          <p class="ml-2 text-gray-700">g/ml</p>
        </div>
        <InputError class="mt-2" :message="form.errors.density_g_per_ml" />
      </div>

    </section>

    <!-- Ingredient nutrient table -->
    <section class="mt-8">

      <InputError :message="form.errors.ingredient_nutrients" />

      <div class="grid grid-cols-1 lg:flex md:gap-x-8">
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
                  <td class="px-4 py-2 text-right">
                    <div class="flex items-baseline">
                      <TextInput
                        type="text"
                        class="mt-1 block w-24 py-1 text-right"
                        v-model="ingredient_nutrient.amount_per_100g"
                        required
                      />
                      <span class="ml-2">{{ingredient_nutrient.unit}}</span>
                    </div>
                    <InputError class="mt-2 text-left" :message="form.errors['ingredient_nutrients.' + ingredient_nutrient.idx + '.id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['ingredient_nutrients.' + ingredient_nutrient.idx + '.nutrient_id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['ingredient_nutrients.' + ingredient_nutrient.idx + '.amount_per_100g']" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <section class="mt-4">

      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        <span v-if="create">Create</span>
        <span v-else>Update</span>
      </PrimaryButton>

      <SecondaryLinkButton
        :href="route('ingredients.index')"
        class="ml-4"
      >
        Cancel
      </SecondaryLinkButton>

    </section>

  </form>

</template>
