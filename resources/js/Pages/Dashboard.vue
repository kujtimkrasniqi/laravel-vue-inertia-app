<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCards from '@/Components/DashboardCards.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Overview of your client base.</p>
                </div>
                <Link
                    href="/clients"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition"
                >
                    View All Clients →
                </Link>
            </div>
        </template>

        <!-- Stats cards wired to real client data -->
        <DashboardCards :clients="clients" />

        <!-- Quick summary table -->
        <div class="mt-8 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-700">Recent Clients</h2>
                <Link href="/clients" class="text-sm text-indigo-600 hover:underline font-medium">
                    Manage →
                </Link>
            </div>

            <!-- Empty state -->
            <div v-if="clients.length === 0" class="py-12 text-center">
                <div class="text-4xl mb-2">📋</div>
                <p class="text-gray-500 text-sm">No clients yet. <Link href="/clients" class="text-indigo-600 font-medium hover:underline">Add one →</Link></p>
            </div>

            <!-- Mini table -->
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/60 border-b border-gray-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Name</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Expiry</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="client in clients.slice(0, 5)"
                        :key="client.id"
                        class="hover:bg-gray-50/50 transition-colors"
                    >
                        <td class="px-5 py-3.5 font-semibold text-gray-800">{{ client.name }}</td>
                        <td class="px-5 py-3.5 text-gray-500 hidden sm:table-cell">{{ client.phone }}</td>
                        <td class="px-5 py-3.5 text-gray-600">{{ client.expiry_date }}</td>
                        <td class="px-5 py-3.5">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="client.is_active
                                    ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
                                    : 'bg-red-50 text-red-600 ring-1 ring-red-200'"
                            >
                                <span class="w-1.5 h-1.5 rounded-full"
                                    :class="client.is_active ? 'bg-emerald-500' : 'bg-red-400'"
                                />
                                {{ client.is_active ? 'Active' : 'Expired' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AppLayout>
</template>
