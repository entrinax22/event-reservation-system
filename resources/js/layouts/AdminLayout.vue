<template>
    <div class="flex min-h-screen bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-[#00f5a0]">
        <!-- Mobile Toggle Button -->
        <button class="fixed top-4 left-4 z-50 rounded-md bg-black/70 p-2 text-[#00f5a0] md:hidden" @click="sidebarOpen = !sidebarOpen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Sidebar -->
        <transition name="slide">
            <aside
                v-if="sidebarOpen || isDesktop"
                class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col overflow-hidden rounded-r-2xl bg-black/80 shadow-xl md:static md:inset-auto"
            >
                <!-- Scrollable content container -->
                <div class="flex h-full flex-col px-6 py-8">
                    <!-- Top section with logo and nav (scrollable) -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Logo -->
                        <div class="mb-8 flex items-center gap-3">
                            <div class="h-11 w-11 rounded-full bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] shadow-[0_0_18px_rgba(0,255,191,0.15)]">
                                <img src="/logo.png" alt="Big City Logo" class="h-full w-full rounded-full object-cover" />
                            </div>
                            <div>
                                <h1 class="font-orbitron m-0 text-base text-[#d8ffef]">BIG CITY PRO AUDIO</h1>
                                <div class="text-xs text-[#7fbfb0]">Admin Panel</div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <nav class="flex flex-col gap-4">
                            <!-- Main -->
                            <div>
                                <p class="px-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">Main</p>
                                <div class="mt-2 flex flex-col gap-2">
                                    <Link :href="route('home')" class="sidebar-link">Home</Link>
                                    <Link :href="route('admin')" class="sidebar-link">Dashboard</Link>
                                    <Link :href="route('notifications.admin')" class="sidebar-link relative">
                                        Notifications
                                        <span
                                            v-if="count > 0"
                                            class="absolute top-1 right-4 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff007a] text-[10px] font-bold text-white"
                                        >
                                            {{ count }}
                                        </span>
                                    </Link>
                                </div>
                            </div>

                            <!-- Tables -->
                            <div>
                                <p class="px-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">Tables</p>
                                <div class="mt-2 flex flex-col gap-2">
                                    <Link :href="route('admin.users.table')" class="sidebar-link">Users Table</Link>
                                    <Link :href="route('admin.events.table')" class="sidebar-link">Events Table</Link>
                                    <Link :href="route('admin.materials.table')" class="sidebar-link">Materials Table</Link>
                                    <Link :href="route('admin.reserved-events.table')" class="sidebar-link">Reserved Events Table</Link>
                                    <Link :href="route('admin.payments.table')" class="sidebar-link">Payments Table</Link>
                                </div>
                            </div>
                        </nav>
                    </div>

                    <!-- Bottom section with user info (fixed at bottom) -->
                    <div class="flex-shrink-0 border-t border-gray-700/50 pt-4">
                        <div class="mb-2 flex items-center gap-2">
                            <span class="font-semibold">{{ user?.name || 'Admin' }}</span>
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full rounded-lg px-4 py-2 text-left text-sm text-red-400 transition-colors hover:bg-black/60"
                            >Logout</Link
                        >
                    </div>
                </div>
            </aside>
        </transition>

        <!-- Main content -->
        <main class="min-h-screen flex-1 overflow-x-auto overflow-y-auto md:ml-0">
            <div class="min-w-full p-4 md:p-10">
                <slot />
            </div>
        </main>

        <!-- Mobile backdrop -->
        <div v-if="sidebarOpen && !isDesktop" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 md:hidden"></div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, onUnmounted, ref } from 'vue';
const page = usePage();
const user = page.props.auth.user || null;
const count = ref(0);
const sidebarOpen = ref(false);
const isDesktop = ref(false);

const handleResize = () => {
    isDesktop.value = window.innerWidth >= 768; // md breakpoint
    if (isDesktop.value) {
        sidebarOpen.value = true;
    } else {
        sidebarOpen.value = false;
    }
};

onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
    fetchCount();
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

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
</script>

<style scoped>
.font-orbitron {
    font-family: 'Orbitron', sans-serif;
}

.sidebar-link {
    display: block;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: #00f5a0;
    transition: all 0.2s ease;
    text-decoration: none;
}

.sidebar-link:hover,
.sidebar-link.active {
    background: linear-gradient(90deg, #00f5a0, #00b2ff);
    color: #001;
    transform: translateX(2px);
}

/* Slide animation for mobile menu */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease-out;
}

.slide-enter-from {
    transform: translateX(-100%);
}

.slide-leave-to {
    transform: translateX(-100%);
}

/* Ensure proper scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(0, 245, 160, 0.3);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 245, 160, 0.5);
}
</style>
