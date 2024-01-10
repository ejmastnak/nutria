<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ScaleIcon } from '@heroicons/vue/24/outline'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'
import LogBodyWeightRecordDialog from '@/Shared/LogBodyWeightRecordDialog.vue'
import LogBodyWeightRecordsDialog from '@/Shared/LogBodyWeightRecordsDialog.vue'
import { bodyWeightRecordsForm } from '@/Shared/store.js'

const props = defineProps({
  units: Array,
})

const logBodyWeightRecordDialogRef = ref(null)
const logBodyWeightRecordsDialogRef = ref(null)

function logBodyWeight() {
  if (bodyWeightRecordsForm.bodyWeightRecords.length >= 1) {
    logBodyWeightRecordsDialogRef.value.open()
  } else {
    logBodyWeightRecordDialogRef.value.open(null)
  }
}

</script>

<template>

  <SidebarButton @click="logBodyWeight" class="flex">
    <SidebarIcon>
      <ScaleIcon />
    </SidebarIcon>
    <p class="ml-1.5">Log body weight</p>
  </SidebarButton>

  <LogBodyWeightRecordDialog :units="units" ref="logBodyWeightRecordDialogRef" />
  <LogBodyWeightRecordsDialog :units="units" ref="logBodyWeightRecordsDialogRef" />

</template>
