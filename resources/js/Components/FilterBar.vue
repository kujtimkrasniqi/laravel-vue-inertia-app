<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    activeFilter: {
        type: String,
        default: 'all',
    },
    clients: {
        type: Array,
        default: () => [],
    },
});

// ── Filter definitions ───────────────────────────────────────────────────────

const today     = new Date();
today.setHours(0, 0, 0, 0);

const weekEnd = new Date(today);
weekEnd.setDate(today.getDate() + (6 - today.getDay())); // end of current week (Sunday)

const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0); // last day of month

function parseDate(str) {
    return new Date(str + 'T00:00:00');
}

const counts = computed(() => {
    const all       = props.clients.length;
    const active    = props.clients.filter(c =>  c.is_active).length;
    const expired   = props.clients.filter(c =>  c.is_expired).length;
    const thisWeek  = props.clients.filter(c => {
        const d = parseDate(c.expiry_date);
        return d >= today && d <= weekEnd;
    }).length;
    const thisMonth = props.clients.filter(c => {
        const d = parseDate(c.expiry_date);
        return d >= today && d <= monthEnd;
    }).length;

    return { all, active, expired, thisWeek, thisMonth };
});

const filters = computed(() => [
    {
        key:   'all',
        label: 'All',
        count: counts.value.all,
        icon:  '👥',
        activeClass:  'bg-indigo-600 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeClass:   'bg-indigo-500 text-white',
        inactiveBadge:'bg-gray-100 text-gray-500',
    },
    {
        key:   'active',
        label: 'Active',
        count: counts.value.active,
        icon:  '✅',
        activeClass:  'bg-emerald-600 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeClass:   'bg-emerald-500 text-white',
        inactiveBadge:'bg-gray-100 text-gray-500',
    },
    {
        key:   'expired',
        label: 'Expired',
        count: counts.value.expired,
        icon:  '⛔',
        activeClass:  'bg-red-500 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeClass:   'bg-red-400 text-white',
        inactiveBadge:'bg-gray-100 text-gray-500',
    },
    {
        key:   'this_week',
        label: 'This Week',
        count: counts.value.thisWeek,
        icon:  '📅',
        activeClass:  'bg-amber-500 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeClass:   'bg-amber-400 text-white',
        inactiveBadge:'bg-gray-100 text-gray-500',
    },
    {
        key:   'this_month',
        label: 'This Month',
        count: counts.value.thisMonth,
        icon:  '🗓️',
        activeClass:  'bg-violet-600 text-white shadow-sm',
        inactiveClass:'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
        badgeClass:   'bg-violet-500 text-white',
        inactiveBadge:'bg-gray-100 text-gray-500',
    },
]);

// ── Navigation ───────────────────────────────────────────────────────────────

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
            <!-- Count badge -->
            <span
                class="rounded-full px-1.5 py-0.5 text-xs font-bold leading-none"
                :class="activeFilter === f.key ? f.badgeClass : f.inactiveBadge"
            >
                {{ f.count }}
            </span>
        </button>
    </div>
</template>
