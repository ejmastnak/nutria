<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CreateOrEdit from './Partials/CreateOrEdit.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarView from '@/Shared/CrudNavBarView.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarCloneButton from '@/Shared/CrudNavBarCloneButton.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import H1 from '@/Components/H1ForCrud.vue'

const props = defineProps({
  ingredient: Object,
  ingredient_categories: Array,
  nutrients: Array,
  nutrient_categories: Array,
  units: Array,
  user_ingredients: Array,
  can_create: Boolean,
  can_view: {type: Boolean, default: false},
  can_clone: {type: Boolean, default: false},
  can_update: {type: Boolean, default: false},
  can_delete: {type: Boolean, default: false},
})

const searchDialog = ref(null)
const cloneExistingDialog = ref(null)

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>
    <Head title="New Ingredient" />
    <H1 class="mt-8" text="New Ingredient" />
    <p v-if="ingredient" class="text-gray-700">(Cloned from {{ingredient.name}})</p>

    <CreateOrEdit
      :ingredient="ingredient"
      :ingredient_categories="ingredient_categories"
      :nutrients="nutrients"
      :nutrient_categories="nutrient_categories"
      :units="units"
      :create="true"
    />

  </div>
</template>
