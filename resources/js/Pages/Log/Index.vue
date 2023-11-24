<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import MyLink from '@/Components/MyLink.vue'
import LogBodyWeightDialog from './Partials/LogBodyWeightDialog.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  user_ingredients: Array,
  meals: Array,
  food_lists: Array,
  units: Array,
})

const logBodyWeightDialogRef = ref(null)

const ingredients = props.user_ingredients.concat(window.usdaIngredients ? window.usdaIngredients : [])
</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>
    <Head title="Log Data" />

    <h1 class="text-xl">Log data</h1>
    <ul class="mt-4">
      <li>Body weight.</li>
      <li>Food.</li>
    </ul>

    <SecondaryButton @click="logBodyWeightDialogRef.open(null)" >
      Body Weight
    </SecondaryButton>
    <LogBodyWeightDialog :units="units" ref="logBodyWeightDialogRef" />

  </div>
</template>
