<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: [String, Number, Array], // selected ID
    options: { type: Array, required: true },
    multiple: { type: Boolean, default: false },
    placeholder: { type: String, default: 'Select...' },
    allowOther: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'other-input']);

const isOpen = ref(false);
const showOtherInput = ref(false);
const otherInput = ref('');

// selected option (only IDs)
const selected = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

// toggle option click
const toggleOption = (option) => {
    if (option.value === 'other' && props.allowOther) {
        showOtherInput.value = true;
        otherInput.value = '';
        selected.value = null; // clear actual ID
        emit('update:modelValue', null); // ensure parent event_id cleared
        return;
    }

    if (props.multiple) {
        const arr = Array.isArray(selected.value) ? [...selected.value] : [];
        selected.value = arr.includes(option.value) ? arr.filter((v) => v !== option.value) : [...arr, option.value];
    } else {
        selected.value = option.value;
        isOpen.value = false;
        showOtherInput.value = false;
        otherInput.value = '';
    }
};

// Add typed "Other" value
const addOtherValue = () => {
    if (!otherInput.value) return;

    emit('other-input', otherInput.value); // pass typed value to parent
    // showOtherInput.value = false;
    isOpen.value = false;
    // otherInput.value = ''; // clear input
};
</script>

<template>
    <div class="relative w-full">
        <div
            @click="isOpen = !isOpen"
            class="flex cursor-pointer items-center justify-between gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white transition-all hover:border-[#00f5a0]/50"
            :class="{ 'border-[#00f5a0] ring-1 ring-[#00f5a0]': isOpen }"
        >
            <span v-if="!selected && !showOtherInput" class="text-gray-500">{{ placeholder }}</span>
            <span v-else-if="showOtherInput" class="text-white">{{ otherInput || 'Other...' }}</span>
            <span v-else class="text-white">{{ options.find((o) => o.value === selected)?.label || selected }}</span>

            <svg
                class="ml-auto h-4 w-4 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180 text-[#00f5a0]': isOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <div
            v-if="isOpen"
            class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-[#00f5a0]/30 bg-[#0a0a0a] shadow-[0_10px_40px_rgba(0,0,0,0.8)] backdrop-blur-xl"
        >
            <ul class="custom-scrollbar max-h-60 overflow-auto text-sm">
                <li
                    v-for="option in options"
                    :key="option.value"
                    @click="toggleOption(option)"
                    class="flex cursor-pointer items-center justify-between px-4 py-3 text-gray-300 transition-colors hover:bg-[#00f5a0] hover:font-bold hover:text-black"
                    :class="{ 'bg-[#00f5a0]/10 text-[#00f5a0]': selected === option.value }"
                >
                    <span>{{ option.label }}</span>
                    <span v-if="selected === option.value" class="font-bold">✔</span>
                </li>

                <li v-if="showOtherInput" class="border-t border-white/10 bg-black/20 px-4 py-3">
                    <input
                        type="text"
                        v-model="otherInput"
                        @keyup.enter="addOtherValue"
                        placeholder="Type your option..."
                        class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                    />
                    <button
                        @click="addOtherValue"
                        class="mt-2 w-full rounded-lg bg-[#00f5a0] px-3 py-2 text-sm font-bold text-black hover:bg-[#00d68f]"
                    >
                        Confirm
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 245, 160, 0.3);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 245, 160, 0.6);
}
</style>
