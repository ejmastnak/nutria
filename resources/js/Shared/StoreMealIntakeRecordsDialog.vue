<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import cloneDeep from "lodash/cloneDeep"
import { PlusCircleIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import MealIntakeRecordDialog from './MealIntakeRecordDialog.vue'

const props = defineProps({
  meals: Array,
  units: Array,
})

defineExpose({ open })

const form = useForm({
  meal_intake_records: [],
  // Maps a record's serial id to the record's array index on last form submit.
  // This is to preserve association with errors sent from backend.
  id_idx_mapping: {},
})

const mealIntakeRecords = ref([])
var nextId = 1

const isOpen = ref(false)
function open() {
  isOpen.value = true
  if (mealIntakeRecords.value.length === 0) {
    setTimeout(() => {
      addMealIntakeRecord()
    }, 0);
  }
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
    meal_intake_record: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id]],
    meal_id: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.meal_id'],
    amount: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.amount'],
    unit_id: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.unit_id'],
    date: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.date'],
    time: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.time'],
    date_time_utc: form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.date_time_utc'],
  })
}

function deleteMealIntakeRecord(idx) {
  if (idx >= 0 && idx < mealIntakeRecords.value.length) mealIntakeRecords.value.splice(idx, 1)
}

function addOrUpdateMealIntakeRecord(updatedMealIntakeRecord) {
  const idx = mealIntakeRecords.value.findIndex(mealIntakeRecord => mealIntakeRecord.id === mealIntakeRecordIdToUpdate)

  if (idx >= 0) {  // update existing custom unit
    mealIntakeRecords.value[idx].meal_intake_record = cloneDeep(updatedMealIntakeRecord)
  } else {  // add a new custom unit
    mealIntakeRecords.value.push({
      id: nextId,
      meal_intake_record: cloneDeep(updatedMealIntakeRecord),
    })
    nextId += 1
  }

  mealIntakeRecordIdToUpdate = null
}

function submit() {
  form.id_idx_mapping = {}
  mealIntakeRecords.value.forEach((record, idx) => form.id_idx_mapping[record.id] = idx)
  form.meal_intake_records = mealIntakeRecords.value.map(record => record.meal_intake_record)
  form.post(route('meal-intake-records.store'), {
    onSuccess: () => {
      form.reset()
      form.clearErrors()
      mealIntakeRecords.value = []
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
        <form>
          <div>
            <DialogTitle class="text-lg font-bold text-gray-600">
              Add Meal intake records
            </DialogTitle>
            <InputError :message="form.errors.meal_intake_records" />
          </div>

          <div>

            <ol class="mt-2 space-y-1" >
              <li
                v-for="(mealIntakeRecord, idx) in mealIntakeRecords" :key="mealIntakeRecord.id"
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
                    <span class="font-medium">({{mealIntakeRecord.meal_intake_record.amount}} {{mealIntakeRecord.meal_intake_record.unit.name}})</span>
                  </button>
                  <div class="mt-1">
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id]]" />
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.meal_id']" />
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.amount']" />
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.unit_id']" />
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.date']" />
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.time']" />
                    <InputError :message="form.errors['meal_intake_records.' + form.id_idx_mapping[mealIntakeRecord.id] + '.date_time_utc']" />
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

            <p v-if="mealIntakeRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
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

          <MealIntakeRecordDialog
            ref="mealIntakeRecordDialogRef"
            @confirm="addOrUpdateMealIntakeRecord"
            @close="mealIntakeRecordIdToUpdate = null"
            :meals="meals"
            :units="units"
          />

        </form>

      </DialogPanel>
    </div>
  </Dialog>
</template>
