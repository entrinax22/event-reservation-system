<template>
    <div class="flex min-h-screen bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-[#00f5a0]">
        <!-- Mobile Toggle Button -->
        <button class="absolute top-4 left-4 z-50 rounded-md bg-black/70 p-2 text-[#00f5a0] md:hidden" @click="sidebarOpen = !sidebarOpen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Sidebar -->
        <transition name="slide">
            <aside
                v-if="sidebarOpen || isDesktop"
                class="fixed top-0 left-0 z-40 flex h-full w-64 flex-shrink-0 flex-col overflow-y-auto rounded-r-2xl bg-black/80 px-6 py-8 shadow-xl md:static"
            >
                <div>
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
                    <nav class="mt-4 flex flex-col gap-4">
                        <!-- Main -->
                        <div>
                            <p class="px-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">Main</p>
                            <div class="mt-2 flex flex-col gap-2">
                                <Link :href="route('home')" class="sidebar-link">Home</Link>
                                <Link :href="route('admin')" class="sidebar-link">Notifications</Link>
                            </div>
                        </div>

                        <!-- Tables -->
                        <div>
                            <p class="px-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">Tables</p>
                            <div class="mt-2 flex flex-col gap-2">
                                <Link class="sidebar-link">Bookings Table</Link>
                                <Link class="sidebar-link">Users Table</Link>
                                <Link class="sidebar-link">Services Table</Link>
                                <Link class="sidebar-link">Materials Table</Link>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div>
                            <p class="px-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">Settings</p>
                            <div class="mt-2">
                                <div class="sidebar-link flex items-center justify-between">
                                    <span class="flex items-center gap-2">
                                        <span>Settings</span>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3a7 7 0 100 14 7 7 0 000-14zm0 2a5 5 0 110 10A5 5 0 0110 5z" />
                                        <path d="M10.707 9.293a1 1 0 00-1.414-1.414l-2.828 2.828a1 1 0 001.414 1.414l2.828-2.828z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- User & Logout -->
                <div class="mt-6 mb-4">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="font-semibold">{{ user?.name || 'Admin' }}</span>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="w-full rounded-lg px-4 py-2 text-left text-sm text-red-400 hover:bg-black/60"
                        >Logout</Link
                    >
                </div>
            </aside>
        </transition>

        <!-- Main content -->
        <main class="flex-1 p-10 md:ml-0">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const page = usePage();
const user = page.props.auth.user || null;

const sidebarOpen = ref(false);
const isDesktop = ref(false);

const handleResize = () => {
    isDesktop.value = window.innerWidth >= 768; // md breakpoint
    if (isDesktop.value) sidebarOpen.value = true;
    else sidebarOpen.value = false;
};

onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
});
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
    transition: background 0.2s;
}
.sidebar-link:hover,
.sidebar-link.active {
    background: linear-gradient(90deg, #00f5a0, #00b2ff);
    color: #001;
}
/* Slide animation for mobile menu */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}
.slide-enter-from {
    transform: translateX(-100%);
}
.slide-leave-to {
    transform: translateX(-100%);
}
.sidebar {
    height: 100vh; /* Fill the full viewport height */
    overflow-y: auto; /* Enable vertical scrolling */
}

.sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
</style>
