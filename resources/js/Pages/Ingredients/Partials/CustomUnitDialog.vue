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
const errors = ref({})
var isNew = false

const nameInputRef = ref(null)

const isOpen = ref(false)
const allOptionsShowing = ref(false)
function open(custom_unit, passedErrors, passedIsNew) {
  customUnit.value = cloneDeep(custom_unit)
  customUnit.value.custom_unit.custom_unit_amount = customUnit.value.custom_unit.custom_unit_amount
  customUnit.value.custom_unit.custom_mass_amount = customUnit.value.custom_unit.custom_mass_amount
  errors.value = passedErrors
  if (customUnit.value.custom_unit.name && customUnit.value.custom_unit.name.length > 0) allOptionsShowing.value = true;
  isNew = passedIsNew
  isOpen.value = true
}
const customMassAmountInputRef = ref(null)
const customUnitAmountInputRef = ref(null)

function confirm() {
  isOpen.value = false
  emit('confirm', customUnit.value)
}

function okay() {
  if (customUnit.value.custom_unit.custom_mass_amount === null || customUnit.value.custom_unit.custom_mass_amount === "") {
    customMassAmountInputRef.value.focus();
  } else confirm();
}

function cancel() {
  isOpen.value = false
  allOptionsShowing.value = false
  isNew = false
  emit('cancel')
}

function handleNameInputEnter() {
  if (!allOptionsShowing.value) {
    allOptionsShowing.value = true
    setTimeout(() => {
      customMassAmountInputRef.value.focus();
    }, 0);
  }
  else {
    if (customUnit.value.custom_unit.name && customUnit.value.custom_unit.custom_unit_amount && customUnit.value.custom_unit.custom_mass_amount && customUnit.value.custom_unit.custom_mass_unit) confirm();
    else if (!customUnit.value.custom_unit.name || customUnit.value.custom_unit.name.length === 0) return;
    else if (!customUnit.value.custom_unit.custom_unit_amount) {
      customUnitAmountInputRef.value.focus();
    }
    else if (!customUnit.value.custom_unit.custom_mass_amount) {
      customMassAmountInputRef.value.focus();
    }
  }

}

function handleCustomMassAmountInputEnter() {
  if (customUnit.value.custom_unit.name && customUnit.value.custom_unit.custom_unit_amount && customUnit.value.custom_unit.custom_mass_amount && customUnit.value.custom_unit.custom_mass_unit) confirm();
  else if (!customUnit.value.custom_unit.name) {
    nameInputRef.value.focus()
  }
  else if (!customUnit.value.custom_unit.custom_unit_amount) {
    customUnitAmountInputRef.value.focus();
  }
}

</script>

<template>
  <Dialog
    :initialFocus="nameInputRef"
    :open="isOpen"
    @close="cancel"
    class="relative z-10"
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
        </div>

        <div v-show="allOptionsShowing" class="mt-5">
          <!-- Custom unit amount -->
          <div class="mt-2 flex items-baseline text-gray-600 text-sm">
            <div class="w-16">
              <InputLabel for="custom-unit-amount" class="sr-only" value="Amount" />
              <TextInput
                id="custom-unit-amount"
                ref="customUnitAmountInputRef"
                class="w-full py-0.5"
                type="number"
                v-model="customUnit.custom_unit.custom_unit_amount"
              />
            </div>
            <p class="ml-2">{{customUnit.custom_unit.name}} weighs...</p>
          </div>
          <InputError class="mt-1" :message="errors.custom_unit_amount" />

          <div class="mt-3 flex items-baseline">
            <!-- Custom mass amount -->
            <div class="w-24">
              <InputLabel class="!text-base" for="custom-mass-amount" value="Weight" />
              <TextInput
                id="custom-mass-amount"
                ref="customMassAmountInputRef"
                class="w-full"
                type="number"
                placeholder="100"
                @keyup.enter="handleCustomMassAmountInputEnter"
                v-model="customUnit.custom_unit.custom_mass_amount"
              />
              <InputError :message="errors.custom_mass_amount" />
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
          <PrimaryButton @click="okay" v-show="allOptionsShowing" class="ml-2" >
            Okay
          </PrimaryButton>
        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
