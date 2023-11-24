<script setup>
import { TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import { round } from '@/utils/GlobalFunctions.js'

const props = defineProps({
  body_weight_records: Array,
})
</script>

<template>
  <div>
    <table v-if="body_weight_records.length" class="text-sm sm:text-base text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="pl-6 py-3 bg-blue-100 w-10">
            <p class="translate-x-2">Weight</p>
          </th>
          <!-- Unit of weight -->
          <th scope="col" class="pr-6 py-3 bg-blue-100 w-12" />
          <th scope="col" class="px-8 py-3 bg-blue-200">
            Date
          </th>
          <th scope="col" class="px-8 py-3 bg-blue-100" />
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="body_weight_record in body_weight_records" :key="body_weight_record.id"
          class="border-b"
        >
          <td scope="row" class="pr-1 py-4 font-medium text-gray-900 text-right">
            {{Number(body_weight_record.amount).toFixed(1)}}
          </td>
          <td class="text-gray-900">
            {{body_weight_record.unit.name}}
          </td>
          <td class="px-8 py-4">
            {{(new Date(body_weight_record.date)).toLocaleDateString('en-GB', {
              day: 'numeric', month: 'long', year: 'numeric'
            })}}
          </td>
          <td>
            <div class="flex items-center px-8">
              <PencilSquareIcon class="w-5 h-5 hover:text-blue-600" />
              <TrashIcon class="ml-1 w-5 h-5 hover:text-red-700" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else>
      You haven't created any body weight records yet!
    </p>
  </div>
</template>
