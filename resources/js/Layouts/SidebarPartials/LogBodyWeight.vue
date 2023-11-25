<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ScaleIcon } from '@heroicons/vue/24/outline'
import LogBodyWeightDialog from '@/Pages/Data/Partials/LogBodyWeightDialog.vue'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  units: Array,
})
const logBodyWeightDialogRef = ref(null)

</script>

<template>

  <SidebarButton @click="logBodyWeightDialogRef.open()" class="flex">
    <SidebarIcon>
      <ScaleIcon />
    </SidebarIcon>
    <p class="ml-1.5">Log body weight</p>
  </SidebarButton>

  <LogBodyWeightDialog :units="units" ref="logBodyWeightDialogRef" />
</template>

