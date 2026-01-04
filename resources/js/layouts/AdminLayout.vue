<template>
    <div class="flex min-h-screen bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-white">
        <button
            class="fixed top-4 left-4 z-50 rounded-md border border-white/10 bg-black/40 p-2 text-white backdrop-blur-md transition hover:bg-black/60 hover:text-[#00f5a0] md:hidden"
            @click="sidebarOpen = !sidebarOpen"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <transition name="slide">
            <aside
                v-if="sidebarOpen || isDesktop"
                class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col overflow-hidden border-r border-white/10 bg-black/20 shadow-2xl backdrop-blur-xl md:static"
            >
                <div class="flex h-full flex-col px-6 py-8">
                    <div class="custom-scrollbar flex-1 overflow-y-auto">
                        <div class="mb-10 flex items-center gap-3">
                            <div
                                class="h-11 w-11 flex-shrink-0 rounded-full bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] p-[2px] shadow-[0_0_15px_rgba(0,245,160,0.3)]"
                            >
                                <img src="/logo.png" alt="Big City Logo" class="h-full w-full rounded-full bg-black object-cover" />
                            </div>
                            <div>
                                <h1 class="font-orbitron m-0 text-sm font-bold tracking-wider text-white">BIG CITY PRO AUDIO</h1>
                                <div class="text-xs font-medium tracking-wide text-[#00f5a0]">Admin Panel</div>
                            </div>
                        </div>

                        <nav class="flex flex-col gap-6">
                            <div>
                                <p class="mb-3 px-2 text-xs font-bold tracking-widest text-[#00f5a0]/80 uppercase">Main</p>
                                <div class="flex flex-col gap-2">
                                    <Link :href="route('home')" class="sidebar-link">Home</Link>
                                    <Link :href="route('admin')" class="sidebar-link">Dashboard</Link>
                                    <Link :href="route('notifications.admin')" class="sidebar-link relative">
                                        Notifications
                                        <span
                                            v-if="count > 0"
                                            class="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-[#ff007a] text-[10px] font-bold text-white shadow-md"
                                        >
                                            {{ count }}
                                        </span>
                                    </Link>
                                </div>
                            </div>

                            <div>
                                <p class="mb-3 px-2 text-xs font-bold tracking-widest text-[#00f5a0]/80 uppercase">Management</p>
                                <div class="flex flex-col gap-2">
                                    <Link :href="route('admin.users.table')" class="sidebar-link">Users</Link>
                                    <Link :href="route('admin.events.table')" class="sidebar-link">Events</Link>
                                    <Link :href="route('admin.materials.table')" class="sidebar-link">Equipments</Link>
                                    <Link :href="route('admin.reserved-events.table')" class="sidebar-link">Reserved Events</Link>
                                    <Link :href="route('admin.payments.table')" class="sidebar-link">Payments</Link>
                                </div>
                            </div>
                        </nav>
                    </div>

                    <div class="mt-4 flex-shrink-0 border-t border-white/10 pt-6">
                        <div class="mb-3 flex items-center gap-3 px-2">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-[#00f5a0] to-[#00b2ff]"></div>
                            <span class="font-semibold tracking-wide text-white">{{ user?.name || 'Admin' }}</span>
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2 text-left text-sm font-semibold text-red-300 transition-all hover:border-red-500/50 hover:bg-red-500/20 hover:text-red-200"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </aside>
        </transition>

        <main class="min-h-screen flex-1 overflow-x-auto overflow-y-auto md:ml-0">
            <div class="min-w-full p-4 md:p-10">
                <slot />
            </div>
        </main>

        <div v-if="sidebarOpen && !isDesktop" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm md:hidden"></div>
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
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.85); /* White text for better readability on green bg */
    transition: all 0.2s ease;
    text-decoration: none;
    border: 1px solid transparent;
}

.sidebar-link:hover,
.sidebar-link.active {
    background: linear-gradient(90deg, #00f5a0, #00b2ff);
    color: #000; /* Black text on bright gradient hover */
    font-weight: 700;
    box-shadow: 0 0 15px rgba(0, 245, 160, 0.4);
    transform: translateX(4px);
    border-color: rgba(255, 255, 255, 0.2);
}

/* Slide animation for mobile menu */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-enter-from {
    transform: translateX(-100%);
}

.slide-leave-to {
    transform: translateX(-100%);
}

/* Custom Scrollbar for Sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 245, 160, 0.3);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 245, 160, 0.6);
}
</style>
