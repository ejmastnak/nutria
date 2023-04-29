<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import SimpleCombobox from '@/Shared/SimpleCombobox.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  rdi_profile: Object,
  nutrient_categories: Array,
  create: Boolean
})

const form = useForm({
  name: props.rdi_profile.name ?? "",
  rdi_profile_nutrients: props.rdi_profile.rdi_profile_nutrients.map((rdi_profile_nutrient, index) => ({
    idx: index,
    id: props.create ? 0 : rdi_profile_nutrient.id,
    nutrient_id: rdi_profile_nutrient.nutrient_id,
    rdi: rdi_profile_nutrient.rdi.toString(),
    nutrient: {
      id: rdi_profile_nutrient.nutrient.id,
      name: rdi_profile_nutrient.nutrient.display_name,
      unit_id: rdi_profile_nutrient.nutrient.unit_id,
      nutrient_category_id: rdi_profile_nutrient.nutrient.nutrient_category_id,
      unit: rdi_profile_nutrient.nutrient.unit
    }
  }))
})

function submit() {
  if (props.create) {
    form.post(route('rdi-profiles.store'))
  } else {
    form.put(route('rdi-profiles.update', props.rdi_profile.id))
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
          type="text"
          class="mt-1 block w-full"
          v-model="form.name"
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

    </section>

    <!-- RDI nutrient table -->
    <section class="mt-8">

      <InputError :message="form.errors.rdi_profile_nutrients" />

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
                  <th scope="col" class="px-4 py-3 bg-blue-100 text-right">
                    RDI
                  </th>
                  <th scope="col" class="bg-blue-100"></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="rdi_profile_nutrient in form.rdi_profile_nutrients.filter(rdi_profile_nutrient => rdi_profile_nutrient.nutrient.nutrient_category_id === nc.id)"
                  class="border-t text-gray-600"
                >
                  <td scope="row" class="px-3 py-2">
                    {{rdi_profile_nutrient.nutrient.name}}
                  </td>
                  <td class="pl-2 pr-2 py-2 text-right">
                    <div class="flex items-baseline">
                      <TextInput
                        type="text"
                        class="mt-1 block w-24 py-1 text-right"
                        v-model="rdi_profile_nutrient.rdi"
                        required
                      />
                    </div>
                    <InputError class="mt-2 text-left" :message="form.errors['rdi_profile_nutrients.' + rdi_profile_nutrient.idx + '.id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['rdi_profile_nutrients.' + rdi_profile_nutrient.idx + '.nutrient_id']" />
                    <InputError class="mt-2 text-left" :message="form.errors['rdi_profile_nutrients.' + rdi_profile_nutrient.idx + '.rdi']" />
                  </td>
                  <td class="pl-0 pr-4 py-2 text-left whitespace-nowrap">
                    {{rdi_profile_nutrient.nutrient.unit.name}}
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
        :href="route('rdi-profiles.index')"
        class="ml-4"
      >
        Cancel
      </SecondaryLinkButton>

    </section>

  </form>


</template>
