<template>
  <Head title="Admin Dashboard - Reserve Events" />
  <AdminLayout>
    <section class="space-y-8">
      <!-- Header -->
      <div class="border-b border-[#00f5a0]/30 pb-4">
        <h1 class="font-orbitron text-3xl font-bold tracking-wide text-[#00f5a0]">
          Reserve Events
        </h1>
        <p class="text-sm text-[#7fbfb0]">Create and manage reserved events.</p>
      </div>

      <!-- Create Event Form -->
      <form 
        @submit.prevent="createReservation" 
        class="max-w-3xl space-y-6 rounded-xl bg-[rgba(0,0,0,0.85)] p-6 shadow-xl"
      >
        <h2 class="font-orbitron text-xl text-[#00f5a0]">Create New Reservation</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Select User -->
          <div>
            <label class="block text-sm text-[#7fbfb0]">Select User</label>
            <BaseSelect
              v-model="form.user_id"
              :options="userOptions"
              placeholder="Choose a user"
            />
            <p v-if="errors.user_id" class="mt-1 text-xs text-red-400">{{ errors.user_id[0] }}</p>
          </div>

          <!-- Event Date -->
          <div>
            <label for="event_date" class="block text-sm text-[#7fbfb0]">Event Date</label>
            <input
              id="event_date"
              type="date"
              v-model="form.event_date"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              required
            />
            <p v-if="errors.event_date" class="mt-1 text-xs text-red-400">{{ errors.event_date[0] }}</p>
          </div>

          <!-- Event End Date -->
          <div>
            <label for="event_end_date" class="block text-sm text-[#7fbfb0]">Event End Date</label>
            <input
              id="event_end_date"
              type="date"
              v-model="form.event_end_date"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              required
            />
            <p v-if="errors.event_end_date" class="mt-1 text-xs text-red-400">{{ errors.event_end_date[0] }}</p>
          </div>

          <!-- Select Event -->
          <div>
            <label class="block text-sm text-[#7fbfb0]">Select Event</label>
            <BaseSelect
              v-model="form.event_id"
              :options="eventOptions"
              placeholder="Choose an event"
            />
            <p v-if="errors.event_id" class="mt-1 text-xs text-red-400">{{ errors.event_id[0] }}</p>
          </div>

          <!-- Select Materials -->
          <div>
            <label class="block text-sm text-[#7fbfb0]">Select Materials</label>
            <BaseSelect
              v-model="form.materials"
              :options="materialOptions"
              multiple
              placeholder="Choose materials"
            />
            <p v-if="errors.materials" class="mt-1 text-xs text-red-400">{{ errors.materials[0] }}</p>
          </div>

          <!-- Total Cost -->
          <div>
            <label for="total_cost" class="block text-sm text-[#7fbfb0]">Total Cost</label>
            <input
              id="total_cost"
              type="number"
              v-model="form.total_cost"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              placeholder="Enter total cost"
              required
            />
            <p v-if="errors.total_cost" class="mt-1 text-xs text-red-400">{{ errors.total_cost[0] }}</p>
          </div>

          <!-- Downpayment -->
          <div>
            <label for="downpayment_amount" class="block text-sm text-[#7fbfb0]">Downpayment Amount</label>
            <input
              id="downpayment_amount"
              type="number"
              v-model="form.downpayment_amount"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              readonly
            />
            <p v-if="errors.downpayment_amount" class="mt-1 text-xs text-red-400">{{ errors.downpayment_amount[0] }}</p>
          </div>

          <!-- Status -->
          <div >
            <label for="status" class="block text-sm text-[#7fbfb0]">Status</label>
            <select
              id="status"
              v-model="form.status"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              required
            >
              <option value="" disabled>Select status</option>
              <option value="pending">Pending</option>
              <option value="accepted">Accepted</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <p v-if="errors.status" class="mt-1 text-xs text-red-400">{{ errors.status[0] }}</p>
          </div>

          <!-- Notes (full width) -->
          <div class="md:col-span-2">
            <label for="event_notes" class="block text-sm text-[#7fbfb0]">Event Notes</label>
            <textarea
              id="event_notes"
              v-model="form.event_notes"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              rows="3"
              placeholder="Additional notes..."
            ></textarea>
            <p v-if="errors.event_notes" class="mt-1 text-xs text-red-400">{{ errors.event_notes[0] }}</p>
          </div>

          <!-- Submit button (full width) -->
          <div class="md:col-span-2">
            <button
              type="submit"
              :disabled="submitting"
              class="w-full rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:opacity-50"
            >
              {{ submitting ? 'Creating...' : 'Create Reservation' }}
            </button>
          </div>
        </div>
      </form>

    </section>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import { ref, onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import BaseSelect from '@/components/BaseSelect.vue'

const form = ref({
  user_id: '',
  event_date: '',
  event_end_date: '',
  event_id: '',
  materials: [],
  total_cost: '',
  event_notes: '',
  downpayment_amount: '',
  status: ''
})

const errors = ref({})
const submitting = ref(false)

const userOptions = ref([])
const eventOptions = ref([])
const materialOptions = ref([])

async function fetchOptions() {
  try {
    const [usersRes, eventsRes, materialsRes] = await Promise.all([
      axios.get(route('admin.users.selectList')),
      axios.get(route('admin.events.selectList')),
      axios.get(route('admin.materials.selectList'))
    ])
    userOptions.value = usersRes.data.data.map(u => ({ value: u.user_id, label: u.name }))
    eventOptions.value = eventsRes.data.data.map(e => ({ value: e.event_id, label: e.event_name }))
    materialOptions.value = materialsRes.data.data.map(m => ({ value: m.material_id, label: m.material_name }))
  } catch (err) {
    console.error('Error fetching select lists:', err)
  }
}

watch(() => form.value.total_cost, (newVal) => {
  if (newVal && !isNaN(newVal)) {
    form.value.downpayment_amount = (parseFloat(newVal) * 0.05).toFixed(2)
  } else {
    form.value.downpayment_amount = ''
  }
})

async function createReservation() {
  submitting.value = true
  errors.value = {}
  try {
    const payload = {
      ...form.value,
      materials: form.value.materials.map(id => ({ material_id: id })) 
    }

    await axios.post(route('admin.reserved-events.store'), payload)
    form.value = {
      user_id: '',
      event_date: '',
      event_end_date: '',
      event_id: '',
      materials: [],
      total_cost: '',
      event_notes: '',
      downpayment_amount: '',
      status: ''
    }
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

onMounted(fetchOptions)
</script>
