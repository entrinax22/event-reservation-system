<template>
    <Head title="Home" />
    <MainLayout>
        <section class="mx-auto max-w-7xl space-y-12 p-6">
            <!-- Hero Section -->
            <div
                class="relative flex flex-col items-center gap-8 overflow-hidden rounded-3xl bg-gradient-to-r from-[#00f5a0]/60 to-[#00b2ff]/40 p-12 shadow-2xl backdrop-blur-sm md:flex-row"
            >
                <!-- Left: Text Content -->
                <div class="space-y-4 text-center md:w-1/2 md:text-left">
                    <h1 class="text-4xl font-bold text-white drop-shadow-md md:text-5xl">Welcome to Big City Pro Audio</h1>
                    <p class="text-lg text-white/80 md:text-xl">Plan & Reserve Your Events Effortlessly</p>
                    <p class="text-lg text-white/70 md:text-lg">
                        Browse upcoming events, check availability, and secure your spot in just a few clicks.
                    </p>
                    <div class="mt-6 flex flex-col items-center gap-3 md:flex-row md:justify-start md:gap-4">
                        <button
                            @click="showBookingForm = true"
                            class="rounded-lg bg-[#00f5a0] px-6 py-3 font-semibold text-black shadow-lg transition hover:bg-[#00c88a]"
                        >
                            Book Now
                        </button>
                        <button
                            @click="scrollToCalendar"
                            class="rounded-lg border border-white/30 px-6 py-3 font-semibold text-white transition hover:bg-white/10"
                        >
                            View Schedule
                        </button>
                    </div>
                </div>

                <!-- Right: Logo/Image -->
                <div class="flex justify-center md:w-1/2 md:justify-end">
                    <img src="/images/logo.png" alt="Big City Pro Audio Logo" class="w-64 object-contain md:w-72 lg:w-80" />
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-[rgba(0,0,0,0.78)] shadow-2xl backdrop-blur-sm" ref="calendarSection">
                <div class="flex items-center justify-between border-b border-white/10 p-6">
                    <h2 class="text-2xl font-bold tracking-tight text-[#c8fff0] drop-shadow-md">
                        {{ monthYear }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <button
                            @click="prevMonth"
                            class="group flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-[#7fbfb0] transition hover:border-[#c8fff0] hover:text-[#c8fff0]"
                        >
                            &lt;
                        </button>
                        <button
                            @click="today"
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-sm font-medium text-[#7fbfb0] transition hover:bg-white/10 hover:text-[#c8fff0]"
                        >
                            Today
                        </button>
                        <button
                            @click="nextMonth"
                            class="group flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-[#7fbfb0] transition hover:border-[#c8fff0] hover:text-[#c8fff0]"
                        >
                            &gt;
                        </button>
                    </div>
                </div>

                <!-- Weekday Labels -->
                <div
                    class="grid grid-cols-7 border-b border-white/10 bg-black/20 text-center text-xs font-semibold tracking-wider text-[#7fbfb0] uppercase"
                >
                    <div v-for="w in weekdays" :key="w" class="py-3">{{ w }}</div>
                </div>

                <!-- Calendar Cells -->
                <div class="grid grid-cols-7 gap-px bg-white/10">
                    <div
                        v-for="cell in displayCells"
                        :key="cell.key"
                        :class="[
                            'group relative min-h-[140px] p-2 transition-all',
                            cell.isCurrentMonth ? 'bg-[#1a1a1a]/80 hover:bg-[#252525]' : 'bg-[#121212]/60 text-white/20',
                            cell.isToday ? 'bg-[#c8fff0]/5' : '',
                        ]"
                        @click="cell.isCurrentMonth && selectDate(cell.date)"
                    >
                        <div class="mb-2 flex items-center justify-between">
                            <span
                                :class="[
                                    'flex h-7 w-7 items-center justify-center rounded-full text-sm font-medium transition-colors',
                                    cell.isToday
                                        ? 'bg-[#c8fff0] text-black shadow-[0_0_10px_rgba(200,255,240,0.3)]'
                                        : 'text-[#7fbfb0] group-hover:text-white',
                                ]"
                            >
                                {{ cell.label }}
                            </span>
                        </div>

                        <div class="scrollbar-hide flex max-h-[100px] flex-col gap-1.5 overflow-y-auto">
                            <div
                                v-for="(ev, idx) in cell.eventInfo"
                                :key="idx"
                                :class="[
                                    'relative flex cursor-pointer flex-col rounded border-l-2 px-2 py-1.5 text-xs shadow-sm transition hover:scale-[1.02]',
                                    getStatusStyle(ev.status).bg,
                                    getStatusStyle(ev.status).border,
                                ]"
                            >
                                <div class="flex items-center justify-between font-bold" :class="getStatusStyle(ev.status).text">
                                    <span class="truncate">{{ ev.name }}</span>
                                </div>
                                <div class="mt-0.5 flex items-center gap-1 text-[10px] opacity-80" :class="getStatusStyle(ev.status).text">
                                    <span class="max-w-[80px] truncate font-medium">{{ ev.user }}</span>
                                    <span>•</span>
                                    <span class="tracking-tighter uppercase">{{ ev.status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex items-center gap-6 border-t border-white/10 bg-black/20 p-4 text-xs font-medium text-[#7fbfb0]">
                    <div class="flex items-center gap-2"><span class="dot bg-[#c8fff0] shadow-[0_0_8px_rgba(200,255,240,0.5)]"></span> Today</div>
                    <div class="flex items-center gap-2"><span class="dot bg-[#ffd166] shadow-[0_0_8px_rgba(255,209,102,0.4)]"></span> Pending</div>
                    <div class="flex items-center gap-2"><span class="dot bg-[#ff6666] shadow-[0_0_8px_rgba(255,102,102,0.4)]"></span> Booked</div>
                </div>
            </div>
        </section>

        <!-- Booking Form -->
        <Transition name="fade">
            <BookingForm v-if="showBookingForm" @close="showBookingForm = false" />
        </Transition>
    </MainLayout>
</template>

<script setup>
import BookingForm from '@/components/BookingForm.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const shownDate = ref(new Date());
const events = ref([]);

// Month-Year display
const monthYear = computed(() => shownDate.value.toLocaleString('default', { month: 'long', year: 'numeric' }));

// Navigation
function prevMonth() {
    shownDate.value = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth() - 1, 1);
}
function nextMonth() {
    shownDate.value = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth() + 1, 1);
}
function today() {
    shownDate.value = new Date();
}

// Status styles
function getStatusStyle(status) {
    switch (status) {
        case 'pending':
            return { bg: 'bg-yellow-400/10 hover:bg-yellow-400/20', border: 'border-yellow-400', text: 'text-yellow-200' };
        case 'booked':
            return { bg: 'bg-red-500/10 hover:bg-red-500/20', border: 'border-red-500', text: 'text-red-200' };
        default:
            return { bg: 'bg-[#7fbfb0]/10', border: 'border-[#7fbfb0]', text: 'text-[#c8fff0]' };
    }
}

// Generate calendar cells
const displayCells = computed(() => {
    const year = shownDate.value.getFullYear();
    const month = shownDate.value.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDayOfWeek = firstDay.getDay();
    const daysInMonth = lastDay.getDate();
    const cells = [];
    const todayDate = new Date();
    const isSameDay = (d1, d2) => d1.getDate() === d2.getDate() && d1.getMonth() === d2.getMonth() && d1.getFullYear() === d2.getFullYear();

    // Previous month padding
    const prevMonthLastDate = new Date(year, month, 0).getDate();
    for (let i = startDayOfWeek - 1; i >= 0; i--) {
        cells.push({
            key: 'prev-' + i,
            label: prevMonthLastDate - i,
            date: new Date(year, month - 1, prevMonthLastDate - i),
            isCurrentMonth: false,
            isToday: false,
            eventInfo: [],
        });
    }

    // Current month
    for (let d = 1; d <= daysInMonth; d++) {
        const currentDate = new Date(year, month, d);
        const dayEvents = events.value
            .filter((e) => {
                const start = new Date(e.event_date);
                const end = new Date(e.event_end_date);
                const check = new Date(currentDate);
                check.setHours(0, 0, 0, 0);
                start.setHours(0, 0, 0, 0);
                end.setHours(0, 0, 0, 0);
                return check >= start && check <= end;
            })
            .map((e) => ({ name: e.event_name, user: e.user?.name || 'Guest', status: e.status, id: e.id }));
        cells.push({
            key: 'day-' + d,
            label: d,
            date: currentDate,
            isCurrentMonth: true,
            isToday: isSameDay(currentDate, todayDate),
            eventInfo: dayEvents,
        });
    }

    // Next month padding
    const remainingCells = 42 - cells.length;
    for (let i = 1; i <= remainingCells; i++) {
        cells.push({ key: 'next-' + i, label: i, date: new Date(year, month + 1, i), isCurrentMonth: false, isToday: false, eventInfo: [] });
    }

    return cells;
});

// Fetch events
const fetchEventsList = async () => {
    try {
        const response = await axios.get(route('events-list'), {
            params: { month: shownDate.value.getMonth() + 1, year: shownDate.value.getFullYear() },
        });
        events.value = response.data.data;
    } catch (error) {
        console.error(error);
    }
};

watch(shownDate, fetchEventsList, { immediate: true });

function selectDate(date) {
    console.log('Selected:', date);
}

// Hero actions
function goToBooking() {
    window.location.href = route('booking.create');
}

const calendarSection = ref(null); // create ref

function scrollToCalendar() {
    if (calendarSection.value) {
        calendarSection.value.scrollIntoView({ behavior: 'smooth' });
    }
}

const showBookingForm = ref(false);
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}
</style>
