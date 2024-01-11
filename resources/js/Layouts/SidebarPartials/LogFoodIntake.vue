<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import LogIngredientIntakeRecordDialog from '@/Shared/LogIngredientIntakeRecordDialog.vue'
import LogIngredientIntakeRecordsDialog from '@/Shared/LogIngredientIntakeRecordsDialog.vue'
import LogMealIntakeRecordDialog from '@/Shared/LogMealIntakeRecordDialog.vue'
import LogMealIntakeRecordsDialog from '@/Shared/LogMealIntakeRecordsDialog.vue'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

import { ingredientIntakeRecordsForm } from '@/Shared/store.js'
import { mealIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  units: Array,
  ingredients: Array,
  meals: Array,
})

const isOpen = ref(false)
function open() { isOpen.value = true }
function close() { isOpen.value = false }

const logIngredientIntakeRecordDialogRef = ref(null)
const logIngredientIntakeRecordsDialogRef = ref(null)
const logMealIntakeRecordDialogRef = ref(null)
const logMealIntakeRecordsDialogRef = ref(null)

function logIngredientIntake() {
  if (ingredientIntakeRecordsForm.ingredientIntakeRecords.length >= 1) {
    logIngredientIntakeRecordsDialogRef.value.open()
  } else {
    logIngredientIntakeRecordDialogRef.value.open(null)
  }
}

function logMealIntake() {
  if (mealIntakeRecordsForm.mealIntakeRecords.length >= 1) {
    logMealIntakeRecordsDialogRef.value.open()
  } else {
    logMealIntakeRecordDialogRef.value.open(null)
  }
}

</script>

<template>

  <SidebarButton @click="open()" class="flex">
    <SidebarIcon>
      <PencilSquareIcon />
    </SidebarIcon>
    <p class="ml-1.5">Log food intake</p>
  </SidebarButton>

  <!-- For choosing between ingredients and meals -->
  <Dialog
    :open="isOpen"
    @close="close"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow max-h-[600px] overflow-auto">

        <DialogTitle class="text-lg font-bold text-gray-600">
          Log food intake
        </DialogTitle>

        <DialogDescription class="text-gray-600">
          What are you logging?
        </DialogDescription>

        <ul class="mt-2 space-y-1.5">
          <li>
            <SecondaryButton @click="logIngredientIntake" >
              Log Ingredients
            </SecondaryButton>
          </li>
          <li>
            <SecondaryButton @click="logMealIntake" >
              Log Meals
            </SecondaryButton>
          </li>
        </ul>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton ref="confirmCustomUnitsButtonRef" @click="close" class="ml-auto" >
            Close
          </SecondaryButton>
        </div>
      </DialogPanel>
    </div>
  </Dialog>

  <LogIngredientIntakeRecordDialog
    :ingredients="ingredients"
    :units="units"
    ref="logIngredientIntakeRecordDialogRef"
  />
  <LogIngredientIntakeRecordsDialog
    :ingredients="ingredients"
    :units="units"
    ref="logIngredientIntakeRecordsDialogRef"
  />

  <LogMealIntakeRecordDialog
    :meals="meals"
    :units="units"
    ref="logMealIntakeRecordDialogRef"
  />
  <LogMealIntakeRecordsDialog
    :meals="meals"
    :units="units"
    ref="logMealIntakeRecordsDialogRef"
  />

</template>

