<script setup>
import { onMounted, ref } from 'vue';
import { roundNonZero } from '@/utils/GlobalFunctions.js'

defineProps({
    modelValue: [String, Number],
    type: {
        type: String,
        default: "text",
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

</script>

<template>
    <input
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
        :value="modelValue"
        :type="type"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
