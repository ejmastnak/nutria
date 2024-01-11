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

export const ingredientIntakeRecordForm = reactive(
  useForm({
    id: null,
    ingredient_id: null,
    ingredient: null,
    amount: null,
    unit_id: null,
    unit: null,
    date: null,
    time: null,
    date_time_utc: null,
  }),
)

export const ingredientIntakeRecordsForm = reactive(
  useForm({
    ingredient_intake_records: [],
    ingredientIntakeRecords: [],
    nextId: 1,
    id_idx_mapping: {},
  }),
)

export const mealIntakeRecordForm = reactive(
  useForm({
    id: null,
    meal_id: null,
    meal: null,
    amount: null,
    unit_id: null,
    unit: null,
    date: null,
    time: null,
    date_time_utc: null,
  }),
)

export const mealIntakeRecordsForm = reactive(
  useForm({
    meal_intake_records: [],
    mealIntakeRecords: [],
    nextId: 1,
    id_idx_mapping: {},
  }),
)
