<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="flex max-h-[90vh] w-full max-w-4xl flex-col rounded-lg bg-gray-900 shadow-lg">
            <div class="flex shrink-0 items-center justify-between border-b border-gray-700 px-6 py-4">
                <h2 class="text-xl font-bold text-[#00f5a0]">{{ title }}</h2>
                <button @click="$emit('close')" class="text-2xl text-gray-400 hover:text-white">&times;</button>
            </div>

            <div class="custom-scrollbar flex-1 overflow-y-auto px-6 py-6">
                <form @submit.prevent="handleSubmit" id="dynamic-form">
                    <div :class="['grid gap-6 pb-40', columns === 2 ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1']">
                        <div v-for="field in fields" :key="field.key" :class="field.fullWidth ? `col-span-1 md:col-span-${columns}` : ''">
                            <label class="mb-2 block text-sm text-gray-300">{{ field.label }}</label>

                            <input
                                v-if="field.type === 'text' || field.type === 'email'"
                                v-model="form[field.key]"
                                :type="field.type"
                                :placeholder="field.placeholder || ''"
                                class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                            />

                            <input
                                v-else-if="field.type === 'number'"
                                v-model.number="form[field.key]"
                                type="number"
                                step="0.01"
                                :readonly="field.key === 'downpayment_amount'"
                                :placeholder="field.placeholder || ''"
                                :class="[
                                    'mt-1 w-full rounded-md border px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]',
                                    field.key === 'downpayment_amount'
                                        ? 'cursor-not-allowed border-gray-700 bg-gray-800/50'
                                        : 'border-gray-600 bg-black/30',
                                ]"
                            />

                            <input
                                v-else-if="field.type === 'date'"
                                v-model="form[field.key]"
                                type="date"
                                class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                            />

                            <textarea
                                v-else-if="field.type === 'textarea'"
                                v-model="form[field.key]"
                                :placeholder="field.placeholder || ''"
                                rows="3"
                                class="mt-1 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                            ></textarea>

                            <div v-else-if="['baseselect', 'select', 'multiselect'].includes(field.type)" class="relative z-10">
                                <BaseSelect
                                    v-model="form[field.key]"
                                    :options="field.options"
                                    :multiple="field.multiple || field.type === 'multiselect'"
                                    :placeholder="'Select ' + field.label"
                                    :allow-other="true"
                                    @other-input="
                                        (val) => {
                                            form.event_name = val;
                                            form.event_id = '';
                                        }
                                    "
                                />
                            </div>

                            <div v-else-if="field.type === 'materials'">
                                <div v-for="(m, index) in form.materials" :key="index" class="mb-2 flex items-center gap-2">
                                    <BaseSelect v-model="m.material_id" :options="materialOptions" :placeholder="'Select Material'" class="flex-1" />
                                    <input
                                        type="number"
                                        min="1"
                                        v-model.number="m.material_quantity"
                                        class="w-20 rounded border border-gray-600 bg-black/30 px-2 py-1 text-white"
                                    />
                                    <button
                                        type="button"
                                        @click="form.materials.splice(index, 1)"
                                        class="rounded bg-red-600 px-2 py-1 text-white hover:bg-red-500"
                                    >
                                        &times;
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    @click="form.materials.push({ material_id: '', material_quantity: 1 })"
                                    class="text-sm font-semibold text-[#00f5a0] hover:underline"
                                >
                                    + Add Material
                                </button>
                            </div>

                            <slot v-else :field="field" :model-value="form[field.key]" :update="(val) => (form[field.key] = val)"></slot>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex shrink-0 justify-end space-x-2 border-t border-gray-700 bg-gray-900 px-6 py-4">
                <button type="button" @click="$emit('close')" class="rounded bg-gray-700 px-4 py-2 text-gray-300 hover:bg-gray-600">Cancel</button>
                <button type="submit" form="dynamic-form" class="rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#00c88a]">
                    Save
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue';
import BaseSelect from './BaseSelect.vue';

const props = defineProps({
    show: Boolean,
    title: { type: String, default: 'Edit' },
    fields: { type: Array, required: true },
    modelValue: { type: Object, default: () => ({}) },
    materialOptions: { type: Array, default: () => [] },
    columns: { type: Number, default: 2 },
});

const emit = defineEmits(['close', 'save', 'update:modelValue']);
const form = reactive({});

// Sync modelValue to form
watch(
    () => props.modelValue,
    (newVal) => {
        const formattedData = { ...newVal };

        props.fields.forEach((f) => {
            if (f.type === 'date' && formattedData[f.key]) {
                const date = new Date(formattedData[f.key]);
                if (!isNaN(date.getTime())) formattedData[f.key] = date.toISOString().split('T')[0];
            }
            if ((f.type === 'multiselect' || f.key === 'materials') && !Array.isArray(formattedData[f.key])) formattedData[f.key] = [];
        });

        Object.assign(form, formattedData);
    },
    { immediate: true, deep: true }, // Added deep: true just in case
);

// Auto calculate downpayment
watch(
    () => form.total_cost,
    (val) => {
        form.downpayment_amount = val ? parseFloat((val * 0.05).toFixed(2)) : 0;
    },
);

function handleSubmit() {
    const formData = { ...form };
    props.fields.forEach((f) => {
        if (f.type === 'number' && typeof formData[f.key] === 'string' && formData[f.key] !== '') {
            const num = parseFloat(formData[f.key]);
            formData[f.key] = isNaN(num) ? null : num;
        }
    });
    emit('save', formData);
}
</script>

<style scoped>
/* Optional: Makes the scrollbar look nicer inside the dark modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}
</style>
