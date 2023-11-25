<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import FindAThing from '@/Components/FindAThing.vue'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  things: Array,
  thing: String,
  route_basename: String,
})

const searchDialog = ref(null)

function search(thing) {
  if (thing && thing.id) router.get(route(props.route_basename + '.show', thing.id));
}

</script>

<template>

  <SidebarButton @click="searchDialog.open()" class="flex">
    <SidebarIcon>
      <MagnifyingGlassIcon />
    </SidebarIcon>
    <p class="ml-1.5">Find another</p>
  </SidebarButton>

  <FindAThing
    ref="searchDialog"
    :things="things"
    :dialog_title="'Search for another ' + thing"
    button_text="Okay"
    @foundAThing="search"
  />

</template>

