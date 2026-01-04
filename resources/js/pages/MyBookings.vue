<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const bookings = ref([]);
const showPaymentModal = ref(false);
const selectedBooking = ref(null);

const paymentForm = ref({
    reserved_event_id: null,
    amount: '',
    reference_number: '',
    payment_proof: null,
});

const openPaymentModal = (booking) => {
    selectedBooking.value = booking;
    paymentForm.value.reserved_event_id = booking.reserved_event_id;
    paymentForm.value.amount = booking.downpayment_amount;
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    paymentForm.value = {
        reserved_event_id: null,
        amount: '',
        reference_number: '',
        payment_proof: null,
    };
};

const submitPayment = async () => {
    try {
        const formData = new FormData();
        formData.append('reserved_event_id', paymentForm.value.reserved_event_id);
        formData.append('amount', paymentForm.value.amount);
        formData.append('reference_number', paymentForm.value.reference_number);
        if (paymentForm.value.payment_proof) {
            formData.append('payment_proof', paymentForm.value.payment_proof);
        }

        const response = await axios.post('/user/payments/store', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        alert(response.data.message || response.data.msg || 'Payment submitted successfully.');

        closePaymentModal();
        fetchBookings();
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            const messages = Object.values(errors).flat().join('\n');
            alert(messages);
        } else {
            alert(error.response?.data?.message || 'Something went wrong.');
        }
    }
};

const fetchBookings = async () => {
    try {
        const response = await axios.get('/reservations/my-reservations');
        bookings.value = response.data.data;
    } catch (error) {
        console.error('Error fetching bookings:', error);
    }
};

onMounted(() => {
    fetchBookings();
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatCurrency = (amount) => {
    return `₱${Number(amount).toLocaleString()}`;
};

const statusColors = (status) => {
    if (!status) return 'text-gray-500';
    switch (status.toLowerCase()) {
        case 'completed':
            return 'text-blue-400';
        case 'accepted':
            return 'text-green-400';
        case 'confirmed':
            return 'text-green-400';
        case 'pending':
            return 'text-yellow-400';
        case 'cancelled':
            return 'text-red-400';
        default:
            return 'text-gray-400';
    }
};

const enlargeQR = ref(false);
const showDetails = ref(false);
const selectedDetails = ref(null);

const openDetailsModal = (booking) => {
    selectedDetails.value = booking;
    showDetails.value = true;
};

const cancelReservation = async (booking) => {
    if (!confirm('Are you sure you want to cancel this reservation?')) return;

    try {
        const response = await axios.post(route('reservations.cancel'), {
            reserved_event_id: booking.reserved_event_id,
        });

        alert(response.data.message);
        fetchBookings();
    } catch (error) {
        alert(error.response?.data?.message || 'Failed to cancel reservation.');
    }
};
</script>

<template>
    <Head title="My Bookings" />
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-6 shadow-xl">
            <h2 class="font-orbitron mb-6 text-3xl font-bold text-[#00f5a0]">My Bookings</h2>

            <div v-if="bookings.length === 0" class="text-xl text-[#7fbfb0]">You have no bookings.</div>

            <div
                v-for="booking in bookings"
                :key="booking.reserved_event_id"
                class="mb-6 flex items-center justify-between rounded-xl border border-gray-700 bg-white/5 p-6 shadow-lg transition hover:border-[#00f5a0]/30"
            >
                <div class="space-y-2">
                    <h3 class="font-orbitron text-2xl font-bold text-[#00f5a0]">
                        {{ booking.event ? booking.event.event_name : booking.event_name }}
                    </h3>

                    <p class="text-lg text-[#7fbfb0]"><span class="text-gray-400">Date:</span> {{ formatDate(booking.event_date) }}</p>

                    <p class="text-lg text-white">
                        <span class="text-gray-400">Total Cost:</span>
                        <span class="ml-2 font-bold text-[#00f5a0]">
                            {{ formatCurrency(booking.total_cost) }}
                        </span>
                    </p>

                    <p class="text-lg text-white">
                        <span class="text-gray-400">Downpayment Required:</span>
                        <span class="ml-2 font-bold text-[#00f5a0]">
                            {{ formatCurrency(booking.downpayment_amount) }}
                        </span>
                    </p>

                    <p class="text-lg font-medium capitalize" :class="statusColors(booking.status)">
                        <span class="text-gray-400">Status:</span> {{ booking.status }}
                    </p>
                </div>

                <div class="flex flex-col gap-3">
                    <button
                        @click="openDetailsModal(booking)"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-base font-semibold text-white shadow hover:bg-blue-500"
                    >
                        View Details
                    </button>

                    <button
                        v-if="booking.status && booking.status.toLowerCase() !== 'canceled' && booking.status.toLowerCase() === 'pending'"
                        @click="cancelReservation(booking)"
                        class="rounded-lg bg-red-600 px-5 py-3 text-base font-semibold text-white shadow hover:bg-red-500"
                    >
                        Cancel Reservation
                    </button>

                    <button
                        v-if="booking.status && booking.status.toLowerCase() === 'downpayment_update' && Number(booking.downpayment_amount) > 0"
                        @click="openPaymentModal(booking)"
                        class="rounded-lg bg-[#00f5a0] px-5 py-3 text-base font-bold text-black shadow hover:bg-[#02ffbc]"
                    >
                        Make Payment
                    </button>
                </div>
            </div>
        </section>

        <div v-if="showPaymentModal" class="animate-fadeIn fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md">
            <div class="flex w-[850px] rounded-2xl border border-[#00f5a0]/30 bg-[rgba(0,0,0,0.95)] p-8 shadow-[0_0_30px_#00f5a0]">
                <div class="w-2/3 border-r border-[#00f5a0]/20 pr-8">
                    <h3 class="font-orbitron mb-6 text-2xl font-bold text-[#00f5a0]">Submit Payment</h3>

                    <div class="mb-4 space-y-2 text-lg">
                        <p class="text-[#93e3d3]">
                            Event:
                            <span class="ml-2 font-semibold text-white">{{
                                selectedBooking.event ? selectedBooking.event.event_name : selectedBooking.event_name
                            }}</span>
                        </p>

                        <p class="text-[#93e3d3]">
                            Total Cost: <strong class="ml-2 text-white">{{ formatCurrency(selectedBooking.total_cost) }}</strong>
                        </p>

                        <p class="text-[#93e3d3]">
                            Downpayment: <strong class="ml-2 text-[#00f5a0]">{{ formatCurrency(selectedBooking.downpayment_amount) }}</strong>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-base font-medium text-[#7fbfb0]">Amount to Pay</label>
                            <input
                                type="number"
                                v-model="paymentForm.amount"
                                class="w-full rounded-lg border border-[#00f5a0]/30 bg-black/40 px-4 py-3 text-lg text-white placeholder-gray-500 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-base font-medium text-[#7fbfb0]">Reference Number</label>
                            <input
                                type="text"
                                v-model="paymentForm.reference_number"
                                required
                                class="w-full rounded-lg border border-[#00f5a0]/30 bg-black/40 px-4 py-3 text-lg text-white placeholder-gray-500 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-base font-medium text-[#7fbfb0]">Upload Proof</label>
                            <input
                                type="file"
                                @change="paymentForm.payment_proof = $event.target.files[0]"
                                class="w-full text-lg text-[#00f5a0]"
                                required
                            />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <button
                            @click="closePaymentModal"
                            class="rounded-lg border border-gray-500 bg-transparent px-6 py-3 text-base text-white hover:bg-gray-700/50"
                        >
                            Cancel
                        </button>

                        <button @click="submitPayment" class="rounded-lg bg-[#00f5a0] px-6 py-3 text-base font-bold text-black hover:bg-[#07c686]">
                            Submit Payment
                        </button>
                    </div>
                </div>

                <div class="flex w-1/3 flex-col items-center justify-center pl-8">
                    <h4 class="font-orbitron mb-4 text-xl font-bold text-[#00f5a0]">Scan QR to Pay</h4>

                    <div
                        class="cursor-pointer rounded-2xl border-2 border-[#00f5a0]/30 bg-black/30 p-4 shadow-[0_0_15px_#00f5a0] transition hover:scale-105"
                        @click="enlargeQR = true"
                    >
                        <img src="/qr_code/QRCode.png" alt="QR Code" class="h-56 w-56 object-contain" />
                    </div>

                    <p class="mt-4 text-center text-sm text-[#7fbfb0]">Click to enlarge</p>
                </div>
            </div>
        </div>

        <div
            v-if="enlargeQR"
            class="animate-fadeIn fixed inset-0 z-60 flex items-center justify-center bg-black/80 backdrop-blur-md"
            @click="enlargeQR = false"
        >
            <div class="rounded-2xl border-2 border-[#00f5a0] bg-black p-8 shadow-[0_0_50px_#00f5a0]">
                <img src="/qr_code/QRCode.png" alt="Enlarged QR Code" class="h-[500px] w-[500px] object-contain" />
            </div>
        </div>

        <div v-if="showDetails" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-md transition">
            <div
                class="animate-scaleIn max-h-[95vh] w-[900px] overflow-y-auto rounded-3xl border border-[#00f5a0]/40 bg-[#0a0a0a] p-10 shadow-[0_0_50px_rgba(0,245,160,0.2)]"
            >
                <div class="mb-8 border-b border-[#00f5a0]/30 pb-6">
                    <h2 class="font-orbitron text-4xl font-bold tracking-wide text-[#00f5a0]">Booking Details</h2>
                    <p class="mt-2 text-lg text-[#91ffd7]">Viewing reservation information</p>
                </div>

                <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <p class="mb-2 text-base font-bold tracking-wider text-gray-400 uppercase">Event</p>
                        <p class="text-2xl font-semibold text-white">
                            {{ selectedDetails.event ? selectedDetails.event.event_name : selectedDetails.event_name }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-2 text-base font-bold tracking-wider text-gray-400 uppercase">Status</p>
                        <span :class="statusColors(selectedDetails.status)" class="text-2xl font-bold uppercase">
                            {{ selectedDetails.status }}
                        </span>
                    </div>

                    <div>
                        <p class="mb-2 text-base font-bold tracking-wider text-gray-400 uppercase">Start Date</p>
                        <p class="text-xl text-[#dff]">{{ formatDate(selectedDetails.event_date) }}</p>
                    </div>

                    <div>
                        <p class="mb-2 text-base font-bold tracking-wider text-gray-400 uppercase">End Date</p>
                        <p class="text-xl text-[#dff]">{{ formatDate(selectedDetails.event_end_date) }}</p>
                    </div>

                    <div>
                        <p class="mb-2 text-base font-bold tracking-wider text-gray-400 uppercase">Total Cost</p>
                        <p class="text-3xl font-bold text-[#00f5a0]">
                            {{ formatCurrency(selectedDetails.total_cost) }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-2 text-base font-bold tracking-wider text-gray-400 uppercase">Downpayment</p>
                        <p class="text-3xl font-bold text-[#08d6a0]">
                            {{ formatCurrency(selectedDetails.downpayment_amount) }}
                        </p>
                    </div>
                </div>

                <div class="mt-10 border-t border-[#00f5a0]/30 pt-6">
                    <p class="mb-3 text-base font-bold tracking-wider text-gray-400 uppercase">Notes</p>
                    <p class="rounded-xl bg-white/5 p-6 text-lg leading-relaxed text-[#cfe]">
                        {{ selectedDetails.event_notes ?? 'No notes provided.' }}
                    </p>
                </div>

                <div class="mt-10 border-t border-[#00f5a0]/30 pt-6">
                    <h3 class="font-orbitron mb-4 text-2xl font-bold tracking-wide text-[#00f5a0]">Equipments Used</h3>

                    <div v-if="selectedDetails.materials.length > 0" class="grid grid-cols-1 gap-4">
                        <div
                            v-for="mat in selectedDetails.materials"
                            :key="mat.reserved_material_id"
                            class="rounded-lg border border-[#00f5a0]/20 bg-white/5 p-4"
                        >
                            <p class="text-xl font-bold text-white">{{ mat.material_name || 'Unknown Item' }}</p>
                            <p class="mt-1 text-base text-gray-400">
                                {{ mat.material_description }}
                            </p>
                            <p class="mt-1 text-base text-[#00f5a0]">Quantity: {{ mat.material_quantity }}</p>
                        </div>
                    </div>

                    <div v-else class="text-lg text-[#7fbfb0]">No equipments attached.</div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button
                        @click="showDetails = false"
                        class="rounded-lg bg-[#00f5a0] px-8 py-3 text-lg font-bold text-black transition hover:bg-[#07c686]"
                    >
                        Close Details
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style>
.animate-fadeIn {
    animation: fadeIn 0.25s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-scaleIn {
    animation: scaleIn 0.25s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
