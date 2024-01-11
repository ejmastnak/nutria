<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import cloneDeep from "lodash/cloneDeep"
import { getHumanReadableLocalDate } from '@/utils/GlobalFunctions.js'
import { PlusCircleIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import LogMealIntakeRecordsHelperDialog from './LogMealIntakeRecordsHelperDialog.vue'

import { mealIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  meals: Array,
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

const mealIntakeRecordDialogRef = ref(null)
const addMealIntakeRecordButtonRef = ref(null)
var mealIntakeRecordIdToUpdate = null

function addMealIntakeRecord() {
  mealIntakeRecordDialogRef.value.open(null, {});
}

function editMealIntakeRecord(mealIntakeRecord, idx) {
  mealIntakeRecordIdToUpdate = mealIntakeRecord.id
  mealIntakeRecordDialogRef.value.open(mealIntakeRecord.meal_intake_record, {
    meal_intake_record: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id]],
    meal_id: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.meal_id'],
    amount: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.amount'],
    unit_id: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.unit_id'],
    date: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.date'],
    time: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.time'],
    date_time_utc: mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.date_time_utc'],
  })
}

function deleteMealIntakeRecord(idx) {
  if (idx >= 0 && idx < mealIntakeRecordsForm.mealIntakeRecords.length) mealIntakeRecordsForm.mealIntakeRecords.splice(idx, 1)
}

function addOrUpdateMealIntakeRecord(updatedMealIntakeRecord) {
  const idx = mealIntakeRecordsForm.mealIntakeRecords.findIndex(mealIntakeRecord => mealIntakeRecord.id === mealIntakeRecordIdToUpdate)

  if (idx >= 0) {  // update an existing record
    mealIntakeRecordsForm.mealIntakeRecords[idx].meal_intake_record = cloneDeep(updatedMealIntakeRecord)
  } else {  // add a new record
    mealIntakeRecordsForm.mealIntakeRecords.push({
      id: mealIntakeRecordsForm.nextId,
      meal_intake_record: cloneDeep(updatedMealIntakeRecord),
    })
    mealIntakeRecordsForm.nextId += 1
  }

  mealIntakeRecordIdToUpdate = null
}

function submit() {
  mealIntakeRecordsForm.errors = {}
  mealIntakeRecordsForm.id_idx_mapping = {}
  mealIntakeRecordsForm.mealIntakeRecords.forEach((record, idx) => mealIntakeRecordsForm.id_idx_mapping[record.id] = idx)
  mealIntakeRecordsForm.meal_intake_records = mealIntakeRecordsForm.mealIntakeRecords.map(record => record.meal_intake_record)
  mealIntakeRecordsForm.post(route('meal-intake-records.store-many'), {
    onSuccess: () => {
      mealIntakeRecordsForm.reset()
      mealIntakeRecordsForm.clearErrors()
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
            Log meals
          </DialogTitle>
          <InputError :message="mealIntakeRecordsForm.errors.meal_intake_records" />
        </div>

        <div>

          <ol class="mt-2 space-y-1" >
            <li
              v-for="(mealIntakeRecord, idx) in mealIntakeRecordsForm.mealIntakeRecords" :key="mealIntakeRecord.id"
              class="flex items-start"
            >
              <div>
                <button
                  type="button"
                  @mouseup="editMealIntakeRecord(mealIntakeRecord, idx)"
                  @keyup.enter="editMealIntakeRecord(mealIntakeRecord, idx)"
                  @click.prevent
                  class="hover:underline text-left"
                >
                  {{mealIntakeRecord.meal_intake_record.meal.name}}
                  <span class="font-medium mr-px">({{mealIntakeRecord.meal_intake_record.amount}} {{mealIntakeRecord.meal_intake_record.unit.name}})</span>
                </button>
                <div class="mt-1">
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id]]" />
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.meal_id']" />
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.amount']" />
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.unit_id']" />
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.date']" />
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.time']" />
                  <InputError :message="mealIntakeRecordsForm.errors['meal_intake_records.' + mealIntakeRecordsForm.id_idx_mapping[mealIntakeRecord.id] + '.date_time_utc']" />
                </div>
              </div>

              <button
                type="button"
                @click.stop="editMealIntakeRecord(mealIntakeRecord, idx)"
                class="ml-auto"
              >
                <PencilSquareIcon class="-ml-1 w-6 h-6 text-gray-600 hover:text-blue-700 shrink-0" />
              </button>
              <button
                type="button"
                @click.stop="deleteMealIntakeRecord(idx)"
              >
                <TrashIcon class="ml-px w-6 h-6 text-gray-600 hover:text-red-700 shrink-0" />
              </button>

            </li>
          </ol>

          <p v-if="mealIntakeRecordsForm.mealIntakeRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
            You haven't added any meal intake records.
          </p>

          <!-- Avoiding @click because enter then propogates to autofocus input in MealIntakeRecordDialog -->
          <button
            type="button"
            @mouseup="addMealIntakeRecord"
            @keyup.enter="addMealIntakeRecord"
            @click.prevent
            ref="addMealIntakeRecordButtonRef"
            class="mt-4 inline-flex items-center w-fit pl-2 pr-4 py-1 bg-white border border-gray-300 rounded-lg text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            <PlusCircleIcon class="text-gray-600 w-6 h-6"/>
            <span class="ml-2 text-sm">Add more records</span>
          </button>
        </div>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="close" class="ml-auto" >
            Cancel
          </SecondaryButton>

          <PrimaryButton ref="confirmMealIntakeRecordsButtonRef" type="button" @click="submit" class="ml-2" >
            Save
          </PrimaryButton>
        </div>

        <LogMealIntakeRecordsHelperDialog
          ref="mealIntakeRecordDialogRef"
          @confirm="addOrUpdateMealIntakeRecord"
          @close="mealIntakeRecordIdToUpdate = null"
          :meals="meals"
          :units="units"
        />

      </DialogPanel>
    </div>
  </Dialog>
</template>
