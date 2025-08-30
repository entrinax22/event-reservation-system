<template>
    <Head title="Admin Dashboard - Events" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-[#00f5a0]">Events</h1>
                <p class="text-sm text-[#7fbfb0]">Create and manage events.</p>
            </div>

            <!-- Create Event Form -->
            <form 
                @submit.prevent="createEvent" 
                class="max-w-3xl space-y-6 rounded-xl bg-[rgba(0,0,0,0.85)] p-6 shadow-xl"
            >
                <h2 class="font-orbitron text-xl text-[#00f5a0]">Create New Event</h2>

                <div class="grid grid-cols-1 gap-y-4">
                    <!-- Event Name -->
                    <div>
                        <label for="event_name" class="block text-sm text-[#7fbfb0]">Event Name</label>
                        <input
                            id="event_name"
                            type="text"
                            v-model="form.event_name"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter event name"
                            required
                        />
                        <p v-if="errors.event_name" class="mt-1 text-xs text-red-400">{{ errors.event_name[0] }}</p>
                    </div>

                    <!-- Event Description -->
                    <div>
                        <label for="event_description" class="block text-sm text-[#7fbfb0]">Event Description</label>
                        <textarea
                            id="event_description"
                            v-model="form.event_description"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter event description"
                            rows="4"
                            required
                        ></textarea>
                        <p v-if="errors.event_description" class="mt-1 text-xs text-red-400">{{ errors.event_description[0] }}</p>
                    </div>
                </div>

                <!-- Submit button spans full width -->
                <button
                    type="submit"
                    :disabled="submitting"
                    class="w-full rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ submitting ? 'Creating...' : 'Create Event' }}
                </button>
            </form>
        </section>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

// Form state
const form = ref({
    event_name: '',
    event_description: '',
})

const errors = ref({})
const submitting = ref(false)

async function createEvent() {
    submitting.value = true
    errors.value = {}

    try {
        const response = await axios.post(route('admin.events.store'), form.value)
        // Reset form
        form.value = { event_name: '', event_description: '' }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors
        } else {
            alert('An unexpected error occurred.')
        }
    } finally {
        submitting.value = false
    }
}
</script>
