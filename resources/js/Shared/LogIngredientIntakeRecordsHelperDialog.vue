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
  ingredients: Array,
  units: Array,
})

const emit = defineEmits(['cancel', 'confirm'])

const ingredientIntakeRecord = ref({
  ingredient_id: null,
  ingredient: null,
  meal_id: null,
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

function open(passedIngredientIntakeRecord, passedErrors) {
  ingredientIntakeRecord.value.ingredient_id = passedIngredientIntakeRecord ? passedIngredientIntakeRecord.ingredient_id : null
  ingredientIntakeRecord.value.ingredient = passedIngredientIntakeRecord ? cloneDeep(passedIngredientIntakeRecord.ingredient) : {}
  ingredientIntakeRecord.value.amount = passedIngredientIntakeRecord ? passedIngredientIntakeRecord.amount : null
  ingredientIntakeRecord.value.unit_id = passedIngredientIntakeRecord ? passedIngredientIntakeRecord.unit_id : props.units.find(unit => unit.name === 'g').id
  ingredientIntakeRecord.value.unit = passedIngredientIntakeRecord ? cloneDeep(passedIngredientIntakeRecord.unit) : props.units.find(unit => unit.name === 'g')
  ingredientIntakeRecord.value.date = passedIngredientIntakeRecord ? utcTimestampToLocalDate(passedIngredientIntakeRecord.date_time_utc) : currentLocalDate()
  ingredientIntakeRecord.value.time = passedIngredientIntakeRecord ? utcTimestampToLocalTime(passedIngredientIntakeRecord.date_time_utc) : currentLocalTime()
  ingredientIntakeRecord.value.description = passedIngredientIntakeRecord ? passedIngredientIntakeRecord.description : null

  showDescription.value = passedIngredientIntakeRecord ? (!!passedIngredientIntakeRecord.description) : false
  errors.value = passedErrors
  isOpen.value = true
}

const ingredientInputWrapperRef = ref(null)
const amountInputRef = ref(null)
const unitInputWrapperRef = ref(null)
const dateInputRef = ref(null)
const timeInputRef = ref(null)

function passesValidation() {

  // Check that ingredient is set
  if (ingredientIntakeRecord.value.ingredient_id === null) {
    ingredientInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that amount is not empty
  if (ingredientIntakeRecord.value.amount === null || ingredientIntakeRecord.value.amount.length === 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that amount is gt:0
  const amount = Number(ingredientIntakeRecord.value.amount)
  if (isNaN(amount) || amount <= 0) {
    clientSideErrors.value['amount'] = "The amount must be a number greater than 0."
    amountInputRef.value.focus()
    return false
  }

  // Check that unit is set
  if (ingredientIntakeRecord.value.unit_id === null) {
    unitInputWrapperRef.value.querySelectorAll('input')[0].focus()
    return false
  }

  // Check that date is not empty
  if (ingredientIntakeRecord.value.date === null || ingredientIntakeRecord.value.date.length === 0) {
    dateInputRef.value.focus()
    return false
  }

  // Check that time is not empty
  if (ingredientIntakeRecord.value.time === null || ingredientIntakeRecord.value.time.length === 0) {
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

function updateIngredient(newIngredient) {
  ingredientIntakeRecord.value.ingredient = newIngredient
  ingredientIntakeRecord.value.ingredient_id = newIngredient.id

  // Reset ingredient's unit if old unit is not supported by new ingredient.
  const newUnits = props.units.filter(unit => unit.g || (unit.ml && newIngredient.density_g_ml)).concat(newIngredient.custom_units ? newIngredient.custom_units : [])
  if (!newUnits.map(unit => unit.id).includes(ingredientIntakeRecord.value.unit_id)) {
    ingredientIntakeRecord.value.unit_id = props.units.find(unit => unit.name === 'g').id
    ingredientIntakeRecord.value.unit = props.units.find(unit => unit.name === 'g')
    ingredientIntakeRecord.value.amount = null
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
  ingredientIntakeRecord.value.date_time_utc = localTimestampToUtcTimestamp(ingredientIntakeRecord.value.date + " " + ingredientIntakeRecord.value.time)
  emit('confirm', ingredientIntakeRecord.value)
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
          Log ingredient intake
        </DialogTitle>

        <div class="mt-2">
          <div ref="ingredientInputWrapperRef">
            <FuzzyCombobox
              labelText="Ingredient"
              :options="ingredients"
              :modelValue="ingredientIntakeRecord.ingredient"
              :showIcon="false"
              @update:modelValue="newValue => updateIngredient(newValue)"
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
              v-model="ingredientIntakeRecord.amount"
              required
            />
            <InputError :message="errors.amount" />
            <InputError :message="clientSideErrors.amount" />
          </div>

          <!-- Unit -->
          <div class="ml-4 w-40">
            <div ref="unitInputWrapperRef">
              <SimpleCombobox
                :options="units.filter(unit => unit.g || (unit.ml && (ingredientIntakeRecord.ingredient && ingredientIntakeRecord.ingredient.density_g_ml))).concat((ingredientIntakeRecord.ingredient && ingredientIntakeRecord.ingredient.custom_units) ? ingredientIntakeRecord.ingredient.custom_units : [])"
                labelText="Unit"
                inputClasses="w-40"
                @keyup.enter="handleUnitInputEnter"
                :modelValue="ingredientIntakeRecord.unit"
                @update:modelValue="newValue => {
                  ingredientIntakeRecord.unit = newValue
                  ingredientIntakeRecord.unit_id = newValue.id
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
              v-model="ingredientIntakeRecord.date"
              required
            />
            <InputError :message="errors.date" />
          </div>
          <SecondaryButton @click="ingredientIntakeRecord.date = currentLocalDate()" class="ml-2 h-fit">
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
              v-model="ingredientIntakeRecord.time"
            />
            <InputError :message="errors.time" />
          </div>
          <SecondaryButton @click="ingredientIntakeRecord.time = currentLocalTime()" class="ml-2 h-fit">
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
              {{showDescription ? "Hide description" : (ingredientIntakeRecord.description ? "Edit" : "Add") + " description" + (ingredientIntakeRecord.description ? "" : " (optional)")}}
            </p>
          </PlainButton>
          <TextArea
            v-show="showDescription" 
            id="description"
            ref="descriptionInputRef"
            class="mt-1 block w-full h-32 sm:h-36 max-w-xl"
            v-model="ingredientIntakeRecord.description"
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
