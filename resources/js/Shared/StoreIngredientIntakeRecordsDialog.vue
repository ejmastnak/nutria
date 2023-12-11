<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import cloneDeep from "lodash/cloneDeep"
import { PlusCircleIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputError from '@/Components/InputError.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'
import IngredientIntakeRecordDialog from './IngredientIntakeRecordDialog.vue'

const props = defineProps({
  ingredients: Array,
  units: Array,
})

defineExpose({ open })

const form = useForm({
  ingredient_intake_records: [],
  // Maps a record's serial id to the record's array index on last form submit.
  // This is to preserve association with errors sent from backend.
  id_idx_mapping: {},
})

const ingredientIntakeRecords = ref([])
var nextId = 1

const isOpen = ref(false)
function open() {
  isOpen.value = true
  if (ingredientIntakeRecords.value.length === 0) {
    setTimeout(() => {
      addIngredientIntakeRecord()
    }, 0);
  }
}
function close() {
  isOpen.value = false
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
    ingredient_intake_record: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id]],
    ingredient_id: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.ingredient_id'],
    amount: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.amount'],
    unit_id: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.unit_id'],
    date: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.date'],
    time: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.time'],
    date_time_utc: form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.date_time_utc'],
  })
}

function deleteIngredientIntakeRecord(idx) {
  if (idx >= 0 && idx < ingredientIntakeRecords.value.length) ingredientIntakeRecords.value.splice(idx, 1)
}

function addOrUpdateIngredientIntakeRecord(updatedIngredientIntakeRecord) {
  const idx = ingredientIntakeRecords.value.findIndex(ingredientIntakeRecord => ingredientIntakeRecord.id === ingredientIntakeRecordIdToUpdate)

  if (idx >= 0) {  // update existing custom unit
    ingredientIntakeRecords.value[idx].ingredient_intake_record = cloneDeep(updatedIngredientIntakeRecord)
  } else {  // add a new custom unit
    ingredientIntakeRecords.value.push({
      id: nextId,
      ingredient_intake_record: cloneDeep(updatedIngredientIntakeRecord),
    })
    nextId += 1
  }

  ingredientIntakeRecordIdToUpdate = null
}

function submit() {
  form.id_idx_mapping = {}
  ingredientIntakeRecords.value.forEach((record, idx) => form.id_idx_mapping[record.id] = idx)
  form.ingredient_intake_records = ingredientIntakeRecords.value.map(record => record.ingredient_intake_record)
  form.post(route('ingredient-intake-records.store'), {
    onSuccess: () => {
      form.reset()
      form.clearErrors()
      ingredientIntakeRecords.value = []
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
              Add Ingredient intake records
            </DialogTitle>
            <InputError :message="form.errors.ingredient_intake_records" />
          </div>

          <div>

            <ol class="mt-2 space-y-1" >
              <li
                v-for="(ingredientIntakeRecord, idx) in ingredientIntakeRecords" :key="ingredientIntakeRecord.id"
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
                    <span class="font-medium">({{ingredientIntakeRecord.ingredient_intake_record.amount}} {{ingredientIntakeRecord.ingredient_intake_record.unit.name}})</span>
                  </button>
                  <div class="mt-1">
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id]]" />
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.ingredient_id']" />
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.amount']" />
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.unit_id']" />
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.date']" />
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.time']" />
                    <InputError :message="form.errors['ingredient_intake_records.' + form.id_idx_mapping[ingredientIntakeRecord.id] + '.date_time_utc']" />
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

            <p v-if="ingredientIntakeRecords.length === 0" class="mt-3 text-gray-500 text-sm" >
              You haven't added any ingredient intake records.
            </p>

            <!-- Avoiding @click because enter then propogates to autofocus input in IngredientIntakeRecordDialog -->
            <button
              type="button"
              id="add-custom-unit"
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
            <SecondaryButton @click="close" class="ml-auto" >
              Cancel
            </SecondaryButton>

            <PrimaryButton ref="confirmIngredientIntakeRecordsButtonRef" type="button" @click="submit" class="ml-2" >
              Save
            </PrimaryButton>
          </div>

          <IngredientIntakeRecordDialog
            ref="ingredientIntakeRecordDialogRef"
            @confirm="addOrUpdateIngredientIntakeRecord"
            @close="ingredientIntakeRecordIdToUpdate = null"
            :ingredients="ingredients"
            :units="units"
          />

        </form>

      </DialogPanel>
    </div>
  </Dialog>
</template>
