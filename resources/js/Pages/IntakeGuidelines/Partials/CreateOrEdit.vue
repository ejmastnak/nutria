<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { round } from '@/utils/GlobalFunctions.js'
import { PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
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
  description: props.intake_guideline ? props.intake_guideline.description : null,
  priority: props.intake_guideline ? props.intake_guideline.priority : null,
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

const showDescription = ref(props.intake_guideline ? (!!props.intake_guideline.description) : false)
const descriptionInputRef = ref(null)
function toggleDescription() {
  if (!showDescription.value) descriptionInputRef.value.focus();
  showDescription.value = !showDescription.value;
}

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
        <InputError class="mt-1" :message="form.errors.name" />
      </div>

      <!-- Priority -->
      <div class="mt-4">
        <InputLabel for="priority" value="Priority (optional)" />
        <TextInput
          id="priority"
          ref="priorityInput"
          type="number"
          class="mt-1 block w-16"
          v-model="form.priority"
        />
        <InputError class="mt-1" :message="form.errors.priority" />
      </div>

      <!-- Description -->
      <div class="mt-4 w-full">
        <InputLabel for="description" value="Description (optional)" />
        <PlainButton @click="toggleDescription" class="mt-0.5 flex items-center text-sm">
          <PencilSquareIcon v-if="!showDescription" class="-ml-1 w-5 h-5 text-gray-500" />
          <XMarkIcon v-else class="-ml-1 w-5 h-5 text-gray-600" />
          <p class="ml-1.5 whitespace-nowrap">
            {{showDescription ? "Hide description" : (form.description ? "Edit" : "Add") + " description"}}
          </p>
        </PlainButton>
        <TextArea
          v-show="showDescription" 
          id="description"
          ref="descriptionInputRef"
          class="mt-1 block w-full h-36 sm:h-44 md:h-48 max-w-xl"
          v-model="form.description"
        />
        <InputError class="mt-2" :message="form.errors.description" />
      </div>


    </section>

    <!-- Intake Guideline nutrient table -->
    <section class="mt-4">

      <h2 class="text-lg">Recommended Daily Intakes</h2>

      <p class="max-w-md text-gray-700 text-sm">
        Fill out the recommended daily intakes for each macronutrient, mineral, and vitamin.
      </p>

      <InputError class="mt-1" :message="form.errors.intake_guideline_nutrients" />

      <div class="mt-2 lg:flex lg:gap-x-4 space-y-4 lg:space-y-0">
        <div
          v-for="nc in nutrient_categories"
          :key="nc.id"
          class="col-span-1"
        >

          <h2 class="text-lg">{{nc.name}}s</h2>

          <div class="border border-gray-300 rounded-xl overflow-hidden w-full">
            <table class="text-sm sm:text-base text-left w-full">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-3 bg-blue-50">Nutrient</th>
                  <th scope="col" class="px-4 py-3 bg-blue-100">RDI</th>
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
                      <span class="ml-2 whitespace-nowrap">{{intake_guideline_nutrient.nutrient.unit.name}}</span>
                    </div>
                    <InputError class="mt-2 text-left" :message="form.errors['intake_guideline_nutrients.' + String(intake_guideline_nutrient.nutrient.seq_num - 1) + '.id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['intake_guideline_nutrients.' + String(intake_guideline_nutrient.nutrient.seq_num - 1) + '.nutrient_id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['intake_guideline_nutrients.' + String(intake_guideline_nutrient.nutrient.seq_num - 1) + '.rdi']" />
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
