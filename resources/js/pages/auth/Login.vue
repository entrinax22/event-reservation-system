<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

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
                        class="w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0] focus:ring-opacity-50"
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
                </div>

                <!-- Password -->
                <div class="grid gap-2 text-left">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-medium text-white">Password</label>
                        <a
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-[#00ffc8] hover:underline"
                            :tabindex="5"
                        >
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
                        class="w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0] focus:ring-opacity-50"
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
                <a href="/auth/google/redirect" class="btn btn-danger">
                    <i class="fab fa-google"></i> Login with Google
                </a>


                <!-- Register Link -->
                <div class="mt-2 text-center text-sm text-gray-400">
                    Don't have an account?
                    <a :href="route('register')" class="text-[#00ffc8] hover:underline" :tabindex="5">Sign up</a>
                </div>
            </form>
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
