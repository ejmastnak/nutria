<script setup>
/*
  Used to edit an individual ingredient custom unit:
  - name
  - custom_unit_amount
  - custom_mass_amount
  - custom_mass_unit_id
*/
import { ref, computed } from 'vue'
import { round, roundNonZero } from '@/utils/GlobalFunctions.js'
import cloneDeep from "lodash/cloneDeep";
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  units: Array,
})

defineExpose({ open })
const emit = defineEmits(['confirm', 'cancel'])

const customUnit = ref({})
const errors = ref({})  // from backend validation
const clientSideErrors = ref({})  // from client-side validation
var isNew = false

const isOpen = ref(false)
const allOptionsShowing = ref(false)
function open(custom_unit, passedErrors, passedIsNew) {
  customUnit.value = cloneDeep(custom_unit)
  errors.value = passedErrors
  if (customUnit.value.custom_unit.name && customUnit.value.custom_unit.name.length > 0) allOptionsShowing.value = true;
  isNew = passedIsNew
  isOpen.value = true
}
const nameInputRef = ref(null)
const customMassAmountInputRef = ref(null)
const customUnitAmountInputRef = ref(null)

function passesValidation() {

  // Check that custom unit name is not empty
  if (customUnit.value.custom_unit.name === null || customUnit.value.custom_unit.name.length === 0) {
    clientSideErrors.value['name'] = "A name is required."
    nameInputRef.value.focus()
    return false
  }

  // Check that custom_unit_amount is not empty
  if (customUnit.value.custom_unit.custom_unit_amount === null || customUnit.value.custom_unit.custom_unit_amount.length === 0) {
    clientSideErrors.value['custom_unit_amount'] = "The amount must be a number greater than 0."
    customUnitAmountInputRef.value.focus()
    return false
  }

  // Check that custom_mass_amount is not empty
  if (customUnit.value.custom_unit.custom_mass_amount === null || customUnit.value.custom_unit.custom_mass_amount.length === 0) {
    clientSideErrors.value['custom_mass_amount'] = "The weight must be a number greater than 0."
    customMassAmountInputRef.value.focus()
    return false
  }

  // Check that custom_unit_amount is gt:0
  const unitAmount = Number(customUnit.value.custom_unit.custom_unit_amount)
  if (isNaN(unitAmount) || unitAmount <= 0) {
    clientSideErrors.value['custom_unit_amount'] = "The amount must be a number greater than 0."
    customUnitAmountInputRef.value.focus()
    return false
  }

  // Check that custom_mass_amount is gt:0
  const massAmount = Number(customUnit.value.custom_unit.custom_mass_amount)
  if (isNaN(massAmount) || massAmount <= 0) {
    clientSideErrors.value['custom_mass_amount'] = "The weight must be a number greater than 0."
    customMassAmountInputRef.value.focus()
    return false
  }

  return true
}

function confirm() {
  if (passesValidation()) {
    // We set the newly-created unit's gram value so that
    // GlobalFunctions.prepareUnitsForDisplay() will include the unit's mass.
    customUnit.value.custom_unit.custom_grams = customUnit.value.custom_unit.custom_mass_amount * customUnit.value.custom_unit.custom_mass_unit.g / customUnit.value.custom_unit.custom_unit_amount
    isOpen.value = false
    emit('confirm', customUnit.value)
    clientSideErrors.value = {}
  }
}

function cancel() {
  isOpen.value = false
  allOptionsShowing.value = false
  isNew = false
  emit('cancel')
  clientSideErrors.value = {}
}

function handleNameInputEnter() {
  if (!allOptionsShowing.value) {
    allOptionsShowing.value = true
    setTimeout(() => {
      customMassAmountInputRef.value.focus();
    }, 0);
  }
  else confirm();
}

function handleCustomAmountInputEnter() {
  if (customUnit.value.custom_unit.custom_mass_amount === null || customUnit.value.custom_unit.custom_mass_amount.length === 0) {
    customMassAmountInputRef.value.focus()
  } else confirm();
}

</script>

<template>
  <Dialog
    :initialFocus="nameInputRef"
    :open="isOpen"
    @close="cancel"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow">

        <div>
          <DialogTitle class="text-lg font-bold text-gray-600">
            Ingredient custom units
          </DialogTitle>
          <DialogDescription class="mt-1 text-sm">
            To add a custom unit: give the unit a name, then specify how much the unit weighs.
          </DialogDescription>
        </div>

        <!-- Custom unit name -->
        <div class="mt-2 w-full max-w-[22rem]">
          <InputLabel for="custom-unit-name" value="Name" />
          <TextInput
            id="custom-unit-name"
            ref="nameInputRef"
            type="text"
            class="w-full"
            @keyup.enter="handleNameInputEnter"
            v-model="customUnit.custom_unit.name"
            required
          />
          <InputError class="mt-1" :message="errors.name" />
          <InputError class="mt-1" :message="clientSideErrors.name" />
        </div>

        <div v-show="allOptionsShowing" class="mt-5">
          <!-- Custom unit amount -->
          <div class="mt-2 flex items-baseline text-sm">
            <div class="w-16">
              <InputLabel for="custom-unit-amount" class="sr-only" value="Amount" />
              <TextInput
                id="custom-unit-amount"
                ref="customUnitAmountInputRef"
                class="w-full py-0.5"
                type="number"
                step="any"
                @keyup.enter="handleCustomAmountInputEnter"
                v-model="customUnit.custom_unit.custom_unit_amount"
              />
            </div>
            <p class="ml-2">{{customUnit.custom_unit.name}} weighs...</p>
          </div>
          <InputError class="mt-1" :message="errors.custom_unit_amount" />
          <InputError class="mt-1" :message="clientSideErrors.custom_unit_amount" />

          <div class="mt-3 flex items-baseline">
            <!-- Custom mass amount -->
            <div class="w-24">
              <InputLabel class="!text-base" for="custom-mass-amount" value="Weight" />
              <TextInput
                id="custom-mass-amount"
                ref="customMassAmountInputRef"
                class="w-full"
                type="number"
                step="any"
                placeholder="100"
                @keyup.enter="confirm"
                v-model="customUnit.custom_unit.custom_mass_amount"
              />
              <InputError :message="errors.custom_mass_amount" />
              <InputError :message="clientSideErrors.custom_mass_amount" />
            </div>
            <div class="ml-4 w-20">
              <SimpleCombobox
                :options="units.filter(unit => unit.g)"
                labelText="Unit"
                labelClasses="!text-base"
                inputClasses="w-20"
                :modelValue="customUnit.custom_unit.custom_mass_unit"
                @update:modelValue="newValue => {
                  customUnit.custom_unit.custom_mass_unit = newValue
                  customUnit.custom_unit.custom_mass_unit_id = newValue.id
                }"
              />
              <InputError :message="errors.custom_mass_unit_id" />
            </div>
          </div>

        </div>

        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>
          <PrimaryButton @click="allOptionsShowing = true" v-show="!allOptionsShowing" class="ml-2" >
            Next
          </PrimaryButton>
          <PrimaryButton @click="confirm" v-show="allOptionsShowing" class="ml-2" >
            Okay
          </PrimaryButton>
        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
