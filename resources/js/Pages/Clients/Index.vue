<script setup>
import { ref } from 'vue';
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
                <button
                    @click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-sm"
                >
                    <span class="text-base leading-none">＋</span>
                    Add Client
                </button>
            </div>
        </template>

        <!-- Stats Cards — always uses ALL clients from the unfiltered set -->
        <DashboardCards :clients="clients" class="mb-6" />

        <!-- Filter Bar -->
        <div class="mb-5">
            <FilterBar
                :active-filter="activeFilter"
                :clients="clients"
            />
        </div>

        <!-- Client Table (filtered results) -->
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
