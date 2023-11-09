<script setup>
/*
  Used to specify an ingredient's:
  - density_mass_unit_id
  - density_mass_amount
  - density_volume_unit_id
  - density_volume_amount
*/
import { ref, computed } from 'vue'
import { round } from '@/utils/GlobalFunctions.js'
import cloneDeep from "lodash/cloneDeep"
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  density_mass_amount: [Number, String],
  density_mass_unit_id: Number,
  density_mass_unit: Object,
  density_volume_amount: [Number, String],
  density_volume_unit_id: Number,
  density_volume_unit: Object,
  units: Array,
  errors: Object,
})

defineExpose({ open })
const emit = defineEmits(['confirm'])

const densityObj = ref({})

const isOpen = ref(false)
function open() {
  densityObj.value = {
    density_mass_amount: props.density_mass_amount,
    density_mass_unit_id: props.density_mass_unit_id ? props.density_mass_unit_id : props.units.find(unit => unit.name === 'g').id,
    density_mass_unit: props.density_mass_unit ? cloneDeep(props.density_mass_unit) : props.units.find(unit => unit.name === 'g'),
    density_volume_amount: props.density_volume_amount,
    density_volume_unit_id: props.density_volume_unit_id ? props.density_volume_unit_id : props.units.find(unit => unit.name === 'ml').id,
    density_volume_unit: props.density_volume_unit ? cloneDeep(props.density_volume_unit) : props.units.find(unit => unit.name === 'ml'),
  }
  isOpen.value = true
}
function confirm() {
  isOpen.value = false
  emit('confirm', densityObj.value)
}
function cancel() {
  isOpen.value = false
}

const massAmountInputRef = ref(null)
const volumeAmountInputRef = ref(null)
function updateSelectedDensityMassUnit(newValue) {
  densityObj.value.density_mass_unit = newValue
  densityObj.value.density_mass_unit_id = newValue.id
}
function updateSelectedDensityVolumeUnit(newValue) {
  densityObj.value.density_volume_unit = newValue
  densityObj.value.density_volume_unit_id = newValue.id
}

const density = computed(() => {
  return (Number(densityObj.value.density_mass_amount) > 0 && Number(densityObj.value.density_volume_amount) > 0)
    ? Number(densityObj.value.density_mass_amount) / Number(densityObj.value.density_volume_amount)
    : null
})
const densityGMl = computed(() => {
  return density.value ?
    (Number(densityObj.value.density_mass_amount) * densityObj.value.density_mass_unit.g)  / (Number(densityObj.value.density_volume_amount) * densityObj.value.density_volume_unit.ml )
    : null
})

function handleMassAmountInputEnter() {
  if (density.value) confirm();
  else volumeAmountInputRef.value.focus();
}

function handleVolumeAmountInputEnter() {
  if (density.value) confirm();
}

</script>

<template>
  <Dialog
    :initialFocus="massAmountInputRef"
    :open="isOpen"
    @close="cancel"
    class="relative z-10"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg shadow bg-white max-h-[600px] overflow-y-auto">
        <div>
          <DialogTitle class="text-lg font-bold text-gray-600">
            Ingredient density
          </DialogTitle>
          <DialogDescription class="mt-1 text-sm">
            To give the ingredient a density, specify the ingredient's mass to volume ratio.
            <!-- <p>Once you give the ingredient a density, you will be able to specify the ingredient's amount in units of volume.</p> -->
          </DialogDescription>
        </div>

        <div class="mt-5 flex items-baseline border-b border-gray-300 w-fit pb-4">

          <p class="w-16 translate-y-7 text-md text-gray-800 mr-2">Mass:</p>

          <!-- Density mass amount -->
          <div class="w-24">
            <InputLabel for="density-mass-amount" value="Amount" />
            <TextInput
              id="density-mass-amount"
              ref="massAmountInputRef"
              class="w-full py-1.5"
              type="number"
              placeholder="100"
              :min="0"
              step="any"
              @keyup.enter="handleMassAmountInputEnter"
              v-model="densityObj.density_mass_amount"
            />
            <InputError class="mt-1" :message="errors.density_mass_amount" />
          </div>

          <!-- Density mass unit -->
          <div class="ml-2 w-24">
            <SimpleCombobox
              :options="units.filter(unit => unit.g)"
              labelText="Unit"
              inputClasses="py-1.5 w-24"
              :modelValue="densityObj.density_mass_unit"
              @update:modelValue="newValue => updateSelectedDensityMassUnit(newValue)"
            />
            <InputError class="mt-1" :message="errors.density_mass_unit_id" />
          </div>

        </div>

        <div class="mt-2 flex items-baseline">

          <p class="w-16 translate-y-7 text-md text-gray-800 mr-2">
            Volume:
          </p>

          <!-- Density volume amount -->
          <div class="w-24">
            <InputLabel class="whitespace-nowrap" for="density-volume-amount" value="Amount" />
            <TextInput
              id="density-volume-amount"
              ref="volumeAmountInputRef"
              class="w-full py-1.5"
              type="number"
              placeholder="100"
              :min="0"
              step="any"
              @keyup.enter="handleVolumeAmountInputEnter"
              v-model="densityObj.density_volume_amount"
            />
            <InputError class="mt-1" :message="errors.density_volume_amount" />
          </div>

          <!-- Density volume unit -->
          <div class="ml-2 w-24">
            <SimpleCombobox
              :options="units.filter(unit => unit.ml)"
              labelText="Unit"
              inputClasses="py-1.5 w-24"
              :modelValue="densityObj.density_volume_unit"
              @update:modelValue="newValue => updateSelectedDensityVolumeUnit(newValue)"
            />
            <InputError class="mt-1" :message="errors.density_volume_unit_id" />
          </div>

        </div>

        <!-- Resulting density -->
        <div class="mt-8 flex items-baseline text-gray-800">
          Density:
          <p v-if="density" class="ml-2 font-bold">
            {{round(density, 2)}} {{densityObj.density_mass_unit.name}}/{{densityObj.density_volume_unit.name}}
            <span v-if="densityObj.density_mass_unit.name !== 'g' || densityObj.density_volume_unit.name !== 'ml'">
              ({{round(densityGMl, 2)}} g/ml)
            </span>
          </p>
          <p v-else class="ml-2 text-gray-600 leading-tight">
            The ingredient does not (yet) have a valid density.
          </p>
        </div>

        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>
          <PrimaryButton @click="confirm" class="ml-2" >
            Okay
          </PrimaryButton>
        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
