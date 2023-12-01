<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { getCurrentLocalYYYYMMDD, getCurrentLocalHHmm, getLocalYYYYMMDD, getLocalHHMM, getUTCDateTime } from '@/utils/GlobalFunctions.js'
import { ClockIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  meals: Array,
  units: Array,
})

const form = useForm({
  id: null,
  meal_id: null,
  meal: null,
  amount: null,
  unit_id: null,
  unit: null,
  date: null,
  time: null,
  date_time_utc: null,
})

defineExpose({ open })
const isOpen = ref(false)
function open(mealIntakeRecord) {
  form.id = mealIntakeRecord ? mealIntakeRecord.id : null
  form.meal_id = mealIntakeRecord ? mealIntakeRecord.meal_id : null
  form.meal = mealIntakeRecord ? mealIntakeRecord.meal : {}
  form.amount = mealIntakeRecord ? mealIntakeRecord.amount : null
  form.unit_id = mealIntakeRecord ? mealIntakeRecord.unit_id : null
  form.unit = mealIntakeRecord ? mealIntakeRecord.unit : {}
  form.date = mealIntakeRecord ? getLocalYYYYMMDD(mealIntakeRecord.date_time_utc) : getCurrentLocalYYYYMMDD()
  form.time = mealIntakeRecord ? getLocalHHMM(mealIntakeRecord.date_time_utc) : getCurrentLocalHHmm()

  isOpen.value = true
}
function cancel() {
  form.clearErrors()
  form.reset()
  isOpen.value = false
}
function submit() {
  form.date_time_utc = getUTCDateTime(form.date + " " + form.time + ":00")
  if (form.id) {
    form.put(route('meal-intake-records.update', form.id), {
      onSuccess: () => cancel()
    })
  } else {
    form.post(route('meal-intake-records.store'), {
      onSuccess: () => cancel()
    })
  }
}

</script>

<template>

  <Dialog
    :open="isOpen"
    @close="cancel"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-md rounded-lg bg-white shadow max-h-[600px] overflow-auto">
        <form @submit.prevent="submit">

          <DialogTitle class="text-lg font-bold text-gray-600">
            Log Meal Intake
          </DialogTitle>

          <div class="mt-2">
            <FuzzyCombobox
              labelText="Meal"
              :options="meals"
              :modelValue="form.meal"
              :showIcon="false"
              @update:modelValue="newValue => {
                form.meal = newValue
                form.meal_id = newValue.id
                form.amount = 1
                form.unit = newValue.meal_unit
                form.unit_id = newValue.meal_unit.id
              }"
            />
            <InputError :message="form.errors.meal_id" />
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
                :options="units.filter(unit => unit.g).concat(form.meal.meal_unit ? Array(form.meal.meal_unit) : [])"
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
          <div class="mt-3 flex items-end">
            <div class="w-40">
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
            <SecondaryButton @click="form.date = getCurrentLocalYYYYMMDD()" class="ml-2 h-fit">
              <CalendarIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
              <p class="ml-1">Today</p>
            </SecondaryButton>
          </div>

          <!-- Time -->
          <div class="mt-3 flex items-end">
            <div class="w-40">
              <InputLabel for="time" value="Time" />
              <TextInput
                id="time"
                class="w-full bg-white"
                type="time"
                step="60"
                v-model="form.time"
              />
              <InputError :message="form.errors.time" />
            </div>
            <SecondaryButton @click="form.time = getCurrentLocalHHmm()" class="ml-2 h-fit">
              <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
              <p class="ml-1">Now</p>
            </SecondaryButton>
          </div>

          <div class="mt-2">
              <InputError :message="form.errors.date_time_utc" />
          </div>

          <!-- Cancel/Confirm buttons -->
          <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
            <SecondaryButton @click="cancel" class="ml-2" >
              Cancel
            </SecondaryButton>
            <PrimaryButton class="ml-2" >
              Save
            </PrimaryButton>
          </div>

        </form>
      </DialogPanel>
    </div>
  </Dialog>
</template>
