import { reactive } from 'vue'

export const store = reactive({
  nutrientProfileTrends: {
    nutrientProfiles: [],
    showProfile: false,
    processing: false,
    fromDate: null,
    toDate: null,
    form: {
      from_date: null,
      to_date: null,
    },
    clientSideErrors: {},
    errors: {},
  }
})
