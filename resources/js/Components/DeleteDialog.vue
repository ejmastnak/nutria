<script setup>
import {ref} from 'vue'
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import {
  TransitionRoot,
  Dialog,
  DialogPanel,
  DialogTitle,
  DialogDescription,
} from '@headlessui/vue'

defineProps({ description: String })
defineExpose({ open })
const emit = defineEmits(['delete', 'cancel'])

const isOpen = ref(false)
function open() { isOpen.value = true }

function cancel() {
  isOpen.value = false
  emit('cancel')
}

function closeAndDelete() {
  isOpen.value = false
  emit('delete')
}

</script>

<template>
  <Dialog :open="isOpen" @close="cancel" class="relative z-50">
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg overflow-hidden bg-white shadow">

        <div class="flex">

          <div class="rounded-full bg-red-100 p-1 h-fit">
            <ExclamationTriangleIcon class="w-6 h-6 text-red-700" />
          </div>

          <div class="ml-5">
            <DialogTitle class="text-lg font-bold text-gray-600">
              Delete {{description}}
            </DialogTitle>
            <DialogDescription class="mt-2 text-sm">
              This will permanently delete the {{description}}.
              Are you sure?
            </DialogDescription>
          </div>
        </div>

        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>
          <DangerButton @click="closeAndDelete" class="ml-2" >
            Delete
          </DangerButton>
        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
