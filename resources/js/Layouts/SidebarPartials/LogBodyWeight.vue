<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ScaleIcon } from '@heroicons/vue/24/outline'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  units: Array,
})

</script>

<template>

  <SidebarButton @click="" class="flex">
    <SidebarIcon>
      <ScaleIcon />
    </SidebarIcon>
    <p class="ml-1.5">Log body weight</p>
  </SidebarButton>

</template>
