<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <section class="space-y-8">
            <div class="flex items-center justify-between border-b border-[#00f5a0]/30 pb-4">
                <div>
                    <h1 class="font-orbitron text-4xl font-bold tracking-wide text-white">Admin Notifications</h1>
                    <p class="text-lg text-white">Welcome back, Admin. Here’s what’s happening today.</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div
                    v-for="item in notifications"
                    :key="item.id"
                    @click="openNotification(item)"
                    :class="[
                        'cursor-pointer rounded-lg p-5 shadow transition-transform hover:scale-[1.01]',
                        item.is_read ? 'bg-black/40 text-white' : 'bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] text-black',
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xl font-bold">{{ item.data.message }}</p>

                            <p class="mt-1 text-sm" :class="item.is_read ? 'text-white' : 'font-medium text-black/80'">
                                Event Date: {{ formatDate(item.data.event_date) }} | Status: {{ item.data.status }}
                            </p>
                        </div>
                        <div v-if="!item.is_read" class="h-4 w-4 rounded-full bg-[#ff007a] shadow-md"></div>
                    </div>

                    <p class="mt-2 text-sm" :class="item.is_read ? 'text-white' : 'font-medium text-black/80'">
                        {{ formatDate(item.created_at) }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-center gap-2 text-lg">
                <button
                    class="rounded bg-[#00f5a0]/30 px-4 py-2 hover:bg-[#00f5a0]"
                    :disabled="currentPage === 1"
                    @click="fetchNotifications(currentPage - 1)"
                >
                    Previous
                </button>

                <span class="px-4 py-2 text-[#7fbfb0]"> Page {{ currentPage }} of {{ pagination.last_page }} </span>

                <button
                    class="rounded bg-[#00f5a0]/30 px-4 py-2 hover:bg-[#00f5a0]"
                    :disabled="currentPage === pagination.last_page"
                    @click="fetchNotifications(currentPage + 1)"
                >
                    Next
                </button>
            </div>

            <transition name="fade">
                <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                    <div class="relative max-h-[80vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-black/90 p-8 text-lg leading-relaxed shadow-lg">
                        <button
                            @click="closeModal"
                            class="absolute top-4 right-4 rounded-full bg-[#ff007a]/70 px-4 py-2 font-bold text-white hover:bg-[#ff007a]"
                        >
                            X
                        </button>

                        <h2 class="mb-6 text-3xl font-semibold text-[#00f5a0]">Reservation Details</h2>

                        <div class="mb-6 border-b border-[#00f5a0]/30 pb-4 text-xl">
                            <p class="mb-2"><strong>User:</strong> {{ details.user.name }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ details.user.email }}</p>
                            <p class="mb-2"><strong>Event:</strong> {{ details.event.event_name }}</p>
                        </div>

                        <div class="mb-6 border-b border-[#00f5a0]/30 pb-4 text-xl">
                            <p class="mb-2"><strong>Event Date:</strong> {{ formatDate(details.event_date) }}</p>
                            <p class="mb-2"><strong>Event End Date:</strong> {{ formatDate(details.event_end_date) }}</p>
                            <p class="mb-2"><strong>Status:</strong> {{ details.status }}</p>
                            <p class="mb-2"><strong>Total Cost:</strong> ₱{{ details.total_cost }}</p>
                            <p v-if="details.event_notes" class="mb-2"><strong>Notes:</strong> {{ details.event_notes }}</p>
                        </div>

                        <div class="text-xl">
                            <h3 class="mb-3 text-2xl font-semibold text-[#00f5a0]">Materials Reserved</h3>

                            <ul class="list-inside list-disc space-y-2">
                                <li v-for="m in details.materials" :key="m.reserved_material_id">
                                    {{ m.material.material_name }} - {{ m.material.material_description }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </transition>
        </section>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const notifications = ref([]);
const selected = ref(null);
const details = ref([]);
const currentPage = ref(1);
const pagination = ref({ current_page: 1, last_page: 1 });

// Fetch notifications
const fetchNotifications = async (page = 1) => {
    try {
        const response = await axios.get(route('notifications.index', { page }));
        if (response.data.result) {
            notifications.value = response.data.data;
            pagination.value = response.data.pagination;
            currentPage.value = page;
        }
    } catch (error) {
        console.log(error);
    }
};

// Fetch details of a selected notification
const fetchNotificationDetails = async (reserved_event_id) => {
    try {
        const response = await axios.get(route('reservations.user.reservation', reserved_event_id));
        if (response.data.result) {
            details.value = response.data.data;
        }
    } catch (error) {
        console.log(error);
    }
};

// Open notification and mark as read
const openNotification = async (item) => {
    selected.value = item;

    if (!item.is_read) {
        await axios.post(route('notifications.mark-as-read', item.id));
        item.is_read = true; // update UI
    }

    fetchNotificationDetails(item.data.reservation_id);
};

// Close modal
const closeModal = () => {
    selected.value = null;
    details.value = [];
};

// Format date nicely
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(() => {
    fetchNotifications();
});
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
