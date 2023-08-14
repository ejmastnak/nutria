<script setup>
import { ref, computed } from 'vue'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { QuestionMarkCircleIcon } from '@heroicons/vue/24/outline'

function closePopover(close) {
  close()
}
</script>

<template>
  <Popover class="relative">

    <PopoverButton class="p-0.5 rounded-full focus:outline-none focus:ring-1 focus:ring-blue-700">
      <QuestionMarkCircleIcon class="w-5 h-5 text-gray-600" />
    </PopoverButton>

    <PopoverPanel v-slot="{ close }" class="absolute z-10 px-3 p-2 border border-gray-200 shadow-sm rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-blue-700">
      <div>
        <slot/>
      </div>
      <button @click="closePopover(close)">Close</button>
    </PopoverPanel>
  </Popover>
</template>
