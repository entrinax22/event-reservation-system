<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />
    <div class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,_#00ffc8,_#242222)]">
        <div class="animate-fadeIn w-full max-w-md rounded-2xl bg-[rgba(20,20,20,0.95)] p-8 text-center shadow-[0_0_25px_rgba(0,255,200,0.4)]">
            <img src="/logos.png" alt="Big City Logo" class="animate-pulseGlow mx-auto mb-5 w-36 drop-shadow-[0_0_10px_#00ffc8]" />
            <h2 class="text-shadow mb-5 text-2xl font-bold text-[#00ffc8]">Register</h2>

            <form method="POST" @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-6">
                    <!-- Name -->
                    <div class="grid gap-2 text-left">
                        <label for="name" class="text-sm font-medium text-white">Name</label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            placeholder="Full name"
                            class="w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0] focus:ring-opacity-50"
                        />
                        <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div class="grid gap-2 text-left">
                        <label for="email" class="text-sm font-medium text-white">Email address</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            placeholder="email@example.com"
                            class="w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0] focus:ring-opacity-50"
                        />
                        <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div class="grid gap-2 text-left">
                        <label for="password" class="text-sm font-medium text-white">Password</label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            placeholder="Password"
                            class="w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0] focus:ring-opacity-50"
                        />
                        <p v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="grid gap-2 text-left">
                        <label for="password_confirmation" class="text-sm font-medium text-white">Confirm password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                            class="w-full rounded-md border border-gray-600 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-[#00f5a0] focus:ring focus:ring-[#00f5a0] focus:ring-opacity-50"
                        />
                        <p v-if="form.errors.password_confirmation" class="text-sm text-red-500">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        class="mt-2 flex w-full items-center justify-center rounded-md bg-[#00ffc8] px-4 py-2 font-semibold text-black hover:bg-[#00c88a] disabled:cursor-not-allowed disabled:bg-gray-500"
                        tabindex="5"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Create account
                    </button>
                </div>

                <!-- Login Link -->
                <div class="mt-2 text-center text-sm text-gray-400">
                    Already have an account?
                    <a :href="route('login')" class="text-[#00ffc8] hover:underline" :tabindex="6">Log in</a>
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
