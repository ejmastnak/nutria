<script setup>
import { ref } from 'vue'
import FuzzyCombobox from '@/Components/FuzzyCombobox.vue'
import {
  TransitionRoot,
  Dialog,
  DialogPanel,
  DialogTitle,
  DialogDescription,
} from '@headlessui/vue'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
  things: Array,
  input_label: String,
  dialog_title: String,
  button_text: String,
  search_key: {
    type: String,
    default: "name"
  }
})

defineExpose({open})
const emit = defineEmits(['foundAThing'])

const isOpen = ref(false)
const thing = ref(null)

function open() {
  thing.value = null
  isOpen.value = true
}

function cancel() {
  isOpen.value = false
}

function selectAThing(newThing) {
  thing.value = newThing
  confirm()
}

function confirm() {
  if (thing.value) emit('foundAThing', thing.value);
  isOpen.value = false
}

</script>

<template>
  <Dialog :open="isOpen" @close="cancel" class="relative z-50">
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80 overflow-y-auto">
      <DialogPanel class="flex flex-col px-6 pt-6 w-full max-w-md rounded-lg bg-white shadow">

        <DialogTitle class="text-lg font-bold text-gray-600">{{dialog_title}}</DialogTitle>

        <FuzzyCombobox
          :labelText="input_label"
          :searchKey="search_key"
          :targets="things"
          :modelValue="thing"
          @update:modelValue="newValue => selectAThing(newValue)"
        />

        <div class="mt-4 flex -mx-6 px-4 py-3 bg-gray-50 rounded-lg">

          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>

          <PrimaryButton @click="confirm" class="ml-2" >
            {{button_text}}
          </PrimaryButton>

        </div>

      </DialogPanel>
    </div>
  </Dialog>
</template>
