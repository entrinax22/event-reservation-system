<template>
    <Head title="Notifications" />
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-6 shadow-xl">
            <h2 class="font-orbitron mb-6 text-3xl font-bold tracking-wide text-[#00f5a0]">Notifications</h2>

            <div v-if="notifications.length === 0" class="text-lg text-[#7fbfb0]">No notifications yet.</div>

            <ul class="space-y-4">
                <li
                    v-for="item in notifications"
                    :key="item.id"
                    @click="openNotification(item)"
                    :class="[
                        'flex cursor-pointer items-center justify-between rounded-xl p-5 shadow-lg transition-transform hover:scale-[1.01]',
                        item.is_read ? 'border border-[#00f5a0]/20 bg-black/40' : 'border-none bg-gradient-to-r from-[#00f5a0] to-[#00b2ff]',
                    ]"
                >
                    <div class="w-full">
                        <div class="mb-1 text-xl" :class="item.is_read ? 'text-white' : 'font-bold text-black'">
                            {{ item.data.message }}
                        </div>

                        <div class="text-sm" :class="item.is_read ? 'text-white/80' : 'font-medium text-black/70'">
                            {{ formatDate(item.created_at) }}
                        </div>
                    </div>

                    <div v-if="!item.is_read" class="ml-4 h-4 w-4 flex-shrink-0 rounded-full bg-[#ff007a] shadow-md"></div>
                </li>
            </ul>
        </section>

        <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div
                class="relative max-h-[85vh] w-full max-w-xl overflow-y-auto rounded-2xl border-2 border-[#00f5a0] bg-black p-8 shadow-[0_0_30px_rgba(0,245,160,0.3)]"
            >
                <h3 class="mb-4 text-2xl font-bold text-[#00f5a0]">Notification Details</h3>

                <p class="mb-6 text-xl leading-relaxed text-white">
                    {{ selected.data.message }}
                </p>

                <div class="mb-6 text-sm font-semibold text-[#00f5a0]">
                    Received: <span class="text-white">{{ formatDate(selected.created_at) }}</span>
                </div>

                <div v-if="details && details.event" class="border-t border-[#00f5a0]/50 pt-6">
                    <h4 class="mb-4 text-xl font-bold text-[#00f5a0]">Reservation Info:</h4>

                    <div class="space-y-4 text-lg">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold tracking-wider text-[#00f5a0] uppercase">Event Name</span>
                            <span class="text-white">{{ details.event.event_name }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold tracking-wider text-[#00f5a0] uppercase">Date</span>
                            <span class="text-white">{{ formatDate(details.event_date) }} - {{ formatDate(details.event_end_date) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold tracking-wider text-[#00f5a0] uppercase">Status</span>
                            <span class="text-white uppercase">{{ details.status }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold tracking-wider text-[#00f5a0] uppercase">Notes</span>
                            <span class="text-white">{{ details.event_notes || 'None' }}</span>
                        </div>
                    </div>

                    <h4 class="mt-8 mb-3 text-xl font-bold text-[#00f5a0]">Equipments:</h4>

                    <ul class="list-inside list-disc space-y-2 text-lg text-white">
                        <li v-for="m in details.materials" :key="m.reserved_material_id">
                            {{ m.material.material_name }}
                        </li>
                    </ul>
                </div>

                <div class="mt-8 flex justify-end">
                    <button
                        class="rounded-lg bg-[#00f5a0] px-6 py-2 text-lg font-bold text-white shadow-lg transition-all hover:bg-white hover:text-[#00f5a0]"
                        @click="selected = null"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const notifications = ref([]);
const selected = ref(null);
const details = ref([]);

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data.data;
    } catch (error) {
        console.error('Error fetching notifications', error);
    }
};

const fetchNotificationDetails = async (reserved_event_id) => {
    try {
        const response = await axios.get(route('reservations.user.reservation', reserved_event_id));
        if (response.data.result) {
            details.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching details', error);
    }
};

const openNotification = async (item) => {
    selected.value = item;

    if (!item.is_read) {
        item.is_read = true;
        try {
            await axios.post(route('notifications.mark-as-read', item.id));
        } catch (error) {
            console.error('Error marking as read', error);
        }
    }

    details.value = [];
    fetchNotificationDetails(item.data.reservation_id);
};

onMounted(fetchNotifications);

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
</style>
