<script setup>
import { ref } from 'vue'
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
import MyLink from '@/Components/MyLink.vue'

const props = defineProps({
  deleteRoute: String,
  thing: String
})

defineExpose({ open })

const cancelButton = ref(null)

const emit = defineEmits(['delete'])
const isOpen = ref(false)

var idToDelete = -1

// This is a bandaid I've added on for warning user when deleting a meal if a
// linked ingredient created from that meal will also be deleted.
var ingredient = null

function open(id, optionalIngredient=null) {
  idToDelete = id
  ingredient = optionalIngredient
  isOpen.value = true
}

function closeWithoutDeleting() {
  isOpen.value = false
  idToDelete = -1
  ingredient = null
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
  ingredient = null
  idToDelete = -1
}

</script>

<template>
  <Dialog :initialFocus="cancelButton" :open="isOpen" @close="closeWithoutDeleting" class="relative z-50">
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

              <!-- Hardcoded addon when deleting meals with a linked ingredient -->
              <div v-if="ingredient" class="mt-2 text-gray-500">
                One ingredient based on this meal, <MyLink :colored="true" :href="route('ingredients.show', ingredient.id)">{{ingredient.name}}</MyLink>, will also be deleted if you delete this meal.
              </div>

            </DialogDescription>
          </div>
        </div>

        <div class="flex mt-5 -mx-6 px-4 py-3 bg-gray-50">
          <SecondaryButton ref="cancelButton" @click="closeWithoutDeleting" class="ml-auto" >
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
