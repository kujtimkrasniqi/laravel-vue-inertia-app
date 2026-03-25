<script setup>
import { Link } from '@inertiajs/vue3';

const navigation = [
    { name: 'Dashboard', href: '/' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <!-- Left: Brand + Nav Links -->
                    <div class="flex items-center gap-8">
                        <Link href="/" class="text-xl font-bold text-indigo-600 tracking-tight">
                            MyApp
                        </Link>

                        <div class="hidden sm:flex gap-4">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Right: User placeholder -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-xs font-semibold text-indigo-600">U</span>
                        </div>
                    </div>

                </div>
            </div>
        </nav>

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
