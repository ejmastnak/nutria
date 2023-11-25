<!-- Provides an overview of an ingredient's custom units -->
<script setup>
import { ref, computed } from 'vue'
import { round, roundNonZero } from '@/utils/GlobalFunctions.js'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription } from '@headlessui/vue'

const props = defineProps({
  custom_units: Array,
})

defineExpose({ open })

const isOpen = ref(false)
function open() { isOpen.value = true }
function close() { isOpen.value = false }

</script>

<template>

  <Dialog
    :open="isOpen"
    @close="close"
    class="relative z-50"
  >
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow max-h-[600px] overflow-auto">

        <div>
          <DialogTitle class="text-lg font-bold text-gray-600">
            Ingredient custom units
          </DialogTitle>
        </div>

        <ol class="mt-2 space-y-1" >
          <li v-for="(custom_unit, idx) in custom_units" :key="custom_unit.id" >
            {{roundNonZero(custom_unit.custom_unit_amount, 2)}}
            {{custom_unit.name}}
            ({{roundNonZero(custom_unit.custom_mass_amount, 2)}} {{custom_unit.custom_mass_unit.name}})
          </li>
        </ol>

        <p v-if="custom_units.length === 0" class="mt-3 text-gray-500 text-sm" >
          This ingredient has no custom units yet.
        </p>

        <!-- Cancel/Confirm buttons -->
        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <PrimaryButton ref="confirmCustomUnitsButtonRef" @click="close" class="ml-2" >
            Okay
          </PrimaryButton>
        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
