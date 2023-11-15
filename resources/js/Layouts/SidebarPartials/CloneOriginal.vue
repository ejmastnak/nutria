<script setup>
import { Link } from '@inertiajs/vue3'
import { DocumentDuplicateIcon } from '@heroicons/vue/24/outline'
import SidebarLink from './SidebarLink.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  href: String,
  enabled: Boolean,
})
</script>

<template>
  <SidebarLink :enabled="enabled" :href="href">
    <SidebarIcon :enabled="enabled">
      <DocumentDuplicateIcon />
    </SidebarIcon>
    <p class="ml-1.5">Clone</p>
  </SidebarLink>
</template>

