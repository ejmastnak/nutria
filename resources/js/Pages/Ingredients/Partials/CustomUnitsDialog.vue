<!-- Provides an overview of an ingredient's custom units -->
<script setup>
import { ref, computed } from 'vue'
import cloneDeep from "lodash/cloneDeep"
import { round, roundNonZero } from '@/utils/GlobalFunctions.js'
import { PlusCircleIcon, TrashIcon, PencilSquareIcon, Bars3Icon } from '@heroicons/vue/24/outline'
import SimpleCombobox from '@/Components/SimpleCombobox.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import CustomUnitDialog from './CustomUnitDialog.vue'

const props = defineProps({
  custom_units: Array,
  units: Array,
  errors: Object,
})

defineExpose({ open })
const emit = defineEmits(['confirm'])

const customUnits = ref([])
var nextId = 1

const isOpen = ref(false)
function open() {
  customUnits.value = cloneDeep(props.custom_units)
  nextId = customUnits.value.length + 1
  isOpen.value = true
}
function confirm() {
  isOpen.value = false
  emit('confirm', customUnits.value)
}
function cancel() {
  isOpen.value = false
}

const customUnitDialogRef = ref(null)
const addCustomUnitButtonRef = ref(null)
var customUnitIdToUpdate = null

function addCustomUnit() {
  customUnitDialogRef.value.open({
    id: nextId,
    custom_unit: {
      id: null,
      name: "",
      seq_num: null,
      ingredient_id: null,
      custom_unit_amount: 1,
      custom_mass_amount: null,
      custom_mass_unit_id: props.units.find(unit => unit.name === 'g').id,
      custom_mass_unit: props.units.find(unit => unit.name === 'g'),
      custom_grams: null,
    }
  }, {});
  nextId += 1
}

function editCustomUnit(customUnit, idx) {
  customUnitDialogRef.value.open(customUnit, {
    name: props.errors['custom_units.' + idx + '.name'],
    custom_unit_amount: props.errors['custom_units.' + idx + '.custom_unit_amount'],
    custom_mass_amount: props.errors['custom_units.' + idx + '.custom_mass_amount'],
    custom_mass_unit_id: props.errors['custom_units.' + idx + '.custom_mass_unit_id'],
  })
  customUnitIdToUpdate = customUnit.id
}

function deleteCustomUnit(idx) {
  if (idx >= 0 && idx < customUnits.value.length) customUnits.value.splice(idx, 1)
}

function addOrUpdateCustomUnit(updatedCustomUnit) {
  const idx = customUnits.value.findIndex(customUnit => customUnit.id === customUnitIdToUpdate)
  if (idx >= 0) {  // update existing custom unit
    customUnits.value[idx] = updatedCustomUnit
  } else {  // add a new custom unit
    customUnits.value.push(updatedCustomUnit)
  }
  customUnitIdToUpdate = null
}

</script>

<template>

  <Dialog
    :open="isOpen"
    @close="cancel"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow max-h-[600px] overflow-auto">

        <div>
          <DialogTitle class="text-lg font-bold text-gray-600">
            Ingredient custom units
          </DialogTitle>
          <InputError :message="errors.custom_units" />
          <DialogDescription class="mt-1 text-sm max-w-sm">
            You can add new units and edit or delete existing units.
          </DialogDescription>
        </div>

        <div>
          <ol class="mt-2 space-y-1" >
            <li v-for="(customUnit, idx) in customUnits" :key="customUnit.id" >

              <div class="flex items-baseline">
                <div>
                  <!-- Avoiding @click because enter then propogates to autofocus input in CustomUnitDialog -->
                  <button
                    type="button"
                    @mouseup="editCustomUnit(customUnit, idx)"
                    @keyup.enter="editCustomUnit(customUnit, idx)"
                    @click.prevent
                    class="hover:underline -translate-y-1"
                  >
                    {{roundNonZero(customUnit.custom_unit.custom_unit_amount, 2)}}
                    {{customUnit.custom_unit.name}}
                    ({{roundNonZero(customUnit.custom_unit.custom_mass_amount, 2)}} {{customUnit.custom_unit.custom_mass_unit.name}})
                  </button>
                  <div>
                    <InputError :message="errors['custom_units.' + idx + '.name']" />
                    <InputError :message="errors['custom_units.' + idx + '.custom_unit_amount']" />
                    <InputError :message="errors['custom_units.' + idx + '.custom_mass_amount']" />
                    <InputError :message="errors['custom_units.' + idx + '.custom_mass_unit_id']" />
                  </div>
                </div>

                <!-- Avoiding @click because enter then propogates to autofocus input in CustomUnitDialog -->
                <button
                  type="button"
                  @mouseup="editCustomUnit(customUnit, idx)"
                  @keyup.enter="editCustomUnit(customUnit, idx)"
                  @click.prevent
                  class="ml-auto p-1 text-gray-700 hover:text-red-700"
                >
                  <PencilSquareIcon class="w-6 h-6" />
                </button>

                <button
                  type="button"
                  @click="deleteCustomUnit(idx)"
                  class="p-1 text-gray-700 hover:text-red-700"
                >
                  <TrashIcon class="w-6 h-6" />
                </button>

              </div>
            </li>
          </ol>
          <p v-if="customUnits.length === 0" class="mt-3 text-gray-500 text-sm" >
            This ingredient has no custom units yet.
          </p>

          <!-- Add custom unit -->
          <!-- Avoiding @click because enter then propogates to autofocus input in CustomUnitDialog -->
          <button
            type="button"
            id="add-custom-unit"
            @mouseup="addCustomUnit"
            @keyup.enter="addCustomUnit"
            @click.prevent
            ref="addCustomUnitButtonRef"
            class="mt-4 inline-flex items-center w-fit pl-2 pr-4 py-1 bg-white border border-gray-300 rounded-lg text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            <PlusCircleIcon class="text-gray-600 w-6 h-6"/>
            <span class="ml-2 text-sm">Add a custom unit</span>
          </button>

        </div>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>
          <PrimaryButton ref="confirmCustomUnitsButtonRef" @click="confirm" class="ml-2" >
            Save
          </PrimaryButton>
        </div>

        <CustomUnitDialog
          ref="customUnitDialogRef"
          @confirm="addOrUpdateCustomUnit"
          @cancel="customUnitIdToUpdate = null"
          :units="units"
        />

      </DialogPanel>
    </div>
  </Dialog>
</template>
