<script setup>
import {ref} from 'vue'
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
  ingredients: Array
})

defineExpose({open})

const emit = defineEmits(['deletedALandmark'])

const isOpen = ref(false)

const ingredientToClone = ref({})

function open() {
  isOpen.value = true
}

function cancel() {
  isOpen.value = false
}

function clone() {
  isOpen.value = false
  if (ingredientToClone.value.id) {
    router.get(route('ingredients.clone', ingredientToClone.value.id))
  }
}

</script>

<template>
  <Dialog :open="isOpen" @close="cancel" class="relative z-50">
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="flex flex-col px-6 pt-6 w-full max-w-sm rounded-lg bg-white shadow">

        <DialogTitle class="text-lg font-bold text-gray-600">Clone ingredient</DialogTitle>

        <form
          @submit.prevent="clone"
          class="mb-4"
        >
          <FuzzyCombobox
            labelText="Search for an ingredient to clone"
            :options="ingredients"
            v-model="ingredientToClone"
          />
        </form>

        <div class="flex mt-auto -mx-6 px-4 py-3 bg-gray-50 rounded-lg">

          <PrimaryButton @click="clone" class="ml-auto" >
            Clone
          </PrimaryButton>

          <SecondaryButton @click="cancel" class="ml-2" >
            Cancel
          </SecondaryButton>
        </div>


      </DialogPanel>
    </div>
  </Dialog>
</template>
