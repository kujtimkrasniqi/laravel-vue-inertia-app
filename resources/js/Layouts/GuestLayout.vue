<script setup>
// Clean centered layout for auth pages (Login / Register)
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex flex-col items-center justify-center px-4 py-12">

        <!-- Logo / Brand -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600 shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">MyApp</h1>
            <p class="text-sm text-gray-500 mt-1">Client Management System</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <slot />
        </div>

    </div>
</template>
