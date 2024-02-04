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
import LogBodyWeightRecordsHelperDialog from './LogBodyWeightRecordsHelperDialog.vue'

import { bodyWeightRecordsForm } from '@/Shared/store.js'

const props = defineProps({
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
  bodyWeightRecordsForm.reset()
  bodyWeightRecordsForm.clearErrors()
  close()
}

const bodyWeightRecordDialogRef = ref(null)
const addBodyWeightRecordButtonRef = ref(null)
var bodyWeightRecordIdToUpdate = null

function addBodyWeightRecord() {
  bodyWeightRecordDialogRef.value.open(null, {});
}

function editBodyWeightRecord(bodyWeightRecord, idx) {
  bodyWeightRecordIdToUpdate = bodyWeightRecord.id
  bodyWeightRecordDialogRef.value.open(bodyWeightRecord.body_weight_record, {
    body_weight_record: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id]],
    amount: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.amount'],
    unit_id: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.unit_id'],
    date: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.date'],
    time: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.time'],
    date_time_utc: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.date_time_utc'],
    description: bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.description'],
  })
}

function deleteBodyWeightRecord(idx) {
  if (idx >= 0 && idx < bodyWeightRecordsForm.bodyWeightRecords.length) bodyWeightRecordsForm.bodyWeightRecords.splice(idx, 1)
}

function addOrUpdateBodyWeightRecord(updatedBodyWeightRecord) {
  const idx = bodyWeightRecordsForm.bodyWeightRecords.findIndex(bodyWeightRecord => bodyWeightRecord.id === bodyWeightRecordIdToUpdate)

  if (idx >= 0) {  // update an existing record
    bodyWeightRecordsForm.bodyWeightRecords[idx].body_weight_record = cloneDeep(updatedBodyWeightRecord)
  } else {  // add a new record
    bodyWeightRecordsForm.bodyWeightRecords.push({
      id: bodyWeightRecordsForm.nextId,
      body_weight_record: cloneDeep(updatedBodyWeightRecord),
    })
    bodyWeightRecordsForm.nextId += 1
  }

  bodyWeightRecordIdToUpdate = null
}

function submit() {
  bodyWeightRecordsForm.errors = {}
  bodyWeightRecordsForm.id_idx_mapping = {}
  bodyWeightRecordsForm.bodyWeightRecords.forEach((record, idx) => bodyWeightRecordsForm.id_idx_mapping[record.id] = idx)
  bodyWeightRecordsForm.body_weight_records = bodyWeightRecordsForm.bodyWeightRecords.map(record => record.body_weight_record)
  bodyWeightRecordsForm.post(route('body-weight-records.store-many'), {
    onSuccess: () => {
      bodyWeightRecordsForm.reset()
      bodyWeightRecordsForm.clearErrors()
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
              Add body weight records
            </DialogTitle>
            <InputError :message="bodyWeightRecordsForm.errors.body_weight_records" />
          </div>

          <div>

            <ol class="mt-2 space-y-1" >
              <li
                v-for="(bodyWeightRecord, idx) in bodyWeightRecordsForm.bodyWeightRecords" :key="bodyWeightRecord.id"
                class="flex items-start"
              >
                <div>
                  <button
                    type="button"
                    @mouseup="editBodyWeightRecord(bodyWeightRecord, idx)"
                    @keyup.enter="editBodyWeightRecord(bodyWeightRecord, idx)"
                    @click.prevent
                    class="hover:underline text-left"
                  >
                    <span class="font-medium mr-px">{{Number(bodyWeightRecord.body_weight_record.amount).toFixed(1)}} {{bodyWeightRecord.body_weight_record.unit.name}}</span>
                    ({{getHumanReadableLocalDate(bodyWeightRecord.body_weight_record.date_time_utc, shortMonth=true)}})
                  </button>
                  <div class="mt-1">
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id]]" />
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.amount']" />
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.unit_id']" />
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.date']" />
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.time']" />
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.date_time_utc']" />
                    <InputError :message="bodyWeightRecordsForm.errors['body_weight_records.' + bodyWeightRecordsForm.id_idx_mapping[bodyWeightRecord.id] + '.description']" />
                  </div>
                </div>

                <button
                  type="button"
                  @click.stop="editBodyWeightRecord(bodyWeightRecord, idx)"
                  class="ml-auto"
                >
                  <PencilSquareIcon class="-ml-1 w-6 h-6 text-gray-600 hover:text-blue-700 shrink-0" />
                </button>
                <button
                  type="button"
                  @click.stop="deleteBodyWeightRecord(idx)"
                >
                  <TrashIcon class="ml-px w-6 h-6 text-gray-600 hover:text-red-700 shrink-0" />
                </button>

              </li>
            </ol>

            <p v-if="bodyWeightRecordsForm.bodyWeightRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
              You haven't added any food list intake records.
            </p>

            <!-- Avoiding @click because enter then propogates to autofocus input in BodyWeightRecordDialog -->
            <button
              type="button"
              @mouseup="addBodyWeightRecord"
              @keyup.enter="addBodyWeightRecord"
              @click.prevent
              ref="addBodyWeightRecordButtonRef"
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

            <PrimaryButton ref="confirmBodyWeightRecordsButtonRef" type="button" @click="submit" class="ml-2" >
              Save
            </PrimaryButton>
          </div>

          <LogBodyWeightRecordsHelperDialog
            ref="bodyWeightRecordDialogRef"
            @confirm="addOrUpdateBodyWeightRecord"
            @close="bodyWeightRecordIdToUpdate = null"
            :units="units"
          />

        </form>

      </DialogPanel>
    </div>
  </Dialog>
</template>
