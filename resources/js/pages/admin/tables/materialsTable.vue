<template>
    <Head title="Admin Dashboard - Materials" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-white">Equipments</h1>
                <p class="text-xl text-white">Manage registered equipments and their details.</p>
            </div>

            <!-- Table with search toolbar -->
            <BaseTable :title="'Equipments'" :columns="columns" :data="materials" :pagination="pagination" @page-changed="fetchMaterials">
                <!-- Search + Add Button -->
                <template #toolbar>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search materials..."
                        class="rounded-md border border-gray-600 bg-black/30 px-4 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                    />
                    <button @click="go" class="rounded-md bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#00c88a]">Add</button>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button @click="openEditMaterial(row)" class="mr-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">Edit</button>
                    <button @click="deleteMaterial(row)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Delete</button>
                </template>
            </BaseTable>

            <!-- Edit Modal -->
            <EditModal
                :show="showEditModal"
                title="Edit Material"
                :fields="materialFields"
                v-model="selectedMaterial"
                :columns="1"
                @close="showEditModal = false"
                @save="updateMaterial"
            />
        </section>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import EditModal from '@/components/EditModal.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const columns = [
    { key: 'material_id', label: 'ID' },
    { key: 'material_name', label: 'Name' },
    { key: 'material_description', label: 'Description' },
    { key: 'material_quantity', label: 'Quantity' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Actions' },
];

const materials = ref([]);
const pagination = ref(null);
const search = ref('');
const currentPage = ref(1);

async function fetchMaterials(page = 1) {
    currentPage.value = page;

    try {
        const response = await axios.get(route('admin.materials.list'), {
            params: {
                search: search.value,
                page,
            },
        });

        if (response.data.result) {
            materials.value = response.data.data;
            pagination.value = response.data.pagination;
        } else {
            materials.value = [];
            pagination.value = null;
        }
    } catch (error) {
        console.error('Failed to fetch materials:', error);
        materials.value = [];
        pagination.value = null;
    }
}

// Fetch on mount
onMounted(fetchMaterials);

// Search watcher
watch(search, () => {
    fetchMaterials(1); // reset to page 1 when searching
});

function go() {
    router.get(route('admin.materials.create'));
}

// ===== Edit / Update / Delete =====
const showEditModal = ref(false);
const selectedMaterial = ref({});
const materialFields = [
    { key: 'material_name', label: 'Name', type: 'text', placeholder: 'Enter material name' },
    { key: 'material_description', label: 'Description', type: 'textarea', placeholder: 'Enter material description' },
    { key: 'material_quantity', label: 'Quantity', type: 'number', placeholder: 'Enter material quantity' },
    {
        key: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' },
        ],
    },
];

function openEditMaterial(material) {
    selectedMaterial.value = { ...material };
    showEditModal.value = true;
}

async function updateMaterial(updatedData) {
    try {
        const response = await axios.post(route('admin.materials.update'), updatedData);
        showEditModal.value = false;
        fetchMaterials(currentPage.value);

        console.log('Material updated successfully:', response.data);
    } catch (error) {
        console.error('Error updating material:', error.response?.data || error);
    }
}

async function deleteMaterial(material) {
    try {
        const confirmed = window.confirm(`Are you sure you want to delete material "${material.material_name}"?`);
        if (!confirmed) return;

        const response = await axios.post(route('admin.materials.destroy'), { material_id: material.material_id });
        showEditModal.value = false;
        fetchMaterials(currentPage.value);

        console.log('Material deleted successfully:', response.data);
    } catch (error) {
        console.error('Error deleting material:', error.response?.data || error);
    }
}
</script>
