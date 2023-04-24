<script setup>
import {ref} from 'vue'
import { router } from '@inertiajs/vue3'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import {
  TransitionRoot,
  Dialog,
  DialogPanel,
  DialogTitle,
  DialogDescription,
} from '@headlessui/vue'
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
  deleteRoute: String,
  thing: String
})
defineExpose({ open })

const emit = defineEmits(['delete'])
const isOpen = ref(false)

let idToDelete = -1

function open(id) {
  idToDelete = id
  isOpen.value = true
}

function closeWithoutDeleting() {
  isOpen.value = false
  idToDelete = -1
}

function closeAndDelete() {
  isOpen.value = false
  if (idToDelete >= 0) {
    const id = idToDelete;  // save deleted id before overwriting with -1
    router.delete(route(props.deleteRoute, idToDelete), {
      onSuccess: () => {
        emit('delete', id)
      }
    });
  }
  idToDelete = -1
}

</script>

<template>
  <Dialog :open="isOpen" @close="closeWithoutDeleting" class="relative z-50">
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-sm rounded-lg overflow-hidden bg-white shadow">

        <div class="flex">
          <div class="rounded-full bg-red-100 p-1 h-fit">
            <ExclamationTriangleIcon class="w-6 h-6 text-red-700" />
          </div>

          <div class="ml-5">
            <DialogTitle class="text-lg font-bold text-gray-600">
              Delete {{thing}}
            </DialogTitle>
            <DialogDescription class="mt-2 text-sm">
              This will permanently delete the {{thing}}.
              Are you sure?
            </DialogDescription>
          </div>
        </div>

        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton @click="closeWithoutDeleting" class="ml-auto" >
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
