<script setup>
import { Head, Link } from '@inertiajs/vue3'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'

const props = defineProps({
  ingredient: Object,
  nutrient_profile: Array,
  can_edit: Boolean,
  can_delete: Boolean
})

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

    <h1 class="text-xl font-semibold">{{ingredient.name}}</h1>

    <div class="">
      <PrimaryLinkButton
        class="ml-auto"
        :href="route('ingredients.index')"
      >
        Back
      </PrimaryLinkButton>

      <SecondaryLinkButton
        v-if="can_edit"
        class="ml-auto"
        :href="route('ingredients.edit', ingredient.id)"
      >
        Edit
      </SecondaryLinkButton>

      <SecondaryLinkButton
        v-if="can_delete"
        class="ml-auto"
        :href="route('ingredients.destroy', ingredient.id)"
        as="button"
        method="delete"
      >
        Delete
      </SecondaryLinkButton>


    </div>

    <pre class="mt-8">
      {{ingredient}}
    </pre>

    <NutrientProfile :nutrient_profile="nutrient_profile" />

  </div>
  </template>
