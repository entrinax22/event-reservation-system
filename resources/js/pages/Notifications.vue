<template>
    <Head title="Notifications" />
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <h2 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Notifications</h2>

            <div v-if="notifications.length === 0" class="text-sm text-[#7fbfb0]">No notifications yet.</div>

            <ul>
                <li
                    v-for="item in notifications"
                    :key="item.id"
                    class="my-3 flex cursor-pointer items-center justify-between rounded-lg border border-[#00f5a0]/20 p-3 transition hover:bg-black/30"
                    @click="openNotification(item)"
                >
                    <div>
                        <div class="text-sm text-[#00f5a0]">
                            {{ item.data.message }}
                        </div>
                        <div class="text-xs text-[#7fbfb0]">
                            {{ formatDate(item.created_at) }}
                        </div>
                    </div>

                    <!-- unread indicator -->
                    <div v-if="!item.is_read" class="h-3 w-3 rounded-full bg-[#ff007a]"></div>
                </li>
            </ul>
        </section>

        <!-- Modal -->
        <div v-if="selected" class="fixed inset-0 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="max-h-[80vh] w-96 overflow-y-auto rounded-xl border border-[#00f5a0]/20 bg-[#020d0b] p-6">
                <h3 class="mb-2 text-lg text-[#00f5a0]">Notification</h3>

                <p class="mb-4 text-sm text-[#7fbfb0]">
                    {{ selected.data.message }}
                </p>

                <div class="mb-4 text-xs text-[#00f5a0]/80">Date: {{ formatDate(selected.created_at) }}</div>

                <!-- Display Reservation Details -->
                <div v-if="details" class="border-t border-[#00f5a0]/20 pt-4">
                    <h4 class="mb-2 text-sm text-[#00f5a0]">Reservation:</h4>

                    <div class="mb-1 text-xs text-[#7fbfb0]">Event Date: {{ details.event.event_name }}</div>
                    <div class="mb-1 text-xs text-[#7fbfb0]">Event Date: {{ formatDate(details.event_date) }}</div>
                    <div class="mb-1 text-xs text-[#7fbfb0]">Event End Date: {{ formatDate(details.event_end_date) }}</div>
                    <div class="mb-1 text-xs text-[#7fbfb0]">Status: {{ details.status }}</div>

                    <h4 class="mt-3 mb-1 text-sm text-[#00f5a0]">Materials:</h4>

                    <!-- LOOP MATERIALS -->
                    <div v-for="m in details.materials" :key="m.reserved_material_id" class="ml-3 text-xs text-[#7fbfb0]">
                        • {{ m.material.material_name }}
                    </div>
                </div>

                <button class="mt-5 rounded bg-[#00f5a0] px-4 py-2 text-black hover:bg-[#02ffbc]" @click="selected = null">Close</button>
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
    const response = await axios.get(route('notifications.index'));
    notifications.value = response.data.data;
};

const fetchNotificationDetails = async (reserved_event_id) => {
    const response = await axios.get(route('reservations.user.reservation', reserved_event_id));
    if (response.data.result) {
        details.value = response.data.data;
    }
};

const openNotification = async (item) => {
    selected.value = item;

    if (!item.is_read) {
        await axios.post(route('notifications.mark-as-read', item.id));
        item.is_read = true;
    }
    fetchNotificationDetails(item.data.reservation_id);
};

onMounted(fetchNotifications);

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>
