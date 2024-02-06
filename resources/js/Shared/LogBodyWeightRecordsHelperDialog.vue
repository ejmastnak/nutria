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
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  units: Array,
})

const emit = defineEmits(['cancel', 'confirm'])

const bodyWeightRecord = ref({
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

function open(passedBodyWeightRecord, passedErrors) {
  bodyWeightRecord.value.amount = passedBodyWeightRecord ? passedBodyWeightRecord.amount : null
  bodyWeightRecord.value.unit_id = passedBodyWeightRecord ? passedBodyWeightRecord.unit_id : props.units.find(unit => unit.name === 'kg').id
  bodyWeightRecord.value.unit = passedBodyWeightRecord ? cloneDeep(passedBodyWeightRecord.unit) : props.units.find(unit => unit.name === 'kg')
  bodyWeightRecord.value.date = passedBodyWeightRecord ? getLocalYYYYMMDD(passedBodyWeightRecord.date_time_utc) : getCurrentLocalYYYYMMDD()
  bodyWeightRecord.value.time = passedBodyWeightRecord ? getLocalHHMM(passedBodyWeightRecord.date_time_utc) : getCurrentLocalHHmm()
  bodyWeightRecord.value.description = passedBodyWeightRecord ? passedBodyWeightRecord.description : null

  showDescription.value = passedBodyWeightRecord ? (!!passedBodyWeightRecord.description) : false
  errors.value = passedErrors
  isOpen.value = true
}

const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)

function passesValidation() {

  // Check that amount is not empty
  if (bodyWeightRecord.value.amount === null || bodyWeightRecord.value.amount.length === 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(bodyWeightRecord.value.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (bodyWeightRecord.value.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (bodyWeightRecord.value.date === null || bodyWeightRecord.value.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (bodyWeightRecord.value.time === null || bodyWeightRecord.value.time.length === 0) {
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

function cancel() {
  emit('cancel')
  isOpen.value = false
  clientSideErrors.value = {}
}

function checkAndConfirm() {
  if (passesValidation()) confirm();
}

function confirm() {
  bodyWeightRecord.value.date_time_utc = getUTCDateTime(bodyWeightRecord.value.date + " " + bodyWeightRecord.value.time + ":00")
  emit('confirm', bodyWeightRecord.value)
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

      <DialogPanel class="px-6 pt-6 w-full max-w-md rounded-lg bg-white shadow max-h-[600px] overflow-auto">

        <DialogTitle class="text-lg font-bold text-gray-600">
          Log body weight
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
              v-model="bodyWeightRecord.amount"
              required
            />
            <InputError :message="errors.amount" />
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
                :modelValue="bodyWeightRecord.unit"
                @update:modelValue="newValue => {
                  bodyWeightRecord.unit = newValue
                  bodyWeightRecord.unit_id = newValue.id
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
              v-model="bodyWeightRecord.date"
              required
            />
            <InputError :message="errors.date" />
          </div>
          <SecondaryButton @click="bodyWeightRecord.date = getCurrentLocalYYYYMMDD()" class="ml-2 h-fit">
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
              v-model="bodyWeightRecord.time"
            />
            <InputError :message="errors.time" />
          </div>
          <SecondaryButton @click="bodyWeightRecord.time = getCurrentLocalHHmm()" class="ml-2 h-fit">
            <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
            <p class="ml-1">Now</p>
          </SecondaryButton>
        </div>

        <div class="mt-2">
          <InputError :message="errors.date_time_utc" />
        </div>

        <!-- Description -->
        <PlainButton @click="toggleDescription" class="mt-3 flex items-center text-sm">
          <PencilSquareIcon v-if="!showDescription" class="-ml-1 w-5 h-5 text-gray-500" />
          <XMarkIcon v-else class="-ml-1 w-5 h-5 text-gray-600" />
          <p class="ml-1.5 whitespace-nowrap">
            {{showDescription ? "Hide description" : (bodyWeightRecord.description ? "Edit" : "Add") + " description" + (bodyWeightRecord.description ? "" : " (optional)")}}
          </p>
        </PlainButton>
        <div v-show="showDescription" class="mt-2 w-full">
          <InputLabel for="description" value="Description (optional)" />
          <TextArea
            id="description"
            ref="descriptionInputRef"
            class="block w-full h-32 sm:h-36 max-w-xl"
            v-model="bodyWeightRecord.description"
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
