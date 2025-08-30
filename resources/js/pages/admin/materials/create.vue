<template>
    <Head title="Admin Dashboard - Materials" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-[#00f5a0]">Materials</h1>
                <p class="text-sm text-[#7fbfb0]">Create and manage materials.</p>
            </div>

            <!-- Create Event Form -->
            <form 
                @submit.prevent="createMaterial" 
                class="max-w-3xl space-y-6 rounded-xl bg-[rgba(0,0,0,0.85)] p-6 shadow-xl"
            >
                <h2 class="font-orbitron text-xl text-[#00f5a0]">Create New Material</h2>

                <div class="grid grid-cols-1 gap-y-4">
                    <!-- Material Name -->
                    <div>
                        <label for="material_name" class="block text-sm text-[#7fbfb0]">Material Name</label>
                        <input
                            id="material_name"
                            type="text"
                            v-model="form.material_name"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter material name"
                            required
                        />
                        <p v-if="errors.material_name" class="mt-1 text-xs text-red-400">{{ errors.material_name[0] }}</p>
                    </div>

                    <!-- Event Description -->
                    <div>
                        <label for="material_description" class="block text-sm text-[#7fbfb0]">Material Description</label>
                        <textarea
                            id="material_description"
                            v-model="form.material_description"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            placeholder="Enter material description"
                            rows="4"
                            required
                        ></textarea>
                        <p v-if="errors.material_description" class="mt-1 text-xs text-red-400">{{ errors.material_description[0] }}</p>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm text-[#7fbfb0]">Status</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 w-full rounded border border-gray-600 bg-black/40 px-3 py-2 text-white focus:ring-2 focus:ring-[#00f5a0] focus:outline-none"
                            required
                        >
                            <option value="" disabled>Select status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <p v-if="errors.status" class="mt-1 text-xs text-red-400">{{ errors.status[0] }}</p>
                    </div>
                </div>

                <!-- Submit button spans full width -->
                <button
                    type="submit"
                    :disabled="submitting"
                    class="w-full rounded bg-[#00f5a0] px-4 py-2 font-semibold text-black transition hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ submitting ? 'Creating...' : 'Create Event' }}
                </button>
            </form>
        </section>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

// Form state
const form = ref({
    material_name: '',
    material_description: '',
    status:''
})

const errors = ref({})
const submitting = ref(false)

async function createMaterial() {
    submitting.value = true
    errors.value = {}

    try {
        const response = await axios.post(route('admin.materials.store'), form.value)
        // Reset form
        form.value = { 
            material_name: '',
            material_description: '',
            status:''
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors
        } else {
            alert('An unexpected error occurred.')
        }
    } finally {
        submitting.value = false
    }
}
</script>
