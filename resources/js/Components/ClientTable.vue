<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['edit']);

// ── Confirm delete ──────────────────────────────────────────────────────────
const confirmingDelete = ref(null);

function confirmDelete(client) {
    confirmingDelete.value = client;
}

function cancelDelete() {
    confirmingDelete.value = null;
}

function deleteClient(client) {
    router.delete(route('clients.destroy', client.id), {
        onFinish: () => { confirmingDelete.value = null; },
    });
}

// ── Mark as Paid ────────────────────────────────────────────────────────────
function markAsPaid(client) {
    router.patch(route('clients.markAsPaid', client.id));
}
</script>

<template>
    <!-- Delete Confirm Modal -->
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

    <!-- Table wrapper -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- Empty state -->
        <div v-if="clients.length === 0" class="py-16 text-center">
            <div class="text-5xl mb-3">📋</div>
            <p class="text-gray-500 font-medium">No clients yet.</p>
            <p class="text-sm text-gray-400 mt-1">Click "Add Client" to get started.</p>
        </div>

        <!-- Desktop Table -->
        <div v-else class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 uppercase tracking-wide text-xs">Name</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 uppercase tracking-wide text-xs">Phone</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 uppercase tracking-wide text-xs hidden md:table-cell">Email</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 uppercase tracking-wide text-xs">Expiry Date</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 uppercase tracking-wide text-xs">Status</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-500 uppercase tracking-wide text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="client in clients"
                        :key="client.id"
                        class="hover:bg-gray-50/50 transition-colors"
                    >
                        <!-- Name -->
                        <td class="px-5 py-4">
                            <span class="font-semibold text-gray-800">{{ client.name }}</span>
                        </td>

                        <!-- Phone -->
                        <td class="px-5 py-4 text-gray-600">
                            {{ client.phone }}
                        </td>

                        <!-- Email (hidden on mobile) -->
                        <td class="px-5 py-4 text-gray-500 hidden md:table-cell">
                            {{ client.email ?? '—' }}
                        </td>

                        <!-- Expiry Date + days remaining -->
                        <td class="px-5 py-4">
                            <span class="text-gray-700 font-medium">{{ client.expiry_date }}</span>
                            <span
                                v-if="client.is_active"
                                class="ml-1.5 text-xs text-gray-400"
                            >
                                ({{ client.days_remaining }}d left)
                            </span>
                        </td>

                        <!-- Status badge -->
                        <td class="px-5 py-4">
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

                        <!-- Actions -->
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <!-- Mark as Paid -->
                                <button
                                    @click="markAsPaid(client)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition"
                                    title="Extend by 1 month"
                                >
                                    💳 Mark Paid
                                </button>

                                <!-- Edit -->
                                <button
                                    @click="emit('edit', client)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 transition"
                                >
                                    ✏️ Edit
                                </button>

                                <!-- Delete -->
                                <button
                                    @click="confirmDelete(client)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition"
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
