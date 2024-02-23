<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { MagnifyingGlassPlusIcon } from '@heroicons/vue/24/outline'
import FindAThing from '@/Components/FindAThing.vue'
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  things: Array,
  thing: String,
  route_basename: String,
  enabled: Boolean,
})

const searchDialog = ref(null)

function search(thing) {
  if (thing && thing.id) router.get(route(props.route_basename + '.clone', thing.id));
}

</script>

<template>

  <SidebarButton
    @click="searchDialog.open()"
    :enabled="enabled"
  >
    <SidebarIcon :enabled="enabled">
      <MagnifyingGlassPlusIcon />
    </SidebarIcon>
    <p class="ml-1.5">Clone an existing {{thing}}</p>
  </SidebarButton>

  <FindAThing
    ref="searchDialog"
    :things="things"
    :dialog_title="'Search for a' + (thing === 'ingredient' ? 'n ' : ' ') + thing + ' to clone'"
    button_text="Okay"
    @foundAThing="search"
  />

</template>

