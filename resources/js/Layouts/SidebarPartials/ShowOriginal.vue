<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowUturnLeftIcon } from '@heroicons/vue/24/outline'
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
      <ArrowUturnLeftIcon />
    </SidebarIcon>
    <p class="ml-1.5">Original</p>
  </SidebarLink>
</template>

