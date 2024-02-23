<script setup>
import { ref, computed } from 'vue'
import { currentLocalDate } from '@/utils/GlobalFunctions.js'
import { XMarkIcon, MagnifyingGlassIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { TransitionRoot, Dialog, DialogPanel, DialogTitle, DialogDescription, } from '@headlessui/vue'

const emit = defineEmits(['search', 'clear'])
const fromDate = ref("")
const toDate = ref("")

defineExpose({ open })
const isOpen = ref(false)
function open() { isOpen.value = true }
function cancel() {
  isOpen.value = false
}
function search() {
  isOpen.value = false
  emit('search', {
    fromDate: fromDate.value,
    toDate: toDate.value,
  })
}

function dateAsYYYYMMDDString(date) {
  var month = String((date.getMonth() + 1))
  var day = String(date.getDate())
  var year = date.getFullYear()

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return [year, month, day].join('-');
}

function setDateRangeToToday() {
  fromDate.value = currentLocalDate()
  toDate.value = currentLocalDate()
}

function setDateRangeToThisWeek() {
  const weekEnd = new Date();
  const weekStart = new Date(
    weekEnd.getFullYear(),
    weekEnd.getMonth(),
    weekEnd.getDate() - 6,
  );
  fromDate.value = dateAsYYYYMMDDString(weekStart)
  toDate.value = dateAsYYYYMMDDString(weekEnd)
}

function clearFilters() {
  fromDate.value = ""
  toDate.value = ""
  isOpen.value = false
  emit('clear')
}

</script>

<template>

  <Dialog :open="isOpen" @close="cancel" class="relative z-50">
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-blue-50/80">
      <DialogPanel class="px-6 pt-6 w-full max-w-lg rounded-lg overflow-hidden bg-white shadow">

        <div>
          <DialogTitle class="text-lg font-bold text-gray-600">
            Filter by date
          </DialogTitle>
          <DialogDescription class="text-sm text-gray-600">
            <ul class="mt-1 list-disc ml-5">
              <li>Set <span class="font-medium">start date</span> to show all records after <span class="font-medium">start date</span>.</li>
              <li>Set <span class="font-medium">end date</span> to show all records up to <span class="font-medium">end date</span>.</li>
              <li>Set both <span class="font-medium">start</span> and <span class="font-medium">end date</span> to show all records in a date range.</li>
              <li>Leave <span class="font-medium">start</span> and <span class="font-medium">end date</span> empty to show all records.</li>
            </ul>
          </DialogDescription>
        </div>

        <div class="mt-4">

          <!-- Date inputs -->
          <div class="flex items-baseline">
            <!-- From -->
            <div class="w-40">
              <InputLabel for="from-date" value="Start date" />
              <TextInput
                id="from-date"
                class="w-full bg-white"
                type="date"
                v-model="fromDate"
              />
            </div>
            <p class="self-start mt-8 mx-3 text-gray-600">to</p>
            <!-- To -->
            <div class="w-40">
              <InputLabel for="to-date" value="End date" />
              <TextInput
                id="to-date"
                class="w-full bg-white"
                type="date"
                :min="toDate"
                v-model="toDate"
              />
            </div>
          </div>

          <!-- Buttons -->
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

        </div>

        <div class="flex mt-6 -mx-6 px-4 py-3 bg-gray-50">

          <SecondaryButton @click="cancel" class="ml-auto" >
            Cancel
          </SecondaryButton>
          <SecondaryButton @click="clearFilters" class="ml-2">
            Clear filters
          </SecondaryButton>
          <PrimaryButton @click="search" class="ml-2" >
            Apply filter
          </PrimaryButton>

        </div>

      </DialogPanel>
    </div>
  </Dialog>

</template>
