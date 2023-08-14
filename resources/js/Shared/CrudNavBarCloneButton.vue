<script setup>
import CrudNavBarButton from '@/Shared/CrudNavBarButton.vue'
import { DocumentDuplicateIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  text: {
    type: String,
    default: "Clone existing"
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
      <DocumentDuplicateIcon class="h-5 w-5" />
    </template>
  </CrudNavBarButton>
</template>


