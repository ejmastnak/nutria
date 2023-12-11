<script setup>
import { ref, computed } from 'vue'
import cloneDeep from "lodash/cloneDeep"
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
  food_lists: Array,
  units: Array,
})

const emit = defineEmits(['cancel', 'confirm'])

const foodListIntakeRecord = ref({
  food_list_id: null,
  food_list: null,
  amount: null,
  unit_id: null,
  unit: null,
  date: null,
  time: null,
  date_time_utc: null,
})

defineExpose({ open })

const isOpen = ref(false)

const errors = ref({})
const clientSideErrors = ref({})

function open(passedFoodListIntakeRecord, passedErrors) {
  foodListIntakeRecord.value.food_list_id = passedFoodListIntakeRecord ? passedFoodListIntakeRecord.food_list_id : null
  foodListIntakeRecord.value.food_list = passedFoodListIntakeRecord ? cloneDeep(passedFoodListIntakeRecord.food_list) : {}
  foodListIntakeRecord.value.amount = passedFoodListIntakeRecord ? passedFoodListIntakeRecord.amount : null
  foodListIntakeRecord.value.unit_id = passedFoodListIntakeRecord ? passedFoodListIntakeRecord.unit_id : props.units.find(unit => unit.name === 'g').id
  foodListIntakeRecord.value.unit = passedFoodListIntakeRecord ? cloneDeep(passedFoodListIntakeRecord.unit) : props.units.find(unit => unit.name === 'g')
  foodListIntakeRecord.value.date = passedFoodListIntakeRecord ? getLocalYYYYMMDD(passedFoodListIntakeRecord.date_time_utc) : getCurrentLocalYYYYMMDD()
  foodListIntakeRecord.value.time = passedFoodListIntakeRecord ? getLocalHHMM(passedFoodListIntakeRecord.date_time_utc) : getCurrentLocalHHmm()
  errors.value = passedErrors
  isOpen.value = true
}

const foodListInputWrapperRef = ref(null)
const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)

function passesValidation() {

  // Check that food_list is set
  if (foodListIntakeRecord.value.food_list_id === null) {
    foodListInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that amount is not empty
  if (foodListIntakeRecord.value.amount === null || foodListIntakeRecord.value.amount.length === 0) {
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(foodListIntakeRecord.value.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (foodListIntakeRecord.value.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (foodListIntakeRecord.value.date === null || foodListIntakeRecord.value.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (foodListIntakeRecord.value.time === null || foodListIntakeRecord.value.time.length === 0) {
    timeInputRef.value.focus()
    return false
  }

  return true
}

function handleFoodListInputEnter() {
  checkAndConfirm()
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
  foodListIntakeRecord.value.date_time_utc = getUTCDateTime(foodListIntakeRecord.value.date + " " + foodListIntakeRecord.value.time + ":00")
  emit('confirm', foodListIntakeRecord.value)
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
          Log Food List
        </DialogTitle>

        <div class="mt-2">
          <div ref="foodListInputWrapperRef">
            <FuzzyCombobox
              labelText="Food list"
              :options="food_lists"
              :modelValue="foodListIntakeRecord.food_list"
              :showIcon="false"
              @update:modelValue="newValue => {
                foodListIntakeRecord.food_list = newValue
                foodListIntakeRecord.food_list_id = newValue.id
                foodListIntakeRecord.amount = 1
                foodListIntakeRecord.unit = newValue.food_list_unit
                foodListIntakeRecord.unit_id = newValue.food_list_unit.id
              }"
            />
          </div>
          <InputError :message="errors.food_list_id" />
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
              v-model="foodListIntakeRecord.amount"
              required
            />
            <InputError :message="errors.amount" />
            <InputError :message="clientSideErrors.amount" />
          </div>

          <!-- Unit -->
          <div class="ml-4 w-40">
            <div ref="unitInputWrapperRef">
              <SimpleCombobox
                :options="foodListIntakeRecord.food_list.food_list_unit ? Array(foodListIntakeRecord.food_list.food_list_unit) : []"
                labelText="Unit"
                inputClasses="w-40"
                @keyup.enter="handleUnitInputEnter"
                :modelValue="foodListIntakeRecord.unit"
                @update:modelValue="newValue => {
                  foodListIntakeRecord.unit = newValue
                  foodListIntakeRecord.unit_id = newValue.id
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
              v-model="foodListIntakeRecord.date"
              required
            />
            <InputError :message="errors.date" />
          </div>
          <SecondaryButton @click="foodListIntakeRecord.date = getCurrentLocalYYYYMMDD()" class="ml-2 h-fit">
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
              v-model="foodListIntakeRecord.time"
            />
            <InputError :message="errors.time" />
          </div>
          <SecondaryButton @click="foodListIntakeRecord.time = getCurrentLocalHHmm()" class="ml-2 h-fit">
            <ClockIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
            <p class="ml-1">Now</p>
          </SecondaryButton>
        </div>

        <div class="mt-2">
          <InputError :message="errors.date_time_utc" />
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
