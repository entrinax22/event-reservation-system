<template>
    <div class="flex min-h-screen bg-gradient-to-br from-[#03d8a0] via-[#04746a] to-[#0b3b36] text-[#00f5a0]">
        <!-- Sidebar -->
        <aside class="flex h-screen w-64 flex-shrink-0 flex-col justify-between rounded-r-2xl bg-black/80 px-6 py-8 shadow-xl">
            <div>
                <div class="mb-8 flex items-center gap-3">
                    <div class="h-11 w-11 rounded-full bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] shadow-[0_0_18px_rgba(0,255,191,0.15)]">
                        <img src="/logo.png" alt="Big City Logo" class="h-full w-full rounded-full object-cover" />
                    </div>
                    <div>
                        <h1 class="font-orbitron m-0 text-base text-[#d8ffef]">BIG CITY PRO AUDIO</h1>
                        <div class="text-xs text-[#7fbfb0]">Admin Panel</div>
                    </div>
                </div>
                <nav class="mt-4 flex flex-col gap-2">
                    <Link :href="route('admin')" class="sidebar-link">Dashboard</Link>
                    <Link :href="route('notifications')" class="sidebar-link">Notifications</Link>
                    <Link :href="route('mybookings')" class="sidebar-link">My Bookings</Link>
                    <Link :href="route('business')" class="sidebar-link">Business Details</Link>
                </nav>
            </div>
            <div class="mb-4">
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
        <!-- Main content -->
        <main class="flex-1 p-10">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
const user = page.props.auth.user || null;
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
    margin-bottom: 0.25rem;
}
.sidebar-link:hover,
.sidebar-link.active {
    background: linear-gradient(90deg, #00f5a0, #00b2ff);
    color: #001;
}
</style>
