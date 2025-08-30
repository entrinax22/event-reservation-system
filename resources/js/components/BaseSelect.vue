<script setup>
import { ref, computed } from "vue";

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: () => [],
  },
  options: {
    type: Array,
    required: true,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: "Select...",
  },
});

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);

const selected = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const toggleOption = (option) => {
  if (props.multiple) {
    const arr = Array.isArray(selected.value) ? [...selected.value] : [];
    if (arr.includes(option.value)) {
      selected.value = arr.filter((v) => v !== option.value);
    } else {
      selected.value = [...arr, option.value];
    }
  } else {
    selected.value = option.value;
    isOpen.value = false;
  }
};

const isSelected = (option) => {
  return props.multiple
    ? selected.value?.includes(option.value)
    : selected.value === option.value;
};
</script>

<template>
  <div class="relative w-full">
    <!-- Button -->
    <div
      @click="isOpen = !isOpen"
      class="flex flex-wrap gap-2 items-center justify-between px-3 py-2 
             border border-gray-600 rounded-lg bg-black/40 text-white cursor-pointer 
             focus-within:ring-2 focus-within:ring-[#00f5a0]"
    >
      <div class="flex flex-wrap gap-2">
        <!-- Multi -->
        <template v-if="multiple">
          <span
            v-for="opt in options.filter((o) => selected?.includes(o.value))"
            :key="opt.value"
            class="bg-[#00f5a0] text-black text-xs px-2 py-1 rounded"
          >
            {{ opt.label }}
          </span>
          <span v-if="!selected?.length" class="text-gray-400">
            {{ placeholder }}
          </span>
        </template>

        <!-- Single -->
        <template v-else>
          <span v-if="selected" class="text-white">
            {{ options.find((o) => o.value === selected)?.label }}
          </span>
          <span v-else class="text-gray-400">{{ placeholder }}</span>
        </template>
      </div>

      <!-- Arrow -->
      <svg
        class="w-4 h-4 text-gray-400 ml-auto transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M19 9l-7 7-7-7" />
      </svg>
    </div>

    <!-- Options -->
    <div
      v-if="isOpen"
      class="absolute z-20 mt-1 w-full rounded-lg bg-white border border-gray-300 shadow-lg"
    >
      <ul class="max-h-60 overflow-auto text-sm">
        <li
          v-for="option in options"
          :key="option.value"
          @click="toggleOption(option)"
          class="px-4 py-2 cursor-pointer hover:bg-[#00f5a0]/10 flex justify-between items-center text-green-600"
        >
          <span>{{ option.label }}</span>
          <span v-if="isSelected(option)" class="text-[#00f5a0]">✔</span>
        </li>
      </ul>
    </div>
  </div>
</template>
