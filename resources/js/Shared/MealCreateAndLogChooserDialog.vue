<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PlainButton from '@/Components/PlainButton.vue'
import FindAThing from '@/Components/FindAThing.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

import { ingredientIntakeRecordsForm } from '@/Shared/store.js'
import { mealIntakeRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  meals: Array,
})

defineExpose({ open })

const isOpen = ref(false)
function open() { isOpen.value = true }
function close() { isOpen.value = false }

function create() {
  router.get(route('meals.create-and-log'));
}

function clone() {
  searchDialog.value.open()
}

const searchDialog = ref(null)
function cloneAndLog(meal) {
  if (meal && meal.id) {
    router.get(route('meals.clone-and-log', meal.id)); 
  }
}

</script>

<template>

  <!-- For choosing between ingredients and meals -->
  <Dialog
    :open="isOpen"
    @close="close"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow max-h-[600px] overflow-auto">

        <DialogTitle class="text-lg font-bold text-gray-600">
          Create and log a new meal
        </DialogTitle>

        <ul class="mt-2.5 space-y-2">
          <li>
            <PrimaryButton @click="create" >
              Create from scratch
            </PrimaryButton>
          </li>
          <li>
            <SecondaryButton @click="clone" >
              Clone an existing meal
            </SecondaryButton>
          </li>
        </ul>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton ref="confirmCustomUnitsButtonRef" @click="close" class="ml-auto" >
            Cancel
          </SecondaryButton>
        </div>

        <FindAThing
          ref="searchDialog"
          :things="meals"
          dialog_title="Search for a meal to clone"
          button_text="Go"
          @foundAThing="cloneAndLog"
        />

      </DialogPanel>
    </div>
  </Dialog>

</template>

