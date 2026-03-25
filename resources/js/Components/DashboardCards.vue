<script setup>
import { computed } from 'vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});

const stats = computed(() => {
    const total   = props.clients.length;
    const active  = props.clients.filter(c => c.is_active).length;
    const expired = props.clients.filter(c => c.is_expired).length;

    const soonest = props.clients
        .filter(c => c.is_active)
        .sort((a, b) => new Date(a.expiry_date) - new Date(b.expiry_date))[0];

    return [
        {
            label: 'Total Clients',
            value: total,
            icon: '👥',
            color: 'bg-indigo-50 text-indigo-600',
            ring: 'ring-indigo-100',
        },
        {
            label: 'Active',
            value: active,
            icon: '✅',
            color: 'bg-emerald-50 text-emerald-600',
            ring: 'ring-emerald-100',
        },
        {
            label: 'Expired',
            value: expired,
            icon: '⛔',
            color: 'bg-red-50 text-red-600',
            ring: 'ring-red-100',
        },
        {
            label: 'Next Expiry',
            value: soonest ? soonest.expiry_date : '—',
            icon: '📅',
            color: 'bg-amber-50 text-amber-600',
            ring: 'ring-amber-100',
        },
    ];
});
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-center gap-4"
        >
            <!-- Icon bubble -->
            <div
                class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl ring-2 shrink-0"
                :class="[stat.color, stat.ring]"
            >
                {{ stat.icon }}
            </div>

            <!-- Text -->
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                    {{ stat.label }}
                </p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">
                    {{ stat.value }}
                </p>
            </div>
        </div>
    </div>
</template>
