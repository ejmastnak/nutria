<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
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
  goRoute: String,
  label: String,
  title: String,
  action: String
})

defineExpose({open})

const isOpen = ref(false)
const targetThing = ref({})

function open() {
  isOpen.value = true
}

function cancel() {
  isOpen.value = false
}

function go() {
  isOpen.value = false
  if (targetThing.value.id) {
    router.get(route(props.goRoute, targetThing.value.id))
  }
}

</script>

<template>
  <Dialog :open="isOpen" @close="cancel" class="relative z-50">
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="flex flex-col px-6 pt-6 w-full max-w-md rounded-lg bg-white shadow">

        <DialogTitle class="text-lg font-bold text-gray-600">{{title}}</DialogTitle>

        <form
          @submit.prevent="go"
          class="mb-4"
        >
          <FuzzyCombobox
            :labelText="label"
            class="w-96"
            :options="things"
            v-model="targetThing"
          />
        </form>

        <div class="flex mt-auto -mx-6 px-4 py-3 bg-gray-50 rounded-lg">

          <PrimaryButton @click="go" class="ml-auto" >
            {{action}}
          </PrimaryButton>

          <SecondaryButton @click="cancel" class="ml-2" >
            Cancel
          </SecondaryButton>
        </div>


      </DialogPanel>
    </div>
  </Dialog>
</template>
