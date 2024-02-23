<script setup>
import { Link } from '@inertiajs/vue3'
import { HomeIcon } from '@heroicons/vue/24/outline'
import SidebarLink from './SidebarLink.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  href: String,
  thing: String,
})
</script>

<template>
  <SidebarLink :href="href">
    <SidebarIcon>
      <HomeIcon />
    </SidebarIcon>
    <p class="ml-1.5">All {{thing}}s</p>
  </SidebarLink>
</template>

