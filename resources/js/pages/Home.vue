<template>
  <Head title="Home" />
  <MainLayout>
    <section
      class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl"
    >
      <div
        class="min-h-[420px] rounded-xl bg-gradient-to-b from-white/5 to-white/0.5 p-4"
      >
        <!-- Header -->
        <div class="mb-3 flex items-center justify-between">
          <div class="text-lg font-bold text-[#c8fff0]">{{ monthYear }}</div>
          <div>
            <button
              class="btn btn-ghost rounded px-3 py-1"
              @click="prevMonth"
            >&lt;</button>
            <button
              class="btn btn-ghost rounded px-3 py-1"
              @click="nextMonth"
            >&gt;</button>
          </div>
        </div>

        <!-- Weekdays -->
        <div class="mb-2 grid grid-cols-7 gap-2">
          <div
            v-for="w in weekdays"
            :key="w"
            class="text-center text-xs text-[#7fbfb0]"
          >
            {{ w }}
          </div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-2">
          <div
            v-for="cell in displayCells"
            :key="cell.key"
            :class="cell.class"
            @click="cell.available && selectDate(cell.date)"
          >
            <div class="font-semibold">{{ cell.label }}</div>
            <div v-if="cell.badge" :class="cell.badgeClass">{{ cell.badge }}</div>
            <div v-if="cell.eventInfo && cell.eventInfo.length" class="mt-1 space-y-1">
              <div v-for="(ev, idx) in cell.eventInfo" :key="idx" :class="'px-1 py-0.5 rounded text-xs ' + ev.colorClass">
                <span class="font-bold">{{ ev.name }}</span>
                <span class="ml-1">by {{ ev.user }}</span>
                <span class="ml-1 italic">({{ ev.status }})</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Legend -->
        <div class="mt-3 flex items-center gap-4 text-xs">
          <div class="flex items-center gap-2">
            <span class="dot free"></span> Available
          </div>
          <div class="flex items-center gap-2">
            <span class="dot pending"></span> Pending / Price set
          </div>
          <div class="flex items-center gap-2">
            <span class="dot booked"></span> Booked (blocked)
          </div>
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed, ref, onMounted, watch } from 'vue'
import axios from 'axios'

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const shownDate = ref(new Date())
const events = ref([])

// 🧭 Month-Year display
const monthYear = computed(() =>
  shownDate.value.toLocaleString('default', { month: 'long', year: 'numeric' })
)

// 📅 Navigation
function prevMonth() {
  shownDate.value = new Date(
    shownDate.value.getFullYear(),
    shownDate.value.getMonth() - 1,
    1
  )
}
function nextMonth() {
  shownDate.value = new Date(
    shownDate.value.getFullYear(),
    shownDate.value.getMonth() + 1,
    1
  )
}

// 🗓 Generate calendar cells
const displayCells = computed(() => {
  const first = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth(), 1)
  const startDay = first.getDay()
  const daysInMonth = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth() + 1, 0).getDate()
  const cells = []

  // Empty placeholders for previous month
  for (let i = 0; i < startDay; i++) {
    cells.push({
      key: 'empty' + i,
      label: '',
      class: 'opacity-10',
      available: false
    })
  }

  // Fill in day cells
  for (let d = 1; d <= daysInMonth; d++) {
    const date = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth(), d)
    let badge = ''
    let badgeClass = ''
    let baseClass =
      'min-h-[72px] rounded-lg p-2 border border-dashed border-white/10 text-[#dfffe9] bg-transparent hover:-translate-y-1 transition cursor-pointer'

    // Collect all events for this day
    const dayEvents = events.value.filter(e => {
      const start = new Date(e.event_date)
      const end = new Date(e.event_end_date)
      return date >= start && date <= end
    })

    let eventInfo = []
    if (dayEvents.length > 0) {
      dayEvents.forEach((event, idx) => {
        // Generate a unique color for each booking
        const colorPalette = [
          'bg-blue-500/30 text-blue-900',
          'bg-purple-500/30 text-purple-900',
          'bg-pink-500/30 text-pink-900',
          'bg-orange-500/30 text-orange-900',
          'bg-teal-500/30 text-teal-900',
          'bg-cyan-500/30 text-cyan-900',
          'bg-lime-500/30 text-lime-900',
          'bg-amber-500/30 text-amber-900',
          'bg-fuchsia-500/30 text-fuchsia-900',
          'bg-rose-500/30 text-rose-900',
        ];
        // Use reserved_event_id or idx for color selection
        const colorClass = colorPalette[event.reserved_event_id % colorPalette.length];
        eventInfo.push({
          name: event.event_name,
          user: event.user?.name || 'Unknown',
          status: event.status,
          colorClass
        })
      })
      badge = dayEvents[0].status === 'pending' ? 'Pending' : 'Booked'
      badgeClass = dayEvents[0].status === 'pending'
        ? 'text-white bg-yellow-400 px-2 py-1 rounded-full text-[10px]'
        : 'text-white bg-red-500 px-2 py-1 rounded-full text-[10px]'
      baseClass += dayEvents[0].status === 'pending'
        ? ' bg-yellow-400/20 border-yellow-400'
        : ' bg-red-600/20 border-red-500'
    } else {
      badge = 'Free'
      badgeClass = 'text-white bg-green-500 px-2 py-1 rounded-full text-[10px]'
      baseClass += ' bg-green-500/20 border-green-500'
    }

    cells.push({
      key: 'day' + d,
      label: d,
      available: true,
      date,
      badge,
      badgeClass,
      class: baseClass,
      eventInfo // array of events for this day
    })
  }

  return cells
})

// 🛰 Fetch Events
const fetchEventsList = async () => {
    try {
        const response = await axios.get(route('events-list'), {
            params: {
                month: shownDate.value.getMonth() + 1,
                year: shownDate.value.getFullYear(),
            },
        });

        // Just update the events ref, let displayCells handle the rest
        events.value = response.data.data;
    } catch (error) {
        console.error("Error fetching events list:", error);
    }
};


// Refresh when month changes
watch(shownDate, fetchEventsList, { immediate: true })

function selectDate(date) {
  alert('Selected date: ' + date.toISOString().slice(0, 10))
}
</script>

<style scoped>
.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}
.dot.free {
  background: #00f5a0;
}
.dot.pending {
  background: #ffd166;
}
.dot.booked {
  background: #ff6666;
  box-shadow: 0 0 8px rgba(255, 102, 102, 0.18);
}
.card {
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}
</style>
