<script setup>
import CrudNavBarButton from '@/Shared/CrudNavBarButton.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  text: {
    type: String,
    default: "Find another"
  },
  enabled: {
    type: Boolean,
    default: true
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
      <MagnifyingGlassIcon class="h-5 w-5" />
    </template>
  </CrudNavBarButton>
</template>
