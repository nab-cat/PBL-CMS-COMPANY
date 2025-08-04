<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import axios from "axios";

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

<style scoped>
@keyframes zoomPan {
    0% {
        transform: scale(1) translate(0, 0);
    }

    100% {
        transform: scale(3.25) translate(0, 0);
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
    animation: zoomPan 40s ease-in-out forwards;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10;
}

.content {
    position: relative;
    z-index: 20;
}
</style>

<template>
    <div class="relative w-full h-full overflow-hidden">
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

        <div
            class="absolute inset-0 bg-black bg-opacity-50 z-10 pointer-events-none"
        ></div>

        <div class="relative z-20">
            <div
                class="w-full min-h-screen bg-black/50 px-6 py-16 lg:px-8 flex flex-col lg:flex-row items-start lg:items-center justify-center lg:justify-start font-custom"
            >
                <div
                    class="w-full lg:w-[560px] flex flex-col justify-start items-start gap-8"
                >
                    <div class="flex flex-col gap-6">
                        <h1
                            class="text-white text-4xl lg:text-6xl leading-[48px] lg:leading-[67.2px] font-normal"
                        >
                            Selamat datang {{ user?.name ?? "pengunjung" }}!
                        </h1>
                        <p
                            class="text-white text-base lg:text-lg leading-normal lg:leading-relaxed font-normal"
                        >
                            Temukan informasi terkini seputar produk, event,
                            lowongan kerja, hingga kisah menarik lewat artikel
                            dan galeri kami.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <a
                            href="#KontenSlider"
                            class="px-6 py-2.5 bg-white text-black font-medium text-base rounded-full shadow hover:opacity-90 transition inline-block text-center"
                        >
                            Mulai Sekarang
                        </a>
                        <Link
                            href="/profil-perusahaan"
                            class="px-6 py-2.5 bg-white/10 text-white font-medium text-base rounded-full border border-transparent hover:bg-white/20 transition inline-block text-center"
                        >
                            Profil Kami
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
