<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClientTable from '@/Components/ClientTable.vue';
import ClientForm from '@/Components/ClientForm.vue';
import DashboardCards from '@/Components/DashboardCards.vue';
import FilterBar from '@/Components/FilterBar.vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
    activeFilter: {
        type: String,
        default: 'all',
    },
    stats: {
        type: Object,
        default: () => ({
            total: 0, active: 0, expired: 0, this_week: 0, this_month: 0,
        }),
    },
});

// ── Export URL — respects the active filter ──────────────────────────────────
const exportUrl = computed(() => {
    const base = route('clients.export');
    return props.activeFilter !== 'all'
        ? `${base}?filter=${props.activeFilter}`
        : base;
});

// ── Modal state ──────────────────────────────────────────────────────────────
const showForm      = ref(false);
const editingClient = ref(null);

function openCreate() {
    editingClient.value = null;
    showForm.value = true;
}

function openEdit(client) {
    editingClient.value = client;
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
    editingClient.value = null;
}
</script>

<template>
    <AppLayout>

        <!-- Header -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Clients</h1>
                    <p class="text-sm text-gray-400 mt-0.5">
                        {{ clients.length }} {{ clients.length === 1 ? 'client' : 'clients' }}
                        <span v-if="activeFilter !== 'all'" class="text-indigo-500 font-medium">
                            · filtered by "{{ activeFilter.replace('_', ' ') }}"
                        </span>
                    </p>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-3">

                    <!-- Export to Excel -->
                    <a
                        :href="exportUrl"
                        class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition shadow-sm"
                        title="Download current view as Excel file"
                    >
                        <span class="text-base leading-none">📥</span>
                        Export
                        <span
                            v-if="activeFilter !== 'all'"
                            class="rounded-full bg-emerald-200 px-1.5 py-0.5 text-xs font-bold text-emerald-800"
                        >
                            {{ activeFilter.replace('_', ' ') }}
                        </span>
                    </a>

                    <!-- Add Client -->
                    <button
                        @click="openCreate"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-sm"
                    >
                        <span class="text-base leading-none">＋</span>
                        Add Client
                    </button>

                </div>
            </div>
        </template>

        <!-- Stats Cards -->
        <DashboardCards :stats="stats" class="mb-6" />

        <!-- Filter Bar + Export hint -->
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <FilterBar
                :active-filter="activeFilter"
                :stats="stats"
            />
            <p class="text-xs text-gray-400 hidden sm:block">
                📥 Export respects the active filter
            </p>
        </div>

        <!-- Client Table -->
        <ClientTable
            :clients="clients"
            @edit="openEdit"
        />

        <!-- Create / Edit Modal -->
        <ClientForm
            :show="showForm"
            :client="editingClient"
            @close="closeForm"
        />

    </AppLayout>
</template>
