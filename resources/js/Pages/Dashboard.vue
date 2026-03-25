<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCards from '@/Components/DashboardCards.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total: 0, active: 0, expired: 0, this_week: 0, this_month: 0,
        }),
    },
    recent: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout>

        <!-- Header -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Your client base at a glance.</p>
                </div>
                <Link
                    href="/clients"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition"
                >
                    Manage Clients →
                </Link>
            </div>
        </template>

        <!-- Stats Cards -->
        <DashboardCards :stats="stats" />

        <!-- Recent Clients -->
        <div class="mt-8 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-700">Recent Clients</h2>
                <Link
                    href="/clients"
                    class="text-sm font-medium text-indigo-600 hover:underline"
                >
                    View All →
                </Link>
            </div>

            <!-- Empty state -->
            <div v-if="recent.length === 0" class="py-14 text-center">
                <div class="text-4xl mb-3">📋</div>
                <p class="text-gray-500 text-sm font-medium">No clients yet.</p>
                <Link
                    href="/clients"
                    class="mt-3 inline-block text-sm text-indigo-600 font-semibold hover:underline"
                >
                    Add your first client →
                </Link>
            </div>

            <!-- Table -->
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/60 border-b border-gray-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Name</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Expiry</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Days Left</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="client in recent"
                        :key="client.id"
                        class="hover:bg-gray-50/50 transition-colors"
                    >
                        <td class="px-5 py-3.5 font-semibold text-gray-800">{{ client.name }}</td>
                        <td class="px-5 py-3.5 text-gray-500 hidden sm:table-cell">{{ client.phone }}</td>
                        <td class="px-5 py-3.5 text-gray-600 tabular-nums">{{ client.expiry_date }}</td>
                        <td class="px-5 py-3.5">
                            <span
                                class="text-sm font-semibold tabular-nums"
                                :class="client.is_active ? 'text-gray-700' : 'text-red-400'"
                            >
                                {{ client.is_active ? client.days_remaining + 'd' : '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <StatusBadge :active="client.is_active" />
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </AppLayout>
</template>
