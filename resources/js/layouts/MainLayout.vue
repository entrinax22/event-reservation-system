<template>
    <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-white">
        <nav
            class="z-[100] mt-6 flex w-full max-w-5xl items-center justify-between rounded-xl border border-white/10 bg-black/40 px-6 py-3 shadow-2xl backdrop-blur-md"
        >
            <div class="flex items-center gap-3">
                <div
                    class="h-11 w-11 flex-shrink-0 rounded-full bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] p-[2px] shadow-[0_0_15px_rgba(0,245,160,0.3)]"
                >
                    <img src="/logo.png" alt="Big City Logo" class="h-full w-full rounded-full bg-black object-cover" />
                </div>
                <div>
                    <h1 class="font-orbitron m-0 text-base font-bold tracking-wider text-white">BIG CITY PRO AUDIO</h1>
                    <div class="text-xs font-medium tracking-wide text-[#00f5a0]">Event Reservation System</div>
                </div>
            </div>

            <div class="hidden items-center gap-6 md:flex">
                <Link class="text-sm font-medium text-white transition-colors hover:text-[#00f5a0]" :href="route('home')">Home</Link>
                <div class="relative" @click="showDropdown = !showDropdown">
                    <button
                        class="flex items-center gap-2 rounded-lg border border-white/10 bg-black/20 px-3 py-2 transition hover:bg-black/40 hover:text-[#00f5a0]"
                    >
                        <span class="font-semibold">{{ user?.name || 'Account' }}</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        v-if="showDropdown"
                        class="absolute right-0 z-50 mt-2 w-52 overflow-hidden rounded-xl border border-[#00f5a0]/30 bg-black/90 shadow-xl backdrop-blur-xl"
                    >
                        <Link
                            v-if="user.role === 'admin'"
                            :href="route('admin')"
                            class="block px-4 py-3 text-sm text-gray-200 transition hover:bg-[#00f5a0] hover:text-black"
                        >
                            Admin Dashboard
                        </Link>
                        <Link
                            :href="route('user.profile')"
                            class="block px-4 py-3 text-sm text-gray-200 transition hover:bg-[#00f5a0] hover:text-black"
                        >
                            My Profile
                        </Link>
                        <Link
                            :href="route('notifications')"
                            class="relative block px-4 py-3 text-sm text-gray-200 transition hover:bg-[#00f5a0] hover:text-black"
                        >
                            Notifications
                            <span
                                v-if="count > 0"
                                class="absolute top-3 right-4 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff007a] text-[10px] font-bold text-white shadow-sm"
                            >
                                {{ count }}
                            </span>
                        </Link>

                        <Link
                            :href="route('mybookings')"
                            class="block px-4 py-3 text-sm text-gray-200 transition hover:bg-[#00f5a0] hover:text-black"
                        >
                            My Bookings
                        </Link>
                        <div class="h-px bg-white/10"></div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full px-4 py-3 text-left text-sm text-red-400 transition hover:bg-red-500/20 hover:text-red-300"
                        >
                            Logout
                        </Link>
                    </div>
                </div>

                <button
                    class="btn btn-book rounded-lg bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-5 py-2 text-sm font-bold text-black shadow-[0_0_10px_rgba(0,0,0,0.3)] transition-transform hover:scale-105"
                    @click="showBookingForm = true"
                >
                    Book Now
                </button>
            </div>

            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="rounded-md p-2 text-white transition hover:bg-black/20 hover:text-[#00f5a0] md:hidden"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        <transition name="fade">
            <div
                v-if="mobileMenuOpen"
                class="z-50 mt-2 w-full max-w-5xl rounded-xl border border-[#00f5a0]/20 bg-black/90 p-4 backdrop-blur-xl md:hidden"
            >
                <Link class="block rounded px-4 py-3 text-gray-200 hover:bg-[#00f5a0]/10 hover:text-[#00f5a0]" :href="route('home')">Home</Link>
                <Link
                    v-if="user.role === 'admin'"
                    :href="route('admin')"
                    class="block rounded px-4 py-3 text-gray-200 hover:bg-[#00f5a0]/10 hover:text-[#00f5a0]"
                    >Admin Dashboard</Link
                >

                <Link
                    :href="route('notifications')"
                    class="relative block rounded px-4 py-3 text-gray-200 hover:bg-[#00f5a0]/10 hover:text-[#00f5a0]"
                >
                    Notifications
                    <span
                        v-if="count > 0"
                        class="absolute top-3 right-4 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff007a] text-[10px] font-bold text-white"
                    >
                        {{ count }}
                    </span>
                </Link>

                <Link :href="route('mybookings')" class="block rounded px-4 py-3 text-gray-200 hover:bg-[#00f5a0]/10 hover:text-[#00f5a0]"
                    >My Bookings</Link
                >

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="block w-full rounded px-4 py-3 text-left text-red-400 hover:bg-red-500/10"
                    >Logout</Link
                >

                <button
                    class="mt-4 w-full rounded-lg bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-4 py-3 font-bold text-black shadow-lg"
                    @click="showBookingForm = true"
                >
                    Book Now
                </button>
            </div>
        </transition>

        <main class="mt-8 w-full max-w-5xl px-5 pb-10">
            <slot />
        </main>

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

.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.3s ease,
        transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
