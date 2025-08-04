<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";

const profil_perusahaan = ref(null);
const loading = ref(false);
const error = ref(null);

// Function to strip HTML tags
function stripHtml(html: string): string {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// Function to truncate text
function truncateText(text: string, length = 150): string {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

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

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }

    return `/storage/${image}`;
}

// Slider index untuk Visi dan Misi, pakai array thumbnail_perusahaan yang sama
const visiIndex = ref(0);
const misiIndex = ref(0);

let visiInterval, misiInterval;

onMounted(() => {
    // auto slide untuk visi
    visiInterval = setInterval(() => {
        if (profil_perusahaan.value?.thumbnail_perusahaan?.length > 0) {
            visiIndex.value = (visiIndex.value + 1) % profil_perusahaan.value.thumbnail_perusahaan.length;
        }
    }, 4000);

    // auto slide untuk misi
    misiInterval = setInterval(() => {
        if (profil_perusahaan.value?.thumbnail_perusahaan?.length > 0) {
            misiIndex.value = (misiIndex.value + 1) % profil_perusahaan.value.thumbnail_perusahaan.length;
        }
    }, 4000);
});

onUnmounted(() => {
    clearInterval(visiInterval);
    clearInterval(misiInterval);
});
</script>

<template>
    <AppLayout>
        <!-- Section Header -->
        <div class="w-full px-6 lg:px-16 py-28 bg-secondary text-white">
            <div class="w-full max-w-screen-xl mx-auto text-center flex flex-col gap-6 items-center">
                <h1 class="text-4xl lg:text-6xl font-custom font-normal leading-tight">
                    Visi & Misi Perusahaan
                </h1>
                <p class="text-base lg:text-lg font-custom font-normal leading-relaxed max-w-3xl">
                    Nilai-nilai yang menjadi dasar arah langkah dan strategi kami dalam memberikan dampak terbaik bagi
                    pelanggan dan masyarakat.
                </p>
            </div>
        </div>

        <!-- Section: Visi -->
        <div class="w-full px-6 lg:px-16 py-20 bg-white text-black">
            <div class="w-full max-w-screen-xl mx-auto flex flex-col lg:flex-row items-center gap-10">
                <!-- Teks Visi -->
                <div class="lg:w-1/2 flex flex-col gap-4">
                    <h2 class="text-3xl lg:text-5xl font-custom font-semibold text-secondary">
                        Visi Kami
                    </h2>
                    <!-- Use v-html for rich content display -->
                    <div 
                        v-if="profil_perusahaan?.visi_perusahaan"
                        v-html="profil_perusahaan.visi_perusahaan"
                        class="text-base lg:text-lg font-custom font-normal leading-relaxed prose prose-gray max-w-none"
                    ></div>
                    <p 
                        v-else
                        class="text-base lg:text-lg font-custom font-normal leading-relaxed"
                    >
                        Visi perusahaan belum tersedia.
                    </p>
                </div>

                <!-- Slider Gambar Visi -->
                <div class="lg:w-1/2 overflow-hidden rounded-2xl shadow">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${visiIndex * 100}%)` }">
                        <template v-if="profil_perusahaan?.thumbnail_perusahaan?.length">
                            <img v-for="(img, index) in profil_perusahaan.thumbnail_perusahaan"
                                :key="'visi-img-' + index" :src="getImageUrl(img)" :alt="`Gambar Visi ${index + 1}`"
                                class="w-full flex-shrink-0 object-cover" style="height: 400px;" />
                        </template>
                        <template v-else>
                            <img src="/image/placeholder.webp" alt="Placeholder Visi" class="w-full object-cover"
                                style="height: 400px;" />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Misi -->
        <div class="w-full px-6 lg:px-16 py-20 bg-secondary text-white">
            <div class="w-full max-w-screen-xl mx-auto flex flex-col lg:flex-row-reverse items-center gap-10">
                <!-- Teks Misi -->
                <div class="lg:w-1/2 flex flex-col gap-4">
                    <h2 class="text-3xl lg:text-5xl font-custom font-semibold">
                        Misi Kami
                    </h2>
                    <!-- Use v-html for rich content display -->
                    <div 
                        v-if="profil_perusahaan?.misi_perusahaan"
                        v-html="profil_perusahaan.misi_perusahaan"
                        class="text-base lg:text-lg font-custom font-normal leading-relaxed prose prose-invert max-w-none"
                    ></div>
                    <p 
                        v-else
                        class="text-base lg:text-lg font-custom font-normal leading-relaxed"
                    >
                        Misi perusahaan belum tersedia.
                    </p>
                </div>

                <!-- Slider Gambar Misi -->
                <div class="lg:w-1/2 overflow-hidden rounded-2xl shadow">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${misiIndex * 100}%)` }">
                        <template v-if="profil_perusahaan?.thumbnail_perusahaan?.length">
                            <img v-for="(img, index) in profil_perusahaan.thumbnail_perusahaan"
                                :key="'misi-img-' + index" :src="getImageUrl(img)" :alt="`Gambar Misi ${index + 1}`"
                                class="w-full flex-shrink-0 object-cover" style="height: 400px;" />
                        </template>
                        <template v-else>
                            <img src="/image/placeholder.webp" alt="Placeholder Misi" class="w-full object-cover"
                                style="height: 400px;" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Ensure HTML content renders properly */
:deep(.prose) {
    max-width: none;
}

/* Prose styling for Visi section (dark text on white background) */
:deep(.prose-gray h1) {
    @apply text-2xl font-bold text-gray-900 mt-6 mb-4;
}

:deep(.prose-gray h2) {
    @apply text-xl font-bold text-gray-900 mt-6 mb-3;
}

:deep(.prose-gray h3) {
    @apply text-lg font-semibold text-gray-900 mt-4 mb-3;
}

:deep(.prose-gray p) {
    @apply mb-4 leading-relaxed text-gray-800;
}

:deep(.prose-gray ul) {
    @apply list-disc ml-6 mb-4;
}

:deep(.prose-gray ol) {
    @apply list-decimal ml-6 mb-4;
}

:deep(.prose-gray li) {
    @apply mb-2 text-gray-800;
}

:deep(.prose-gray blockquote) {
    @apply border-l-4 border-gray-300 pl-4 italic my-4 bg-gray-50 py-2 text-gray-700;
}

:deep(.prose-gray strong) {
    @apply font-semibold text-gray-900;
}

:deep(.prose-gray em) {
    @apply italic text-gray-800;
}

:deep(.prose-gray a) {
    @apply text-blue-600 hover:text-blue-800 underline;
}

/* Prose styling for Misi section (white text on dark background) */
:deep(.prose-invert h1) {
    @apply text-2xl font-bold text-white mt-6 mb-4;
}

:deep(.prose-invert h2) {
    @apply text-xl font-bold text-white mt-6 mb-3;
}

:deep(.prose-invert h3) {
    @apply text-lg font-semibold text-white mt-4 mb-3;
}

:deep(.prose-invert p) {
    @apply mb-4 leading-relaxed text-white;
}

:deep(.prose-invert ul) {
    @apply list-disc ml-6 mb-4;
}

:deep(.prose-invert ol) {
    @apply list-decimal ml-6 mb-4;
}

:deep(.prose-invert li) {
    @apply mb-2 text-white;
}

:deep(.prose-invert blockquote) {
    @apply border-l-4 border-white/30 pl-4 italic my-4 bg-white/10 py-2 text-white;
}

:deep(.prose-invert strong) {
    @apply font-semibold text-white;
}

:deep(.prose-invert em) {
    @apply italic text-white;
}

:deep(.prose-invert a) {
    @apply text-blue-300 hover:text-blue-100 underline;
}
</style>
