<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Dashboard Heading -->
            <div class="flex items-center justify-between border-b border-[#00f5a0]/30 pb-4">
                <div>
                    <h1 class="font-orbitron text-3xl font-bold tracking-wide text-white">Admin Dashboard</h1>
                    <p class="text-xl text-white">Welcome back, Admin. Here’s what’s happening today.</p>
                </div>
                <button @click="downloadPDF" class="rounded bg-green-400 px-4 py-2 font-bold text-black">Download PDF Report</button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 transition-transform hover:scale-105">
                    <h3 class="text-sm text-[#7fbfb0]">Total Bookings</h3>
                    <p class="mt-2 text-3xl font-bold text-white">{{ stats.totalBookings }}</p>
                </div>
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 transition-transform hover:scale-105">
                    <h3 class="text-sm text-[#7fbfb0]">Pending Requests</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-400">{{ stats.pendingRequests }}</p>
                </div>
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 transition-transform hover:scale-105">
                    <h3 class="text-sm text-[#7fbfb0]">Revenue</h3>
                    <p class="mt-2 text-3xl font-bold text-green-400">₱{{ stats.revenue }}</p>
                </div>
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 transition-transform hover:scale-105">
                    <h3 class="text-sm text-[#7fbfb0]">Users</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-400">{{ stats.totalUsers }}</p>
                </div>
            </div>

            <!-- Recent Bookings -->
            <section class="card overflow-x-auto rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
                <h3 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Recent Bookings</h3>
                <table class="w-full min-w-[600px] text-left text-sm text-white">
                    <thead class="border-b border-gray-700 text-[#7fbfb0]">
                        <tr>
                            <th class="py-3">Date</th>
                            <th class="py-3">End Date</th>
                            <th class="py-3">Client</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="booking in recentBookings"
                            :key="booking.date + booking.client"
                            class="border-b border-gray-700 hover:bg-gray-800/40"
                        >
                            <td class="py-3 text-xl">{{ formatDate(booking.date) }}</td>
                            <td class="py-3 text-xl">{{ formatDate(booking.end_date) }}</td>
                            <td class="py-3 text-xl">{{ booking.client }}</td>
                            <td class="py-3 text-xl">{{ booking.type }}</td>
                            <td class="py-3 text-xl">
                                <span
                                    :class="{
                                        'rounded-full bg-green-600 px-3 py-1 text-xs font-semibold text-white':
                                            booking.status === 'completed' || booking.status === 'accepted',
                                        'rounded-full bg-yellow-400 px-3 py-1 text-xs font-semibold text-black': booking.status === 'pending',
                                        'rounded-full bg-orange-400 px-3 py-1 text-xs font-semibold text-black':
                                            booking.status === 'downpayment_update',
                                        'rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white': booking.status === 'cancelled',
                                    }"
                                >
                                    {{ booking.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Charts -->
            <section class="card rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
                <h3 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Monthly Bookings</h3>
                <canvas id="bookingsChart" class="h-48 max-h-52 w-full"></canvas>
            </section>

            <!-- Popular Events & Materials Charts in 2 columns -->
            <section class="flex gap-6">
                <!-- Popular Events Chart -->
                <div class="card w-1/2 rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
                    <h3 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Popular Events</h3>
                    <canvas id="popularEventsChart" class="h-48 max-h-52 w-full"></canvas>
                </div>

                <!-- Popular Materials Chart -->
                <div class="card w-1/2 rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
                    <h3 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Popular Materials</h3>
                    <canvas id="popularMaterialsChart" class="h-48 max-h-52 w-full"></canvas>
                </div>
            </section>

            <div ref="pdfSection" class="hidden">
                <h2>Admin Dashboard Report</h2>
                <table border="1" cellspacing="0" cellpadding="8" style="width: 100%; border-collapse: collapse">
                    <thead>
                        <tr style="background-color: #00f5a0; color: #000">
                            <th>Date</th>
                            <th>End Date</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="booking in pdfData" :key="booking.id">
                            <td>{{ formatDate(booking.date) }}</td>
                            <td>{{ formatDate(booking.end_date) }}</td>
                            <td>{{ booking.client }}</td>
                            <td>{{ booking.type }}</td>
                            <td>{{ booking.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';
Chart.register(...registerables);

const stats = ref({
    totalBookings: 0,
    pendingRequests: 0,
    revenue: 0,
    totalUsers: 0,
});

const recentBookings = ref([]);
const monthlyBookings = ref([]); // Chart data
const popularEvents = ref([]);
const popularMaterials = ref([]);

async function fetchDashboard() {
    try {
        const res = await axios.get(route('admin.dashboard.stats'));
        if (res.data.result) {
            stats.value = res.data.stats;
            recentBookings.value = res.data.recentBookings;
            monthlyBookings.value = res.data.monthlyBookings;
            popularEvents.value = res.data.popularEvents;
            popularMaterials.value = res.data.popularMaterials;

            renderBookingsChart();
            renderPopularEventsChart();
            renderPopularMaterialsChart();
        }
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
    }
}

function renderPopularEventsChart() {
    const ctx = document.getElementById('popularEventsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: popularEvents.value.map((item) => item.event_name),
            datasets: [
                {
                    label: 'Bookings',
                    data: popularEvents.value.map((item) => item.bookings_count),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
}

function renderPopularMaterialsChart() {
    const ctx = document.getElementById('popularMaterialsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: popularMaterials.value.map((item) => item.material_name),
            datasets: [
                {
                    label: 'Used Count',
                    data: popularMaterials.value.map((item) => item.used_count),
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
}

const pdfSection = ref(null);
const pdfData = ref([]);

async function downloadPDF() {
    try {
        const res = await axios.get(route('admin.generatePDFData'));
        if (res.data.result) {
            const stats = res.data.stats || {};
            const popularEvents = res.data.popularEvents || [];
            const popularMaterials = res.data.popularMaterials || [];
            const reservedEvents = res.data.reservedEvents || [];

            const htmlContent = `
        <html>
          <head>
            <title>BIG CITY PRO AUDIO REPORT</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 20px; color: #000; }
              h2, h3 { text-align: center; color: #00f5a0; margin-bottom: 10px; }
              table { width: 100%; border-collapse: collapse; margin: 20px 0; }
              th, td { border: 1px solid #000; padding: 8px; text-align: left; }
              th { background-color: #00f5a0; color: #000; }
              .section-title { margin-top: 30px; margin-bottom: 10px; }
            </style>
          </head>
          <body>
            <h2>BIG CITY PRO AUDIO REPORT</h2>

            <h3 class="section-title">Stats</h3>
            <table>
              <tr><th>Total Bookings</th><td>${stats.totalBookings}</td></tr>
              <tr><th>Pending Requests</th><td>${stats.pendingRequests}</td></tr>
              <tr><th>Total Revenue</th><td>₱${stats.totalRevenue}</td></tr>
              <tr><th>Total Users</th><td>${stats.totalUsers}</td></tr>
            </table>

            <h3 class="section-title">Popular Events</h3>
            <table>
              <thead>
                <tr><th>Event Name</th><th>Bookings Count</th></tr>
              </thead>
              <tbody>
                ${popularEvents
                    .map(
                        (e) => `
                  <tr>
                    <td>${e.event_name}</td>
                    <td>${e.bookings_count}</td>
                  </tr>
                `,
                    )
                    .join('')}
              </tbody>
            </table>

            <h3 class="section-title">Popular Materials</h3>
            <table>
              <thead>
                <tr><th>Material Name</th><th>Used Count</th></tr>
              </thead>
              <tbody>
                ${popularMaterials
                    .map(
                        (m) => `
                  <tr>
                    <td>${m.material_name}</td>
                    <td>${m.used_count}</td>
                  </tr>
                `,
                    )
                    .join('')}
              </tbody>
            </table>

            <h3 class="section-title">Reserved Events</h3>
            ${reservedEvents
                .map(
                    (ev) => `
              <table>
                <thead>
                  <tr>
                    <th>Date</th><th>End Date</th><th>Client</th>
                    <th>Type</th><th>Status</th><th>Total Cost</th><th>Downpayment</th><th>Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>${formatDate(ev.date)}</td>
                    <td>${formatDate(ev.end_date)}</td>
                    <td>${ev.client}</td>
                    <td>${ev.type}</td>
                    <td>${ev.status}</td>
                    <td>₱${ev.total_cost}</td>
                    <td>₱${ev.downpayment_amount}</td>
                    <td>${ev.event_notes}</td>
                  </tr>
                </tbody>
              </table>
              <table>
                <thead>
                  <tr><th colspan="2">Materials Used</th></tr>
                  <tr><th>Name</th><th>Description</th></tr>
                </thead>
                <tbody>
                  ${ev.materials
                      .map(
                          (mat) => `
                    <tr>
                      <td>${mat.name}</td>
                      <td>${mat.description}</td>
                    </tr>
                  `,
                      )
                      .join('')}
                </tbody>
              </table>
            `,
                )
                .join('')}

          </body>
        </html>
      `;

            const iframe = document.createElement('iframe');
            document.body.appendChild(iframe);
            iframe.style.display = 'none';
            iframe.contentDocument.open();
            iframe.contentDocument.write(htmlContent);
            iframe.contentDocument.close();

            iframe.onload = () => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                document.body.removeChild(iframe);
            };
        }
    } catch (error) {
        console.error('Failed to generate PDF:', error);
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-PH', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function renderBookingsChart() {
    const ctx = document.getElementById('bookingsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyBookings.value.map((item) => item.month),
            datasets: [
                {
                    label: 'Bookings',
                    data: monthlyBookings.value.map((item) => item.count),
                    backgroundColor: 'rgba(0, 245, 160, 0.6)',
                    borderColor: 'rgba(0, 245, 160, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}

onMounted(() => {
    fetchDashboard();
});
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
.card {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}
</style>
