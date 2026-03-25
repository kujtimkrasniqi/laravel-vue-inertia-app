<script setup>
defineProps({
    active: {
        type: Boolean,
        required: true,
    },
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold"
        :class="active
            ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
            : 'bg-red-50 text-red-600 ring-1 ring-red-200'"
    >
        <span
            class="w-1.5 h-1.5 rounded-full"
            :class="active ? 'bg-emerald-500' : 'bg-red-400'"
        />
        {{ active ? 'Active' : 'Expired' }}
    </span>
</template>
