<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, } from "vue";
import axios from "axios";
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const profil_perusahaan = ref(null);
const loading = ref(true);
const error = ref(null);

// Generate placeholder items for skeleton loading
const placeholderItems = [1, 2, 3, 4];

onMounted(() => {
    fetchProfilPerusahaan();
});

async function fetchProfilPerusahaan() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/profil-perusahaan/`);
        profil_perusahaan.value = response.data.data;
        loading.value = false;
    } catch (err) {
        error.value = "Event not found or an error occurred";
        loading.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="w-full px-4 sm:px-8 lg:px-16 py-20 bg-background font-custom text-black">
            <div class="max-w-screen-xl mx-auto">
                <!-- Header Section -->
                <div class="mb-16 text-center">
                    <div class="inline-block px-4 py-2 bg-secondary text-white rounded-full text-sm font-semibold mb-4">
                        Sejarah Perusahaan
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-normal leading-tight mb-6">
                        {{ profil_perusahaan?.nama_perusahaan || 'CMS' }}
                    </h1>
                    <p class="text-lg max-w-3xl mx-auto leading-relaxed">
                        Ikuti perjalanan kami dari awal hingga saat ini. Kami telah melalui banyak tantangan dan
                        pencapaian yang membentuk kami menjadi perusahaan yang kami kenal sekarang.
                    </p>
                </div>

                <!-- Timeline Section -->
                <div class="relative">
                    <!-- Main Timeline Line - Only shown when content is loaded -->
                    <div v-if="!loading"
                        class="absolute left-4 md:left-1/2 top-0 bottom-0 w-1 bg-secondary transform md:translate-x-[-0.5px]">
                    </div>

                    <div class="space-y-12">
                        <!-- Skeleton Loading UI -->
                        <template v-if="loading">
                            <div v-for="(item, index) in placeholderItems" :key="'skeleton-' + index" class="relative">
                                <!-- Skeleton Timeline Dot -->
                                <div
                                    class="absolute left-4 md:left-1/2 w-10 h-10 bg-gray-300 rounded-full transform translate-x-[-15px] md:translate-x-[-20px] animate-pulse">
                                </div>

                                <!-- Skeleton Content Card -->
                                <div class="md:w-[calc(50%-40px)] ml-16 md:ml-0"
                                    :class="index % 2 === 0 ? 'md:mr-auto' : 'md:ml-auto'">
                                    <div
                                        class="bg-gray-200 p-6 rounded-xl relative overflow-hidden animate-pulse">
                                        <!-- Skeleton Year Badge -->
                                        <div class="absolute -right-1 -top-6">
                                            <div class="bg-gray-300 w-20 h-20 rounded-lg"></div>
                                        </div>

                                        <div class="pt-2 pb-4">
                                            <!-- Skeleton Title -->
                                            <div class="h-8 bg-gray-300 rounded-md mb-4 w-3/4"></div>

                                            <!-- Skeleton Text Lines -->
                                            <div class="space-y-3">
                                                <div class="h-4 bg-gray-300 rounded-md w-full"></div>
                                                <div class="h-4 bg-gray-300 rounded-md w-5/6"></div>
                                                <div class="h-4 bg-gray-300 rounded-md w-4/6"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Error State -->
                        <div v-else-if="error" class="text-center py-20 bg-red-50 rounded-lg">
                            <div class="text-red-500 text-lg">{{ error }}</div>
                            <button
                                class="mt-4 px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90 transition-colors"
                                @click="fetchProfilPerusahaan()">
                                Coba Lagi
                            </button>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="!profil_perusahaan?.sejarah_perusahaan?.length"
                            class="text-center py-20 bg-secondary/10 rounded-lg">
                            <div class="text-gray-600 text-lg">Belum ada data sejarah perusahaan.</div>
                        </div>

                        <!-- Actual Content -->
                        <template v-else>
                            <div v-for="(item, index) in profil_perusahaan.sejarah_perusahaan" :key="index"
                                class="relative">
                                <!-- Timeline Dot -->
                                <div
                                    class="absolute left-4 md:left-1/2 w-10 h-10 bg-secondary rounded-full border-4 border-white transform translate-x-[-15px] md:translate-x-[-20px] flex items-center justify-center z-10">
                                    <span class="text-white font-bold text-xs">{{ index + 1 }}</span>
                                </div>

                                <!-- Content Card -->
                                <div class="md:w-[calc(50%-40px)] ml-16 md:ml-0"
                                    :class="index % 2 === 0 ? 'md:mr-auto' : 'md:ml-auto'">
                                    <div
                                        class="bg-secondary text-white p-6 rounded-xl transition-all duration-300 relative overflow-hidden">
                                        <!-- Year Badge - Revamped -->
                                        <div
                                            class="absolute -right-4 -top-4 transform rotate-12">
                                            <div
                                                class="bg-white/20 backdrop-blur-sm w-24 h-24 rounded-lg flex items-center justify-center">
                                                <div class="bg-white/30 w-20 h-20 rounded-lg rotate-45 absolute"></div>
                                                <span class="text-3xl font-bold text-white relative z-10">{{ item.tahun
                                                    }}</span>
                                            </div>
                                        </div>

                                        <div class="pt-2 pb-4">
                                            <h3 class="text-2xl sm:text-3xl font-normal mb-4 pr-16">{{ item.judul }}
                                            </h3>
                                            <p class="text-sm lg:text-md leading-relaxed text-white/90">
                                                {{ item.deskripsi }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Timeline Connector Lines (for non-mobile) - Enhanced -->
                                    <div class="hidden md:block absolute top-5 h-[3px] bg-secondary z-0" :class="index % 2 === 0
                                        ? 'right-0 w-[40px] translate-x-[10px]'
                                        : 'left-0 w-[40px] translate-x-[-40px]'">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>