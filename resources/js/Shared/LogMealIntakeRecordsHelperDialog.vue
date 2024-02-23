<script setup>
import { ref, computed } from 'vue'
import cloneDeep from "lodash/cloneDeep"
import { currentLocalDate, currentLocalTime, utcTimestampToLocalDate, utcTimestampToLocalTime, localTimestampToUtcTimestamp } from '@/utils/GlobalFunctions.js'
import { ClockIcon, CalendarIcon, PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  meals: Array,
  units: Array,
})

const emit = defineEmits(['cancel', 'confirm'])

const mealIntakeRecord = ref({
  ingredient_id: null,
  meal_id: null,
  meal: null,
  amount: null,
  unit_id: null,
  unit: null,
  date: null,
  time: null,
  date_time_utc: null,
  description: null,
})

defineExpose({ open })

const isOpen = ref(false)

const errors = ref({})
const clientSideErrors = ref({})

const showDescription = ref(false)
const descriptionInputRef = ref(null)
function toggleDescription() {
  if (!showDescription.value) descriptionInputRef.value.focus();
  showDescription.value = !showDescription.value;
}

function open(passedMealIntakeRecord, passedErrors) {
  mealIntakeRecord.value.meal_id = passedMealIntakeRecord ? passedMealIntakeRecord.meal_id : null
  mealIntakeRecord.value.meal = passedMealIntakeRecord ? cloneDeep(passedMealIntakeRecord.meal) : {}
  mealIntakeRecord.value.amount = passedMealIntakeRecord ? passedMealIntakeRecord.amount : null
  mealIntakeRecord.value.unit_id = passedMealIntakeRecord ? passedMealIntakeRecord.unit_id : null
  mealIntakeRecord.value.unit = passedMealIntakeRecord ? cloneDeep(passedMealIntakeRecord.unit) : null
  mealIntakeRecord.value.date = passedMealIntakeRecord ? utcTimestampToLocalDate(passedMealIntakeRecord.date_time_utc) : currentLocalDate()
  mealIntakeRecord.value.time = passedMealIntakeRecord ? utcTimestampToLocalTime(passedMealIntakeRecord.date_time_utc) : currentLocalTime()
  mealIntakeRecord.value.description = passedMealIntakeRecord ? passedMealIntakeRecord.description : null

  showDescription.value = passedMealIntakeRecord ? (!!passedMealIntakeRecord.description) : false
  errors.value = passedErrors
  isOpen.value = true
}

const mealInputWrapperRef = ref(null)
const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)

function passesValidation() {

  // Check that meal is set
  if (mealIntakeRecord.value.meal_id === null) {
    mealInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that amount is not empty
  if (mealIntakeRecord.value.amount === null || mealIntakeRecord.value.amount.length === 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(mealIntakeRecord.value.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (mealIntakeRecord.value.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (mealIntakeRecord.value.date === null || mealIntakeRecord.value.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (mealIntakeRecord.value.time === null || mealIntakeRecord.value.time.length === 0) {
    timeInputRef.value.focus()
    return false
  }

  return true
}

function handleAmountInputEnter() {
  checkAndConfirm()
}
function handleUnitInputEnter() {
  checkAndConfirm()
}
function handleDateInputEnter() {
  checkAndConfirm()
}
function handleTimeInputEnter() {
  checkAndConfirm()
}

function updateMeal(newMeal) {
  mealIntakeRecord.value.meal = newMeal
  mealIntakeRecord.value.meal_id = newMeal.id

  // Reset meal's unit to 1 meal if a unit has not yet been selected or if old
  // unit is not supported by new meal (mass units will always be supported,
  // but each meal's natural unit is different).
  if (mealIntakeRecord.value.unit_id === null || mealIntakeRecord.value.unit.g === null) {
    mealIntakeRecord.value.unit = newMeal.meal_unit
    mealIntakeRecord.value.unit_id = newMeal.meal_unit.id
    if (mealIntakeRecord.value.amount === null) mealIntakeRecord.value.amount = 1;
  }
}

function cancel() {
  emit('cancel')
  isOpen.value = false
  clientSideErrors.value = {}
}

function checkAndConfirm() {
  if (passesValidation()) confirm();
}

function confirm() {
  mealIntakeRecord.value.date_time_utc = localTimestampToUtcTimestamp(mealIntakeRecord.value.date + " " + mealIntakeRecord.value.time)
  emit('confirm', mealIntakeRecord.value)
  isOpen.value = false
  clientSideErrors.value = {}
}

</script>

<template>

  <Dialog
    :open="isOpen"
    @close="cancel"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">

      <DialogPanel class="px-6 pt-6 w-full max-w-md rounded-lg bg-white shadow overflow-auto" :class="showDescription ? 'max-h-[700px] ' : 'max-h-[600px]'">

        <DialogTitle class="text-lg font-bold text-gray-600">
          Log meal intake
        </DialogTitle>

        <div class="mt-2">
          <div ref="mealInputWrapperRef">
            <FuzzyCombobox
              labelText="Meal"
              :targets="meals"
              :modelValue="mealIntakeRecord.meal"
              :showIcon="false"
              @update:modelValue="newValue => updateMeal(newValue)"
            />
          </div>
          <InputError :message="errors.meal_id" />
        </div>

        <!-- Amount and Unit -->
        <div class="mt-2 flex items-baseline">

          <!-- Amount -->
          <div class="w-24">
            <InputLabel for="amount" value="Amount" />
            <TextInput
              id="amount"
              ref="amountInputRef"
              @keyup.enter="handleAmountInputEnter"
              class="w-full"
              type="number"
              step="any"
              placeholder="0"
              v-model="mealIntakeRecord.amount"
              required
            />
            <InputError :message="errors.amount" />
            <InputError :message="clientSideErrors.amount" />
          </div>

          <!-- Unit -->
          <div class="ml-4 w-40">
            <div ref="unitInputWrapperRef">
              <SimpleCombobox
                :options="mealIntakeRecord.meal_id ? Array(mealIntakeRecord.meal.meal_unit).concat(units.filter(unit => unit.g)) : []"
                labelText="Unit"
                inputClasses="w-40"
                @keyup.enter="handleUnitInputEnter"
                :modelValue="mealIntakeRecord.unit"
                @update:modelValue="newValue => {
                  mealIntakeRecord.unit = newValue
                  mealIntakeRecord.unit_id = newValue.id
                }"
              />
            </div>
            <InputError :message="errors.unit_id" />
          </div>
        </div>

        <!-- Date -->
        <div class="mt-3 flex items-end">
          <div class="w-40">
            <InputLabel for="date" value="Date" />
            <TextInput
              id="date"
              ref="dateInputRef"
              @keyup.enter="handleDateInputEnter"
              class="w-full bg-white"
              type="date"
              v-model="mealIntakeRecord.date"
              required
            />
            <InputError :message="errors.date" />
          </div>
          <SecondaryButton @click="mealIntakeRecord.date = currentLocalDate()" class="ml-2 h-fit">
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
              ref="timeInputRef"
              @keyup.enter="handleTimeInputEnter"
              class="w-full bg-white"
              type="time"
              step="60"
              v-model="mealIntakeRecord.time"
            />
            <InputError :message="errors.time" />
          </div>
          <SecondaryButton @click="mealIntakeRecord.time = currentLocalTime()" class="ml-2 h-fit">
            <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
            <p class="ml-1">Now</p>
          </SecondaryButton>
        </div>

        <div class="mt-2">
          <InputError :message="errors.date_time_utc" />
        </div>

        <!-- Description -->
        <div class="mt-4 w-full">
          <InputLabel for="description" value="Description (optional)" />
          <PlainButton @click="toggleDescription" class="mt-0.5 flex items-center text-sm">
            <PencilSquareIcon v-if="!showDescription" class="-ml-1 w-5 h-5 text-gray-500" />
            <XMarkIcon v-else class="-ml-1 w-5 h-5 text-gray-600" />
            <p class="ml-1.5 whitespace-nowrap">
              {{showDescription ? "Hide description" : (mealIntakeRecord.description ? "Edit" : "Add") + " description" + (mealIntakeRecord.description ? "" : " (optional)")}}
            </p>
          </PlainButton>
          <TextArea
            v-show="showDescription" 
            id="description"
            ref="descriptionInputRef"
            class="mt-1 block w-full h-32 sm:h-36 max-w-xl"
            v-model="mealIntakeRecord.description"
          />
          <InputError class="mt-2" :message="errors.description" />
        </div>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton
            @mouseup="cancel"
            @keyup.enter="cancel"
            @click.prevent
            class="ml-2"
          >
            Cancel
          </SecondaryButton>
          <PrimaryButton
            @mouseup="checkAndConfirm"
            @keyup.enter="checkAndConfirm"
            @click.prevent
            class="ml-2"
          >
            Okay
          </PrimaryButton>
        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
