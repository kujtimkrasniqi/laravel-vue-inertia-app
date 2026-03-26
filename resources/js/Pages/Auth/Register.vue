<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register.submit'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <GuestLayout>

        <h2 class="text-xl font-bold text-gray-800 mb-1">Create an account</h2>
        <p class="text-sm text-gray-500 mb-6">Fill in the details below to get started</p>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Full name
                </label>
                <input
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    required
                    placeholder="John Doe"
                    class="w-full rounded-xl border px-4 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="form.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white'"
                />
                <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                    {{ form.errors.name }}
                </p>
            </div>

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
                    autocomplete="new-password"
                    required
                    placeholder="Min. 8 characters"
                    class="w-full rounded-xl border px-4 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="form.errors.password ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white'"
                />
                <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Confirm password
                </label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Repeat your password"
                    class="w-full rounded-xl border px-4 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="form.errors.password_confirmation ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white'"
                />
                <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-500">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       disabled:opacity-60 disabled:cursor-not-allowed transition"
            >
                <span v-if="form.processing">Creating account…</span>
                <span v-else>Create account</span>
            </button>

        </form>

        <!-- Login link -->
        <p class="mt-6 text-center text-sm text-gray-500">
            Already have an account?
            <Link :href="route('login')" class="font-semibold text-indigo-600 hover:underline">
                Sign in
            </Link>
        </p>

    </GuestLayout>
</template>
