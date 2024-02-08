<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { getLocalYYYYMMDD, getLocalHHMM, getHumanReadableLocalDate } from '@/utils/GlobalFunctions.js'
import Pagination from '@/Shared/Pagination.vue'
import { TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import LogBodyWeightRecordDialog from '@/Shared/LogBodyWeightRecordDialog.vue'
import LogBodyWeightRecordsDialog from '@/Shared/LogBodyWeightRecordsDialog.vue'
import DeleteDialog from "@/Components/DeleteDialog.vue";
import SecondaryButton from '@/Components/SecondaryButton.vue'

import { bodyWeightRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  body_weight_records_paginator: Object,
  units: Array,
})

const logBodyWeightRecordDialogRef = ref(null)
const logBodyWeightRecordsDialogRef = ref(null)

let idToDelete = ref(null)
const deleteDialog = ref(null)
function deleteBodyWeightRecord() {
  if (idToDelete.value) {
    router.delete(route('body-weight-records.destroy', idToDelete.value));
  }
  idToDelete.value = null
}

function logBodyWeight() {
  if (bodyWeightRecordsForm.bodyWeightRecords.length >= 1) {
    logBodyWeightRecordsDialogRef.value.open()
  } else {
    logBodyWeightRecordDialogRef.value.open(null)
  }
}

</script>

<template>
  <div>

    <p v-if="body_weight_records_paginator.data.length === 0" class="mt-1 mb-2">
      You haven't created any body weight records yet!
    </p>

    <SecondaryButton @click="logBodyWeight" >
      Log body weight
    </SecondaryButton>

    <table v-if="body_weight_records_paginator.data.length" class="mt-2 text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-8 py-3 bg-blue-100 w-16">
            Weight
          </th>
          <th scope="col" class="px-8 py-3 bg-blue-200">
            Date
          </th>
          <th scope="col" class="px-8 py-3 bg-blue-100" />
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="body_weight_record in body_weight_records_paginator.data" :key="body_weight_record.id"
          class="border-b hover:bg-gray-100 cursor-pointer"
          @click.stop="logBodyWeightRecordDialogRef.open(body_weight_record)"
        >
          <td scope="row" class="px-8 py-4 font-medium text-gray-900 text-right whitespace-nowrap">
            {{(body_weight_record.amount).toFixed(1)}}
            {{body_weight_record.unit.name}}
          </td>
          <td class="px-8 py-4">
            {{getHumanReadableLocalDate(body_weight_record.date_time_utc)}}
          </td>
          <td>
            <div class="flex items-center px-8">
              <button
                type="button"
                class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
                @click.stop="logBodyWeightRecordDialogRef.open(body_weight_record)"
              >
                <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
              </button>
              <button
                type="button"
                @click.stop="idToDelete = body_weight_record.id; deleteDialog.open()"
                class="ml-1 mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
              >
                <TrashIcon class="w-5 h-5 hover:text-red-700" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <Pagination class="mt-2" :links="body_weight_records_paginator.links" />

    <LogBodyWeightRecordDialog :units="units" ref="logBodyWeightRecordDialogRef" />
    <LogBodyWeightRecordsDialog :units="units" ref="logBodyWeightRecordsDialogRef" />

    <DeleteDialog
      ref="deleteDialog"
      description="body weight record"
      @delete="deleteBodyWeightRecord"
      @cancel="idToDelete = null"
    />

  </div>
</template>
