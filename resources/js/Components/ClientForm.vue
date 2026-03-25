<script setup>
import { reactive, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    client: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

// Build form — pre-fill when editing, blank when creating
const form = useForm({
    name:       '',
    phone:      '',
    email:      '',
    start_date: '',
});

// Re-populate form whenever the modal opens or client changes
watch(
    () => [props.show, props.client],
    () => {
        if (props.client) {
            form.name       = props.client.name;
            form.phone      = props.client.phone;
            form.email      = props.client.email ?? '';
            form.start_date = props.client.start_date;
        } else {
            form.reset();
        }
    },
);

function submit() {
    if (props.client) {
        form.put(route('clients.update', props.client.id), {
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('clients.store'), {
            onSuccess: () => emit('close'),
        });
    }
}

function close() {
    form.reset();
    form.clearErrors();
    emit('close');
}
</script>

<template>
    <!-- Backdrop -->
    <Transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
            @click="close"
        />
    </Transition>

    <!-- Panel -->
    <Transition name="slide-up">
        <div
            v-if="show"
            class="fixed inset-x-0 bottom-0 z-50 sm:inset-0 sm:flex sm:items-center sm:justify-center"
        >
            <div class="w-full sm:max-w-lg bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl p-6 sm:p-8">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ client ? 'Edit Client' : 'Add Client' }}
                    </h2>
                    <button
                        @click="close"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                    >
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Full name"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                            :class="{ 'border-red-400': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.phone"
                            type="text"
                            placeholder="e.g. 0501234567"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                            :class="{ 'border-red-400': form.errors.phone }"
                        />
                        <p v-if="form.errors.phone" class="mt-1 text-xs text-red-500">
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="email@example.com"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                            :class="{ 'border-red-400': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                            :class="{ 'border-red-400': form.errors.start_date }"
                        />
                        <p v-if="form.errors.start_date" class="mt-1 text-xs text-red-500">
                            {{ form.errors.start_date }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            Expiry date will be set automatically to start + 1 month.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="close"
                            class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60 transition"
                        >
                            {{ form.processing ? 'Saving…' : (client ? 'Save Changes' : 'Add Client') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.25s ease, opacity 0.2s ease; }
.slide-up-enter-from, .slide-up-leave-to       { transform: translateY(40px); opacity: 0; }
</style>
