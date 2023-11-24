<!-- Provides an overview of an ingredient's custom units -->
<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { round, roundNonZero, nowYYYYMMDD } from '@/utils/GlobalFunctions.js'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  ingredients: Array,
  units: Array,
})

const form = useForm({
  id: null,
  ingredient_id: null,
  ingredient: null,
  amount: null,
  unit_id: null,
  unit: null,
  date: null,
  time: null,
})

defineExpose({ open })
const isOpen = ref(false)
function open(ingredientIntakeRecord) {
  form.id = ingredientIntakeRecord ? ingredientIntakeRecord.id : null
  form.ingredient_id = ingredientIntakeRecord ? ingredientIntakeRecord.ingredient_id : null
  form.ingredient = ingredientIntakeRecord ? ingredientIntakeRecord.ingredient : {}
  form.amount = ingredientIntakeRecord ? ingredientIntakeRecord.amount : null
  form.unit_id = ingredientIntakeRecord ? ingredientIntakeRecord.unit_id : props.units.find(unit => unit.name === 'g').id
  form.unit = ingredientIntakeRecord ? ingredientIntakeRecord.unit : props.units.find(unit => unit.name === 'g')
  form.date = ingredientIntakeRecord ? ingredientIntakeRecord.date : nowYYYYMMDD()
  form.time = (ingredientIntakeRecord && ingredientIntakeRecord.time) ? ingredientIntakeRecord.time : null

  isOpen.value = true
}
function cancel() {
  form.clearErrors()
  form.reset()
  isOpen.value = false
}
function confirm() {
  if (form.id) {
    form.put(route('ingredient-intake-records.update', form.id), {
      onSuccess: () => cancel()
    })
  } else {
    form.post(route('ingredient-intake-records.store'), {
      onSuccess: () => cancel()
    })
  }
}

</script>

<template>

  <Dialog
    :open="isOpen"
    @close="cancel"
    class="relative "
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-md rounded-lg bg-white shadow max-h-[600px] overflow-auto">
        <form @submit.prevent="submit">

          <DialogTitle class="text-lg font-bold text-gray-600">
            Log Ingredient Intake
          </DialogTitle>

          <div class="mt-2">
            <FuzzyCombobox
              labelText="Ingredient"
              :options="ingredients"
              :modelValue="form.ingredient"
              :showIcon="false"
              @update:modelValue="newValue => {
                form.ingredient = newValue
                form.ingredient_id = newValue.id
              }"
            />
            <InputError :message="form.errors.ingredient_id" />
          </div>

          <!-- Weight and Unit -->
          <div class="mt-2 flex items-baseline">

            <!-- Weight -->
            <div class="w-24">
              <InputLabel for="amount" value="Weight" />
              <TextInput
                id="amount"
                class="w-full"
                type="number"
                step="any"
                placeholder="0"
                v-model="form.amount"
                required
              />
              <InputError :message="form.errors.amount" />
            </div>

            <!-- Unit -->
            <div class="ml-4 w-40">
              <SimpleCombobox
                :options="units.filter(unit => unit.g || (unit.ml && (form.ingredient && form.ingredient.density_g_ml))).concat(form.ingredient ? form.ingredient.custom_units : [])"
                labelText="Unit"
                inputClasses="w-40"
                :modelValue="form.unit"
                @update:modelValue="newValue => {
                  form.unit = newValue
                  form.unit_id = newValue.id
                }"
              />
              <InputError :message="form.errors.unit_id" />
            </div>
          </div>

          <!-- Date -->
          <div class="mt-3 w-40">
            <InputLabel for="date" value="Date" />
            <TextInput
              id="date"
              class="w-full bg-white"
              type="date"
              v-model="form.date"
              required
            />
            <InputError :message="form.errors.date" />
          </div>

          <!-- Time -->
          <div class="mt-3 w-40">
            <InputLabel for="time" value="Time (optional)" />
            <TextInput
              id="time"
              class="w-full bg-white"
              type="time"
              step="1"
              v-model="form.time"
            />
            <InputError :message="form.errors.time" />
          </div>

          <!-- Cancel/Confirm buttons -->
          <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
            <SecondaryButton @click="cancel" class="ml-2" >
              Cancel
            </SecondaryButton>
            <PrimaryButton @click="confirm" class="ml-2" >
              Okay
            </PrimaryButton>
          </div>

        </form>
      </DialogPanel>
    </div>
  </Dialog>
</template>
