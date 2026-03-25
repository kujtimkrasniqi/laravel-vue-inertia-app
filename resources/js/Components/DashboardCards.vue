<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total:      0,
            active:     0,
            expired:    0,
            this_week:  0,
            this_month: 0,
        }),
    },
});

const cards = [
    {
        key:        'total',
        label:      'Total Clients',
        icon:       '👥',
        bg:         'bg-indigo-50',
        iconColor:  'text-indigo-600',
        ring:       'ring-indigo-100',
        valueColor: 'text-indigo-700',
        link:       null,
        linkLabel:  null,
    },
    {
        key:        'active',
        label:      'Active',
        icon:       '✅',
        bg:         'bg-emerald-50',
        iconColor:  'text-emerald-600',
        ring:       'ring-emerald-100',
        valueColor: 'text-emerald-700',
        link:       '/clients?filter=active',
        linkLabel:  'View →',
    },
    {
        key:        'expired',
        label:      'Expired',
        icon:       '⛔',
        bg:         'bg-red-50',
        iconColor:  'text-red-600',
        ring:       'ring-red-100',
        valueColor: 'text-red-700',
        link:       '/clients?filter=expired',
        linkLabel:  'View →',
    },
    {
        key:        'this_week',
        label:      'Expiring This Week',
        icon:       '📅',
        bg:         'bg-amber-50',
        iconColor:  'text-amber-600',
        ring:       'ring-amber-100',
        valueColor: 'text-amber-700',
        link:       '/clients?filter=this_week',
        linkLabel:  'View →',
    },
    {
        key:        'this_month',
        label:      'Expiring This Month',
        icon:       '🗓️',
        bg:         'bg-violet-50',
        iconColor:  'text-violet-600',
        ring:       'ring-violet-100',
        valueColor: 'text-violet-700',
        link:       '/clients?filter=this_month',
        linkLabel:  'View →',
    },
];
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
        <div
            v-for="card in cards"
            :key="card.key"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-5 flex flex-col gap-3"
        >
            <!-- Top row: icon + label -->
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-xl ring-2 shrink-0"
                    :class="[card.bg, card.ring]"
                >
                    {{ card.icon }}
                </div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide leading-tight">
                    {{ card.label }}
                </p>
            </div>

            <!-- Value -->
            <p class="text-4xl font-extrabold tracking-tight" :class="card.valueColor">
                {{ stats[card.key] ?? 0 }}
            </p>

            <!-- Optional link -->
            <Link
                v-if="card.link"
                :href="card.link"
                class="mt-auto text-xs font-semibold text-gray-400 hover:text-indigo-600 transition-colors"
            >
                {{ card.linkLabel }}
            </Link>
            <span v-else class="mt-auto text-xs text-gray-300">all time</span>
        </div>
    </div>
</template>
