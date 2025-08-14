<template>
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <div class="min-h-[420px] rounded-xl bg-gradient-to-b from-white/5 to-white/0.5 p-4">
                <div class="mb-3 flex items-center justify-between">
                    <div class="text-lg font-bold text-[#c8fff0]">{{ monthYear }}</div>
                    <div>
                        <button class="btn btn-ghost rounded px-3 py-1" @click="prevMonth">&lt;</button>
                        <button class="btn btn-ghost rounded px-3 py-1" @click="nextMonth">&gt;</button>
                    </div>
                </div>
                <div class="mb-2 grid grid-cols-7 gap-2">
                    <div v-for="w in weekdays" :key="w" class="text-center text-xs text-[#7fbfb0]">{{ w }}</div>
                </div>
                <div class="grid grid-cols-7 gap-2">
                    <div v-for="cell in calendarCells" :key="cell.key" :class="cell.class" @click="cell.available && selectDate(cell.date)">
                        <div class="font-semibold">{{ cell.label }}</div>
                        <div v-if="cell.badge" :class="cell.badgeClass">{{ cell.badge }}</div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-2"><span class="dot free"></span> Available</div>
                    <div class="flex items-center gap-2"><span class="dot pending"></span> Pending / Price set</div>
                    <div class="flex items-center gap-2"><span class="dot booked"></span> Booked (blocked)</div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { computed, ref } from 'vue';

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const shownDate = ref(new Date());

const monthYear = computed(() => shownDate.value.toLocaleString('default', { month: 'long', year: 'numeric' }));

function prevMonth() {
    shownDate.value = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth() - 1, 1);
}
function nextMonth() {
    shownDate.value = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth() + 1, 1);
}

const calendarCells = computed(() => {
    const first = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth(), 1);
    const startDay = first.getDay();
    const daysInMonth = new Date(shownDate.value.getFullYear(), shownDate.value.getMonth() + 1, 0).getDate();
    const cells = [];
    for (let i = 0; i < startDay; i++) {
        cells.push({ key: 'empty' + i, label: '', class: 'opacity-10', available: false });
    }
    for (let d = 1; d <= daysInMonth; d++) {
        cells.push({
            key: 'day' + d,
            label: d,
            class: 'min-h-[72px] rounded-lg p-2 border border-dashed border-white/10 text-[#dfffe9] bg-transparent hover:-translate-y-1 transition cursor-pointer',
            available: true,
            date: new Date(shownDate.value.getFullYear(), shownDate.value.getMonth(), d),
            badge: '',
            badgeClass: '',
        });
    }
    return cells;
});

function selectDate(date) {
    // Open booking modal (to be implemented)
    alert('Selected date: ' + date.toISOString().slice(0, 10));
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
