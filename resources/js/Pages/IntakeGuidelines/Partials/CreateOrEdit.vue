<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  intake_guideline: Object,
  nutrients: Array,
  nutrient_categories: Array,
  create: Boolean
})

const form = useForm({
  id: props.intake_guideline ? props.intake_guideline.id : null,
  name: props.intake_guideline ? props.intake_guideline.name : "",
  // Reset zero amounts to null, to make it easier for user to fill values in.
  intake_guideline_nutrients: props.intake_guideline
        ? props.intake_guideline.intake_guideline_nutrients.map((intake_guideline_nutrient) => {
          if (intake_guideline_nutrient.rdi == 0) intake_guideline_nutrient.rdi = null;
          return intake_guideline_nutrient
        })
    : props.nutrients.map((nutrient) => ({
      id: null,
      rdi: null,
      nutrient_id: nutrient.id,
      nutrient: nutrient
    })),
})

const nameInput = ref(null)
onMounted(() => {
  if (props.intake_guideline === null) nameInput.value.focus()
})

function submit() {
  if (props.create) {
    form.post(route('intake-guidelines.store'))
  } else {
    form.put(route('intake-guidelines.update', props.intake_guideline.id))
  }
}
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

    </section>

    <!-- Intake Guideline nutrient table -->
    <section class="mt-4">

      <h2 class="text-lg">Recommended Daily Intakes</h2>

      <p class="max-w-md text-gray-700 text-sm">
        Fill out the recommended daily intakes for each macronutrient, mineral, and vitamin.
      </p>

      <InputError class="mt-1" :message="form.errors.intake_guideline_nutrients" />

      <div class="mt-2 grid grid-cols-1 lg:flex md:gap-x-8 gap-y-3">
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
                  <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
                    RDI
                  </th>
                  <th scope="col" class="bg-blue-100"></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="intake_guideline_nutrient in form.intake_guideline_nutrients.filter(ign => ign.nutrient.nutrient_category_id === nc.id)"
                  class="border-t text-gray-600"
                >
                  <td scope="row" class="px-3 py-2 whitespace-nowrap">
                    {{intake_guideline_nutrient.nutrient.display_name}}
                  </td>
                  <td class="pl-2 pr-2 py-2 text-right">
                    <div class="flex items-baseline">
                      <TextInput
                        type="number"
                        placeholder="0"
                        step="any"
                        class="mt-1 block w-24 py-1 text-right"
                        v-model="intake_guideline_nutrient.rdi"
                      />
                    </div>
                    <InputError class="mt-2 text-left" :message="form.errors['intake_guideline_nutrients.' + String(intake_guideline_nutrient.nutrient.seq_num - 1) + '.id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['intake_guideline_nutrients.' + String(intake_guideline_nutrient.nutrient.seq_num - 1) + '.nutrient_id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['intake_guideline_nutrients.' + String(intake_guideline_nutrient.nutrient.seq_num - 1) + '.rdi']" />
                  </td>
                  <td class="pl-0 pr-4 py-2 text-left whitespace-nowrap">
                    {{intake_guideline_nutrient.nutrient.unit.name}}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Create/Update/Cancel buttons -->
    <section class="mt-4">

      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        <span v-if="create">Create</span>
        <span v-else>Update</span>
      </PrimaryButton>

      <SecondaryLinkButton
        :href="route('intake-guidelines.index')"
        class="ml-4"
      >
        Cancel
      </SecondaryLinkButton>

    </section>

  </form>


</template>
