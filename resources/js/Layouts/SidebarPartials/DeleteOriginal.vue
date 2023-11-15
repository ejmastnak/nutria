<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { TrashIcon } from '@heroicons/vue/24/outline'
import DeleteDialog from "@/Components/DeleteDialog.vue";
import SidebarButton from './SidebarButton.vue'
import SidebarIcon from './SidebarIcon.vue'

const props = defineProps({
  href: String,
  enabled: Boolean,
  thing: String,
})

const deleteDialog = ref(null)

function deleteThing() {
  router.delete(props.href);
}

</script>

<template>

  <SidebarButton :enabled="enabled" @click="deleteDialog.open()" class="flex">
    <SidebarIcon :enabled="enabled">
      <TrashIcon />
    </SidebarIcon>
    <p class="ml-1.5">Delete</p>
  </SidebarButton>

  <DeleteDialog
    ref="deleteDialog"
    :description="thing"
    @delete="deleteThing"
  />

</template>

