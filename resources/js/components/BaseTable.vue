<template>
    <div class="card rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-5 shadow-xl md:p-8">
        <div class="mb-6 flex flex-col items-start space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4" v-if="$slots.toolbar">
            <slot name="toolbar" />
        </div>

        <h3 class="font-orbitron mb-6 text-2xl font-bold tracking-wide text-white md:text-3xl">{{ title }}</h3>

        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-full text-left text-base text-white">
                <thead class="border-b border-gray-600 text-[#7fbfb0]">
                    <tr>
                        <th v-for="col in columns" :key="col.key" class="px-4 py-4 text-base font-bold tracking-wider whitespace-nowrap uppercase">
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in data" :key="row.id || index" class="border-b border-gray-700 transition hover:bg-white/5">
                        <td v-for="col in columns" :key="col.key" class="px-4 py-4 align-middle">
                            <template v-if="col.key === 'actions'">
                                <slot name="actions" :row="row" :index="index"> </slot>
                            </template>
                            <template v-else-if="$slots[`cell-${col.key}`]">
                                <slot :name="`cell-${col.key}`" :value="row[col.key]" :row="row" :index="index" />
                            </template>
                            <template v-else>
                                <span v-if="col.key !== 'status'" class="text-base break-words">
                                    {{ row[col.key] }}
                                </span>
                                <span
                                    v-else
                                    :class="statusColor(row[col.key])"
                                    class="inline-block px-3 py-1 text-sm font-semibold tracking-wide uppercase"
                                >
                                    {{ row[col.key] }}
                                </span>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-6 lg:hidden">
            <div v-for="(row, index) in data" :key="row.id || index" class="rounded-xl border border-gray-600 bg-gray-800/40 p-5 shadow-lg">
                <div class="space-y-4">
                    <template v-for="col in columns" :key="col.key">
                        <div
                            v-if="col.key !== 'actions'"
                            class="flex flex-col border-b border-white/5 pb-2 last:border-0 sm:flex-row sm:justify-between sm:pb-0"
                        >
                            <dt class="mb-1 text-base font-bold text-[#7fbfb0] sm:mb-0">{{ col.label }}:</dt>

                            <dd class="flex-1 text-base text-white sm:ml-4 sm:text-right">
                                <template v-if="$slots[`cell-${col.key}`]">
                                    <slot :name="`cell-${col.key}`" :value="row[col.key]" :row="row" :index="index" />
                                </template>
                                <template v-else>
                                    <span v-if="col.key !== 'status'" class="break-words">
                                        {{ row[col.key] }}
                                    </span>
                                    <span v-else :class="statusColor(row[col.key])" class="inline-block px-3 py-1 text-sm font-semibold uppercase">
                                        {{ row[col.key] }}
                                    </span>
                                </template>
                            </dd>
                        </div>
                    </template>

                    <div class="flex flex-wrap gap-3 border-t border-gray-500 pt-4">
                        <slot name="actions" :row="row" :index="index"> </slot>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!data || data.length === 0" class="py-10 text-center text-lg text-gray-400">
            <p>No data available</p>
        </div>

        <div
            v-if="pagination && data && data.length > 0"
            class="mt-8 flex flex-col items-center justify-center space-y-4 text-base sm:flex-row sm:space-y-0 sm:space-x-4"
        >
            <div class="flex flex-wrap justify-center gap-3">
                <button
                    @click="$emit('page-changed', pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="rounded-lg bg-[#00f5a0] px-5 py-2 font-bold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-600 disabled:text-gray-400"
                    aria-label="Previous Page"
                >
                    Prev
                </button>

                <button
                    v-for="page in pageNumbersToShow"
                    :key="page"
                    @click="$emit('page-changed', page)"
                    :class="[
                        'rounded-lg px-4 py-2 font-bold transition',
                        page === pagination.current_page
                            ? 'cursor-default bg-[#00f5a0] text-black shadow-[0_0_10px_#00f5a0]'
                            : 'bg-black/40 text-white hover:bg-[#00c88a] hover:text-black',
                    ]"
                    :aria-current="page === pagination.current_page ? 'page' : null"
                >
                    {{ page }}
                </button>

                <button
                    @click="$emit('page-changed', pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="rounded-lg bg-[#00f5a0] px-5 py-2 font-bold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-600 disabled:text-gray-400"
                    aria-label="Next Page"
                >
                    Next
                </button>
            </div>

            <div class="text-base text-gray-400">Page {{ pagination.current_page }} of {{ pagination.last_page }}</div>
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

// statusColor helper
function statusColor(status) {
    if (!status) return '';
    switch (status.toLowerCase()) {
        case 'pending':
            return 'rounded-full bg-yellow-500/80 text-black';
        case 'accepted':
            return 'rounded-full bg-blue-500 text-white';
        case 'completed':
            return 'rounded-full bg-green-500 text-black';
        case 'cancelled':
            return 'rounded-full bg-red-600 text-white';
        case 'active':
            return 'rounded-full bg-green-500 text-black';
        case 'inactive':
            return 'rounded-full bg-gray-500 text-white';
        case 'downpayment_update':
            return 'rounded-full bg-orange-500 text-black';
        default:
            return 'rounded-full bg-gray-600 text-white';
    }
}

// Show maximum of 3 pages on mobile, 5 on desktop
const pageNumbersToShow = computed(() => {
    if (!props.pagination) return [];

    const total = props.pagination.last_page;
    const current = props.pagination.current_page;

    // Responsive page count - fewer on mobile
    const maxPagesToShow = window.innerWidth < 640 ? 3 : 5;

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

/* Custom scrollbar for horizontal scroll */
.overflow-x-auto::-webkit-scrollbar {
    height: 10px; /* Increased height */
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 5px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #00f5a0;
    border-radius: 5px;
    border: 2px solid rgba(0, 0, 0, 0.3); /* Adds a little padding look */
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #00c88a;
}
</style>
