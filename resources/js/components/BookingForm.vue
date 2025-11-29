<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md transition-opacity duration-300"
        @click.self="$emit('close')"
    >
        <div
            class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl border border-white/10 bg-[#0a0a0a] shadow-[0_0_50px_rgba(0,245,160,0.1)] transition-all duration-300 sm:p-0"
        >
            <div class="relative flex items-center justify-between border-b border-white/5 bg-[#111] px-8 py-6">
                <div>
                    <h2 class="font-orbitron text-xl font-bold tracking-wide text-white">Create Reservation</h2>
                    <p class="mt-1 text-xs text-gray-500">Fill in the details below to book a new event.</p>
                </div>
                <button
                    @click="$emit('close')"
                    class="group flex h-8 w-8 items-center justify-center rounded-full bg-white/5 transition hover:bg-red-500/20 hover:text-red-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-gray-400 group-hover:text-red-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="max-h-[85vh] overflow-y-auto px-8 py-8">
                <form @submit.prevent="createReservation" class="space-y-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="group relative">
                            <label
                                class="mb-2 block text-[10px] font-bold tracking-widest text-gray-500 uppercase transition-colors group-focus-within:text-[#00f5a0]"
                                >Start Date</label
                            >
                            <div class="relative flex items-center">
                                <input
                                    type="date"
                                    v-model="form.event_date"
                                    class="peer w-full rounded-xl border border-white/10 bg-[#151515] px-4 py-3 text-sm text-white placeholder-transparent shadow-sm transition-all focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                                    required
                                />
                                <div class="pointer-events-none absolute right-4 text-gray-400 peer-focus:text-[#00f5a0]">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <p v-if="errors.event_date" class="mt-1 text-xs font-medium text-red-500">{{ errors.event_date[0] }}</p>
                        </div>

                        <div class="group relative">
                            <label
                                class="mb-2 block text-[10px] font-bold tracking-widest text-gray-500 uppercase transition-colors group-focus-within:text-[#00f5a0]"
                                >End Date</label
                            >
                            <div class="relative flex items-center">
                                <input
                                    type="date"
                                    v-model="form.event_end_date"
                                    class="peer w-full rounded-xl border border-white/10 bg-[#151515] px-4 py-3 text-sm text-white placeholder-transparent shadow-sm transition-all focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                                    required
                                />
                                <div class="pointer-events-none absolute right-4 text-gray-400 peer-focus:text-[#00f5a0]">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <p v-if="errors.event_end_date" class="mt-1 text-xs font-medium text-red-500">{{ errors.event_end_date[0] }}</p>
                        </div>
                    </div>

                    <div class="group">
                        <label class="mb-2 block text-[10px] font-bold tracking-widest text-gray-500 uppercase">Select Event</label>
                        <div class="relative">
                            <BaseSelect
                                v-model="form.event_id"
                                :options="eventOptions"
                                placeholder="Choose an event type..."
                                class="custom-select-override"
                            />
                        </div>
                        <p v-if="errors.event_id" class="mt-1 text-xs font-medium text-red-500">{{ errors.event_id[0] }}</p>
                    </div>

                    <div class="group">
                        <label class="mb-2 block text-[10px] font-bold tracking-widest text-gray-500 uppercase">Materials Needed</label>
                        <div class="relative">
                            <BaseSelect
                                v-model="form.materials"
                                :options="materialOptions"
                                multiple
                                placeholder="Select materials..."
                                class="custom-select-override"
                            />
                        </div>
                        <p v-if="errors.materials" class="mt-1 text-xs font-medium text-red-500">{{ errors.materials[0] }}</p>
                    </div>

                    <div class="group">
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-gray-500 uppercase transition-colors group-focus-within:text-[#00f5a0]"
                            >Notes</label
                        >
                        <textarea
                            v-model="form.event_notes"
                            rows="3"
                            placeholder="Any special requirements or details..."
                            class="w-full rounded-xl border border-white/10 bg-[#151515] px-4 py-3 text-sm text-white placeholder-gray-600 shadow-sm transition-all focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="relative w-full overflow-hidden rounded-xl bg-[#00f5a0] py-3.5 text-sm font-bold tracking-wider text-black uppercase transition-all hover:bg-[#00d68f] hover:shadow-[0_0_20px_rgba(0,245,160,0.4)] disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span v-if="!submitting">Confirm Reservation</span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <svg class="h-4 w-4 animate-spin text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import BaseSelect from './BaseSelect.vue';

const emit = defineEmits(['close']);
const submitting = ref(false);
const errors = reactive({});

const form = reactive({
    event_id: '',
    event_date: '',
    event_end_date: '',
    materials: [],
    event_notes: '',
});

const eventOptions = ref([]);
const materialOptions = ref([]);

async function fetchOptions() {
    try {
        const [eventsRes, materialsRes] = await Promise.all([axios.get(route('events.selectList')), axios.get(route('materials.selectList'))]);

        const eventsData = eventsRes.data?.data ?? eventsRes.data ?? [];
        const materialsData = materialsRes.data?.data ?? materialsRes.data ?? [];

        eventOptions.value = eventsData.map((e) => ({ value: e.event_id, label: e.event_name }));
        materialOptions.value = materialsData.map((m) => ({ value: m.material_id, label: m.material_name }));
    } catch (err) {
        console.error('Error fetching select lists:', err);
        eventOptions.value = [];
        materialOptions.value = [];
    }
}

onMounted(fetchOptions);

async function createReservation() {
    submitting.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);

    try {
        const payload = {
            ...form,
            materials: form.materials.map((id) => ({ material_id: id })),
        };

        const response = await axios.post(route('reserved-online'), payload);

        if (response.data?.result === false) {
            alert(response.data.message);
            return;
        }
        alert('Reservation created successfully!');
        emit('close');
    } catch (error) {
        if (error.response?.status === 422) {
            Object.assign(errors, error.response.data.errors);
        } else {
            console.error('Unexpected error:', error);
            alert('Something went wrong. Please try again.');
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}

/* Date Picker Customization Magic 
   We hide the native ugly indicator but keep the input functionality.
   This allows the user to click anywhere on the input (including our custom SVG icon area)
   to trigger the native browser date picker.
*/
input[type='date']::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}
</style>
