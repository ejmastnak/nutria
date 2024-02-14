<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import cloneDeep from "lodash/cloneDeep"
import { PlusCircleIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import LogIngredientIntakeRecordsHelperDialog from './LogIngredientIntakeRecordsHelperDialog.vue'

import { ingredientIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  ingredients: Array,
  units: Array,
})

defineExpose({ open })

const isOpen = ref(false)
function open() {
  isOpen.value = true
}
function close() {
  isOpen.value = false
}
function cancel() {
  ingredientIntakeRecordsForm.reset()
  ingredientIntakeRecordsForm.clearErrors()
  close()
}

const ingredientIntakeRecordDialogRef = ref(null)
const addIngredientIntakeRecordButtonRef = ref(null)
var ingredientIntakeRecordIdToUpdate = null

function addIngredientIntakeRecord() {
  ingredientIntakeRecordDialogRef.value.open(null, {});
}

function editIngredientIntakeRecord(ingredientIntakeRecord, idx) {
  ingredientIntakeRecordIdToUpdate = ingredientIntakeRecord.id
  ingredientIntakeRecordDialogRef.value.open(ingredientIntakeRecord.ingredient_intake_record, {
    ingredient_intake_record: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id]],
    ingredient_id: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.ingredient_id'],
    amount: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.amount'],
    unit_id: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.unit_id'],
    date: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.date'],
    time: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.time'],
    date_time_utc: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.date_time_utc'],
    description: ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.description'],
  })
}

function deleteIngredientIntakeRecord(idx) {
  if (idx >= 0 && idx < ingredientIntakeRecordsForm.ingredientIntakeRecords.length) ingredientIntakeRecordsForm.ingredientIntakeRecords.splice(idx, 1)
}

function addOrUpdateIngredientIntakeRecord(updatedIngredientIntakeRecord) {
  const idx = ingredientIntakeRecordsForm.ingredientIntakeRecords.findIndex(ingredientIntakeRecord => ingredientIntakeRecord.id === ingredientIntakeRecordIdToUpdate)

  if (idx >= 0) {  // update an existing record
    ingredientIntakeRecordsForm.ingredientIntakeRecords[idx].ingredient_intake_record = cloneDeep(updatedIngredientIntakeRecord)
  } else {  // add a new record
    ingredientIntakeRecordsForm.ingredientIntakeRecords.push({
      id: ingredientIntakeRecordsForm.nextId,
      ingredient_intake_record: cloneDeep(updatedIngredientIntakeRecord),
    })
    ingredientIntakeRecordsForm.nextId += 1
  }

  ingredientIntakeRecordIdToUpdate = null
}

function submit() {
  ingredientIntakeRecordsForm.errors = {}
  ingredientIntakeRecordsForm.id_idx_mapping = {}
  ingredientIntakeRecordsForm.ingredientIntakeRecords.forEach((record, idx) => ingredientIntakeRecordsForm.id_idx_mapping[record.id] = idx)
  ingredientIntakeRecordsForm.ingredient_intake_records = ingredientIntakeRecordsForm.ingredientIntakeRecords.map(record => record.ingredient_intake_record)
  ingredientIntakeRecordsForm.post(route('food-intake-records.store-ingredients'), {
    onSuccess: () => {
      ingredientIntakeRecordsForm.reset()
      ingredientIntakeRecordsForm.clearErrors()
      close()
    }
  })
}

</script>

<template>

  <Dialog
    :open="isOpen"
    @close="close"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-lg rounded-lg bg-white shadow max-h-[600px] overflow-auto">
        <div>
          <DialogTitle class="text-lg font-bold text-gray-600">
            Log ingredients
          </DialogTitle>
          <InputError :message="ingredientIntakeRecordsForm.errors.ingredient_intake_records" />
        </div>

        <div>

          <ol class="mt-2 space-y-1" >
            <li
              v-for="(ingredientIntakeRecord, idx) in ingredientIntakeRecordsForm.ingredientIntakeRecords" :key="ingredientIntakeRecord.id"
              class="flex items-start"
            >
              <div>
                <button
                  type="button"
                  @mouseup="editIngredientIntakeRecord(ingredientIntakeRecord, idx)"
                  @keyup.enter="editIngredientIntakeRecord(ingredientIntakeRecord, idx)"
                  @click.prevent
                  class="hover:underline text-left"
                >
                  {{ingredientIntakeRecord.ingredient_intake_record.ingredient.name}}
                  <span class="font-medium mr-px">({{ingredientIntakeRecord.ingredient_intake_record.amount}} {{ingredientIntakeRecord.ingredient_intake_record.unit.name}})</span>
                </button>
                <div class="mt-1">
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id]]" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.ingredient_id']" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.amount']" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.unit_id']" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.date']" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.time']" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.date_time_utc']" />
                  <InputError :message="ingredientIntakeRecordsForm.errors['ingredient_intake_records.' + ingredientIntakeRecordsForm.id_idx_mapping[ingredientIntakeRecord.id] + '.description']" />
                </div>
              </div>

              <button
                type="button"
                @click.stop="editIngredientIntakeRecord(ingredientIntakeRecord, idx)"
                class="ml-auto"
              >
                <PencilSquareIcon class="-ml-1 w-6 h-6 text-gray-600 hover:text-blue-700 shrink-0" />
              </button>
              <button
                type="button"
                @click.stop="deleteIngredientIntakeRecord(idx)"
              >
                <TrashIcon class="ml-px w-6 h-6 text-gray-600 hover:text-red-700 shrink-0" />
              </button>

            </li>
          </ol>

          <p v-if="ingredientIntakeRecordsForm.ingredientIntakeRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
            You haven't added any ingredient intake records.
          </p>

          <!-- Avoiding @click because enter then propogates to autofocus input in IngredientIntakeRecordDialog -->
          <button
            type="button"
            @mouseup="addIngredientIntakeRecord"
            @keyup.enter="addIngredientIntakeRecord"
            @click.prevent
            ref="addIngredientIntakeRecordButtonRef"
            class="mt-4 inline-flex items-center w-fit pl-2 pr-4 py-1 bg-white border border-gray-300 rounded-lg text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            <PlusCircleIcon class="text-gray-600 w-6 h-6"/>
            <span class="ml-2 text-sm">Add more records</span>
          </button>
        </div>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>

          <PrimaryButton ref="confirmIngredientIntakeRecordsButtonRef" type="button" @click="submit" class="ml-2" >
            Save
          </PrimaryButton>
        </div>

        <LogIngredientIntakeRecordsHelperDialog
          ref="ingredientIntakeRecordDialogRef"
          @confirm="addOrUpdateIngredientIntakeRecord"
          @close="ingredientIntakeRecordIdToUpdate = null"
          :ingredients="ingredients"
          :units="units"
        />

      </DialogPanel>
    </div>
  </Dialog>
</template>
