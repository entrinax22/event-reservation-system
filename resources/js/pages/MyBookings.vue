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

        // Always show the correct message
        alert(response.data.message || response.data.msg || 'Payment submitted successfully.');

        closePaymentModal();
        fetchBookings();
    } catch (error) {
        if (error.response?.status === 422) {
            // validation errors
            const errors = error.response.data.errors;
            const messages = Object.values(errors).flat().join('\n');
            alert(messages);
        } else {
            // other errors
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
            return 'text-blue-500';
        case 'accepted':
            return 'text-green-500';
        case 'confirmed':
            return 'text-green-500';
        case 'pending':
            return 'text-yellow-500';
        case 'cancelled':
            return 'text-red-500';
        default:
            return 'text-gray-500';
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
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <h2 class="font-orbitron mb-4 text-xl text-[#00f5a0]">My Bookings</h2>

            <div v-if="bookings.length === 0" class="text-sm text-[#7fbfb0]">You have no bookings.</div>

            <!--Bookings-->
            <div
                v-for="booking in bookings"
                :key="booking.reserved_event_id"
                class="mb-4 flex items-center justify-between rounded-lg border border-gray-700 p-4"
            >
                <div>
                    <h3 class="font-orbitron mb-2 text-lg text-[#00f5a0]">Event: {{ booking.event.event_name }}</h3>
                    <p class="text-sm text-[#7fbfb0]">Date: {{ formatDate(booking.event_date) }}</p>

                    <!-- TOTAL COST -->
                    <p class="text-sm text-white">
                        Total Cost:
                        <span class="font-semibold text-[#00f5a0]">
                            {{ formatCurrency(booking.total_cost) }}
                        </span>
                    </p>

                    <!-- DOWNPAYMENT REQUIRED -->
                    <p class="text-sm text-white">
                        Downpayment Required:
                        <span class="font-semibold text-[#00f5a0]">
                            {{ formatCurrency(booking.downpayment_amount) }}
                        </span>
                    </p>

                    <p class="text-sm capitalize" :class="statusColors(booking.status)">Status: {{ booking.status }}</p>
                </div>

                <div class="flex gap-2">
                    <button @click="openDetailsModal(booking)" class="rounded bg-blue-500 px-3 py-2 text-sm text-white hover:bg-blue-600">
                        View Details
                    </button>
                    <button
                        v-if="booking.status && booking.status.toLowerCase() !== 'canceled' && booking.status.toLowerCase() === 'pending'"
                        @click="cancelReservation(booking)"
                        class="rounded bg-red-500 px-3 py-2 text-sm text-white hover:bg-red-600"
                    >
                        Cancel Reservation
                    </button>

                    <button
                        v-if="booking.status && booking.status.toLowerCase() === 'downpayment_update' && Number(booking.downpayment_amount) > 0"
                        @click="openPaymentModal(booking)"
                        class="rounded bg-[#00f5a0] px-3 py-2 text-sm text-black hover:bg-[#07c686]"
                    >
                        Make Payment
                    </button>
                </div>
            </div>
        </section>

        <div v-if="showPaymentModal" class="animate-fadeIn fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="flex w-[720px] rounded-2xl border border-[#00f5a0]/30 bg-[rgba(0,0,0,0.88)] p-6 shadow-[0_0_20px_#00f5a0]">
                <!-- LEFT: PAYMENT FORM -->
                <div class="w-2/3 border-r border-[#00f5a0]/20 pr-6">
                    <h3 class="font-orbitron mb-4 text-lg text-[#00f5a0]">Submit Payment</h3>

                    <p class="mb-1 text-sm text-[#93e3d3]">
                        Event:
                        <span class="font-semibold text-white">{{ selectedBooking.event.event_name }}</span>
                    </p>

                    <p class="text-sm text-[#93e3d3]">
                        Total Cost:
                        <strong class="text-white">{{ formatCurrency(selectedBooking.total_cost) }}</strong>
                    </p>

                    <p class="mb-4 text-sm text-[#93e3d3]">
                        Downpayment Required:
                        <strong class="text-[#00f5a0]">{{ formatCurrency(selectedBooking.downpayment_amount) }}</strong>
                    </p>

                    <label class="mb-1 block text-sm font-medium text-[#7fbfb0]">Amount to Pay</label>
                    <input
                        type="number"
                        v-model="paymentForm.amount"
                        class="mb-3 w-full rounded-md border border-[#00f5a0]/20 bg-black/40 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:outline-none"
                    />

                    <label class="mb-1 block text-sm font-medium text-[#7fbfb0]">Reference Number</label>
                    <input
                        type="text"
                        v-model="paymentForm.reference_number"
                        required
                        class="mb-3 w-full rounded-md border border-[#00f5a0]/20 bg-black/40 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:outline-none"
                    />

                    <label class="mb-1 block text-sm font-medium text-[#7fbfb0]">Upload Proof</label>
                    <input type="file" @change="paymentForm.payment_proof = $event.target.files[0]" class="mb-4 w-full text-[#00f5a0]" required />

                    <div class="flex justify-end gap-2">
                        <button
                            @click="closePaymentModal"
                            class="rounded-md border border-gray-500 bg-transparent px-4 py-2 text-white hover:bg-gray-700/50"
                        >
                            Cancel
                        </button>

                        <button @click="submitPayment" class="rounded-md bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#07c686]">
                            Submit
                        </button>
                    </div>
                </div>

                <!-- RIGHT: QR CODE PANEL -->
                <div class="flex w-1/3 flex-col items-center justify-center pl-6">
                    <h4 class="font-orbitron text-md mb-3 text-[#00f5a0]">Scan QR to Pay</h4>

                    <div class="rounded-xl border border-[#00f5a0]/30 bg-black/30 p-3 shadow-[0_0_12px_#00f5a0]" @click="enlargeQR = true">
                        <img src="/qr_code/QRCode.png" alt="QR Code" class="h-48 w-48 object-contain" />
                    </div>

                    <p class="mt-3 text-center text-xs text-[#7fbfb0]">After paying, enter the reference number and upload proof.</p>
                </div>
            </div>
        </div>

        <div
            v-if="enlargeQR"
            class="animate-fadeIn fixed inset-0 z-60 flex items-center justify-center bg-black/70 backdrop-blur-sm"
            @click="enlargeQR = false"
        >
            <div class="rounded-xl border border-[#00f5a0]/40 bg-black/80 p-6 shadow-[0_0_20px_#00f5a0]">
                <img src="/qr_code/QRCode.png" alt="Enlarged QR Code" class="h-96 w-96 object-contain" />
            </div>
        </div>

        <div v-if="showDetails" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md transition">
            <div
                class="animate-scaleIn max-h-[92vh] w-[820px] overflow-y-auto rounded-2xl border border-[#00f5a0]/40 bg-gradient-to-b from-[rgba(0,0,0,0.92)] to-[rgba(0,20,20,0.92)] p-7 shadow-[0_0_35px_#00f5a0]"
            >
                <!-- HEADER -->
                <div class="mb-6 border-b border-[#00f5a0]/30 pb-4">
                    <h2 class="font-orbitron text-2xl tracking-wide text-[#00f5a0]">Booking Details</h2>
                    <p class="mt-1 text-xs text-[#91ffd7]">Viewing reservation information</p>
                </div>

                <!-- GRID INFO -->
                <div class="grid grid-cols-2 gap-x-10 gap-y-3">
                    <div>
                        <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">Event</p>
                        <p class="text-base text-[#dff]">{{ selectedDetails.event.event_name }}</p>
                    </div>

                    <div>
                        <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">Status</p>
                        <span :class="statusColors(selectedDetails.status)" class="text-base">
                            {{ selectedDetails.status }}
                        </span>
                    </div>

                    <div>
                        <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">Start Date</p>
                        <p class="text-base text-[#dff]">{{ formatDate(selectedDetails.event_date) }}</p>
                    </div>

                    <div>
                        <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">End Date</p>
                        <p class="text-base text-[#dff]">{{ formatDate(selectedDetails.event_end_date) }}</p>
                    </div>

                    <div>
                        <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">Total Cost</p>
                        <p class="text-lg font-semibold text-[#00f5a0]">
                            {{ formatCurrency(selectedDetails.total_cost) }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">Downpayment</p>
                        <p class="text-lg font-semibold text-[#08d6a0]">
                            {{ formatCurrency(selectedDetails.downpayment_amount) }}
                        </p>
                    </div>
                </div>

                <!-- NOTES -->
                <div class="mt-7 border-t border-[#00f5a0]/30 pt-4">
                    <p class="mb-1 text-sm tracking-wide text-[#889] uppercase">Notes</p>
                    <p class="rounded-lg bg-black/30 p-3 text-sm leading-relaxed text-[#cfe]">
                        {{ selectedDetails.event_notes ?? 'No notes provided.' }}
                    </p>
                </div>

                <!-- MATERIALS -->
                <div class="mt-8 border-t border-[#00f5a0]/30 pt-4">
                    <h3 class="font-orbitron mb-3 text-lg tracking-wide text-[#00f5a0]">Materials Used</h3>

                    <div v-if="selectedDetails.materials.length > 0">
                        <div
                            v-for="mat in selectedDetails.materials"
                            :key="mat.reserved_material_id"
                            class="mb-2 rounded-md border border-[#00f5a0]/20 bg-black/20 p-3"
                        >
                            <p class="font-semibold text-[#dff]">{{ mat.material.material_name }}</p>
                            <p class="text-sm text-[#aef]">
                                {{ mat.material.material_description }}
                            </p>
                        </div>
                    </div>

                    <div v-else class="text-sm text-[#7fbfb0]">No materials attached.</div>
                </div>

                <!-- FOOTER -->
                <div class="mt-8 flex justify-end">
                    <button
                        @click="showDetails = false"
                        class="rounded-md bg-[#00f5a0] px-5 py-2.5 font-semibold text-black transition hover:bg-[#07c686]"
                    >
                        Close
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
        transform: scale(0.8);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
