<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ScaleIcon } from '@heroicons/vue/24/outline'
import StoreBodyWeightRecordsDialog from '@/Shared/StoreBodyWeightRecordsDialog.vue'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  units: Array,
})
const storeBodyWeightRecordsDialogRef = ref(null)

</script>

<template>

  <SidebarButton @click="storeBodyWeightRecordsDialogRef.open()" class="flex">
    <SidebarIcon>
      <ScaleIcon />
    </SidebarIcon>
    <p class="ml-1.5">Log body weight</p>
  </SidebarButton>

  <StoreBodyWeightRecordsDialog :units="units" ref="storeBodyWeightRecordsDialogRef" />
</template>

