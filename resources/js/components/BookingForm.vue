<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
    <!-- Modal Box -->
    <div
      class="relative w-full max-w-3xl rounded-xl bg-[rgba(0,0,0,0.85)] p-6 shadow-2xl border border-gray-700"
    >
      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute right-4 top-4 text-gray-400 hover:text-[#00f5a0] transition"
      >
        ✕
      </button>

      <!-- Title -->
      <h2 class="mb-4 text-center font-orbitron text-2xl text-[#00f5a0]">
        Create New Reservation
      </h2>

      <!-- Form -->
      <form @submit.prevent="createReservation" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Event Date -->
          <div>
            <label class="block text-sm text-[#7fbfb0]">Event Date</label>
            <input
              type="date"
              v-model="form.event_date"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white 
                focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              required
            />
            <p v-if="errors.event_date" class="mt-1 text-xs text-red-400">{{ errors.event_date[0] }}</p>
          </div>

          <!-- Event End Date -->
          <div>
            <label class="block text-sm text-[#7fbfb0]">Event End Date</label>
            <input
              type="date"
              v-model="form.event_end_date"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white 
                focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
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

          <!-- Notes -->
          <div class="md:col-span-2">
            <label class="block text-sm text-[#7fbfb0]">Event Notes</label>
            <textarea
              v-model="form.event_notes"
              class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white 
                placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
              rows="3"
              placeholder="Additional notes..."
            ></textarea>
          </div>
        </div>

        <!-- Submit -->
        <div class="pt-4">
          <button
            type="submit"
            :disabled="submitting"
            class="w-full rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black 
              transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:opacity-50"
          >
            {{ submitting ? 'Creating...' : 'Create Reservation' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import BaseSelect from './BaseSelect.vue'
import axios from 'axios'

const emit = defineEmits(['close'])
const submitting = ref(false)
const errors = reactive({})

const form = reactive({
  event_id: '',
  event_date: '',
  event_end_date: '',
  materials: [],
  event_notes: ''
})

const eventOptions = ref([])
const materialOptions = ref([])

async function fetchOptions() {
  try {
    const [eventsRes, materialsRes] = await Promise.all([
      axios.get(route('events.selectList')),
      axios.get(route('materials.selectList'))
    ])

    // ✅ Safe fallback for both {data: []} or []
    const eventsData = eventsRes.data?.data ?? eventsRes.data ?? []
    const materialsData = materialsRes.data?.data ?? materialsRes.data ?? []

    eventOptions.value = eventsData.map(e => ({
      value: e.event_id,
      label: e.event_name
    }))

    materialOptions.value = materialsData.map(m => ({
      value: m.material_id,
      label: m.material_name
    }))
  } catch (err) {
    console.error('Error fetching select lists:', err)
    eventOptions.value = []
    materialOptions.value = []
  }
}

onMounted(fetchOptions)

async function createReservation() {
  submitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  try {
    // ✅ Fix materials mapping and actually use payload
    const payload = {
      ...form,
      materials: form.materials.map(id => ({ material_id: id }))
    }

    await axios.post(route('reserved-online'), payload)

    alert('Reservation created successfully!')
    emit('close')
  } catch (error) {
    if (error.response?.status === 422) {
      Object.assign(errors, error.response.data.errors)
    } else {
      console.error('Unexpected error:', error)
      alert('Something went wrong. Please try again.')
    }
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.font-orbitron {
  font-family: 'Orbitron', sans-serif;
}
</style>
