<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md transition-opacity duration-300"
        @click.self="$emit('close')"
    >
        <div
            class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl border border-white/20 bg-black/90 shadow-[0_0_50px_rgba(0,245,160,0.15)] backdrop-blur-xl transition-all duration-300 sm:p-0"
        >
            <div class="relative flex items-center justify-between border-b border-white/10 bg-white/5 px-8 py-6">
                <div>
                    <h2 class="font-orbitron text-xl font-bold tracking-wide text-white">Create Reservation</h2>
                    <p class="mt-1 text-xs text-gray-400">Fill in the details below to book a new event.</p>
                </div>
                <button
                    @click="$emit('close')"
                    class="group flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition hover:bg-red-500/20 hover:text-red-400"
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

            <div class="custom-scrollbar max-h-[85vh] overflow-y-auto px-8 py-8">
                <form @submit.prevent="createReservation" class="space-y-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="group relative">
                            <label
                                class="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase transition-colors group-focus-within:text-[#00f5a0]"
                                >Start Date</label
                            >
                            <div class="relative flex items-center">
                                <input
                                    type="date"
                                    v-model="form.event_date"
                                    class="peer w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-transparent shadow-sm transition-all focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
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
                                class="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase transition-colors group-focus-within:text-[#00f5a0]"
                                >End Date</label
                            >
                            <div class="relative flex items-center">
                                <input
                                    type="date"
                                    v-model="form.event_end_date"
                                    class="peer w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-transparent shadow-sm transition-all focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
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
                        <label class="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase">Select Event</label>
                        <div class="relative">
                            <BaseSelect
                                v-model="selectedEvent"
                                :options="eventOptions"
                                placeholder="Choose an event type..."
                                class="custom-select-override"
                                :allow-other="true"
                                @other-input="(val) => (form.event_name = val)"
                            />
                        </div>
                        <p v-if="errors.event_id" class="mt-1 text-xs font-medium text-red-500">{{ errors.event_id[0] }}</p>
                    </div>

                    <div class="group">
                        <label class="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase"> Materials Needed </label>

                        <div class="space-y-3">
                            <div v-for="(item, index) in form.materials" :key="index" class="flex gap-3">
                                <BaseSelect
                                    v-model="item.material_id"
                                    :options="materialOptions"
                                    placeholder="Select material..."
                                    class="custom-select-override flex-1"
                                />

                                <input
                                    type="number"
                                    min="1"
                                    v-model.number="item.quantity"
                                    class="w-24 rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                                    placeholder="Qty"
                                />

                                <button
                                    v-if="form.materials.length > 1"
                                    type="button"
                                    @click="removeMaterial(index)"
                                    class="rounded-xl border border-red-500/20 bg-red-500/10 px-3 text-red-400 hover:border-red-500/50 hover:bg-red-500/20"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="addMaterial"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl border border-[#00f5a0]/30 px-4 py-2 text-xs font-bold tracking-wider text-[#00f5a0] uppercase transition-colors hover:bg-[#00f5a0] hover:text-black"
                        >
                            + Add Material
                        </button>

                        <p v-if="errors.materials" class="mt-1 text-xs font-medium text-red-500">
                            {{ errors.materials[0] }}
                        </p>
                    </div>

                    <div class="group">
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase transition-colors group-focus-within:text-[#00f5a0]"
                            >Notes</label
                        >
                        <textarea
                            v-model="form.event_notes"
                            rows="3"
                            placeholder="Any special requirements or details..."
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-gray-500 shadow-sm transition-all focus:border-[#00f5a0] focus:bg-black focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="relative w-full overflow-hidden rounded-xl bg-[#00f5a0] py-4 text-sm font-bold tracking-wider text-black uppercase transition-all hover:bg-[#00d68f] hover:shadow-[0_0_20px_rgba(0,245,160,0.4)] disabled:cursor-not-allowed disabled:opacity-50"
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

        <Transition name="fade">
            <div v-if="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md">
                <div class="max-w-sm rounded-2xl border border-[#00f5a0]/30 bg-black p-8 text-center shadow-[0_0_30px_rgba(0,245,160,0.2)]">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#00f5a0]/10">
                        <svg class="h-8 w-8 text-[#00f5a0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="mb-2 text-xl font-bold text-[#00f5a0]">Reservation Successful!</h2>
                    <p class="text-gray-400">Your reservation has been created successfully.</p>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref, watch } from 'vue';
import BaseSelect from './BaseSelect.vue';

const emit = defineEmits(['close']);
const submitting = ref(false);
const errors = reactive({});
const successModal = ref(false);
const otherInput = ref('');
const selectedEvent = ref('');

watch(selectedEvent, (val) => {
    if (val === 'other') {
        form.event_id = '';
    } else {
        form.event_id = val;
        form.event_name = '';
    }
});

const form = reactive({
    event_id: '',
    event_date: '',
    event_name: '',
    event_end_date: '',
    materials: [
        {
            material_id: '',
            quantity: 1,
        },
    ],
    event_notes: '',
});

const eventOptions = ref([]);
const materialOptions = ref([]);

watch(otherInput, (val) => {
    if (selectedEvent.value === 'other') {
        form.event_name = val;
    }
});

async function fetchOptions() {
    try {
        const [eventsRes, materialsRes] = await Promise.all([axios.get(route('events.selectList')), axios.get(route('materials.selectList'))]);

        const eventsData = eventsRes.data?.data ?? eventsRes.data ?? [];
        const materialsData = materialsRes.data?.data ?? materialsRes.data ?? [];

        eventOptions.value = [...eventsData.map((e) => ({ value: e.event_id, label: e.event_name })), { value: 'other', label: 'Other' }];
        materialOptions.value = materialsData.map((m) => ({
            value: m.material_id,
            label: m.material_name + (m.material_quantity !== undefined ? ` (Available Qty: ${m.material_quantity})` : ''),
        }));
    } catch (err) {
        console.error('Error fetching select lists:', err);
        eventOptions.value = [];
        materialOptions.value = [];
    }
}

onMounted(fetchOptions);

function addMaterial() {
    form.materials.push({
        material_id: '',
        quantity: 1,
    });
}

function removeMaterial(index) {
    form.materials.splice(index, 1);
}

async function createReservation() {
    submitting.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);

    try {
        const payload = {
            ...form,
            materials: form.materials.map((m) => ({
                material_id: m.material_id,
                quantity: m.quantity,
            })),
        };

        if (!form.event_id && form.event_name) {
            delete payload.event_id;
        }

        const response = await axios.post(route('reserved-online'), payload);

        if (response.data?.result === false) {
            alert(response.data.message);
            return;
        }
        successModal.value = true;
        fetchOptions();
        setTimeout(() => {
            successModal.value = false;
            emit('close');
        }, 3000);
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

/* Date Picker Customization Magic */
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

/* Custom Scrollbar for the form body */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 245, 160, 0.3);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 245, 160, 0.6);
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
