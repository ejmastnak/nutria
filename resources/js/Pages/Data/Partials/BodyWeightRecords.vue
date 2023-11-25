<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import LogBodyWeightDialog from './LogBodyWeightDialog.vue'
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
  body_weight_records: Array,
  units: Array,
})

const logBodyWeightDialogRef = ref(null)

let idToDelete = ref(null)
const deleteDialog = ref(null)
function deleteBodyWeightRecord() {
  if (idToDelete.value) {
    router.delete(route('body-weight-records.destroy', idToDelete.value));
  }
  idToDelete.value = null
}


</script>

<template>
  <div>
    <table v-if="body_weight_records.length" class="text-sm sm:text-base text-left text-gray-500">
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
          v-for="body_weight_record in body_weight_records" :key="body_weight_record.id"
          class="border-b hover:bg-gray-100 cursor-pointer"
          @click="logBodyWeightDialogRef.open(body_weight_record)"
        >
          <td scope="row" class="px-8 py-4 font-medium text-gray-900 text-right whitespace-nowrap">
            {{body_weight_record.amount}}
            {{body_weight_record.unit.name}}
          </td>
          <td class="px-8 py-4">
            {{(new Date(body_weight_record.date)).toLocaleDateString('en-GB', {
              day: 'numeric', month: 'long', year: 'numeric'
            })}}
          </td>
          <td>
            <div class="flex items-center px-8">
              <button
                type="button"
                @click.stop="logBodyWeightDialogRef.open(body_weight_record)"
                class="mx-auto p-px rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700"
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
    <p v-else>
      You haven't created any body weight records yet!
    </p>

    <LogBodyWeightDialog :units="units" ref="logBodyWeightDialogRef" />

    <DeleteDialog
      ref="deleteDialog"
      description="body weight record"
      @delete="deleteBodyWeightRecord"
      @cancel="idToDelete = null"
    />

  </div>
</template>
