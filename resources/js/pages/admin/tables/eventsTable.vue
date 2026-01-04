<template>
    <Head title="Admin Dashboard - Events" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-white">Events</h1>
                <p class="text-xl text-white">Manage registered events and event details.</p>
            </div>

            <!-- Table with search toolbar -->
            <BaseTable :title="'Events'" :columns="columns" :data="events" :pagination="pagination" @page-changed="fetchEvents">
                <template #toolbar>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search events..."
                        class="rounded-md border border-gray-600 bg-black/30 px-4 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                    />
                    <button @click="go" class="rounded-md bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#00c88a]">Add</button>
                </template>

                <template #actions="{ row }">
                    <button @click="openEditEvent(row)" class="mr-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">Edit</button>
                    <button @click="deleteEvent(row)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Delete</button>
                </template>
            </BaseTable>

            <!-- Edit Modal -->
            <EditModal
                :show="showEditModal"
                title="Edit Event"
                :fields="eventFields"
                v-model="selectedEvent"
                :columns="1"
                @close="showEditModal = false"
                @save="updateEvent"
            />
        </section>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import EditModal from '@/components/EditModal.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const columns = [
    { key: 'event_id', label: 'ID' },
    { key: 'event_name', label: 'Name' },
    { key: 'event_description', label: 'Description' },
    { key: 'actions', label: 'Actions' },
];

const events = ref([]);
const pagination = ref(null);
const search = ref('');
const currentPage = ref(1);

async function fetchEvents(page = 1) {
    currentPage.value = page;

    try {
        const response = await axios.get(route('admin.events.list'), {
            params: {
                search: search.value,
                page,
            },
        });

        if (response.data.result) {
            events.value = response.data.data;
            pagination.value = response.data.pagination;
        } else {
            events.value = [];
            pagination.value = null;
        }
    } catch (error) {
        console.error('Failed to fetch events:', error);
        events.value = [];
        pagination.value = null;
    }
}

// Fetch on mount
onMounted(fetchEvents);

watch(search, (newVal) => {
    fetchEvents(currentPage.value, newVal);
});

function go() {
    router.get(route('admin.events.create'));
}

// Edit modal
const showEditModal = ref(false);
const selectedEvent = ref({});
const eventFields = [
    { key: 'event_name', label: 'Name', type: 'text', placeholder: 'Enter event name' },
    { key: 'event_description', label: 'Description', type: 'textarea', placeholder: 'Enter event description' },
];

function openEditEvent(event) {
    selectedEvent.value = { ...event };
    showEditModal.value = true;
}

async function updateEvent(updatedData) {
    try {
        const response = await axios.post(route('admin.events.update'), updatedData);
        showEditModal.value = false;
        fetchEvents(currentPage.value);

        console.log('Event updated successfully:', response.data);
    } catch (error) {
        console.error('Error updating event:', error.response?.data || error);
    }
}

async function deleteEvent(event) {
    try {
        const confirmed = window.confirm(`Are you sure you want to delete event "${event.event_name}"?`);
        if (!confirmed) return;

        const response = await axios.post(route('admin.events.destroy'), { event_id: event.event_id });
        showEditModal.value = false;
        fetchEvents(currentPage.value);

        console.log('Event deleted successfully:', response.data);
    } catch (error) {
        console.error('Error deleting event:', error.response?.data || error);
    }
}
</script>
