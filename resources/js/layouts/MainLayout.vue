<template>
    <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-[#00f5a0]">
        <nav class="mt-6 flex w-full max-w-5xl items-center justify-between rounded-xl bg-black/60 px-6 py-3 shadow-lg backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] shadow-[0_0_18px_rgba(0,255,191,0.15)]">
                    <img src="logo.png" alt="Big City Logo" class="h-full w-full rounded-full object-cover" />
                </div>
                <div>
                    <h1 class="font-orbitron m-0 text-base text-[#d8ffef]">BIG CITY PRO AUDIO</h1>
                    <div class="text-xs text-[#7fbfb0]">Event Reservation System</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <Link class="mr-4 font-semibold text-[#00f5a0]" :href="route('home')">Home</Link>
                <Link class="mr-4 font-semibold text-[#00f5a0]" :href="route('business')">Business Details</Link>

                <!-- User Dropdown -->
                <div class="relative" @click="showDropdown = !showDropdown">
                    <button class="flex items-center gap-2 rounded-lg bg-black/30 px-3 py-2 transition hover:bg-black/50">
                        <span class="font-semibold">{{ user?.name || 'Account' }}</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-if="showDropdown" class="absolute right-0 z-50 mt-2 w-48 rounded-lg bg-black/100 shadow-lg">
                        <Link :href="route('admin')" class="block px-4 py-2 text-sm text-[#00f5a0] hover:bg-black/60">Admin Dashboard</Link>
                        <Link :href="route('notifications')" class="block px-4 py-2 text-sm text-[#00f5a0] hover:bg-black/60">Notifications</Link>
                        <Link :href="route('mybookings')" class="block px-4 py-2 text-sm text-[#00f5a0] hover:bg-black/60">My Bookings</Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-black/60"
                            >Logout</Link
                        >
                    </div>
                </div>
                <button class="btn btn-book ml-2 rounded-lg bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-4 py-2 font-bold text-black shadow">
                    Book Now
                </button>
            </div>
        </nav>
        <main class="mt-7 w-full max-w-5xl px-5">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const user = ref(page.props.auth.user || null);

const showDropdown = ref(false);
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
</style>
