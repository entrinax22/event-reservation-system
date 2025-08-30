<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-4xl rounded-lg bg-gray-900 p-6 shadow-lg mx-4">
      <!-- Header -->
      <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-2">
        <h2 class="text-xl font-bold text-[#00f5a0]">{{ title }}</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white text-2xl">&times;</button>
      </div>

      <!-- Dynamic Form -->
      <form @submit.prevent="handleSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="field in fields" 
            :key="field.key"
            :class="field.fullWidth ? 'md:col-span-2' : ''"
          >
            <label class="block text-sm text-gray-300 mb-2">{{ field.label }}</label>

            <!-- Input: Text / Email -->
            <input
              v-if="field.type === 'text' || field.type === 'email'"
              v-model="form[field.key]"
              :type="field.type"
              class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
              :placeholder="field.placeholder || ''"
            />

            <!-- Input: Number -->
            <input
              v-else-if="field.type === 'number'"
              v-model.number="form[field.key]"
              type="number"
              step="0.01"
              :readonly="field.key === 'downpayment_amount'"
              :class="[ 
                'mt-1 w-full rounded-md border border-gray-600 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]', 
                field.key === 'downpayment_amount' ? 'bg-gray-800/50 cursor-not-allowed' : 'bg-black/30' 
              ]"
              :placeholder="field.placeholder || ''"
            />

            <!-- Date Input -->
            <input
              v-else-if="field.type === 'date'"
              v-model="form[field.key]"
              type="date"
              class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
            />

            <!-- Textarea -->
            <textarea
              v-else-if="field.type === 'textarea'"
              v-model="form[field.key]"
              class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
              :placeholder="field.placeholder || ''"
              rows="3"
            ></textarea>

            <!-- BaseSelect: single or multi -->
            <BaseSelect
              v-else-if="field.type === 'baseselect' || field.type === 'select' || field.type === 'multiselect'"
              v-model="form[field.key]"
              :options="field.options"
              :multiple="field.multiple || field.type === 'multiselect'"
              :placeholder="'Select ' + field.label"
            />

            <!-- Custom slot for overrides -->
            <slot
              v-else
              :field="field"
              :model-value="form[field.key]"
              :update="(val) => (form[field.key] = val)"
            ></slot>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-2">
          <button
            type="button"
            @click="$emit('close')"
            class="rounded bg-gray-700 px-4 py-2 text-gray-300 hover:bg-gray-600"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#00c88a]"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';
import BaseSelect from './BaseSelect.vue';

const props = defineProps({
  show: Boolean,
  title: { type: String, default: 'Edit' },
  fields: { type: Array, required: true }, // [{key, label, type, fullWidth?, options, multiple}]
  modelValue: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close', 'save', 'update:modelValue']);
const form = reactive({});

// Sync modelValue to form
watch(
  () => props.modelValue,
  (newVal) => {
    const formattedData = { ...newVal };

    // Format dates for date inputs
    props.fields.forEach(f => {
      if (f.type === 'date' && formattedData[f.key]) {
        const date = new Date(formattedData[f.key]);
        if (!isNaN(date.getTime())) formattedData[f.key] = date.toISOString().split('T')[0];
      }
    });

    // Ensure array fields
    props.fields.forEach(f => {
      if ((f.type === 'multiselect' || f.key === 'materials') && !Array.isArray(formattedData[f.key])) {
        formattedData[f.key] = [];
      }
    });

    Object.assign(form, formattedData);
  },
  { immediate: true }
);

// Auto calculate downpayment
watch(() => form.total_cost, (val) => {
  form.downpayment_amount = val ? parseFloat((val * 0.05).toFixed(2)) : 0;
});

function handleSubmit() {
  const formData = { ...form };
  props.fields.forEach(f => {
    if (f.type === 'number' && typeof formData[f.key] === 'string' && formData[f.key] !== '') {
      const num = parseFloat(formData[f.key]);
      formData[f.key] = isNaN(num) ? null : num;
    }
  });
  emit('save', formData);
}
</script>
