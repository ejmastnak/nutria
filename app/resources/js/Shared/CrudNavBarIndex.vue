<script setup>
import { Link } from '@inertiajs/vue3'
import CrudNavBarLink from '@/Shared/CrudNavBarLink.vue'
import { HomeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  href: String,
  text: {
    type: String,
    default: "All"
  },
  enabled: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <CrudNavBarLink
    :href="href"
    :text="text"
    :enabled="enabled"
  >
    <template v-slot:icon>
      <HomeIcon class="h-5 w-5" />
    </template>
  </CrudNavBarLink>
</template>

