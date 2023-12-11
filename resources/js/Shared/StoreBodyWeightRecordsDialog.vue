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
import BodyWeightRecordDialog from './BodyWeightRecordDialog.vue'

const props = defineProps({
  units: Array,
})

defineExpose({ open })

const form = useForm({
  body_weight_records: [],
  // Maps a record's serial id to the record's array index on last form submit.
  // This is to preserve association with errors sent from backend.
  id_idx_mapping: {},
})

const bodyWeightRecords = ref([])
var nextId = 1

const isOpen = ref(false)
function open() {
  isOpen.value = true
  if (bodyWeightRecords.value.length === 0) {
    setTimeout(() => {
      addBodyWeightRecord()
    }, 0);
  }
}
function close() {
  isOpen.value = false
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
    body_weight_record: form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id]],
    amount: form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.amount'],
    unit_id: form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.unit_id'],
    date: form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.date'],
    time: form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.time'],
    date_time_utc: form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.date_time_utc'],
  })
}

function deleteBodyWeightRecord(idx) {
  if (idx >= 0 && idx < bodyWeightRecords.value.length) bodyWeightRecords.value.splice(idx, 1)
}

function addOrUpdateBodyWeightRecord(updatedBodyWeightRecord) {
  const idx = bodyWeightRecords.value.findIndex(bodyWeightRecord => bodyWeightRecord.id === bodyWeightRecordIdToUpdate)

  if (idx >= 0) {  // update existing custom unit
    bodyWeightRecords.value[idx].body_weight_record = cloneDeep(updatedBodyWeightRecord)
  } else {  // add a new custom unit
    bodyWeightRecords.value.push({
      id: nextId,
      body_weight_record: cloneDeep(updatedBodyWeightRecord),
    })
    nextId += 1
  }

  bodyWeightRecordIdToUpdate = null
}

function submit() {
  form.id_idx_mapping = {}
  bodyWeightRecords.value.forEach((record, idx) => form.id_idx_mapping[record.id] = idx)
  form.body_weight_records = bodyWeightRecords.value.map(record => record.body_weight_record)
  form.post(route('body-weight-records.store'), {
    onSuccess: () => {
      form.reset()
      form.clearErrors()
      bodyWeightRecords.value = []
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
            <InputError :message="form.errors.body_weight_records" />
          </div>

          <div>

            <ol class="mt-2 space-y-1" >
              <li
                v-for="(bodyWeightRecord, idx) in bodyWeightRecords" :key="bodyWeightRecord.id"
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
                    ({{getHumanReadableLocalDate(bodyWeightRecord.body_weight_record.date_time_utc, longMonth=false)}})
                  </button>
                  <div class="mt-1">
                    <InputError :message="form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id]]" />
                    <InputError :message="form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.amount']" />
                    <InputError :message="form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.unit_id']" />
                    <InputError :message="form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.date']" />
                    <InputError :message="form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.time']" />
                    <InputError :message="form.errors['body_weight_records.' + form.id_idx_mapping[bodyWeightRecord.id] + '.date_time_utc']" />
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

            <p v-if="bodyWeightRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
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
            <SecondaryButton @click="close" class="ml-auto" >
              Cancel
            </SecondaryButton>

            <PrimaryButton ref="confirmBodyWeightRecordsButtonRef" type="button" @click="submit" class="ml-2" >
              Save
            </PrimaryButton>
          </div>

          <BodyWeightRecordDialog
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
