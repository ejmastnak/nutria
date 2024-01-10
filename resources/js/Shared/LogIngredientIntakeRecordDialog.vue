<script setup>
import { ref, computed } from 'vue'
import cloneDeep from "lodash/cloneDeep"
import { getCurrentLocalYYYYMMDD, getCurrentLocalHHmm, getLocalYYYYMMDD, getLocalHHMM, getUTCDateTime } from '@/utils/GlobalFunctions.js'
import { ClockIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import LogIngredientIntakeRecordsDialog from '@/Shared/LogIngredientIntakeRecordsDialog.vue'

import { ingredientIntakeRecordForm } from '@/Shared/store.js'
import { ingredientIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  ingredients: Array,
  units: Array,
})

defineExpose({ open })

const isOpen = ref(false)

const errors = ref({})
const clientSideErrors = ref({})

function open(record) {
  ingredientIntakeRecordForm.id = record ? record.id : null
  ingredientIntakeRecordForm.ingredient_id = record ? record.ingredient_id : null
  ingredientIntakeRecordForm.ingredient = record ? cloneDeep(record.ingredient) : {}
  ingredientIntakeRecordForm.amount = record ? record.amount : null
  ingredientIntakeRecordForm.unit_id = record ? record.unit_id : props.units.find(unit => unit.name === 'g').id
  ingredientIntakeRecordForm.unit = record ? cloneDeep(record.unit) : props.units.find(unit => unit.name === 'g')
  ingredientIntakeRecordForm.date = record ? getLocalYYYYMMDD(record.date_time_utc) : getCurrentLocalYYYYMMDD()
  ingredientIntakeRecordForm.time = record ? getLocalHHMM(record.date_time_utc) : getCurrentLocalHHmm()
  isOpen.value = true
}

const ingredientInputWrapperRef = ref(null)
const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)
const logIngredientIntakeRecordsDialogRef = ref(null)

function passesValidation() {

  // Check that ingredient is set
  if (ingredientIntakeRecordForm.ingredient_id === null) {
    ingredientInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that amount is not empty
  if (ingredientIntakeRecordForm.amount === null || ingredientIntakeRecordForm.amount.length === 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(ingredientIntakeRecordForm.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (ingredientIntakeRecordForm.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (ingredientIntakeRecordForm.date === null || ingredientIntakeRecordForm.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (ingredientIntakeRecordForm.time === null || ingredientIntakeRecordForm.time.length === 0) {
    timeInputRef.value.focus()
    return false
  }

  return true
}

// Used to enable/disable "Add More" button; same checks as passesValidation,
// but without interacting with UI or populating client-side errors.
const allInputsValid = computed(() => {

  // Check that ingredient is set
  if (ingredientIntakeRecordForm.ingredient_id === null) return false;

  // Check that amount is not empty
  if (ingredientIntakeRecordForm.amount === null || ingredientIntakeRecordForm.amount.length === 0) return false;

  // Check that amount is gt:0
  const amount = Number(ingredientIntakeRecordForm.amount)
  if (isNaN(amount) || amount <= 0) return false;

  // Check that unit is set
  if (ingredientIntakeRecordForm.unit_id === null) return false;

  // Check that date is not empty
  if (ingredientIntakeRecordForm.date === null || ingredientIntakeRecordForm.date.length === 0) return false;

  // Check that time is not empty
  if (ingredientIntakeRecordForm.time === null || ingredientIntakeRecordForm.time.length === 0) return false;

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

function close() {
  ingredientIntakeRecordForm.reset()
  ingredientIntakeRecordForm.clearErrors()
  isOpen.value = false
  clientSideErrors.value = {}
}

function save() {
  if (passesValidation()) {
    ingredientIntakeRecordForm.date_time_utc = getUTCDateTime(ingredientIntakeRecordForm.date + " " + ingredientIntakeRecordForm.time + ":00")

    if (ingredientIntakeRecordForm.id) {
      ingredientIntakeRecordForm.put(route('ingredient-intake-records.update', ingredientIntakeRecordForm.id), {
        onSuccess: () => close()
      })
    } else {
      ingredientIntakeRecordForm.post(route('ingredient-intake-records.store'), {
        onSuccess: () => close()
      })
    }
  }
}

function addMore() {
  if (passesValidation()) {

    // Explicit reset of form data and errors
    ingredientIntakeRecordsForm.reset()
    ingredientIntakeRecordsForm.clearErrors()

    // Transfer record to first record of ingredientIntakeRecordsForm
    ingredientIntakeRecordsForm.ingredientIntakeRecords[0] = {
      id: ingredientIntakeRecordsForm.nextId,
      ingredient_intake_record: {
        id: ingredientIntakeRecordForm.id,
        ingredient_id: ingredientIntakeRecordForm.ingredient_id,
        ingredient: cloneDeep(ingredientIntakeRecordForm.ingredient),
        amount: ingredientIntakeRecordForm.amount,
        unit_id: ingredientIntakeRecordForm.unit_id,
        unit: cloneDeep(ingredientIntakeRecordForm.unit),
        date: ingredientIntakeRecordForm.date,
        time: ingredientIntakeRecordForm.time,
        date_time_utc: getUTCDateTime(ingredientIntakeRecordForm.date + " " + ingredientIntakeRecordForm.time + ":00"),
      },
    }
    ingredientIntakeRecordsForm.nextId += 1

    // Clear `ingredientIntakeRecordForm` data and errors; close dialog
    close()

    logIngredientIntakeRecordsDialogRef.value.open()
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

        <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow max-h-[600px] overflow-auto">

          <DialogTitle class="text-lg font-bold text-gray-600">
            {{ingredientIntakeRecordForm.id === null ? 'Log' : 'Update'}} ingredient intake {{ingredientIntakeRecordForm.id === null ? '' : 'record'}}
          </DialogTitle>

          <div class="mt-2">
            <div ref="ingredientInputWrapperRef">
              <FuzzyCombobox
                labelText="Ingredient"
                :options="ingredients"
                :modelValue="ingredientIntakeRecordForm.ingredient"
                :showIcon="false"
                @update:modelValue="newValue => {
                  ingredientIntakeRecordForm.ingredient = newValue
                  ingredientIntakeRecordForm.ingredient_id = newValue.id
                }"
              />
            </div>
            <InputError :message="errors.ingredient_id" />
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
                v-model="ingredientIntakeRecordForm.amount"
                required
              />
              <InputError :message="ingredientIntakeRecordForm.errors.amount" />
              <InputError :message="clientSideErrors.amount" />
            </div>

            <!-- Unit -->
            <div class="ml-4 w-40">
              <div ref="unitInputWrapperRef">
                <SimpleCombobox
                  :options="units.filter(unit => unit.g || (unit.ml && (ingredientIntakeRecordForm.ingredient && ingredientIntakeRecordForm.ingredient.density_g_ml))).concat((ingredientIntakeRecordForm.ingredient && ingredientIntakeRecordForm.ingredient.custom_units) ? ingredientIntakeRecordForm.ingredient.custom_units : [])"
                  labelText="Unit"
                  inputClasses="w-40"
                  @keyup.enter="handleUnitInputEnter"
                  :modelValue="ingredientIntakeRecordForm.unit"
                  @update:modelValue="newValue => {
                    ingredientIntakeRecordForm.unit = newValue
                    ingredientIntakeRecordForm.unit_id = newValue.id
                  }"
                />
              </div>
              <InputError :message="ingredientIntakeRecordForm.errors.unit_id" />
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
                v-model="ingredientIntakeRecordForm.date"
                required
              />
              <InputError :message="ingredientIntakeRecordForm.errors.date" />
            </div>
            <SecondaryButton @click="ingredientIntakeRecordForm.date = getCurrentLocalYYYYMMDD()" class="ml-2 h-fit">
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
                v-model="ingredientIntakeRecordForm.time"
              />
              <InputError :message="ingredientIntakeRecordForm.errors.time" />
            </div>
            <SecondaryButton @click="ingredientIntakeRecordForm.time = getCurrentLocalHHmm()" class="ml-2 h-fit">
              <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
              <p class="ml-1">Now</p>
            </SecondaryButton>
          </div>

          <div class="mt-2">
            <InputError :message="ingredientIntakeRecordForm.errors.date_time_utc" />
          </div>

          <!-- Cancel/Confirm buttons -->
          <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">

            <PlainButton
              v-if="ingredientIntakeRecordForm.id === null"
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
              :class="{ 'opacity-25': ingredientIntakeRecordForm.processing }"
              :disabled="ingredientIntakeRecordForm.processing"
              class="ml-2"
            >
              {{ingredientIntakeRecordForm.id === null ? 'Save' : 'Update'}}
            </PrimaryButton>

          </div>

        </DialogPanel>
      </div>
    </Dialog>

    <LogIngredientIntakeRecordsDialog 
      :ingredients="ingredients"
      :units="units" ref="logIngredientIntakeRecordsDialogRef" 
    />

  </div>
</template>
