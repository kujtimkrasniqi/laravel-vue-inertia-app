<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const flash = computed(() => page.props.flash ?? {});

const navigation = [
    { name: 'Dashboard', href: '/',        routeName: 'dashboard'      },
    { name: 'Clients',   href: '/clients', routeName: 'clients.index'  },
];

function isActive(routeName) {
    return page.props.ziggy?.location?.includes(routeName) ||
           route().current(routeName);
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <!-- Left: Brand + Nav -->
                    <div class="flex items-center gap-8">
                        <Link href="/" class="text-xl font-bold text-indigo-600 tracking-tight">
                            MyApp
                        </Link>

                        <div class="hidden sm:flex gap-1">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="route().current(item.routeName)
                                    ? 'bg-indigo-50 text-indigo-700'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Right: User avatar placeholder -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 ring-2 ring-indigo-200 flex items-center justify-center">
                            <span class="text-xs font-bold text-indigo-600">U</span>
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

        <!-- Page Header slot -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-100 mt-0">
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
