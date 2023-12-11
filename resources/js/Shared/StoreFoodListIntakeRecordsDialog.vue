<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import cloneDeep from "lodash/cloneDeep"
import { PlusCircleIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import FoodListIntakeRecordDialog from './FoodListIntakeRecordDialog.vue'

const props = defineProps({
  food_lists: Array,
  units: Array,
})

defineExpose({ open })

const form = useForm({
  food_list_intake_records: [],
  // Maps a record's serial id to the record's array index on last form submit.
  // This is to preserve association with errors sent from backend.
  id_idx_mapping: {},
})

const foodListIntakeRecords = ref([])
var nextId = 1

const isOpen = ref(false)
function open() {
  isOpen.value = true
  if (foodListIntakeRecords.value.length === 0) {
    setTimeout(() => {
      addFoodListIntakeRecord()
    }, 0);
  }
}
function close() {
  isOpen.value = false
}

const foodListIntakeRecordDialogRef = ref(null)
const addFoodListIntakeRecordButtonRef = ref(null)
var foodListIntakeRecordIdToUpdate = null

function addFoodListIntakeRecord() {
  foodListIntakeRecordDialogRef.value.open(null, {});
}

function editFoodListIntakeRecord(foodListIntakeRecord, idx) {
  foodListIntakeRecordIdToUpdate = foodListIntakeRecord.id
  foodListIntakeRecordDialogRef.value.open(foodListIntakeRecord.food_list_intake_record, {
    food_list_intake_record: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id]],
    food_list_id: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.food_list_id'],
    amount: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.amount'],
    unit_id: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.unit_id'],
    date: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.date'],
    time: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.time'],
    date_time_utc: form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.date_time_utc'],
  })
}

function deleteFoodListIntakeRecord(idx) {
  if (idx >= 0 && idx < foodListIntakeRecords.value.length) foodListIntakeRecords.value.splice(idx, 1)
}

function addOrUpdateFoodListIntakeRecord(updatedFoodListIntakeRecord) {
  const idx = foodListIntakeRecords.value.findIndex(foodListIntakeRecord => foodListIntakeRecord.id === foodListIntakeRecordIdToUpdate)

  if (idx >= 0) {  // update existing custom unit
    foodListIntakeRecords.value[idx].food_list_intake_record = cloneDeep(updatedFoodListIntakeRecord)
  } else {  // add a new custom unit
    foodListIntakeRecords.value.push({
      id: nextId,
      food_list_intake_record: cloneDeep(updatedFoodListIntakeRecord),
    })
    nextId += 1
  }

  foodListIntakeRecordIdToUpdate = null
}

function submit() {
  form.id_idx_mapping = {}
  foodListIntakeRecords.value.forEach((record, idx) => form.id_idx_mapping[record.id] = idx)
  form.food_list_intake_records = foodListIntakeRecords.value.map(record => record.food_list_intake_record)
  form.post(route('food-list-intake-records.store'), {
    onSuccess: () => {
      form.reset()
      form.clearErrors()
      foodListIntakeRecords.value = []
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
              Add Food List intake records
            </DialogTitle>
            <InputError :message="form.errors.food_list_intake_records" />
          </div>

          <div>

            <ol class="mt-2 space-y-1" >
              <li
                v-for="(foodListIntakeRecord, idx) in foodListIntakeRecords" :key="foodListIntakeRecord.id"
                class="flex items-start"
              >
                <div>
                  <button
                    type="button"
                    @mouseup="editFoodListIntakeRecord(foodListIntakeRecord, idx)"
                    @keyup.enter="editFoodListIntakeRecord(foodListIntakeRecord, idx)"
                    @click.prevent
                    class="hover:underline text-left"
                  >
                    {{foodListIntakeRecord.food_list_intake_record.food_list.name}}
                    <span class="font-medium">({{foodListIntakeRecord.food_list_intake_record.amount}} {{foodListIntakeRecord.food_list_intake_record.unit.name}})</span>
                  </button>
                  <div class="mt-1">
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id]]" />
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.food_list_id']" />
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.amount']" />
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.unit_id']" />
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.date']" />
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.time']" />
                    <InputError :message="form.errors['food_list_intake_records.' + form.id_idx_mapping[foodListIntakeRecord.id] + '.date_time_utc']" />
                  </div>
                </div>

                <button
                  type="button"
                  @click.stop="editFoodListIntakeRecord(foodListIntakeRecord, idx)"
                  class="ml-auto"
                >
                  <PencilSquareIcon class="-ml-1 w-6 h-6 text-gray-600 hover:text-blue-700 shrink-0" />
                </button>
                <button
                  type="button"
                  @click.stop="deleteFoodListIntakeRecord(idx)"
                >
                  <TrashIcon class="ml-px w-6 h-6 text-gray-600 hover:text-red-700 shrink-0" />
                </button>

              </li>
            </ol>

            <p v-if="foodListIntakeRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
              You haven't added any food list intake records.
            </p>

            <!-- Avoiding @click because enter then propogates to autofocus input in FoodListIntakeRecordDialog -->
            <button
              type="button"
              id="add-custom-unit"
              @mouseup="addFoodListIntakeRecord"
              @keyup.enter="addFoodListIntakeRecord"
              @click.prevent
              ref="addFoodListIntakeRecordButtonRef"
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

            <PrimaryButton ref="confirmFoodListIntakeRecordsButtonRef" type="button" @click="submit" class="ml-2" >
              Save
            </PrimaryButton>
          </div>

          <FoodListIntakeRecordDialog
            ref="foodListIntakeRecordDialogRef"
            @confirm="addOrUpdateFoodListIntakeRecord"
            @close="foodListIntakeRecordIdToUpdate = null"
            :food_lists="food_lists"
            :units="units"
          />

        </form>

      </DialogPanel>
    </div>
  </Dialog>
</template>
