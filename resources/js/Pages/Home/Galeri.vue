<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import AOS from 'aos'
import 'aos/dist/aos.css'
import { ChevronRight, Image } from 'lucide-vue-next'

const galleries = ref([])

const leftColumn = ref([])
const rightColumn = ref([])

const currentIndexLeftTop = ref(0)
const currentIndexLeftBottom = ref(0)
const currentIndexRightTop = ref(0)
const currentIndexRightBottom = ref(0)

let intervalLeftTop, intervalLeftBottom, intervalRightTop, intervalRightBottom

const isLoaded = ref(false)

// Function to strip HTML tags from description
function stripHtml(html) {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

const fetchGaleri = async () => {
    try {
        const response = await axios.get('/api/galeri')
        galleries.value = response.data.data || []

        if (galleries.value.length === 0) {
            const placeholders = [
                {
                    id_galeri: 'placeholder-1',
                    judul_galeri: 'Galeri akan segera hadir',
                    deskripsi_galeri: 'Tim kami sedang mempersiapkan konten galeri yang menarik untuk Anda.',
                    thumbnail_galeri: null,
                    slug: '#'
                },
                {
                    id_galeri: 'placeholder-2',
                    judul_galeri: 'Galeri akan segera hadir',
                    deskripsi_galeri: 'Tim kami sedang mempersiapkan konten galeri yang menarik untuk Anda.',
                    thumbnail_galeri: null,
                    slug: '#'
                }
            ]
            galleries.value = placeholders
        }

        leftColumn.value = galleries.value.filter((_, i) => i % 2 === 0)
        rightColumn.value = galleries.value.filter((_, i) => i % 2 !== 0)

        // Ensure at least one item in each column
        if (leftColumn.value.length === 0) leftColumn.value = [galleries.value[0]]
        if (rightColumn.value.length === 0) rightColumn.value = [galleries.value[0]]

        isLoaded.value = true
    } catch (err) {
        // Add fallback on error
        handleEmptyGallery()
    }
}

function handleEmptyGallery() {
    const placeholders = [
        {
            id_galeri: 'error-1',
            judul_galeri: 'Konten galeri tidak tersedia',
            deskripsi_galeri: 'Silakan coba lagi nanti.',
            thumbnail_galeri: null,
            slug: '#'
        },
        {
            id_galeri: 'error-2',
            judul_galeri: 'Konten galeri tidak tersedia',
            deskripsi_galeri: 'Silakan coba lagi nanti.',
            thumbnail_galeri: null,
            slug: '#'
        }
    ]
    galleries.value = placeholders
    leftColumn.value = [placeholders[0]]
    rightColumn.value = [placeholders[1]]
    isLoaded.value = true
}

onMounted(() => {
    fetchGaleri()
    AOS.init({
        duration: 1000,
        once: false
    })

    intervalLeftTop = setInterval(() => {
        currentIndexLeftTop.value = (currentIndexLeftTop.value + 1) % (leftColumn.value.length || 1)
    }, 4000)

    intervalLeftBottom = setInterval(() => {
        currentIndexLeftBottom.value = (currentIndexLeftBottom.value + 1) % (leftColumn.value.length || 1)
    }, 5000)

    intervalRightTop = setInterval(() => {
        currentIndexRightTop.value = (currentIndexRightTop.value + 1) % (rightColumn.value.length || 1)
    }, 6000)

    intervalRightBottom = setInterval(() => {
        currentIndexRightBottom.value = (currentIndexRightBottom.value + 1) % (rightColumn.value.length || 1)
    }, 7000)
})

onBeforeUnmount(() => {
    clearInterval(intervalLeftTop)
    clearInterval(intervalLeftBottom)
    clearInterval(intervalRightTop)
    clearInterval(intervalRightBottom)
})

function getImage(image) {
    if (!image) {
        return "/image/placeholder.webp"
    }
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp"
    }
    return `/storage/${image}`
}
</script>

<template>
    <div
        class="w-full max-w-[1440px] mx-auto px-4 md:px-10 lg:px-16 py-28 flex flex-col items-center gap-20 overflow-x-hidden font-custom">

        <!-- Judul -->
        <div class="w-full max-w-[768px] flex flex-col items-center gap-6 text-center" data-aos="fade-up">
            <h2 class="text-Color-Scheme-1-Text text-4xl lg:text-5xl font-normal leading-tight">Cerita dalam galeri</h2>
            <p class="text-Color-Scheme-1-Text text-base lg:text-lg leading-relaxed">Setiap foto punya cerita. Yuk,
                lihat keseruan dan kebersamaan tim kami dari balik lensa.</p>
        </div>

        <!-- Kontainer Galeri -->
        <div class="flex flex-col lg:flex-row gap-8 justify-center w-full" data-aos="zoom-in">

            <!-- Kolom Kiri -->
            <div class="flex flex-col gap-8 items-center lg:items-start w-full lg:w-1/2">

                <!-- Kiri Atas -->
                <div class="relative w-full aspect-[1/1] overflow-hidden rounded-2xl group">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${currentIndexLeftTop * 100}%)` }">

                        <div v-for="(item, index) in leftColumn" :key="'left1-' + index"
                            class="w-full aspect-[1/1] flex-shrink-0 bg-cover bg-center relative"
                            :style="{ backgroundImage: `url(${getImage(item.thumbnail_galeri)})` }">

                            <!-- Enhanced Modern Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out" />

                            <!-- Modern Content Container -->
                            <div
                                class="absolute inset-0 flex flex-col justify-end p-6 opacity-0 group-hover:opacity-100 transform translate-y-6 group-hover:translate-y-0 transition-all duration-500 ease-out">
                                <!-- Category/Tag -->
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium text-white/90 tracking-wide">
                                        <Image class="w-3 h-3" />
                                        Galeri
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-3 leading-tight">
                                    {{ item.judul_galeri }}
                                </h3>

                                <!-- Description -->
                                <p class="text-white/80 text-sm lg:text-base leading-relaxed mb-4 line-clamp-2">
                                    {{ stripHtml(item.deskripsi_galeri) }}
                                </p>

                                <!-- Modern CTA Button -->
                                <a :href="`/galeri/${item.slug}`"
                                    class="inline-flex items-center gap-2 text-white font-medium text-sm group/btn hover:gap-3 transition-all duration-300">
                                    <span>Lihat Detail</span>
                                    <div
                                        class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover/btn:bg-white/30 transition-all duration-300">
                                        <ChevronRight class="w-3 h-3" />
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kiri Bawah -->
                <div class="relative w-full aspect-[5/3] overflow-hidden rounded-2xl group">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${currentIndexLeftBottom * 100}%)` }">

                        <div v-for="(item, index) in leftColumn" :key="'left2-' + index"
                            class="w-full aspect-[5/3] flex-shrink-0 bg-cover bg-center relative"
                            :style="{ backgroundImage: `url(${getImage(item.thumbnail_galeri)})` }">

                            <!-- Enhanced Modern Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out" />

                            <!-- Modern Content Container -->
                            <div
                                class="absolute inset-0 flex flex-col justify-end p-6 opacity-0 group-hover:opacity-100 transform translate-y-6 group-hover:translate-y-0 transition-all duration-500 ease-out">
                                <!-- Category/Tag -->
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium text-white/90 tracking-wide">
                                        <Image class="w-3 h-3" />
                                        Galeri
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-3 leading-tight">
                                    {{ item.judul_galeri }}
                                </h3>

                                <!-- Description -->
                                <p class="text-white/80 text-sm lg:text-base leading-relaxed mb-4 line-clamp-2">
                                    {{ stripHtml(item.deskripsi_galeri) }}
                                </p>

                                <!-- Modern CTA Button -->
                                <a :href="`/galeri/${item.slug}`"
                                    class="inline-flex items-center gap-2 text-white font-medium text-sm group/btn hover:gap-3 transition-all duration-300">
                                    <span>Lihat Detail</span>
                                    <div
                                        class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover/btn:bg-white/30 transition-all duration-300">
                                        <ChevronRight class="w-3 h-3" />
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="flex flex-col gap-8 items-center lg:items-start w-full lg:w-1/2">

                <!-- Kanan Atas -->
                <div class="relative w-full aspect-[5/3] overflow-hidden rounded-2xl group">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${currentIndexRightTop * 100}%)` }">

                        <div v-for="(item, index) in rightColumn" :key="'right1-' + index"
                            class="w-full aspect-[5/3] flex-shrink-0 bg-cover bg-center relative"
                            :style="{ backgroundImage: `url(${getImage(item.thumbnail_galeri)})` }">

                            <!-- Enhanced Modern Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out" />

                            <!-- Modern Content Container -->
                            <div
                                class="absolute inset-0 flex flex-col justify-end p-6 opacity-0 group-hover:opacity-100 transform translate-y-6 group-hover:translate-y-0 transition-all duration-500 ease-out">
                                <!-- Category/Tag -->
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium text-white/90 tracking-wide">
                                        <Image class="w-3 h-3" />
                                        Galeri
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-3 leading-tight">
                                    {{ item.judul_galeri }}
                                </h3>

                                <!-- Description -->
                                <p class="text-white/80 text-sm lg:text-base leading-relaxed mb-4 line-clamp-2">
                                    {{ stripHtml(item.deskripsi_galeri) }}
                                </p>

                                <!-- Modern CTA Button -->
                                <a :href="`/galeri/${item.slug}`"
                                    class="inline-flex items-center gap-2 text-white font-medium text-sm group/btn hover:gap-3 transition-all duration-300">
                                    <span>Lihat Detail</span>
                                    <div
                                        class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover/btn:bg-white/30 transition-all duration-300">
                                        <ChevronRight class="w-3 h-3" />
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kanan Bawah -->
                <div class="relative w-full aspect-[1/1] overflow-hidden rounded-2xl group">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${currentIndexRightBottom * 100}%)` }">

                        <div v-for="(item, index) in rightColumn" :key="'right2-' + index"
                            class="w-full aspect-[1/1] flex-shrink-0 bg-cover bg-center relative"
                            :style="{ backgroundImage: `url(${getImage(item.thumbnail_galeri)})` }">

                            <!-- Enhanced Modern Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out" />

                            <!-- Modern Content Container -->
                            <div
                                class="absolute inset-0 flex flex-col justify-end p-6 opacity-0 group-hover:opacity-100 transform translate-y-6 group-hover:translate-y-0 transition-all duration-500 ease-out">
                                <!-- Category/Tag -->
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium text-white/90 tracking-wide">
                                        <Image class="w-3 h-3" />
                                        Galeri
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-3 leading-tight">
                                    {{ item.judul_galeri }}
                                </h3>

                                <!-- Description -->
                                <p class="text-white/80 text-sm lg:text-base leading-relaxed mb-4 line-clamp-2">
                                    {{ stripHtml(item.deskripsi_galeri) }}
                                </p>

                                <!-- Modern CTA Button -->
                                <a :href="`/galeri/${item.slug}`"
                                    class="inline-flex items-center gap-2 text-white font-medium text-sm group/btn hover:gap-3 transition-all duration-300">
                                    <span>Lihat Detail</span>
                                    <div
                                        class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover/btn:bg-white/30 transition-all duration-300">
                                        <ChevronRight class="w-3 h-3" />
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
