import { reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'

export const bodyWeightRecordForm = reactive(
  useForm({
    id: null,
    amount: null,
    unit_id: null,
    unit: null,
    date: null,
    time: null,
    date_time_utc: null,
  }),
)

export const bodyWeightRecordsForm = reactive(
  useForm({
    body_weight_records: [],
    bodyWeightRecords: [],
    nextId: 1,
    id_idx_mapping: {},
  }),
)
