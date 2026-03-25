<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const stats = [
    { label: 'Total Users',   value: '—', icon: '👥' },
    { label: 'Active Sessions', value: '—', icon: '🟢' },
    { label: 'Revenue',        value: '—', icon: '💰' },
    { label: 'Pending Tasks',  value: '—', icon: '📋' },
];
</script>

<template>
    <AppLayout>

        <!-- Page Header -->
        <template #header>
            <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
        </template>

        <!-- Welcome Banner -->
        <div class="bg-indigo-600 rounded-2xl px-8 py-6 mb-8 text-white">
            <p class="text-sm font-medium text-indigo-200 mb-1">Welcome back 👋</p>
            <h2 class="text-3xl font-bold">Good to see you!</h2>
            <p class="mt-1 text-indigo-200 text-sm">Here's what's happening with your app today.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-center gap-4"
            >
                <div class="text-3xl">{{ stat.icon }}</div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ stat.label }}</p>
                    <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ stat.value }}</p>
                </div>
            </div>
        </div>

        <!-- Placeholder Content Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-8 text-center">
            <div class="text-5xl mb-3">🚀</div>
            <h3 class="text-lg font-semibold text-gray-700">Project foundation is ready</h3>
            <p class="text-sm text-gray-400 mt-1">
                Laravel 12 · Vue 3 · Inertia.js · TailwindCSS v4 · Docker Sail · MySQL
            </p>
        </div>

    </AppLayout>
</template>
