<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    activeFilter: {
        type: String,
        default: 'all',
    },
    // Pre-computed counts from the server (always reflects full dataset)
    stats: {
        type: Object,
        default: () => ({
            total: 0, active: 0, expired: 0, this_week: 0, this_month: 0,
        }),
    },
});

const filters = computed(() => [
    {
        key:          'all',
        label:        'All',
        count:        props.stats.total,
        icon:         '👥',
        activeClass:  'bg-indigo-600 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeActive:  'bg-white/20 text-white',
        badgeInactive:'bg-gray-100 text-gray-500',
    },
    {
        key:          'active',
        label:        'Active',
        count:        props.stats.active,
        icon:         '✅',
        activeClass:  'bg-emerald-600 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeActive:  'bg-white/20 text-white',
        badgeInactive:'bg-gray-100 text-gray-500',
    },
    {
        key:          'expired',
        label:        'Expired',
        count:        props.stats.expired,
        icon:         '⛔',
        activeClass:  'bg-red-500 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeActive:  'bg-white/20 text-white',
        badgeInactive:'bg-gray-100 text-gray-500',
    },
    {
        key:          'this_week',
        label:        'This Week',
        count:        props.stats.this_week,
        icon:         '📅',
        activeClass:  'bg-amber-500 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeActive:  'bg-white/20 text-white',
        badgeInactive:'bg-gray-100 text-gray-500',
    },
    {
        key:          'this_month',
        label:        'This Month',
        count:        props.stats.this_month,
        icon:         '🗓️',
        activeClass:  'bg-violet-600 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeActive:  'bg-white/20 text-white',
        badgeInactive:'bg-gray-100 text-gray-500',
    },
]);

function applyFilter(key) {
    router.get(
        route('clients.index'),
        key !== 'all' ? { filter: key } : {},
        { preserveState: true, replace: true },
    );
}
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <button
            v-for="f in filters"
            :key="f.key"
            @click="applyFilter(f.key)"
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all"
            :class="activeFilter === f.key ? f.activeClass : f.inactiveClass"
        >
            <span class="text-base leading-none">{{ f.icon }}</span>
            {{ f.label }}
            <span
                class="rounded-full px-1.5 py-0.5 text-xs font-bold leading-none"
                :class="activeFilter === f.key ? f.badgeActive : f.badgeInactive"
            >
                {{ f.count }}
            </span>
        </button>
    </div>
</template>
