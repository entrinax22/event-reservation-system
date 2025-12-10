<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

// LOGIN FORM
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// ===== MODALS =====
const showForgotModal = ref(false);
const showVerifyModal = ref(false);

// ===== FORMS =====
const forgot = ref({ email: '' });
const otpForm = ref({ otp: '', password: '', password_confirmation: '' });

// ===== ERROR OBJECTS =====
const forgotErrors = ref({});
const otpErrors = ref({});

// ===== OPEN/CLOSE MODALS =====
const openForgotModal = () => (showForgotModal.value = true);
const closeForgotModal = () => {
    showForgotModal.value = false;
    forgotErrors.value = {};
    forgot.value.email = '';
};
const closeVerifyModal = () => {
    showVerifyModal.value = false;
    otpErrors.value = {};
    otpForm.value = { otp: '', password: '', password_confirmation: '' };
};

// ===== SEND OTP =====
const sendOtp = async () => {
    forgotErrors.value = {};
    try {
        const response = await axios.post(route('password.sendOtp'), { email: forgot.value.email });

        if (response.data.result) {
            alert('OTP sent to your email!');
            showForgotModal.value = false;
            showVerifyModal.value = true;
        } else {
            alert(response.data.message || 'Failed to send OTP.');
        }
    } catch (err: any) {
        if (err.response?.status === 422) {
            forgotErrors.value = err.response.data.errors;
        } else {
            console.error(err);
            alert('Error sending OTP.');
        }
    }
};

// ===== VERIFY OTP + RESET PASSWORD =====
const verifyOtp = async () => {
    otpErrors.value = {};
    try {
        const response = await axios.post(route('password.verifyOtp'), {
            email: forgot.value.email,
            otp: otpForm.value.otp,
            password: otpForm.value.password,
            password_confirmation: otpForm.value.password_confirmation,
        });

        if (response.data.result) {
            alert('Password changed successfully!');
            closeVerifyModal();
        } else {
            alert(response.data.message || 'Invalid OTP or password mismatch.');
        }
    } catch (err: any) {
        if (err.response?.status === 422) {
            otpErrors.value = err.response.data.errors;
        } else {
            console.error(err);
            alert('Error verifying OTP.');
        }
    }
};
</script>

<template>
    <Head title="Login" />
    <div class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,_#00ffc8,_#242222)]">
        <div class="animate-fadeIn w-full max-w-md rounded-2xl bg-[rgba(20,20,20,0.95)] p-8 text-center shadow-[0_0_25px_rgba(0,255,200,0.4)]">
            <img src="/logos.png" alt="Big City Logo" class="animate-pulseGlow mx-auto mb-5 w-36 drop-shadow-[0_0_10px_#00ffc8]" />
            <h2 class="text-shadow mb-5 text-2xl font-bold text-[#00ffc8]">Login</h2>

            <form method="POST" @submit.prevent="submit" class="flex flex-col gap-6">
                <!-- Email -->
                <div class="grid gap-2 text-left">
                    <label for="email" class="text-sm font-medium text-white">Email address</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="focus:ring-opacity-50 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0]"
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
                </div>

                <!-- Password -->
                <div class="grid gap-2 text-left">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-medium text-white">Password</label>
                        <a v-if="canResetPassword" @click="openForgotModal" class="text-sm text-[#00ffc8] hover:underline" :tabindex="5">
                            Forgot password?
                        </a>
                    </div>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Password"
                        class="focus:ring-opacity-50 w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0]"
                    />
                    <p v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</p>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center space-x-2 text-left">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        :tabindex="3"
                        class="h-4 w-4 rounded border-gray-600 bg-black/30 text-[#00f5a0] focus:ring-[#00f5a0]"
                    />
                    <label for="remember" class="text-sm text-white">Remember me</label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="mt-4 flex w-full items-center justify-center rounded-md bg-[#00ffc8] px-4 py-2 font-semibold text-black hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-500"
                    :tabindex="4"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    Log in
                </button>
                <a href="/auth/google/redirect" class="btn btn-danger"> <i class="fab fa-google"></i> Login with Google </a>

                <!-- Register Link -->
                <div class="mt-2 text-center text-sm text-gray-400">
                    Don't have an account?
                    <a :href="route('register')" class="text-[#00ffc8] hover:underline" :tabindex="5">Sign up</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div v-if="showForgotModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="w-80 rounded-xl border border-[#00f5a0]/30 bg-[#020d0b] p-6">
            <h3 class="mb-3 text-lg text-[#00f5a0]">Forgot Password</h3>

            <label class="text-[#7fbfb0]">Email</label>
            <input v-model="forgot.email" type="email" class="mt-1 mb-3 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]" />
            <p v-if="forgotErrors.email" class="mt-1 text-xs text-red-500">{{ forgotErrors.email[0] }}</p>

            <button @click="sendOtp" class="w-full rounded bg-[#00f5a0] py-2 text-black hover:bg-[#02ffbc]">Send OTP</button>
            <button @click="closeForgotModal" class="mt-3 w-full rounded border border-[#00f5a0] py-2 text-[#00f5a0] hover:bg-[#00f5a0]/20">
                Cancel
            </button>
        </div>
    </div>

    <!-- Verify OTP + Reset Password Modal -->
    <div v-if="showVerifyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="w-80 rounded-xl border border-[#00f5a0]/30 bg-[#020d0b] p-6">
            <h3 class="mb-3 text-lg text-[#00f5a0]">Verify OTP & Reset Password</h3>

            <label class="text-[#7fbfb0]">OTP Code</label>
            <input v-model="otpForm.otp" type="text" class="mt-1 mb-3 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]" />
            <p v-if="otpErrors.otp" class="mt-1 text-xs text-red-500">{{ otpErrors.otp[0] }}</p>

            <label class="text-[#7fbfb0]">New Password</label>
            <input
                v-model="otpForm.password"
                type="password"
                class="mt-1 mb-3 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
            />
            <p v-if="otpErrors.password" class="mt-1 text-xs text-red-500">{{ otpErrors.password[0] }}</p>

            <label class="text-[#7fbfb0]">Confirm Password</label>
            <input
                v-model="otpForm.password_confirmation"
                type="password"
                class="mt-1 mb-3 w-full rounded border border-[#00f5a0]/30 bg-black/40 p-2 text-[#00f5a0]"
            />
            <p v-if="otpErrors.password_confirmation" class="mt-1 text-xs text-red-500">{{ otpErrors.password_confirmation[0] }}</p>

            <button @click="verifyOtp" class="w-full rounded bg-[#00f5a0] py-2 text-black hover:bg-[#02ffbc]">Change Password</button>
            <button @click="closeVerifyModal" class="mt-3 w-full rounded border border-[#00f5a0] py-2 text-[#00f5a0] hover:bg-[#00f5a0]/20">
                Cancel
            </button>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulseGlow {
    0%,
    100% {
        filter: drop-shadow(0 0 10px #00ffc8);
    }
    50% {
        filter: drop-shadow(0 0 20px #00ffc8);
    }
}
.animate-pulseGlow {
    animation: pulseGlow 2s infinite ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
.animate-fadeIn {
    animation: fadeIn 0.6s ease-in-out;
}
.text-shadow {
    text-shadow: 0 0 8px #00ffc8;
}
</style>
