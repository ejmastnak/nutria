<script setup>
import { ref, computed } from 'vue'
import NutrientProfileTables from '@/Shared/NutrientProfileTables.vue'
import NutrientProfileOptions from '@/Shared/NutrientProfileOptions.vue'

const props = defineProps({
  intake_guidelines: Array,
  nutrient_profiles: Array,
  nutrient_categories: Array,
  defaultMassInGrams: Number,
  displayMassInput: Boolean
})

// Converted to String for use in text input field
const howManyGrams = ref(props.defaultMassInGrams.toString());

// Find index of nutrient profile with `intake_guideline_id` matching
// selectedIntakeGuideline
const selectedIntakeGuideline = ref(props.intake_guidelines[0])
const selectedNutrientProfile = computed(() => {
  const idx = props.nutrient_profiles.map(profile => profile.intake_guideline_id).indexOf(selectedIntakeGuideline.value.id)
  return props.nutrient_profiles[idx ?? 0].nutrient_profile
})
</script>

<template>
  <section>

    <h2 class="text-lg">Nutrient profile</h2>

    <NutrientProfileOptions
      :intake_guidelines="intake_guidelines"
      :displayMassInput="displayMassInput"
      v-model:how-many-grams="howManyGrams"
      v-model:selected-intake-guideline="selectedIntakeGuideline"
    />

    <NutrientProfileTables
      class="w-full mt-4"
      :nutrient_profile="selectedNutrientProfile"
      :nutrient_categories="nutrient_categories"
      :howManyGrams="Number(howManyGrams)"
      :defaultMassInGrams="defaultMassInGrams"
    />

  </section>
</template>
