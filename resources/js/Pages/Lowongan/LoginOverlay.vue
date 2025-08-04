<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="relative">
        <!-- Blurred Form Background (for visual effect) -->
        <div class="opacity-20 blur-sm pointer-events-none">
            <div class="space-y-4">
                <div class="h-12 bg-white/20 rounded-lg"></div>
                <div class="h-12 bg-white/20 rounded-lg"></div>
                <div class="h-12 bg-white/20 rounded-lg"></div>
                <div class="h-12 bg-white/20 rounded-lg"></div>
                <div class="h-24 bg-white/20 rounded-lg"></div>
                <div class="h-12 bg-white/30 rounded-lg"></div>
            </div>
        </div>

        <!-- Login Required Message -->
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl">
            <img src="/image/login.svg" alt="Login Illustration" class="w-36 h-36 mb-4 object-contain" />
            <h4 class="text-lg font-semibold text-white mb-2">Mau Mendaftar?</h4>
            <p class="text-white/70 mb-4">Silakan login terlebih dahulu untuk mengirim lamaran Anda</p>
            <a href="/login"
                class="px-6 py-2 bg-white text-black hover:bg-white/90 transition-colors duration-300 rounded-full font-medium">
                Login Sekarang
            </a>
        </div>
    </div>
</template>