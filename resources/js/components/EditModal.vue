<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-lg rounded-lg bg-gray-900 p-6 shadow-lg">
      <!-- Header -->
      <div class="mb-4 flex items-center justify-between border-b border-gray-700 pb-2">
        <h2 class="text-xl font-bold text-[#00f5a0]">{{ title }}</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white">&times;</button>
      </div>

      <!-- Dynamic Form -->
      <form @submit.prevent="handleSubmit">
        <div class="space-y-4">
          <div v-for="field in fields" :key="field.key">
            <label class="block text-sm text-gray-300">{{ field.label }}</label>

            <!-- Input: Text / Email / Number -->
            <input
              v-if="field.type === 'text' || field.type === 'email' || field.type === 'number'"
              v-model="form[field.key]"
              :type="field.type"
              class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
              :placeholder="field.placeholder || ''"
            />

            <!-- Textarea -->
            <textarea
              v-else-if="field.type === 'textarea'"
              v-model="form[field.key]"
              class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
              :placeholder="field.placeholder || ''"
              rows="3"
            ></textarea>

            <!-- Select -->
            <select
              v-else-if="field.type === 'select'"
              v-model="form[field.key]"
              class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
            >
              <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>

            <!-- Custom Slot (for overrides) -->
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

const props = defineProps({
  show: Boolean,
  title: { type: String, default: 'Edit' },
  fields: { type: Array, required: true }, // [{key, label, type, placeholder, options}]
  modelValue: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close', 'save', 'update:modelValue']);

const form = reactive({});

// When props.modelValue changes, sync to form
watch(
  () => props.modelValue,
  (newVal) => {
    Object.assign(form, newVal);
  },
  { immediate: true }
);

function handleSubmit() {
  emit('save', { ...form }); // pass form back to parent
}
</script>
