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

const form = ref({
  from_date: null,
  to_date: null,
})

const clientSideErrors = ref({})
const errors = ref({})

function setDateRangeToToday() {
  form.value.from_date = getCurrentLocalYYYYMMDD()
  form.value.to_date = getCurrentLocalYYYYMMDD()
}

function setDateRangeToThisWeek() {
  const weekEnd = new Date();
  const weekStart = new Date(
    weekEnd.getFullYear(),
    weekEnd.getMonth(),
    weekEnd.getDate() - 6,
  );
  form.value.from_date = dateAsYYYYMMDDString(weekStart)
  form.value.to_date = dateAsYYYYMMDDString(weekEnd)
}

function handleFromDateInputEnter() { submit() }
function handleToDateInputEnter() { submit() }

function isValidDate(d) {
  return d instanceof Date && !isNaN(d);
}

function passesValidation() {
  if (form.value.from_date === null || form.value.from_date === "" || !isValidDate(new Date(form.value.from_date))) {
    clientSideErrors.value['from_date'] = "The start date must be a valid date."
    fromDateInputRef.value.focus()
    return false
  }

  if (form.value.to_date === null || form.value.to_date === "" || !isValidDate(new Date(form.value.to_date))) {
    clientSideErrors.value['to_date'] = "The end date must be a valid date."
    toDateInputRef.value.focus()
    return false
  }

  if (new Date(form.value.to_date) < new Date(form.value.from_date)) {
    clientSideErrors.value['to_date'] = "The end date must be greater than or equal to the start date."
    toDateInputRef.value.focus()
    return false
  }

  return true
}

const nutrientProfiles = ref([])
const showProfile = ref(false)
const processing = ref(false)

const fromDate = ref(null)
const toDate = ref(null)

function submit() {
  if (passesValidation()) {

    processing.value = true
    axios.post(route('nutrient-profile-for-date-range'), {
      from_date_time_utc: getUTCDateTime(form.value.from_date + " 00:00:00"),
      to_date_time_utc: getUTCDateTime(form.value.to_date + " 23:59:59"),
    })
      .then((response) => {
        nutrientProfiles.value = response.data.nutrient_profiles ? response.data.nutrient_profiles : []
        clientSideErrors.value = {}
        errors.value = {}
        processing.value = false
        showProfile.value = true
        fromDate.value = form.value.from_date
        toDate.value = form.value.to_date
      })
      .catch((error) => {
        // Response beyond 2xx received from server
        if (error.response) {
          if (error.response.data.errors) errors.value = error.response.data.errors;
          else alert("Server error.");
        } else if (error.request) {
          alert("Error: no response received from server.");
        } else {
          alert("Error.");
        }
        processing.value = false
        showProfile.value = false
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
          v-model="form.from_date"
          required
        />
        <InputError :message="clientSideErrors.from_date" />
        <InputError v-for="error in errors.from_date_time_utc" :message="error" />
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
          :min="form.from_date"
          v-model="form.to_date"
          required
        />
        <InputError :message="clientSideErrors.to_date" />
        <InputError v-for="error in errors.to_date_time_utc" :message="error" />
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
        :disabled="processing"
        :class="{ 'opacity-25': processing }"
      >
        Get nutrient profile
      </PrimaryButton>
    </div>

    <NutrientProfileForDateRange
      v-if="showProfile"
      class="mt-6"
      :intakeGuidelines="intake_guidelines"
      :nutrientCategories="nutrient_categories"
      :nutrientProfiles="nutrientProfiles"
      :fromDate="fromDate"
      :toDate="toDate"
    />

  </div>
</template>
