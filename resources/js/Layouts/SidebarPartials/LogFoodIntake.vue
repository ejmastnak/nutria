<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import StoreIngredientIntakeRecordsDialog from '@/Shared/StoreIngredientIntakeRecordsDialog.vue'
import LogMealIntakeDialog from '@/Shared/LogMealIntakeDialog.vue'
import LogFoodListIntakeDialog from '@/Shared/LogFoodListIntakeDialog.vue'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  units: Array,
  ingredients: Array,
  meals: Array,
  food_lists: Array,
})

const isOpen = ref(false)
function open() { isOpen.value = true }
function close() { isOpen.value = false }

const storeIngredientIntakeRecordsDialogRef = ref(null)
const logMealIntakeDialogRef = ref(null)
const logFoodListIntakeDialogRef = ref(null)

</script>

<template>

  <SidebarButton @click="open()" class="flex">
    <SidebarIcon>
      <PencilSquareIcon />
    </SidebarIcon>
    <p class="ml-1.5">Log food intake</p>
  </SidebarButton>

  <!-- For choosing between ingredients, meals, and food lists -->
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
          Which food type are you logging?
        </DialogDescription>

        <ul class="mt-2 space-y-1.5">
          <li>
            <SecondaryButton @click="storeIngredientIntakeRecordsDialogRef.open(null); close()" >
              Log Ingredients
            </SecondaryButton>
          </li>
          <li>
            <SecondaryButton @click="logMealIntakeDialogRef.open(null); close()" >
              Log Meal
            </SecondaryButton>
          </li>
          <li>
            <SecondaryButton @click="logFoodListIntakeDialogRef.open(null); close()" >
              Log Food List
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

  <StoreIngredientIntakeRecordsDialog
    ref="storeIngredientIntakeRecordsDialogRef"
    :ingredients="ingredients"
    :units="units"
  />

  <LogMealIntakeDialog
    :meals="meals"
    :units="units"
    ref="logMealIntakeDialogRef"
  />
  <LogFoodListIntakeDialog
    :food_lists="food_lists"
    :units="units"
    ref="logFoodListIntakeDialogRef"
  />
</template>

