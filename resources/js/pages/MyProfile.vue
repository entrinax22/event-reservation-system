<template>
    <Head title="Profile" />
    <MainLayout>
        <section class="rounded-2xl border border-black/20 bg-[rgba(0,0,0,0.78)] p-6 shadow-xl">
            <h2 class="font-orbitron mb-6 text-xl text-[#00f5a0]">My Profile</h2>

            <div class="flex flex-col gap-6 md:flex-row">
                <!-- LEFT SIDE -->
                <div class="flex w-full flex-col items-center rounded-xl border border-[#00f5a0]/20 bg-[#020d0b] p-5 md:w-1/3">
                    <!-- PROFILE PHOTO -->
                    <div class="relative mb-4 h-28 w-28">
                        <img :src="user.photo_url ?? '/logo.png'" class="h-28 w-28 rounded-full border border-[#00f5a0]/40 object-cover shadow-lg" />

                        <label
                            class="absolute right-0 bottom-0 cursor-pointer rounded-full bg-[#00f5a0] px-2 py-1 text-xs text-black hover:bg-[#02ffbc]"
                        >
                            <input type="file" class="hidden" @change="uploadPhoto" accept="image/*" />
                            Edit
                        </label>
                    </div>

                    <h3 class="text-lg text-[#00f5a0]">{{ user.name }}</h3>
                    <p class="text-sm text-[#7fbfb0]">{{ user.email }}</p>
                    <p class="text-sm text-[#7fbfb0]">{{ user.contact_information ?? 'No contact set' }}</p>

                    <!-- STATS -->
                    <div class="mt-4 flex w-full justify-between text-center">
                        <div>
                            <p class="text-lg font-bold text-[#00f5a0]">{{ stats.total_reservations }}</p>
                            <p class="text-[10px] text-[#7fbfb0]">Reservations</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-[#00f5a0]">{{ stats.pending_reservations }}</p>
                            <p class="text-[10px] text-[#7fbfb0]">Pending</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-[#00f5a0]">{{ stats.completed_reservations }}</p>
                            <p class="text-[10px] text-[#7fbfb0]">Completed</p>
                        </div>
                    </div>

                    <button class="my-5 rounded bg-[#00f5a0] px-3 py-1 text-black hover:bg-[#02ffbc]" @click="openSendOtpModal">
                        Change Password
                    </button>
                </div>

                <!-- RIGHT SIDE: EDIT FORM -->
                <div class="w-full rounded-xl border border-[#00f5a0]/20 bg-[#020d0b] p-6 md:w-2/3">
                    <h3 class="mb-3 text-lg text-[#00f5a0]">Edit Information</h3>

                    <form @submit.prevent="saveProfile" class="space-y-4 text-sm">
                        <!-- NAME -->
                        <div>
                            <label class="text-[#7fbfb0]">Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-1 w-full rounded-md border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0] focus:border-[#00f5a0]"
                            />
                            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <label class="text-[#7fbfb0]">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="mt-1 w-full rounded-md border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
                            />
                            <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                        </div>

                        <!-- CONTACT INFORMATION -->
                        <div>
                            <label class="text-[#7fbfb0]">Mobile Number</label>
                            <input
                                v-model="form.contact_information"
                                type="text"
                                class="mt-1 w-full rounded-md border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
                            />
                            <p v-if="errors.contact_information" class="mt-1 text-xs text-red-500">{{ errors.contact_information[0] }}</p>
                        </div>

                        <!-- ADDRESS -->
                        <div>
                            <label class="text-[#7fbfb0]">Address</label>
                            <textarea
                                v-model="form.address"
                                rows="2"
                                class="mt-1 w-full rounded-md border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
                            ></textarea>
                            <p v-if="errors.address" class="mt-1 text-xs text-red-500">{{ errors.address[0] }}</p>
                        </div>

                        <!-- SUBMIT -->
                        <button type="submit" class="rounded bg-[#00f5a0] px-4 py-2 text-black transition hover:bg-[#02ffbc]">Save Changes</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- ========================= -->
        <!-- SEND OTP MODAL -->
        <!-- ========================= -->
        <div v-if="showSendOtpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="w-80 rounded-xl border border-[#00f5a0]/30 bg-[#020d0b] p-6">
                <h3 class="mb-3 text-lg text-[#00f5a0]">Send OTP</h3>
                <p class="mb-4 text-sm text-[#7fbfb0]">Click send to receive a verification code in your email.</p>

                <button @click="sendOtp" class="w-full rounded bg-[#00f5a0] py-2 text-black hover:bg-[#02ffbc]">Send OTP</button>

                <button @click="closeSendOtpModal" class="mt-3 w-full rounded border border-[#00f5a0] py-2 text-[#00f5a0] hover:bg-[#00f5a0]/20">
                    Cancel
                </button>
            </div>
        </div>

        <!-- ========================= -->
        <!-- VERIFY OTP + CHANGE PASSWORD MODAL -->
        <!-- ========================= -->
        <div v-if="showVerifyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="w-80 rounded-xl border border-[#00f5a0]/30 bg-[#020d0b] p-6">
                <h3 class="mb-3 text-lg text-[#00f5a0]">Verify OTP</h3>

                <label class="text-[#7fbfb0]">OTP Code</label>
                <input v-model="otp.code" type="text" class="mt-1 mb-3 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]" />
                <p v-if="otpErrors.otp" class="mt-1 text-xs text-red-500">{{ otpErrors.otp[0] }}</p>

                <label class="text-[#7fbfb0]">New Password</label>
                <input
                    v-model="otp.password"
                    type="password"
                    class="mt-1 mb-3 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
                />
                <p v-if="otpErrors.password" class="mt-1 text-xs text-red-500">{{ otpErrors.password[0] }}</p>

                <label class="text-[#7fbfb0]">Confirm Password</label>
                <input
                    v-model="otp.password_confirmation"
                    type="password"
                    class="mt-1 mb-4 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
                />
                <p v-if="otpErrors.password_confirmation" class="mt-1 text-xs text-red-500">{{ otpErrors.password_confirmation[0] }}</p>

                <button @click="verifyOtp" class="w-full rounded bg-[#00f5a0] py-2 text-black hover:bg-[#02ffbc]">Change Password</button>

                <button @click="closeVerifyModal" class="mt-3 w-full rounded border border-[#00f5a0] py-2 text-[#00f5a0] hover:bg-[#00f5a0]/20">
                    Cancel
                </button>
            </div>
        </div>
    </MainLayout>
</template>
<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const user = ref({});
const stats = ref({});
const errors = ref({});
const otpErrors = ref({});

const form = ref({
    name: '',
    email: '',
    contact_information: '',
    address: '',
    password: '',
    password_confirmation: '',
    photo_url: null,
});

// FETCH USER + STATS
const fetchUserData = async () => {
    try {
        const response = await axios.get(route('user.data'));

        if (response.data.result) {
            user.value = response.data.data;
            stats.value = response.data.stats;

            form.value.name = user.value.name;
            form.value.email = user.value.email;
            form.value.contact_information = user.value.contact_information;
            form.value.address = user.value.address ?? '';
        }
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
};

onMounted(fetchUserData);

// SAVE PROFILE
const saveProfile = async () => {
    errors.value = {}; // reset errors
    try {
        const fd = new FormData();
        fd.append('name', form.value.name);
        fd.append('email', form.value.email);
        fd.append('contact_information', form.value.contact_information);
        fd.append('address', form.value.address);

        if (form.value.password !== '') {
            fd.append('password', form.value.password);
            fd.append('password_confirmation', form.value.password_confirmation);
        }

        if (form.value.photo_url) {
            fd.append('photo_url', form.value.photo_url);
        }

        const response = await axios.post(route('user.profile.update'), fd, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        if (response.data.result) {
            alert('Profile updated successfully!');
            fetchUserData();
        } else {
            alert('Failed to update profile.');
        }
    } catch (err) {
        if (err.response && err.response.status === 422) {
            // Laravel validation errors
            errors.value = err.response.data.errors;
        } else {
            console.error(err);
            alert('Something went wrong during update.');
        }
    }
};

// HANDLE IMAGE UPLOAD
const uploadPhoto = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.value.photo_url = file;

        // OPTIONAL live preview:
        user.value.photo_url = URL.createObjectURL(file);
    }
};

// ===== MODAL STATES =====
const showSendOtpModal = ref(false);
const showVerifyModal = ref(false);

const otp = ref({
    code: '',
    password: '',
    password_confirmation: '',
});

// ===== OPEN/CLOSE MODALS =====
const openSendOtpModal = () => (showSendOtpModal.value = true);
const closeSendOtpModal = () => (showSendOtpModal.value = false);
const closeVerifyModal = () => (showVerifyModal.value = false);

// ===== SEND OTP TO EMAIL =====
const sendOtp = async () => {
    try {
        const response = await axios.post(route('user.password.sendOtp'));

        if (response.data.result) {
            alert('OTP sent to your email!');
            showSendOtpModal.value = false;
            showVerifyModal.value = true;
        } else {
            alert('Failed to send OTP.');
        }
    } catch (err) {
        if (err.response && err.response.status === 422) {
            // Validation errors from Laravel
            const errors = err.response.data.errors;
            let messages = Object.values(errors).flat().join('\n');
            alert(messages);
        } else {
            console.error(err);
            alert('Error sending OTP.');
        }
    }
};

// ===== VERIFY OTP + CHANGE PASSWORD =====
const verifyOtp = async () => {
    otpErrors.value = {}; // reset errors
    try {
        const response = await axios.post(route('user.password.verifyOtp'), {
            otp: otp.value.code,
            password: otp.value.password,
            password_confirmation: otp.value.password_confirmation,
        });

        if (response.data.result) {
            alert('Password changed successfully!');
            showVerifyModal.value = false;
            otp.value = { code: '', password: '', password_confirmation: '' };
        } else {
            alert(response.data.message ?? 'Invalid OTP or password mismatch.');
        }
    } catch (err) {
        if (err.response && err.response.status === 422) {
            otpErrors.value = err.response.data.errors;
        } else {
            console.error(err);
            alert('Error verifying OTP.');
        }
    }
};
</script>

<style scoped>
input,
textarea {
    transition: 0.2s;
}
input:focus,
textarea:focus {
    box-shadow: 0 0 10px #00f5a0;
}
</style>
