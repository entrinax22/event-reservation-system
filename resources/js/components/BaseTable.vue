<!-- components/BaseTable.vue -->
<template>
    <div class="card rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
        <div class="mb-4 flex items-center space-x-2" v-if="$slots.toolbar">
            <slot name="toolbar" />
        </div>

        <h3 class="font-orbitron mb-4 text-xl text-[#00f5a0]">{{ title }}</h3>

        <table class="w-full text-left text-sm text-white">
            <thead class="border-b border-gray-700 text-[#7fbfb0]">
                <tr>
                    <th v-for="col in columns" :key="col.key" class="py-3">
                        {{ col.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, index) in data" :key="row.id || index" class="border-b border-gray-700 hover:bg-gray-800/40">
                    <td v-for="col in columns" :key="col.key" class="py-3">
                        <template v-if="col.key === 'actions'">
                            <!-- Slot for custom actions, fallback empty -->
                            <slot name="actions" :row="row" :index="index"> </slot>
                        </template>
                        <template v-else>
                            <span v-if="col.key !== 'status'">
                                {{ row[col.key] }}
                            </span>
                            <span v-else :class="statusColor(row[col.key])">
                                {{ row[col.key] }}
                            </span>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Replace the existing pagination div with this -->

        <div v-if="pagination" class="mt-6 flex justify-center space-x-2 text-sm">
            <button
                @click="$emit('page-changed', pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="rounded-full bg-[#00f5a0] px-4 py-1 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-400"
                aria-label="Previous Page"
            >
                Prev
            </button>

            <!-- Page numbers -->
            <button
                v-for="page in pageNumbersToShow"
                :key="page"
                @click="$emit('page-changed', page)"
                :class="[
                    'rounded-full px-4 py-1 font-semibold',
                    page === pagination.current_page ? 'cursor-default bg-[#00f5a0] text-black' : 'bg-black/40 text-white hover:bg-[#00c88a]',
                ]"
                :aria-current="page === pagination.current_page ? 'page' : null"
            >
                {{ page }}
            </button>

            <button
                @click="$emit('page-changed', pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="rounded-full bg-[#00f5a0] px-4 py-1 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-400"
                aria-label="Next Page"
            >
                Next
            </button>
        </div>
    </div>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: { type: String, default: 'Table' },
    columns: { type: Array, required: true },
    data: { type: Array, required: true },
    pagination: { type: Object, default: null },
});

// Show maximum of 5 page buttons at a time
const pageNumbersToShow = computed(() => {
    if (!props.pagination) return [];

    const total = props.pagination.last_page;
    const current = props.pagination.current_page;
    const maxPagesToShow = 5;
    let start = Math.max(current - Math.floor(maxPagesToShow / 2), 1);
    let end = start + maxPagesToShow - 1;

    if (end > total) {
        end = total;
        start = Math.max(end - maxPagesToShow + 1, 1);
    }

    const pages = [];
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
.card {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}
</style>
