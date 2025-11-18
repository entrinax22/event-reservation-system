<template>
    <Head title="My Bookings" />
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <h2 class="font-orbitron mb-4 text-xl text-[#00f5a0]">My Bookings</h2>
            <div class="text-sm text-[#7fbfb0]">You have no bookings.</div>

            <!--Bookings-->
            <div v-for="booking in bookings" :key="booking.id" class="mb-4 rounded-lg border border-gray-700 p-4">
                <h3 class="font-orbitron mb-2 text-lg text-[#00f5a0]">Event: {{ booking.event_name }}</h3>
                <p class="text-sm text-[#7fbfb0]">Date: {{ booking.event_date }}</p>
                <p class="text-sm text-[#7fbfb0]">Status: {{ booking.status }}</p>
            </div>
        </section>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const bookings = ref([]);

const fetchBookings = async () => {
    try {
        const response = await axios.get('/reservations/my-reservations');
        bookings.value = response.data;
    } catch (error) {
        console.error('Error fetching bookings:', error);
    }
};

onMounted(() => {
    fetchBookings();
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
