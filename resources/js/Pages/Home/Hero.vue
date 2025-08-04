<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { ChevronDown, Play, ArrowRight } from "lucide-vue-next";

const user = usePage().props.auth.user;

const defaultImages = [
    "/image/21.webp",
    "/image/22.webp",
    "/image/23.webp",
    "/image/24.webp",
    "/image/25.webp",
    "/image/26.webp",
];

const currentImage = ref(0);
const profilPerusahaan = ref(null);
const loading = ref(false);
const error = ref(null);
let intervalId = null;

async function fetchProfilPerusahaan() {
    try {
        loading.value = true;
        const response = await axios.get("/api/profil-perusahaan");
        profilPerusahaan.value = response.data.data;
        loading.value = false;

        const slides = getThumbnails();
        if (slides.length > 1) {
            intervalId = setInterval(() => {
                currentImage.value = (currentImage.value + 1) % slides.length;
            }, 6000);
        }
    } catch (err) {
        error.value = "Profil perusahaan gagal dimuat.";
        loading.value = false;
    }
}

function getThumbnails() {
    const thumbs = profilPerusahaan.value?.thumbnail_perusahaan;
    if (!thumbs) return [];
    if (Array.isArray(thumbs)) return thumbs;
    return [thumbs];
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    return image.startsWith("/image/") ? image : `/storage/${image}`;
}

onMounted(fetchProfilPerusahaan);

onBeforeUnmount(() => {
    clearInterval(intervalId);
});
</script>

<template>
    <div class="relative w-full h-screen overflow-hidden">
        <!-- Background Images -->
        <template v-if="getThumbnails().length > 0">
            <div
                v-for="(img, index) in getThumbnails()"
                :key="index"
                class="background-image transition-opacity"
                :style="{
                    backgroundImage: `url(${getImageUrl(img)})`,
                    opacity: index === currentImage ? 1 : 0,
                }"
                :class="{ 'animate-zoomPan': index === currentImage }"
            ></div>
        </template>

        <template v-else>
            <div
                v-for="(img, index) in defaultImages"
                :key="index"
                class="background-image transition-opacity"
                :style="{
                    backgroundImage: `url(${img})`,
                    opacity: index === currentImage ? 1 : 0,
                }"
                :class="{ 'animate-zoomPan': index === currentImage }"
            ></div>
        </template>

        <!-- Modern Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-black/70 z-10"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 left-20 w-32 h-32 bg-primary/20 rounded-full blur-3xl z-10"></div>
        <div class="absolute bottom-32 right-32 w-48 h-48 bg-accent/20 rounded-full blur-3xl z-10"></div>

        <!-- Main Content -->
        <div class="relative z-20 w-full h-full">
            <div class="w-full h-full flex flex-col items-center justify-center text-center px-6 lg:px-8 font-custom">
                
                
                <!-- <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full mb-8 border border-white/20">
                    <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                    <span class="text-white/90 text-sm font-medium">
                        {{ user ? `Selamat datang, ${user.name}!` : 'Selamat datang!' }}
                    </span>
                </div> -->

                <!-- Main Heading -->
                <div class="max-w-6xl mx-auto mb-8">
                    <h1 class="text-4xl lg:text-6xl font-light text-white leading-tight mb-6">
                        <span class="block font-bold bg-gradient-to-r from-primary via-white to-accent bg-clip-text text-transparent">
                            Continuous Improvement
                        </span>
                    </h1>
                    
                    <p class="text-lg md:text-xl lg:text-2xl text-white/80 font-light leading-relaxed max-w-4xl mx-auto">
                        The best teams don't aim to be perfect - they aim to be better than yesterday.
                    </p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mb-16 items-center sm:items-start">
                    <!-- Primary CTA -->
                    <a
                        href="#KontenSlider"
                        class="w-full sm:w-auto group inline-flex items-center justify-center gap-3 px-8 py-4 bg-secondary hover:bg-primary/90 text-white hover:text-black font-semibold rounded-2xl transition-all duration-300 hover:scale-105"
                    >
                        <Play class="w-5 h-5" />
                        <span>Mulai Jelajahi</span>
                        <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                    </a>
                    
                    <!-- Secondary CTA -->
                    <Link
                        href="/profil-perusahaan"
                        class="w-full sm:w-auto group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-semibold rounded-2xl border border-white/30 hover:border-white/50 transition-all duration-300"
                    >
                        <span>Tentang Kami</span>
                        <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>

                <!-- Scroll Indicator -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
                    <div class="flex flex-col items-center gap-2 text-white/60">
                        <span class="text-sm font-medium">Scroll untuk menjelajah</span>
                        <ChevronDown class="w-6 h-6 animate-bounce" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes zoomPan {
    0% {
        transform: scale(1) translate(0, 0);
    }
    100% {
        transform: scale(1.1) translate(-2%, -2%);
    }
}

.background-image {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-position: center;
    background-size: cover;
    transition: opacity 2s ease-in-out;
    z-index: 0;
    overflow: hidden;
}

.animate-zoomPan {
    animation: zoomPan 6s ease-in-out forwards;
}

/* Gradient text animation */
@keyframes gradient-shift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradient-shift 4s ease infinite;
}

/* Glass morphism effects */
.backdrop-blur-sm {
    backdrop-filter: blur(8px);
}

/* Smooth hover animations */
.group:hover {
    transform: translateY(-2px);
}
</style>
