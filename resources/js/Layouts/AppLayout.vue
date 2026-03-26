<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const flash   = computed(() => page.props.flash ?? {});
const user    = computed(() => page.props.auth?.user ?? null);

// User initials for avatar
const initials = computed(() => {
    if (!user.value?.name) return 'U';
    return user.value.name
        .split(' ')
        .map(w => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
});

const navigation = [
    { name: 'Dashboard', routeName: 'dashboard' },
    { name: 'Clients',   routeName: 'clients.index' },
];

// User dropdown
const dropdownOpen = ref(false);

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

function closeDropdown() {
    dropdownOpen.value = false;
}

function logout() {
    dropdownOpen.value = false;
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-gray-50" @click="closeDropdown">

        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <!-- Left: Brand + Nav -->
                    <div class="flex items-center gap-8">
                        <Link :href="route('dashboard')" class="text-xl font-bold text-indigo-600 tracking-tight">
                            MyApp
                        </Link>

                        <div class="hidden sm:flex gap-1">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="route(item.routeName)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="route().current(item.routeName)
                                    ? 'bg-indigo-50 text-indigo-700'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Right: User dropdown -->
                    <div class="flex items-center" @click.stop>
                        <div class="relative">
                            <button
                                @click="toggleDropdown"
                                class="flex items-center gap-2.5 rounded-xl px-3 py-1.5 hover:bg-gray-100 transition focus:outline-none"
                            >
                                <!-- Avatar -->
                                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center shadow-sm">
                                    <span class="text-xs font-bold text-white">{{ initials }}</span>
                                </div>
                                <!-- Name (hidden on mobile) -->
                                <span class="hidden sm:block text-sm font-medium text-gray-700">
                                    {{ user?.name }}
                                </span>
                                <!-- Chevron -->
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="dropdownOpen ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <Transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-if="dropdownOpen"
                                    class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white shadow-lg border border-gray-100 py-1 z-50"
                                >
                                    <!-- User info -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ user?.name }}</p>
                                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ user?.email }}</p>
                                    </div>

                                    <!-- Logout -->
                                    <button
                                        @click="logout"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Sign out
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Flash message -->
        <Transition name="fade">
            <div
                v-if="flash.success"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
            >
                <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 font-medium">
                    <span>✅</span>
                    {{ flash.success }}
                </div>
            </div>
        </Transition>

        <Transition name="fade">
            <div
                v-if="flash.error"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
            >
                <div class="flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 font-medium">
                    <span>❌</span>
                    {{ flash.error }}
                </div>
            </div>
        </Transition>

        <!-- Page Header slot -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
