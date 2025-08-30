<template>
    <Head title="Admin Dashboard - Reserved Events" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-[#00f5a0]">
                    Reserved Events
                </h1>
                <p class="text-sm text-[#7fbfb0]">
                    Manage reservations, events, and assigned materials.
                </p>
            </div>

            <!-- Table with search toolbar -->
            <BaseTable
                :title="'Reserved Events'"
                :columns="columns"
                :data="reservedEvents"
                :pagination="pagination"
                @page-changed="fetchReservedEvents"
            >
                <!-- Search + Add Button -->
                <template #toolbar>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search materials..."
                        class="rounded-md border border-gray-600 bg-black/30 px-4 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                    />
                    <button
                        @click="go"
                        class="rounded-md bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#00c88a] whitespace-nowrap text-sm"
                    >
                        Add Event
                    </button>
                </template>

                <!-- Custom Event Date Column -->
                <template #cell-event_date="{ value }">
                    <span v-if="value" class="text-white">
                        {{ formatDate(value) }}
                    </span>
                    <span v-else class="text-gray-500 text-sm">No date set</span>
                </template>

                <!-- Custom Event End Date Column -->
                <template #cell-event_end_date="{ value }">
                    <span v-if="value" class="text-white">
                        {{ formatDate(value) }}
                    </span>
                    <span v-else class="text-gray-500 text-sm">No end date</span>
                </template>

                <!-- Custom Total Cost Column -->
                <template #cell-total_cost="{ value }">
                    <span v-if="value !== null && value !== undefined" class="font-semibold text-[#00f5a0]">
                        {{ formatCurrency(value) }}
                    </span>
                    <span v-else class="text-gray-500 text-sm">₱0.00</span>
                </template>

                <!-- Custom Downpayment Column -->
                <template #cell-downpayment_amount="{ value }">
                    <span v-if="value !== null && value !== undefined" class="font-medium text-blue-400">
                        {{ formatCurrency(value) }}
                    </span>
                    <span v-else class="text-gray-500 text-sm">₱0.00</span>
                </template>

                <!-- Custom Materials Column -->
                <template #cell-materials="{ value }">
                    <div class="max-w-xs">
                        <div v-if="value && value.length > 0" class="space-y-1">
                            <div
                                v-for="material in value"
                                :key="material.material_id"
                                class="rounded bg-gray-700 px-2 py-1 text-xs"
                            >
                                <div class="font-medium text-[#00f5a0]">
                                    {{ material.material_name }}
                                </div>
                                <div class="text-gray-300 truncate">
                                    {{ material.material_description }}
                                </div>
                            </div>
                        </div>
                        <span v-else class="text-gray-500 text-sm">No materials</span>
                    </div>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button
                        @click="openEditReservation(row)"
                        class="mr-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                    >
                        Edit
                    </button>
                    <button
                        @click="deleteReservation(row)"
                        class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700"
                    >
                        Delete
                    </button>
                </template>
            </BaseTable>

            <!-- Edit Modal -->
            <EditModal
                :show="showEditModal"
                title="Edit Reservation"
                :fields="reservationFields"
                v-model="selectedReservation"
                @close="showEditModal = false"
                @save="updateReservation"
            />
        </section>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import EditModal from '@/components/EditModal.vue'
import { Head, router } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue'
import axios from 'axios'

// ===== Table Columns =====
const columns = [
    { key: 'reserved_event_id', label: 'ID' },
    { key: 'user_name', label: 'Client Name' },
    { key: 'event_name', label: 'Event' },
    { key: 'event_date', label: 'Event Date' },
    { key: 'event_end_date', label: 'Event End Date' },
    { key: 'total_cost', label: 'Total Cost' },
    { key: 'downpayment_amount', label: 'Downpayment' },
    { key: 'materials', label: 'Materials' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Actions' },
]

const reservedEvents = ref([])
const pagination = ref(null)
const search = ref('')
const currentPage = ref(1)

// Options for select dropdowns
const userOptions = ref([])
const eventOptions = ref([])
const materialOptions = ref([])

// ===== Fetch Select Options =====
async function fetchSelectOptions() {
    try {
        const [usersRes, eventsRes, materialsRes] = await Promise.all([
            axios.get(route('admin.users.selectList')),
            axios.get(route('admin.events.selectList')),
            axios.get(route('admin.materials.selectList'))
        ])
        
        userOptions.value = usersRes.data.data.map(u => ({ 
            value: u.user_id, 
            label: u.name 
        }))
        
        eventOptions.value = eventsRes.data.data.map(e => ({ 
            value: e.event_id, 
            label: e.event_name 
        }))
        
        materialOptions.value = materialsRes.data.data.map(m => ({ 
            value: m.material_id, 
            label: m.material_name 
        }))
    } catch (error) {
        console.error('Error fetching select options:', error)
    }
}

// ===== Date Formatting Function =====
function formatDate(dateString) {
    if (!dateString) return 'No date set'
    
    try {
        const date = new Date(dateString)
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            return 'Invalid date'
        }
        
        // Format options
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            // Uncomment below if you want to include time
            // hour: '2-digit',
            // minute: '2-digit',
            // hour12: true
        }
        
        return date.toLocaleDateString('en-US', options)
    } catch (error) {
        console.error('Error formatting date:', error)
        return 'Invalid date'
    }
}

// ===== Currency Formatting Function =====
function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return '₱0.00'
    }
    
    try {
        // Convert to number if it's a string
        const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount
        
        // Format with Philippine Peso symbol and proper decimal places
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(numAmount)
    } catch (error) {
        console.error('Error formatting currency:', error)
        return '₱0.00'
    }
}

// ===== Fetch Function =====
async function fetchReservedEvents(page = 1) {
    currentPage.value = page

    try {
        const response = await axios.get(route('admin.reserved-events.list'), {
            params: {
                search: search.value,
                page,
            },
        })

        if (response.data.result) {
            reservedEvents.value = response.data.data
            pagination.value = response.data.pagination
        } else {
            reservedEvents.value = []
            pagination.value = null
        }
    } catch (error) {
        console.error('Failed to fetch reserved events:', error)
        reservedEvents.value = []
        pagination.value = null
    }
}

// ===== Fetch on Mount =====
onMounted(() => {
    fetchReservedEvents()
    fetchSelectOptions() // Load dropdown options on component mount
})

// ===== Search Watcher =====
watch(search, () => {
    fetchReservedEvents(1)
})

// ===== Navigation to Create Page =====
function go() {
    router.get(route('admin.reserved-events.create'))
}

// ===== Edit / Update / Delete =====
const showEditModal = ref(false)
const selectedReservation = ref({})

const reservationFields = [
    { key: 'user_id', label: 'User', type: 'baseselect', options: [] },
    { key: 'event_id', label: 'Event', type: 'baseselect', options: [] },
    { key: 'event_date', label: 'Event Date', type: 'date' },
    { key: 'event_end_date', label: 'End Date', type: 'date' },
    { key: 'total_cost', label: 'Total Cost', type: 'number' },
    { key: 'downpayment_amount', label: 'Downpayment (5% of Total)', type: 'number' },
    { key: 'materials', label: 'Materials', type: 'baseselect', multiple: true, options: [] },
    { key: 'event_notes', label: 'Event Notes', type: 'textarea' },
    {
        key: 'status',
        label: 'Status',
        type: 'baseselect',
        options: [
            { value: 'pending', label: 'Pending' },
            { value: 'accepted', label: 'Accepted' },
            { value: 'downpayment_update', label: 'Downpayment Update' },
            { value: 'completed', label: 'Completed' },
            { value: 'cancelled', label: 'Cancelled' },
        ],
    }
]

async function openEditReservation(reservation) {
    // Fetch select options if not already loaded
    if (userOptions.value.length === 0 || eventOptions.value.length === 0 || materialOptions.value.length === 0) {
        await fetchSelectOptions()
    }
    
    // Update the field options dynamically
    reservationFields.forEach(field => {
        if (field.key === 'user_id') {
            field.options = userOptions.value
        } else if (field.key === 'event_id') {
            field.options = eventOptions.value
        } else if (field.key === 'materials') {
            field.options = materialOptions.value
        }
    })
    
    // Clone the reservation data
    const reservationData = { ...reservation }
    
    // Convert materials array to array of material_ids for the form
    if (reservationData.materials && Array.isArray(reservationData.materials)) {
        reservationData.materials = reservationData.materials.map(m => m.material_id)
        } else {
        reservationData.materials = []
    }
    
    // Auto-calculate downpayment as 5% of total cost
    if (reservationData.total_cost && reservationData.total_cost > 0) {
        reservationData.downpayment_amount = parseFloat((reservationData.total_cost * 0.05).toFixed(2))
    }
    
    selectedReservation.value = reservationData
    showEditModal.value = true
}

async function updateReservation(updatedData) {
    try {
        const payload = {
            ...updatedData,
            materials: (updatedData.materials || []).map(id => ({ material_id: id }))
        }

        const response = await axios.post(route('admin.reserved-events.update'), payload)
        showEditModal.value = false
        fetchReservedEvents(currentPage.value)

        console.log('Reservation updated successfully:', response.data)
    } catch (error) {
        console.error('Error updating reservation:', error.response?.data || error)
    }
}

async function deleteReservation(reservation) {
    try {
        const confirmed = window.confirm(
            `Are you sure you want to delete reservation for event "${reservation.event_name}"?`
        )
        if (!confirmed) return

        const response = await axios.post(route('admin.reserved-events.destroy'), {
            reserved_event_id: reservation.reserved_event_id,
        })
        showEditModal.value = false
        fetchReservedEvents(currentPage.value)

        console.log('Reservation deleted successfully:', response.data)
    } catch (error) {
        console.error('Error deleting reservation:', error.response?.data || error)
    }
}
</script>