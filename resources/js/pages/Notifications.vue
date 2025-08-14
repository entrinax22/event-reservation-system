<template>
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <h2 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Notifications</h2>
            <div class="text-sm text-[#7fbfb0]">No notifications yet.</div>
        </section>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
.card {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}
</style>
