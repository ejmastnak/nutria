<script setup>
import { ref, reactive } from 'vue'
import { getCurrentLocalYYYYMMDD, getUTCDateTime } from '@/utils/GlobalFunctions.js'
import { CalendarIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import NutrientProfileForDateRange from './NutrientProfileForDateRange.vue'

// To preserve variable values when switching between tabs
import { store } from './store.js'

const props = defineProps({
  intake_guidelines: Array,
  nutrient_categories: Array,
})

const fromDateInputRef = ref(null)
const toDateInputRef = ref(null)

function dateAsYYYYMMDDString(date) {
  var month = String((date.getMonth() + 1))
  var day = String(date.getDate())
  var year = date.getFullYear()

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return [year, month, day].join('-');
}

function setDateRangeToToday() {
  store.nutrientProfileTrends.form.from_date = getCurrentLocalYYYYMMDD()
  store.nutrientProfileTrends.form.to_date = getCurrentLocalYYYYMMDD()
}

function setDateRangeToThisWeek() {
  const weekEnd = new Date();
  const weekStart = new Date(
    weekEnd.getFullYear(),
    weekEnd.getMonth(),
    weekEnd.getDate() - 6,
  );
  store.nutrientProfileTrends.form.from_date = dateAsYYYYMMDDString(weekStart)
  store.nutrientProfileTrends.form.to_date = dateAsYYYYMMDDString(weekEnd)
}

function handleFromDateInputEnter() { submit() }

function handleToDateInputEnter() { submit() }

function isValidDate(d) {
  return d instanceof Date && !isNaN(d);
}

function passesValidation() {
  if (store.nutrientProfileTrends.form.from_date === null || store.nutrientProfileTrends.form.from_date === "" || !isValidDate(new Date(store.nutrientProfileTrends.form.from_date))) {
    store.nutrientProfileTrends.clientSideErrors['from_date'] = "The start date must be a valid date."
    fromDateInputRef.value.focus()
    return false
  }

  if (store.nutrientProfileTrends.form.to_date === null || store.nutrientProfileTrends.form.to_date === "" || !isValidDate(new Date(store.nutrientProfileTrends.form.to_date))) {
    store.nutrientProfileTrends.clientSideErrors['to_date'] = "The end date must be a valid date."
    toDateInputRef.value.focus()
    return false
  }

  if (new Date(store.nutrientProfileTrends.form.to_date) < new Date(store.nutrientProfileTrends.form.from_date)) {
    store.nutrientProfileTrends.clientSideErrors['to_date'] = "The end date must be greater than or equal to the start date."
    toDateInputRef.value.focus()
    return false
  }

  return true
}

function submit() {
  if (passesValidation()) {

    store.nutrientProfileTrends.processing = true
    axios.post(route('nutrient-profile-for-date-range'), {
      from_date_time_utc: getUTCDateTime(store.nutrientProfileTrends.form.from_date + " 00:00:00"),
      to_date_time_utc: getUTCDateTime(store.nutrientProfileTrends.form.to_date + " 23:59:59"),
    })
      .then((response) => {
        store.nutrientProfileTrends.nutrientProfiles = response.data.nutrient_profiles ? response.data.nutrient_profiles : []
        store.nutrientProfileTrends.clientSideErrors = {}
        store.nutrientProfileTrends.errors = {}
        store.nutrientProfileTrends.processing = false
        store.nutrientProfileTrends.showProfile = true
        store.nutrientProfileTrends.fromDate = store.nutrientProfileTrends.form.from_date
        store.nutrientProfileTrends.toDate = store.nutrientProfileTrends.form.to_date
      })
      .catch((error) => {
        // Response beyond 2xx received from server
        if (error.response) {
          if (error.response.data.errors) store.nutrientProfileTrends.errors = error.response.data.errors;
          else alert("Server error.");
        } else if (error.request) {
          alert("Error: no response received from server.");
        } else {
          alert("Error.");
        }
        store.nutrientProfileTrends.processing = false
        store.nutrientProfileTrends.showProfile = false
      });
  }

}

</script>

<template>
  <div>

    <p class="max-w-md text-gray-700">
      Use this page to view your average daily nutrient profile for all food consumed over a given time period.
    </p>

    <!-- Custom range -->
    <div class="mt-4 flex items-baseline">

      <!-- From -->
      <div class="w-40">
        <InputLabel for="from-date" value="Start date" />
        <TextInput
          id="from-date"
          ref="fromDateInputRef"
          @keyup.enter="handleFromDateInputEnter"
          class="w-full bg-white"
          type="date"
          v-model="store.nutrientProfileTrends.form.from_date"
          required
        />
        <InputError :message="store.nutrientProfileTrends.clientSideErrors.from_date" />
        <InputError v-for="error in store.nutrientProfileTrends.errors.from_date_time_utc" :message="error" />
      </div>

      <p class="self-start mt-8 mx-3 text-gray-600">to</p>

      <!-- To -->
      <div class="w-40">
        <InputLabel for="to-date" value="End date" />
        <TextInput
          id="to-date"
          ref="toDateInputRef"
          @keyup.enter="handleToDateInputEnter"
          class="w-full bg-white"
          type="date"
          :min="store.nutrientProfileTrends.form.from_date"
          v-model="store.nutrientProfileTrends.form.to_date"
          required
        />
        <InputError :message="store.nutrientProfileTrends.clientSideErrors.to_date" />
        <InputError v-for="error in store.nutrientProfileTrends.errors.to_date_time_utc" :message="error" />
      </div>

    </div>

    <div class="mt-4">
      <SecondaryButton @click="setDateRangeToToday" class="h-fit">
        <CalendarIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
        <p class="ml-1">Today</p>
      </SecondaryButton>

      <SecondaryButton @click="setDateRangeToThisWeek" class="ml-2 h-fit">
        <CalendarIcon class="w-5 h-5 -ml-1 w-6 h-6 text-gray-600 shrink-0"/>
        <p class="ml-1">This Week</p>
      </SecondaryButton>
    </div>

    <div class="mt-4">
      <PrimaryButton 
        type="submit"
        @click="submit"
        :disabled="store.nutrientProfileTrends.processing"
        :class="{ 'opacity-25': store.nutrientProfileTrends.processing }"
      >
        Get nutrient profile
      </PrimaryButton>
    </div>

    <NutrientProfileForDateRange
      v-if="store.nutrientProfileTrends.showProfile"
      class="mt-6"
      :intakeGuidelines="intake_guidelines"
      :nutrientCategories="nutrient_categories"
      :nutrientProfiles="store.nutrientProfileTrends.nutrientProfiles"
      :fromDate="store.nutrientProfileTrends.fromDate"
      :toDate="store.nutrientProfileTrends.toDate"
    />

  </div>
</template>
