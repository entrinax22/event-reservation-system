
<template>
    <Head title="Admin Dashboard - Users" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-[#00f5a0]">Users Table</h1>
                <p class="text-sm text-[#7fbfb0]">Manage registered users and their roles.</p>
            </div>

            <!-- Create User Form -->
            <form @submit.prevent="createUser" class="max-w-3xl space-y-6 rounded-xl bg-[rgba(0,0,0,0.85)] p-6 shadow-xl">
                <h2 class="font-orbitron text-xl text-[#00f5a0]">Create New User</h2>

                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="name" class="block text-sm text-[#7fbfb0]">Name</label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter full name"
                            required
                        />
                        <p v-if="errors.name" class="mt-1 text-xs text-red-400">{{ errors.name[0] }}</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm text-[#7fbfb0]">Email</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter email"
                            required
                        />
                        <p v-if="errors.email" class="mt-1 text-xs text-red-400">{{ errors.email[0] }}</p>
                    </div>

                    <div>
                        <label for="contact" class="block text-sm text-[#7fbfb0]">Contact Information</label>
                        <input
                            id="contact_information"
                            type="text"
                            v-model="form.contact_information"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter contact info"
                        />
                        <p v-if="errors.contact" class="mt-1 text-xs text-red-400">{{ errors.contact[0] }}</p>
                    </div>

                    <div>
                        <label for="role" class="block text-sm text-[#7fbfb0]">Role</label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            required
                        >
                            <option value="" disabled>Select role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        <p v-if="errors.role" class="mt-1 text-xs text-red-400">{{ errors.role[0] }}</p>
                    </div>
                </div>

                <!-- Submit button spans full width -->
                <button
                    type="submit"
                    :disabled="submitting"
                    class="w-full rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ submitting ? 'Creating...' : 'Create User' }}
                </button>
            </form>
        </section>
    </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import axios from 'axios';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
// Form state
const form = ref({
    name: '',
    email: '',
    contact_information: '',
    role: '',
    password: '',
    password_confirmation: '',
});

const errors = ref({});
const submitting = ref(false);

async function createUser() {
    submitting.value = true;
    errors.value = {};

    try {
        const response = await axios.post(route('admin.users.store'), form.value);
        alert('User created successfully!');
        // Reset form
        form.value = { name: '', email: '', contact: '', role: '', password: '', password_confirmation: '' };
    } catch (error) {
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        } else {
            alert('An unexpected error occurred.');
        }
    } finally {
        submitting.value = false;
    }
}
</script>
