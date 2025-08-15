<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Password</Label>
                            <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                                Forgot password?
                            </TextLink>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <Label for="remember" class="flex items-center space-x-3">
                            <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                            <span>Remember me</span>
                        </Label>
                    </div>

                    <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Log in
                    </Button>
                </div>

                <div class="mt-2 text-center text-sm text-muted-foreground">
                    Don't have an account?
                    <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
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
