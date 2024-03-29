<script setup>
import { ref, computed, onMounted } from 'vue'
import cloneDeep from "lodash/cloneDeep"
import { currentLocalDate, currentLocalTime, utcTimestampToLocalDate, utcTimestampToLocalTime, localTimestampToUtcTimestamp } from '@/utils/GlobalFunctions.js'
import { ClockIcon, CalendarIcon, PlusCircleIcon, PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import LogMealIntakeRecordsDialog from '@/Shared/LogMealIntakeRecordsDialog.vue'
import MealCreateAndLogChooserDialog from '@/Shared/MealCreateAndLogChooserDialog.vue'

import { mealIntakeRecordForm } from '@/Shared/store.js'
import { mealIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  meals: Array,
  units: Array,
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

const mealInputWrapperRef = ref(null)
const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)
const logMealIntakeRecordsDialogRef = ref(null)
const mealCreateAndLogChooserDialogRef = ref(null)

function open(record) {
  mealIntakeRecordForm.id = record ? record.id : null
  mealIntakeRecordForm.meal_id = record ? record.meal_id : null
  mealIntakeRecordForm.meal = record ? cloneDeep(record.meal) : {}
  mealIntakeRecordForm.amount = record ? record.amount : null
  mealIntakeRecordForm.unit_id = record ? record.unit_id : null
  mealIntakeRecordForm.unit = record ? cloneDeep(record.unit) : null
  mealIntakeRecordForm.date = record ? utcTimestampToLocalDate(record.date_time_utc) : currentLocalDate()
  mealIntakeRecordForm.time = record ? utcTimestampToLocalTime(record.date_time_utc) : currentLocalTime()
  mealIntakeRecordForm.description = record ? record.description : null

  showDescription.value = record ? (!!record.description) : false
  isOpen.value = true

  // Autofocus meal input (and not "New Meal" button) for new meals
  if (mealIntakeRecordForm.id === null) {
    setTimeout(() => {
      if (mealInputWrapperRef) {
        mealInputWrapperRef.value.querySelectorAll('input')[0].focus()
      }
    }, 0);
  }

}

function passesValidation() {

  // Check that meal is set
  if (mealIntakeRecordForm.meal_id === null) {
    mealInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that amount is not empty
  if (mealIntakeRecordForm.amount === null || mealIntakeRecordForm.amount.length === 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(mealIntakeRecordForm.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (mealIntakeRecordForm.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (mealIntakeRecordForm.date === null || mealIntakeRecordForm.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (mealIntakeRecordForm.time === null || mealIntakeRecordForm.time.length === 0) {
    timeInputRef.value.focus()
    return false
  }

  return true
}

// Used to enable/disable "Add More" button; same checks as passesValidation,
// but without interacting with UI or populating client-side errors.
const allInputsValid = computed(() => {

  // Check that meal is set
  if (mealIntakeRecordForm.meal_id === null) return false;

  // Check that amount is not empty
  if (mealIntakeRecordForm.amount === null || mealIntakeRecordForm.amount.length === 0) return false;

  // Check that amount is gt:0
  const amount = Number(mealIntakeRecordForm.amount)
  if (isNaN(amount) || amount <= 0) return false;

  // Check that unit is set
  if (mealIntakeRecordForm.unit_id === null) return false;

  // Check that date is not empty
  if (mealIntakeRecordForm.date === null || mealIntakeRecordForm.date.length === 0) return false;

  // Check that time is not empty
  if (mealIntakeRecordForm.time === null || mealIntakeRecordForm.time.length === 0) return false;

  return true
})

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

function checkAndConfirm() {
  if (passesValidation()) save();
}

function updateMeal(newMeal) {
  mealIntakeRecordForm.meal = newMeal
  mealIntakeRecordForm.meal_id = newMeal.id

  // Reset meal's unit to 1 meal if a unit has not yet been selected or if old
  // unit is not supported by new meal (mass units will always be supported,
  // but each meal's natural unit is different).
  if (mealIntakeRecordForm.unit_id === null || mealIntakeRecordForm.unit.g === null) {
    mealIntakeRecordForm.unit = newMeal.meal_unit
    mealIntakeRecordForm.unit_id = newMeal.meal_unit.id
    if (mealIntakeRecordForm.amount === null) mealIntakeRecordForm.amount = 1;
  }
}


function close() {
  mealIntakeRecordForm.reset()
  mealIntakeRecordForm.clearErrors()
  isOpen.value = false
  clientSideErrors.value = {}
}

function save() {
  if (passesValidation()) {
    mealIntakeRecordForm.date_time_utc = localTimestampToUtcTimestamp(mealIntakeRecordForm.date + " " + mealIntakeRecordForm.time)

    if (mealIntakeRecordForm.id) {
      mealIntakeRecordForm.put(route('food-intake-records.update-meal', mealIntakeRecordForm.id), {
        onSuccess: () => close()
      })
    } else {
      mealIntakeRecordForm.post(route('food-intake-records.store-meal'), {
        onSuccess: () => close()
      })
    }
  }
}

function addMore() {
  if (passesValidation()) {

    // Explicit reset of form data and errors
    mealIntakeRecordsForm.reset()
    mealIntakeRecordsForm.clearErrors()

    // Transfer record to first record of mealIntakeRecordsForm
    mealIntakeRecordsForm.mealIntakeRecords[0] = {
      id: mealIntakeRecordsForm.nextId,
      meal_intake_record: {
        id: mealIntakeRecordForm.id,
        meal_id: mealIntakeRecordForm.meal_id,
        meal: cloneDeep(mealIntakeRecordForm.meal),
        amount: mealIntakeRecordForm.amount,
        unit_id: mealIntakeRecordForm.unit_id,
        unit: cloneDeep(mealIntakeRecordForm.unit),
        date: mealIntakeRecordForm.date,
        time: mealIntakeRecordForm.time,
        date_time_utc: localTimestampToUtcTimestamp(mealIntakeRecordForm.date + " " + mealIntakeRecordForm.time),
        description: mealIntakeRecordForm.description,
      },
    }
    mealIntakeRecordsForm.nextId += 1

    // Clear `mealIntakeRecordForm` data and errors; close dialog
    close()

    logMealIntakeRecordsDialogRef.value.open()
  }
}

</script>

<template>
  <div>
    <Dialog
      :open="isOpen"
      @close="close"
      class="relative z-50"
    >
      <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">

        <DialogPanel class="px-6 pt-6 w-full rounded-lg bg-white shadow overflow-auto max-w-md" :class="showDescription ? 'max-h-[700px] ' : 'max-h-[600px]'">

          <div class="flex items-center">
            <DialogTitle class="text-lg font-bold text-gray-600">
              {{mealIntakeRecordForm.id === null ? 'Log' : 'Update'}} meal intake {{mealIntakeRecordForm.id === null ? '' : 'record'}}
            </DialogTitle>

            <SecondaryButton v-if="mealIntakeRecordForm.id === null" @click="mealCreateAndLogChooserDialogRef.open()" class="flex ml-auto !py-1">
              <PlusCircleIcon class="-ml-1 w-6 h-6 text-gray-600 shrink-0" />
              <p class="ml-1">New meal</p>
            </SecondaryButton>
            <SecondaryLinkButton v-else :href="route('meals.edit-logged', mealIntakeRecordForm.meal_id)" class="flex ml-auto !py-1">
              <PencilSquareIcon class="-ml-1 w-6 h-6 text-gray-600 shrink-0" />
              <p class="ml-1">Update meal</p>
            </SecondaryLinkButton>

          </div>

          <div class="mt-2">
            <div ref="mealInputWrapperRef">
              <FuzzyCombobox
                labelText="Meal"
                :targets="meals"
                :modelValue="mealIntakeRecordForm.meal"
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
                v-model="mealIntakeRecordForm.amount"
                required
              />
              <InputError :message="mealIntakeRecordForm.errors.amount" />
              <InputError :message="clientSideErrors.amount" />
            </div>

            <!-- Unit -->
            <div class="ml-4 w-40">
              <div ref="unitInputWrapperRef">
                <SimpleCombobox
                  :options="mealIntakeRecordForm.meal_id ? Array(mealIntakeRecordForm.meal.meal_unit).concat(units.filter(unit => unit.g)) : []"
                  labelText="Unit"
                  inputClasses="w-40"
                  @keyup.enter="handleUnitInputEnter"
                  :modelValue="mealIntakeRecordForm.unit"
                  @update:modelValue="newValue => {
                    mealIntakeRecordForm.unit = newValue
                    mealIntakeRecordForm.unit_id = newValue.id
                  }"
                />
              </div>
              <InputError :message="mealIntakeRecordForm.errors.unit_id" />
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
                v-model="mealIntakeRecordForm.date"
                required
              />
              <InputError :message="mealIntakeRecordForm.errors.date" />
            </div>
            <SecondaryButton @click="mealIntakeRecordForm.date = currentLocalDate()" class="ml-2 h-fit">
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
                v-model="mealIntakeRecordForm.time"
              />
              <InputError :message="mealIntakeRecordForm.errors.time" />
            </div>
            <SecondaryButton @click="mealIntakeRecordForm.time = currentLocalTime()" class="ml-2 h-fit">
              <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
              <p class="ml-1">Now</p>
            </SecondaryButton>
          </div>

          <div class="mt-2">
            <InputError :message="mealIntakeRecordForm.errors.date_time_utc" />
          </div>

          <!-- Description -->
          <div class="mt-4 w-full">
            <InputLabel for="description" value="Description (optional)" />
            <PlainButton @click="toggleDescription" class="mt-0.5 flex items-center text-sm">
              <PencilSquareIcon v-if="!showDescription" class="-ml-1 w-5 h-5 text-gray-500" />
              <XMarkIcon v-else class="-ml-1 w-5 h-5 text-gray-600" />
              <p class="ml-1.5 whitespace-nowrap">
                {{showDescription ? "Hide description" : (mealIntakeRecordForm.description ? "Edit" : "Add") + " description" + (mealIntakeRecordForm.description ? "" : " (optional)")}}
              </p>
            </PlainButton>
            <TextArea
              v-show="showDescription" 
              id="description"
              ref="descriptionInputRef"
              class="mt-1 block w-full h-32 sm:h-36 max-w-xl"
              v-model="mealIntakeRecordForm.description"
            />
            <InputError class="mt-2" :message="mealIntakeRecordForm.errors.description" />
          </div>

          <!-- Cancel/Confirm buttons -->
          <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">

            <PlainButton
              v-if="mealIntakeRecordForm.id === null"
              @click="addMore"
              class="text-sm font-medium"
              :class="{ 'opacity-25': !allInputsValid }"
            >
              Save and add more
            </PlainButton>

            <SecondaryButton
              @click="close"
              class="ml-auto"
            >
              Cancel
            </SecondaryButton>

            <PrimaryButton
              @click="save"
              :class="{ 'opacity-25': mealIntakeRecordForm.processing }"
              :disabled="mealIntakeRecordForm.processing"
              class="ml-2"
            >
              {{mealIntakeRecordForm.id === null ? 'Save' : 'Update'}}
            </PrimaryButton>

          </div>

          <MealCreateAndLogChooserDialog ref="mealCreateAndLogChooserDialogRef" :meals="meals" />

        </DialogPanel>
      </div>
    </Dialog>

    <LogMealIntakeRecordsDialog 
      :meals="meals"
      :units="units" ref="logMealIntakeRecordsDialogRef" 
    />

  </div>
</template>
