<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Dashboard Heading -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold text-[#00f5a0] tracking-wide">
                    Admin Dashboard
                </h1>
                <p class="text-sm text-[#7fbfb0]">Welcome back, Admin. Here’s what’s happening today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 hover:scale-105 transition-transform">
                    <h3 class="text-sm text-[#7fbfb0]">Total Bookings</h3>
                    <p class="mt-2 text-3xl font-bold text-white">128</p>
                </div>
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 hover:scale-105 transition-transform">
                    <h3 class="text-sm text-[#7fbfb0]">Pending Requests</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-400">12</p>
                </div>
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 hover:scale-105 transition-transform">
                    <h3 class="text-sm text-[#7fbfb0]">Revenue</h3>
                    <p class="mt-2 text-3xl font-bold text-green-400">₱52,000</p>
                </div>
                <div class="card rounded-xl border border-black/30 bg-[rgba(0,0,0,0.78)] p-6 hover:scale-105 transition-transform">
                    <h3 class="text-sm text-[#7fbfb0]">Users</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-400">542</p>
                </div>
            </div>

            <!-- Recent Bookings -->
            <section class="card rounded-2xl border border-black/30 bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
                <h3 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Recent Bookings</h3>
                <table class="w-full text-left text-sm text-white">
                    <thead class="border-b border-gray-700 text-[#7fbfb0]">
                        <tr>
                            <th class="py-3">Date</th>
                            <th class="py-3">Client</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-700 hover:bg-gray-800/40">
                            <td class="py-3">2025-08-14</td>
                            <td class="py-3">John Doe</td>
                            <td class="py-3">Sound System</td>
                            <td class="py-3 text-green-400">Confirmed</td>
                        </tr>
                        <tr class="border-b border-gray-700 hover:bg-gray-800/40">
                            <td class="py-3">2025-08-13</td>
                            <td class="py-3">Jane Smith</td>
                            <td class="py-3">Lighting</td>
                            <td class="py-3 text-yellow-400">Pending</td>
                        </tr>
                        <tr class="hover:bg-gray-800/40">
                            <td class="py-3">2025-08-12</td>
                            <td class="py-3">Mike Johnson</td>
                            <td class="py-3">Stage Setup</td>
                            <td class="py-3 text-red-400">Cancelled</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Quick Actions -->
            <section>
                <h3 class="font-orbitron mb-4 text-lg text-[#00f5a0]">Quick Actions</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <button class="rounded-xl bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-4 py-3 font-bold text-black hover:opacity-90">
                        View All Bookings
                    </button>
                    <button class="rounded-xl bg-gradient-to-r from-yellow-400 to-orange-500 px-4 py-3 font-bold text-black hover:opacity-90">
                        Add New Service
                    </button>
                    <button class="rounded-xl bg-gradient-to-r from-pink-500 to-red-500 px-4 py-3 font-bold text-white hover:opacity-90">
                        Manage Users
                    </button>
                    <button class="rounded-xl bg-gradient-to-r from-purple-500 to-indigo-500 px-4 py-3 font-bold text-white hover:opacity-90">
                        View Reports
                    </button>
                </div>
            </section>
        </section>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
.card {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}
</style>
