<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['edit']);

// ── Delete confirmation ──────────────────────────────────────────────────────
const confirmingDelete = ref(null);

function confirmDelete(client) {
    confirmingDelete.value = client;
}

function cancelDelete() {
    confirmingDelete.value = null;
}

function deleteClient(client) {
    router.delete(route('clients.destroy', client.id), {
        preserveScroll: true,
        onFinish: () => { confirmingDelete.value = null; },
    });
}

// ── Mark as paid ─────────────────────────────────────────────────────────────
// Track which client ID is currently being processed to show loading state
// and prevent double-clicks.
const processingId = ref(null);

function markAsPaid(client) {
    if (processingId.value !== null) return;   // already processing another row

    processingId.value = client.id;

    router.patch(route('clients.markAsPaid', client.id), {}, {
        preserveScroll: true,                  // don't jump to top after success
        onFinish: () => { processingId.value = null; },
    });
}
</script>

<template>
    <!-- Delete Confirmation Modal -->
    <Transition name="fade">
        <div
            v-if="confirmingDelete"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
        >
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
                <div class="text-center mb-4">
                    <div class="text-4xl mb-2">🗑️</div>
                    <h3 class="text-lg font-bold text-gray-800">Delete Client?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="font-medium text-gray-700">{{ confirmingDelete.name }}</span>
                        will be permanently removed.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="cancelDelete"
                        class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteClient(confirmingDelete)"
                        class="flex-1 rounded-xl bg-red-500 py-2.5 text-sm font-semibold text-white hover:bg-red-600 transition"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </Transition>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- Empty state -->
        <div v-if="clients.length === 0" class="py-16 text-center">
            <div class="text-5xl mb-3">📋</div>
            <p class="text-gray-500 font-medium">No clients found.</p>
            <p class="text-sm text-gray-400 mt-1">Try a different filter or add a new client.</p>
        </div>

        <!-- Data table -->
        <div v-else class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Email</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiry Date</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="client in clients"
                        :key="client.id"
                        class="hover:bg-gray-50/50 transition-colors"
                        :class="processingId === client.id ? 'opacity-60' : ''"
                    >
                        <td class="px-5 py-4 font-semibold text-gray-800">
                            {{ client.name }}
                        </td>

                        <td class="px-5 py-4 text-gray-600">
                            {{ client.phone }}
                        </td>

                        <td class="px-5 py-4 text-gray-500 hidden md:table-cell">
                            {{ client.email ?? '—' }}
                        </td>

                        <!-- Expiry + days remaining -->
                        <td class="px-5 py-4">
                            <span class="font-medium text-gray-700 tabular-nums">{{ client.expiry_date }}</span>
                            <span v-if="client.is_active" class="ml-1.5 text-xs text-gray-400">
                                ({{ client.days_remaining }}d left)
                            </span>
                        </td>

                        <!-- Shared StatusBadge component -->
                        <td class="px-5 py-4">
                            <StatusBadge :active="client.is_active" />
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <!-- Mark as Paid -->
                                <button
                                    @click="markAsPaid(client)"
                                    :disabled="processingId !== null"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition disabled:cursor-not-allowed"
                                    :class="processingId === client.id
                                        ? 'bg-indigo-100 text-indigo-400'
                                        : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'"
                                    title="Extend subscription by 1 month from current expiry date"
                                >
                                    <span v-if="processingId === client.id">⏳ Processing…</span>
                                    <span v-else>💳 Mark Paid</span>
                                </button>

                                <button
                                    @click="emit('edit', client)"
                                    :disabled="processingId !== null"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 transition disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    ✏️ Edit
                                </button>

                                <button
                                    @click="confirmDelete(client)"
                                    :disabled="processingId !== null"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition disabled:cursor-not-allowed disabled:opacity-50"
                                    title="Permanently delete this client"
                                >
                                    🗑️
                                </button>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
