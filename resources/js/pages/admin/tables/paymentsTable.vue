<template>
    <Head title="Admin Dashboard - Payments" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-white">Event Payments</h1>
                <p class="text-xl text-white">Manage event payments and its details.</p>
            </div>

            <!-- Table with search toolbar -->
            <BaseTable :title="'Event Payments'" :columns="columns" :data="payments" :pagination="pagination" @page-changed="fetch">
                <!-- Toolbar -->
                <template #toolbar>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search payments..."
                        class="rounded-md border border-gray-600 bg-black/30 px-4 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                    />
                </template>

                <!-- Custom Amount Column -->
                <template #cell-amount="{ value }">
                    <span v-if="value !== null" class="font-semibold text-[#00f5a0]">
                        {{ formatCurrency(value) }}
                    </span>
                    <span v-else class="text-sm text-gray-500">₱0.00</span>
                </template>

                <template #cell-payment_proof="{ value }">
                    <div v-if="value">
                        <img
                            :src="value"
                            alt="Payment Proof"
                            class="max-h-20 w-auto cursor-pointer rounded border border-gray-600 object-contain"
                            @click="openPreview(value)"
                        />
                    </div>
                    <span v-else class="text-sm text-gray-500">No proof</span>
                </template>

                <!-- Custom Payment Date Column -->
                <template #cell-payment_date="{ value }">
                    <span v-if="value" class="text-white">{{ formatDate(value) }}</span>
                    <span v-else class="text-sm text-gray-500">No date</span>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button @click="openEditPayment(row)" class="mr-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">Edit</button>
                    <button @click="deletePayment(row)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Delete</button>
                </template>
            </BaseTable>

            <transition name="fade">
                <div v-if="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70">
                    <div class="relative max-w-3xl rounded bg-gray-900 p-4 shadow-lg">
                        <button
                            @click="showPreviewModal = false"
                            class="absolute top-2 right-2 rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700"
                        >
                            Close
                        </button>
                        <img :src="previewImage" alt="Preview" class="max-h-[80vh] w-auto rounded" />
                    </div>
                </div>
            </transition>

            <!-- Edit Payment Modal -->
            <EditModal
                :show="showEditModal"
                title="Edit Payment Status"
                :fields="paymentFields"
                v-model="selectedPayment"
                :columns="2"
                @close="showEditModal = false"
                @save="updatePayment"
            >
                <div class="col-span-2">
                    <img
                        v-if="selectedPayment.payment_proof"
                        :src="selectedPayment.payment_proof"
                        alt="Payment Proof"
                        class="mt-1 max-h-64 w-full rounded border border-gray-600 object-contain"
                    />
                </div>
            </EditModal>
        </section>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import EditModal from '@/components/EditModal.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const columns = [
    { key: 'user_name', label: 'User' },
    { key: 'event_name', label: 'Event' },
    { key: 'amount', label: 'Amount' },
    { key: 'reference_number', label: 'Reference' },
    { key: 'payment_date', label: 'Payment Date' },
    { key: 'payment_proof', label: 'Payment Proof' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Actions' },
];

const payments = ref([]);
const pagination = ref(null);
const search = ref('');
const currentPage = ref(1);

// Fetch payments
async function fetch(page = 1) {
    try {
        const response = await axios.get(route('admin.payments.list'), {
            params: { search: search.value, page },
        });
        if (response.data.result) {
            payments.value = response.data.data;
            pagination.value = response.data.pagination;
        } else {
            payments.value = [];
            pagination.value = null;
        }
    } catch (error) {
        console.error('Error fetching payments:', error);
    }
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'No date';
    const date = new Date(dateString);
    if (isNaN(date)) return 'Invalid date';
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

// Format currency
function formatCurrency(amount) {
    if (!amount || isNaN(amount)) return '₱0.00';
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 2 }).format(Number(amount));
}

onMounted(() => fetch());

// Watch search
watch(search, () => fetch(1));

const showPreviewModal = ref(false);
const previewImage = ref(null);

function openPreview(imageUrl) {
    previewImage.value = imageUrl;
    showPreviewModal.value = true;
}

// ===== Edit Payment =====
const showEditModal = ref(false);
const selectedPayment = ref({});

const paymentFields = [
    { key: 'user_name', label: 'User', type: 'text', readonly: true },
    { key: 'event_name', label: 'Event', type: 'text', readonly: true },
    { key: 'amount', label: 'Amount', type: 'number', readonly: true },
    { key: 'reference_number', label: 'Reference Number', type: 'text', readonly: true },
    { key: 'payment_date', label: 'Payment Date', type: 'text', readonly: true },

    {
        key: 'status',
        label: 'Status',
        type: 'baseselect',
        options: [
            { value: 'pending', label: 'Pending' },
            { value: 'completed', label: 'Completed' },
            { value: 'failed', label: 'Failed' },
        ],
    },
    { key: 'payment_proof', label: 'Payment Proof', type: 'image', readonly: true },
];

function openEditPayment(payment) {
    selectedPayment.value = {
        payment_id: payment.payment_id,
        user_name: payment.user_name,
        event_name: payment.event_name,
        amount: payment.amount,
        reference_number: payment.reference_number,
        payment_date: payment.payment_date,
        status: payment.status,
        payment_proof: payment.payment_proof,
    };
    showEditModal.value = true;
}

async function updatePayment(updatedData) {
    try {
        await axios.post(route('admin.payments.update'), updatedData);
        showEditModal.value = false;
        fetch(currentPage.value);
    } catch (error) {
        console.error('Error updating payment:', error.response?.data || error);
    }
}

// ===== Delete Payment =====
async function deletePayment(payment) {
    const confirmed = window.confirm(`Are you sure you want to delete payment for "${payment.event_name}"?`);
    if (!confirmed) return;

    try {
        await axios.post(route('admin.payments.destroy'), { payment_id: payment.payment_id });
        fetch(currentPage.value);
    } catch (error) {
        console.error('Error deleting payment:', error.response?.data || error);
    }
}
</script>
