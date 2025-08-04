<script setup>
import { Link } from '@inertiajs/vue3'
import { AlertCircle, Clock, Archive, BriefcaseBusiness } from 'lucide-vue-next'
</script>

<template>
    <div class="bg-red-500/20 backdrop-blur-sm border border-red-400/30 rounded-lg p-4 text-center">
        <AlertCircle class="w-12 h-12 text-red-300 mx-auto mb-3" />
        <p class="text-white font-medium">Lowongan ini sudah tidak menerima lamaran.</p>
        <p class="text-sm text-white/70 mt-1">Silakan cek lowongan lainnya yang masih dibuka.</p>
        <Link 
            href="/lowongan" 
            class="mt-3 inline-flex px-4 py-2 bg-white text-black hover:bg-white/90 transition duration-200 rounded-full text-sm font-medium items-center justify-center gap-1"
        >
            <BriefcaseBusiness class="w-4 h-4" />
            Lowongan Lainnya
        </Link>
    </div>
</template>