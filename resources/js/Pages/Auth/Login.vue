<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login.submit'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <GuestLayout>

        <h2 class="text-xl font-bold text-gray-800 mb-1">Welcome back</h2>
        <p class="text-sm text-gray-500 mb-6">Sign in to your account</p>

        <!-- Default credentials hint -->
        <div class="mb-5 rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3 text-xs text-indigo-700">
            <span class="font-semibold">Default login:</span>
            admin@example.com / password
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email address
                </label>
                <input
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    required
                    placeholder="you@example.com"
                    class="w-full rounded-xl border px-4 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="form.errors.email ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white'"
                />
                <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password
                </label>
                <input
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    required
                    placeholder="••••••••"
                    class="w-full rounded-xl border px-4 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="form.errors.password ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white'"
                />
                <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Remember me -->
            <div class="flex items-center gap-2">
                <input
                    id="remember"
                    v-model="form.remember"
                    type="checkbox"
                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <label for="remember" class="text-sm text-gray-600 select-none">
                    Remember me
                </label>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       disabled:opacity-60 disabled:cursor-not-allowed transition"
            >
                <span v-if="form.processing">Signing in…</span>
                <span v-else>Sign in</span>
            </button>

        </form>

        <!-- Register link -->
        <p class="mt-6 text-center text-sm text-gray-500">
            Don't have an account?
            <Link :href="route('register')" class="font-semibold text-indigo-600 hover:underline">
                Create one
            </Link>
        </p>

    </GuestLayout>
</template>
