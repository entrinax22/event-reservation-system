<!-- components/BaseTable.vue -->
<template>
    <div class="card rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-3 shadow-xl md:p-6">
        <!-- Toolbar -->
        <div class="mb-4 flex flex-col items-start space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2" v-if="$slots.toolbar">
            <slot name="toolbar" />
        </div>

        <!-- Title -->
        <h3 class="font-orbitron mb-4 text-lg text-white md:text-xl">{{ title }}</h3>

        <!-- Desktop Table View -->
        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-full text-left text-sm text-white">
                <thead class="border-b border-gray-700 text-[#7fbfb0]">
                    <tr>
                        <th v-for="col in columns" :key="col.key" class="px-2 py-3 whitespace-nowrap">
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in data" :key="row.id || index" class="border-b border-gray-700 hover:bg-gray-800/40">
                        <td v-for="col in columns" :key="col.key" class="px-2 py-3">
                            <template v-if="col.key === 'actions'">
                                <slot name="actions" :row="row" :index="index"> </slot>
                            </template>
                            <template v-else-if="$slots[`cell-${col.key}`]">
                                <slot :name="`cell-${col.key}`" :value="row[col.key]" :row="row" :index="index" />
                            </template>
                            <template v-else>
                                <span v-if="col.key !== 'status'" class="break-words">
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
        </div>

        <!-- Mobile/Tablet Card View -->
        <div class="space-y-4 lg:hidden">
            <div v-for="(row, index) in data" :key="row.id || index" class="rounded-lg border border-gray-700 bg-gray-800/40 p-4">
                <!-- Row cards for mobile -->
                <div class="space-y-3">
                    <template v-for="col in columns" :key="col.key">
                        <!-- Skip actions column in main content, we'll add it at bottom -->
                        <div v-if="col.key !== 'actions'" class="flex flex-col sm:flex-row sm:justify-between">
                            <dt class="mb-1 text-sm font-medium text-[#7fbfb0] sm:mb-0">{{ col.label }}:</dt>
                            <dd class="flex-1 text-sm text-white sm:ml-4 sm:text-right">
                                <template v-if="$slots[`cell-${col.key}`]">
                                    <slot :name="`cell-${col.key}`" :value="row[col.key]" :row="row" :index="index" />
                                </template>
                                <template v-else>
                                    <span v-if="col.key !== 'status'" class="break-words">
                                        {{ row[col.key] }}
                                    </span>
                                    <span v-else :class="statusColor(row[col.key])">
                                        {{ row[col.key] }}
                                    </span>
                                </template>
                            </dd>
                        </div>
                    </template>

                    <!-- Actions at bottom of each card -->
                    <div class="flex flex-wrap gap-2 border-t border-gray-600 pt-3">
                        <slot name="actions" :row="row" :index="index"> </slot>
                    </div>
                </div>
            </div>
        </div>

        <!-- No data message -->
        <div v-if="!data || data.length === 0" class="py-8 text-center text-gray-400">
            <p>No data available</p>
        </div>

        <!-- Pagination -->
        <div
            v-if="pagination && data && data.length > 0"
            class="mt-6 flex flex-col items-center justify-center space-y-2 text-sm sm:flex-row sm:space-y-0 sm:space-x-2"
        >
            <!-- Mobile: Stack buttons vertically on very small screens -->
            <div class="flex flex-wrap justify-center gap-2">
                <button
                    @click="$emit('page-changed', pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="rounded-full bg-[#00f5a0] px-3 py-1 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-400 sm:px-4"
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
                        'rounded-full px-3 py-1 text-xs font-semibold sm:px-4 sm:text-sm',
                        page === pagination.current_page ? 'cursor-default bg-[#00f5a0] text-black' : 'bg-black/40 text-white hover:bg-[#00c88a]',
                    ]"
                    :aria-current="page === pagination.current_page ? 'page' : null"
                >
                    {{ page }}
                </button>

                <button
                    @click="$emit('page-changed', pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="rounded-full bg-[#00f5a0] px-3 py-1 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-400 sm:px-4"
                    aria-label="Next Page"
                >
                    Next
                </button>
            </div>

            <!-- Pagination info -->
            <div class="mt-2 text-xs text-gray-400 sm:mt-0">Page {{ pagination.current_page }} of {{ pagination.last_page }}</div>
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
            return 'px-2 py-1 rounded-full bg-yellow-500 text-white text-xs';
        case 'accepted':
            return 'px-2 py-1 rounded-full bg-blue-600 text-white text-xs';
        case 'completed':
            return 'px-2 py-1 rounded-full bg-green-600 text-white text-xs';
        case 'cancelled':
            return 'px-2 py-1 rounded-full bg-red-600 text-white text-xs';
        case 'active':
            return 'px-2 py-1 rounded-full bg-green-600 text-white text-xs';
        case 'inactive':
            return 'px-2 py-1 rounded-full bg-gray-500 text-white text-xs';
        case 'downpayment_update':
            return 'px-2 py-1 rounded-full bg-orange-600 text-white text-xs';
        default:
            return 'px-2 py-1 rounded-full bg-gray-600 text-white text-xs';
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
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #00f5a0;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #00c88a;
}
</style>
