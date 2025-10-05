<template>
    <Head title="Business Details" />
    <MainLayout>
        <section class="card rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-5 shadow-xl">
            <h2 class="font-orbitron mb-4 text-xl text-[#00f5a0]">Business Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-2 text-[#7fbfb0] text-sm">Business Name</div>
                    <div class="font-bold text-lg text-[#c8fff0]">Big City Pro Audio</div>
                </div>
                <div>
                    <div class="mb-2 text-[#7fbfb0] text-sm">Owner</div>
                    <div class="font-bold text-lg text-[#c8fff0]">Jaden Co</div>
                </div>
                <div>
                    <div class="mb-2 text-[#7fbfb0] text-sm">Address</div>
                    <div class="font-bold text-lg text-[#c8fff0]">123 Main St, Big City, Country</div>
                </div>
                <div>
                    <div class="mb-2 text-[#7fbfb0] text-sm">Contact</div>
                    <div class="font-bold text-lg text-[#c8fff0]">(+63) 912-345-6789</div>
                </div>
                <div class="md:col-span-2">
                    <div class="mb-2 text-[#7fbfb0] text-sm">Description</div>
                    <div class="text-[#dfffe9]">A modern event center offering premium spaces for weddings, conferences, and parties. We provide top-notch amenities and professional services to make your event memorable.</div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
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
