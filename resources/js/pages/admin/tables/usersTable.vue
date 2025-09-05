
<template>
    <Head title="Admin Dashboard - Users" />
    <AdminLayout>
        <section class="space-y-8">
            <!-- Header -->
            <div class="border-b border-[#00f5a0]/30 pb-4">
                <h1 class="font-orbitron text-3xl font-bold tracking-wide text-[#00f5a0]">Users Table</h1>
                <p class="text-sm text-[#7fbfb0]">Manage registered users and their roles.</p>
            </div>

            <!-- Table with search toolbar -->
            <BaseTable :title="'Users Table'" :columns="columns" :data="users" :pagination="pagination" @page-changed="fetchUsers">
                <template #toolbar>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search users..."
                        class="rounded-md border border-gray-600 bg-black/30 px-4 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                    />
                    <!-- <button @click="go" class="rounded-md bg-[#00f5a0] px-4 py-2 font-semibold text-black hover:bg-[#00c88a]">Add</button> -->
                </template>

                <template #actions="{ row }">
                    <button @click="openEditUser(row)" class="mr-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">Edit</button>
                    <button @click="deleteUser(row)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Delete</button>
                </template>
            </BaseTable>

            <EditModal
                :show="showEditModal"
                title="Edit User"
                :fields="userFields"
                v-model="selectedUser"
                :columns="1"
                @close="showEditModal = false"
                @save="updateUser"
            >
                <!-- Override "role" field with custom toggle -->
                <template #default="{ field, modelValue, update }">
                    <div v-if="field.key === 'role'">
                    <label class="block text-sm text-gray-300">Custom Role Toggle</label>
                    <button
                        class="mt-1 rounded bg-blue-500 px-3 py-1 text-white"
                        @click="update(modelValue === 'admin' ? 'user' : 'admin')"
                    >
                        {{ modelValue }}
                    </button>
                    </div>
                </template>
            </EditModal>
        </section>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import EditModal from '@/components/EditModal.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';

const columns = [
    { key: 'user_id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Role' },
    { key: 'actions', label: 'Actions' },
];

const users = ref([]);
const pagination = ref(null);
const search = ref('');
const currentPage = ref(1);

async function fetchUsers(page = 1) {
    currentPage.value = page;

    try {
        const response = await axios.get(route('admin.users.list'), {
            params: {
                search: search.value,
                page: page,
            },
        });

        if (response.data.result) {
            users.value = response.data.data;
            pagination.value = response.data.pagination;
        } else {
            users.value = [];
            pagination.value = null;
        }
    } catch (error) {
        console.error('Failed to fetch users:', error);
        users.value = [];
        pagination.value = null;
    }
}

// Fetch users on component mount
onMounted(() => {
    fetchUsers();
});

watch(search, (newVal) => {
    fetchUsers(currentPage.value, newVal);
});

function go() {
    router.get(route('admin.users.create'));
}

//Edit
const showEditModal = ref(false);
const selectedUser = ref({});
const userFields = [
  { key: 'name', label: 'Name', type: 'text', placeholder: 'Enter name' },
  { key: 'email', label: 'Email', type: 'email', placeholder: 'Enter email' },
  { key: 'contact_information', label: 'Contact Information', type: 'text', placeholder: 'Enter contact information' },
  {
    key: 'role',
    label: 'Role',
    type: 'select',
    options: [
      { value: 'admin', label: 'Admin' },
      { value: 'user', label: 'User' },
    ],
  },
];

function openEditUser(user) {
  selectedUser.value = { ...user };
  showEditModal.value = true;
}

async function updateUser(updatedData) {
    try {
        const response = await axios.post(
            route('admin.users.update'), 
            updatedData                  
        );

        showEditModal.value = false;
        fetchUsers(currentPage.value);

        console.log("User updated successfully:", response.data);
    } catch (error) {
        console.error("Error updating user:", error.response?.data || error);
    }
}
async function deleteUser(user) {
    try {
        const confirmed = window.confirm(
            `Are you sure you want to delete user "${user.name}"?`
        );

        if (!confirmed) {
            return; 
        }

        const response = await axios.post(
            route('admin.users.destroy'),
            { user_id: user.user_id }
        );

        showEditModal.value = false;
        fetchUsers(currentPage.value);

        console.log("User deleted successfully:", response.data);
    } catch (error) {
        console.error("Error deleting user:", error.response?.data || error);
    }
}
</script>
