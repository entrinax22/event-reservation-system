<template>
    <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-[#00f5a0]">
        <!-- Navbar -->
        <nav class="z-[100] mt-6 flex w-full max-w-5xl items-center justify-between rounded-xl bg-black/60 px-6 py-3 shadow-lg backdrop-blur-md">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 flex-shrink-0 rounded-full bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] shadow-[0_0_18px_rgba(0,255,191,0.15)]">
                    <img src="logo.png" alt="Big City Logo" class="h-full w-full rounded-full object-cover" />
                </div>
                <div>
                    <h1 class="font-orbitron m-0 text-base text-[#d8ffef]">BIG CITY PRO AUDIO</h1>
                    <div class="text-xs text-[#7fbfb0]">Event Reservation System</div>
                </div>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden items-center gap-3 md:flex">
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
                        <Link v-if="user.role === 'admin'" :href="route('admin')" class="block px-4 py-2 text-sm text-[#00f5a0] hover:bg-black/60"
                            >Admin Dashboard</Link
                        >
                        <Link :href="route('notifications')" class="relative block px-4 py-2 text-sm text-[#00f5a0] hover:bg-black/60">
                            Notifications
                            <span
                                v-if="count > 0"
                                class="absolute top-1 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff007a] text-[10px] font-bold text-white"
                            >
                                {{ count }}
                            </span>
                        </Link>

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

                <!-- Book Button -->
                <button
                    class="btn btn-book ml-2 rounded-lg bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-4 py-2 font-bold text-black shadow"
                    @click="showBookingForm = true"
                >
                    Book Now
                </button>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="rounded-md p-2 text-[#00f5a0] hover:bg-black/40 md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        <!-- Mobile Menu -->
        <transition name="fade">
            <div v-if="mobileMenuOpen" class="z-50 mt-2 w-full max-w-5xl rounded-xl bg-black/80 p-4 md:hidden">
                <Link class="block px-4 py-2 text-[#00f5a0] hover:bg-black/60" :href="route('home')">Home</Link>
                <Link class="block px-4 py-2 text-[#00f5a0] hover:bg-black/60" :href="route('business')">Business Details</Link>
                <Link v-if="user.role === 'admin'" :href="route('admin')" class="block px-4 py-2 text-[#00f5a0] hover:bg-black/60"
                    >Admin Dashboard</Link
                >
                <Link :href="route('notifications')" class="relative block px-4 py-2 text-sm text-[#00f5a0] hover:bg-black/60">
                    Notifications
                    <span
                        v-if="count > 0"
                        class="absolute top-1 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff007a] text-[10px] font-bold text-white"
                    >
                        {{ count }}
                    </span>
                </Link>

                <Link :href="route('mybookings')" class="block px-4 py-2 text-[#00f5a0] hover:bg-black/60">My Bookings</Link>
                <Link :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-red-400 hover:bg-black/60"
                    >Logout</Link
                >
                <button
                    class="mt-3 w-full rounded-lg bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-4 py-2 font-bold text-black shadow"
                    @click="showBookingForm = true"
                >
                    Book Now
                </button>
            </div>
        </transition>

        <!-- Main Content -->
        <main class="mt-7 w-full max-w-5xl px-5">
            <slot />
        </main>

        <!-- Booking Form -->
        <Transition name="fade">
            <BookingForm v-if="showBookingForm" @close="showBookingForm = false" />
        </Transition>
    </div>
</template>

<script setup>
import BookingForm from '@/components/BookingForm.vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';
const page = usePage();
const user = ref(page.props.auth.user || null);
const count = ref(0);
const showDropdown = ref(false);
const showBookingForm = ref(false);
const mobileMenuOpen = ref(false);

const fetchCount = async () => {
    try {
        const response = await axios.get(route('notifications.unread-count'));
        if (response.data.result) {
            count.value = response.data.unread_count;
        }
    } catch (error) {
        console.log(error);
    }
};

onMounted(() => {
    fetchCount();
});
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}
</style>
