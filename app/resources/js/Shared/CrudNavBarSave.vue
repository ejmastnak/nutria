<script setup>
import CrudNavBarButton from '@/Shared/CrudNavBarButton.vue'
import { ArchiveBoxArrowDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  text: {
    type: String,
    default: "Save"
  },
  enabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['wasClicked'])

</script>

<template>
  <CrudNavBarButton
    :enabled="enabled"
    :text="text"
    @wasClicked="emit('wasClicked')"
  >
    <template v-slot:icon>
      <ArchiveBoxArrowDownIcon class="h-5 w-5" />
    </template>
  </CrudNavBarButton>
</template>


