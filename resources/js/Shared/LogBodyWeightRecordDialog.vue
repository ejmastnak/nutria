<script setup>
import { ref, computed } from 'vue'
import cloneDeep from "lodash/cloneDeep"
import { getCurrentLocalYYYYMMDD, getCurrentLocalHHmm, getLocalYYYYMMDD, getLocalHHMM, getUTCDateTime } from '@/utils/GlobalFunctions.js'
import { ClockIcon, CalendarIcon, PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import LogBodyWeightRecordsDialog from '@/Shared/LogBodyWeightRecordsDialog.vue'

import { bodyWeightRecordForm } from '@/Shared/store.js'
import { bodyWeightRecordsForm } from '@/Shared/store.js'

const props = defineProps({
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

function open(record) {
  bodyWeightRecordForm.id = record ? record.id : null
  bodyWeightRecordForm.amount = record ? record.amount : null
  bodyWeightRecordForm.unit_id = record ? record.unit_id : props.units.find(unit => unit.name === 'kg').id
  bodyWeightRecordForm.unit = record ? cloneDeep(record.unit) : props.units.find(unit => unit.name === 'kg')
  bodyWeightRecordForm.date = record ? getLocalYYYYMMDD(record.date_time_utc) : getCurrentLocalYYYYMMDD()
  bodyWeightRecordForm.time = record ? getLocalHHMM(record.date_time_utc) : getCurrentLocalHHmm()
  bodyWeightRecordForm.description = record ? record.description : null

  showDescription.value = record ? (!!record.description) : false
  isOpen.value = true
}

const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)
const logBodyWeightRecordsDialogRef = ref(null)

function passesValidation() {

  // Check that amount is not empty
  if (bodyWeightRecordForm.amount === null || bodyWeightRecordForm.amount.length === 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(bodyWeightRecordForm.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (bodyWeightRecordForm.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (bodyWeightRecordForm.date === null || bodyWeightRecordForm.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (bodyWeightRecordForm.time === null || bodyWeightRecordForm.time.length === 0) {
    timeInputRef.value.focus()
    return false
  }

  return true
}

// Used to enable/disable "Add More" button; same checks as passesValidation,
// but without interacting with UI or populating client-side errors.
const allInputsValid = computed(() => {

  // Check that amount is not empty
  if (bodyWeightRecordForm.amount === null || bodyWeightRecordForm.amount.length === 0) return false;

  // Check that amount is gt:0
  const amount = Number(bodyWeightRecordForm.amount)
  if (isNaN(amount) || amount <= 0) return false;

  // Check that unit is set
  if (bodyWeightRecordForm.unit_id === null) return false;

  // Check that date is not empty
  if (bodyWeightRecordForm.date === null || bodyWeightRecordForm.date.length === 0) return false;

  // Check that time is not empty
  if (bodyWeightRecordForm.time === null || bodyWeightRecordForm.time.length === 0) return false;

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
  bodyWeightRecordForm.reset()
  bodyWeightRecordForm.clearErrors()
  isOpen.value = false
  clientSideErrors.value = {}
}

function save() {
  if (passesValidation()) {
    bodyWeightRecordForm.date_time_utc = getUTCDateTime(bodyWeightRecordForm.date + " " + bodyWeightRecordForm.time + ":00")

    if (bodyWeightRecordForm.id) {
      bodyWeightRecordForm.put(route('body-weight-records.update', bodyWeightRecordForm.id), {
        onSuccess: () => close()
      })
    } else {
      bodyWeightRecordForm.post(route('body-weight-records.store'), {
        onSuccess: () => close()
      })
    }
  }
}

function addMore() {
  if (passesValidation()) {

    // Explicit reset of form data and errors
    bodyWeightRecordsForm.reset()
    bodyWeightRecordsForm.clearErrors()

    // Transfer record to first record of bodyWeightRecordsForm
    bodyWeightRecordsForm.bodyWeightRecords[0] = {
      id: bodyWeightRecordsForm.nextId,
      body_weight_record: {
        id: bodyWeightRecordForm.id,
        amount: bodyWeightRecordForm.amount,
        unit_id: bodyWeightRecordForm.unit_id,
        unit: cloneDeep(bodyWeightRecordForm.unit),
        date: bodyWeightRecordForm.date,
        time: bodyWeightRecordForm.time,
        date_time_utc: getUTCDateTime(bodyWeightRecordForm.date + " " + bodyWeightRecordForm.time + ":00"),
        description: bodyWeightRecordForm.description,
      },
    }
    bodyWeightRecordsForm.nextId += 1

    // Clear `bodyWeightRecordForm` data and errors; close dialog
    close()

    logBodyWeightRecordsDialogRef.value.open()
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
            {{bodyWeightRecordForm.id === null ? 'Log' : 'Update'}} body weight {{bodyWeightRecordForm.id === null ? '' : 'record'}}
          </DialogTitle>

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
                v-model="bodyWeightRecordForm.amount"
                required
              />
              <InputError :message="bodyWeightRecordForm.errors.amount" />
              <InputError :message="clientSideErrors.amount" />
            </div>

            <!-- Unit -->
            <div class="ml-4 w-40">
              <div ref="unitInputWrapperRef">
                <SimpleCombobox
                  :options="units.filter(unit => unit.name === 'kg' || unit.name === 'lb')"
                  labelText="Unit"
                  inputClasses="w-40"
                  @keyup.enter="handleUnitInputEnter"
                  :modelValue="bodyWeightRecordForm.unit"
                  @update:modelValue="newValue => {
                    bodyWeightRecordForm.unit = newValue
                    bodyWeightRecordForm.unit_id = newValue.id
                  }"
                />
              </div>
              <InputError :message="bodyWeightRecordForm.errors.unit_id" />
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
                v-model="bodyWeightRecordForm.date"
                required
              />
              <InputError :message="bodyWeightRecordForm.errors.date" />
            </div>
            <SecondaryButton @click="bodyWeightRecordForm.date = getCurrentLocalYYYYMMDD()" class="ml-2 h-fit">
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
                v-model="bodyWeightRecordForm.time"
              />
              <InputError :message="bodyWeightRecordForm.errors.time" />
            </div>
            <SecondaryButton @click="bodyWeightRecordForm.time = getCurrentLocalHHmm()" class="ml-2 h-fit">
              <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
              <p class="ml-1">Now</p>
            </SecondaryButton>
          </div>

          <div class="mt-2">
            <InputError :message="bodyWeightRecordForm.errors.date_time_utc" />
          </div>

          <!-- Description -->
          <PlainButton @click="toggleDescription" class="mt-3 flex items-center text-sm">
            <PencilSquareIcon v-if="!showDescription" class="-ml-1 w-5 h-5 text-gray-500" />
            <XMarkIcon v-else class="-ml-1 w-5 h-5 text-gray-600" />
            <p class="ml-1.5 whitespace-nowrap">
              {{showDescription ? "Hide description" : (bodyWeightRecordForm.description ? "Edit" : "Add") + " description" + (bodyWeightRecordForm.description ? "" : " (optional)")}}
            </p>
          </PlainButton>
          <div v-show="showDescription" class="mt-2 w-full">
            <InputLabel for="description" value="Description (optional)" />
            <TextArea
              id="description"
              ref="descriptionInputRef"
              class="block w-full h-32 sm:h-36 max-w-xl"
              v-model="bodyWeightRecordForm.description"
            />
            <InputError class="mt-2" :message="bodyWeightRecordForm.errors.description" />
          </div>

          <!-- Cancel/Confirm buttons -->
          <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">

            <PlainButton
              v-if="bodyWeightRecordForm.id === null"
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
              :class="{ 'opacity-25': bodyWeightRecordForm.processing }"
              :disabled="bodyWeightRecordForm.processing"
              class="ml-2"
            >
              {{bodyWeightRecordForm.id === null ? 'Save' : 'Update'}}
            </PrimaryButton>

          </div>

        </DialogPanel>
      </div>
    </Dialog>

    <LogBodyWeightRecordsDialog :units="units" ref="logBodyWeightRecordsDialogRef" />

  </div>
</template>
