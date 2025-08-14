<template>
    <AdminLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <h2 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Admin Dashboard</h2>
            <div class="text-sm text-[#7fbfb0]">No requests</div>
        </section>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
.card {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
}
</style>
