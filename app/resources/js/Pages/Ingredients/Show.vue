<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import { TrashIcon, DocumentDuplicateIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import NutrientProfile from '@/Shared/NutrientProfile.vue'
import FuzzyCombobox from '@/Shared/FuzzyCombobox.vue'
import DeleteDialog from '@/Shared/DeleteDialog.vue'
import SearchForThingAndGo from '@/Shared/SearchForThingAndGo.vue'
import CrudNavBar from '@/Shared/CrudNavBar.vue'
import CrudNavBarEdit from '@/Shared/CrudNavBarEdit.vue'
import CrudNavBarCloneLink from '@/Shared/CrudNavBarCloneLink.vue'
import CrudNavBarDelete from '@/Shared/CrudNavBarDelete.vue'
import CrudNavBarCreate from '@/Shared/CrudNavBarCreate.vue'
import CrudNavBarIndex from '@/Shared/CrudNavBarIndex.vue'
import CrudNavBarSearch from '@/Shared/CrudNavBarSearch.vue'
import PrimaryLinkButton from '@/Components/PrimaryLinkButton.vue'
import SecondaryLinkButton from '@/Components/SecondaryLinkButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
  ingredient: Object,
  nutrient_profile: Array,
  nutrient_categories: Array,
  ingredients: Array,
  can_edit: Boolean,
  can_clone: Boolean,
  can_delete: Boolean,
  can_create: Boolean
})

const howManyGrams = ref("100");
const defaultMassInGrams = 100

const searchDialog = ref(null)
const deleteDialog = ref(null)

const searchIngredient = ref({})
function search() {
  router.get(route('ingredients.show', searchIngredient.value.id))
}

</script>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
  layout: AppLayout,
}
</script>

<template>
  <div>
    <Head :title="ingredient.name" />

    <CrudNavBar>
      <CrudNavBarIndex :href="route('ingredients.index')" />
      <CrudNavBarSearch @wasClicked="searchDialog.open()" thing="ingredient" />
      <CrudNavBarCreate :enabled="can_create" :href="route('ingredients.create')" />
      <div class="flex ml-auto">
        <CrudNavBarEdit :enabled="can_edit" :href="route('ingredients.edit', ingredient.id)" />
        <CrudNavBarCloneLink :enabled="can_clone" :href="route('ingredients.clone', ingredient.id)" />
        <CrudNavBarDelete :enabled="can_delete" @wasClicked="deleteDialog.open(ingredient.id)" />
      </div>
    </CrudNavBar>

    <div class="mt-8">

      <h1 class="text-xl w-2/3">{{ingredient.name}}</h1>

      <!-- Ingredient category -->
      <div class="flex">
        <div class="mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          Ingredient
        </div>

        <div class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.ingredient_category.name}}
        </div>

        <div v-if="ingredient.density_g_per_ml" class="ml-2 mt-2 bg-blue-50 px-3 py-1 rounded-xl font-medium border border-gray-300 text-gray-800 text-sm w-fit">
          {{ingredient.density_g_per_ml}} g/ml
        </div>

      </div>


    </div>

    <section class="mt-10">

      <div class="flex">
        <h2 class="text-lg">Nutrient profile</h2>

        <!-- How many grams text input -->
        <div class="ml-auto flex items-baseline text-gray-500 text-md">
          <div>
            <InputLabel for="howManyGrams" value="Meal mass" class="sr-only" />
            <TextInput
              id="howManyGrams"
              type="number"
              min="0"
              class="mt-1 mx-1.5 pl-1 pr-0 text-right text-lg w-20 font-bold block py-px"
              v-model="howManyGrams"
            />
          </div>
          <p>grams</p>
        </div>
      </div>

      <NutrientProfile
        class="w-full mt-4"
        :nutrient_profile="nutrient_profile"
        :nutrient_categories="nutrient_categories"
        :howManyGrams="Number(howManyGrams)"
        :defaultMassInGrams="Number(defaultMassInGrams)"
      />

    </section>

    <SearchForThingAndGo
      ref="searchDialog"
      :things="ingredients"
      goRoute="ingredients.show"
      label="Search for another ingredient"
      title=""
      action="Go"
    />

    <DeleteDialog ref="deleteDialog" deleteRoute="ingredients.destroy" thing="ingredient" />

  </div>
</template>
