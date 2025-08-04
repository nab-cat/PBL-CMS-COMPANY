<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'
// Import Lucide icons
import {
    Facebook,
    Instagram,
    Linkedin,
    Twitter,
    Youtube,
    Github,
    MessageCircle, 
    Send,
    Music
} from "lucide-vue-next";

// Data
const profil_perusahaan = ref(null)
const loading = ref(false)
const error = ref(null)
const mediaSosial = ref([])

// Text reveal animation refs
const isTextVisible = ref(false)
const observer = ref(null)

// Function to truncate HTML content while preserving tags
function truncateHtml(html, maxSentences) {
    if (!html) return '';
    
    // Split by sentences while preserving HTML tags
    const sentences = html.split(/(?<=[.!?])\s+/);
    
    if (sentences.length <= maxSentences) {
        return html;
    }
    
    return sentences.slice(0, maxSentences).join(' ') + '...';
}

// Function to strip HTML tags (keep this for checking length)
function stripHtml(html) {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// Jumlah kalimat untuk visi misi
const maxKalimat = 1;

// Visi - UPDATED to handle HTML properly
const truncatedVisi = computed(() => {
    if (!profil_perusahaan.value?.visi_perusahaan) return 'Visi perusahaan belum tersedia.';
    return truncateHtml(profil_perusahaan.value.visi_perusahaan, maxKalimat);
})

const showReadMoreVisi = computed(() => {
    if (!profil_perusahaan.value?.visi_perusahaan) return false;
    const cleanText = stripHtml(profil_perusahaan.value.visi_perusahaan);
    const sentences = cleanText.split(/(?<=[.!?])\s+/).filter(s => s.trim().length > 0);
    return sentences.length > maxKalimat;
})

// Misi - UPDATED to handle HTML properly  
const truncatedMisi = computed(() => {
    if (!profil_perusahaan.value?.misi_perusahaan) return 'Misi perusahaan belum tersedia.';
    return truncateHtml(profil_perusahaan.value.misi_perusahaan, maxKalimat);
})

const showReadMoreMisi = computed(() => {
    if (!profil_perusahaan.value?.misi_perusahaan) return false;
    const cleanText = stripHtml(profil_perusahaan.value.misi_perusahaan);
    const sentences = cleanText.split(/(?<=[.!?])\s+/).filter(s => s.trim().length > 0);
    return sentences.length > maxKalimat;
})

// Fetch data
async function fetchProfilPerusahaan() {
    try {
        loading.value = true
        const response = await axios.get(`/api/profil-perusahaan/`)
        profil_perusahaan.value = response.data.data
        loading.value = false
    } catch (err) {
        error.value = "Event not found or an error occurred"
        loading.value = false
    }
}

async function fetchMediaSosial() {
    try {
        const response = await axios.get('/api/media-sosial');
        mediaSosial.value = [];

        // Process the response format - sama seperti di footer
        for (const [platform, data] of Object.entries(response.data.data)) {
            if (data.active) {
                mediaSosial.value.push({
                    name: platform,
                    link: data.link
                });
            }
        }
    } catch (err) {
        console.error('Error fetching media sosial:', err);
    }
}

// Handle contact button click
const handleContactClick = () => {
    if (profil_perusahaan.value?.email_perusahaan) {
        window.location.href = `mailto:${profil_perusahaan.value.email_perusahaan}?subject=Inquiry - Company Profile`;
    } else {
        // Fallback to contact page if email not available
        window.location.href = '/kontak';
    }
}

// Utility
function getImageUrl(image) {
    if (!image) return ""
    if (Array.isArray(image)) {
        return image.length > 0 ? `/storage/${image[0]}` : ""
    }
    return `/storage/${image}`
}

// SLIDER TERPISAH
const topIndex = ref(0)
const bottomIndex = ref(0)

const thumbnailTop = computed(() => {
    return profil_perusahaan.value?.thumbnail_perusahaan?.slice(0, 2) || []
})
const thumbnailBottom = computed(() => {
    return profil_perusahaan.value?.thumbnail_perusahaan?.slice(2) || []
})

// Auto slide
let intervalTop = null
let intervalBottom = null

onMounted(() => {
    fetchProfilPerusahaan()
    fetchMediaSosial()

    intervalTop = setInterval(() => {
        if (thumbnailTop.value.length > 1) {
            topIndex.value = (topIndex.value + 1) % thumbnailTop.value.length
        }
    }, 4000)

    intervalBottom = setInterval(() => {
        if (thumbnailBottom.value.length > 1) {
            bottomIndex.value = (bottomIndex.value + 1) % thumbnailBottom.value.length
        }
    }, 4000)

    // Intersection Observer for text reveal - Modified untuk refresh berulang
    observer.value = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    // Reset dulu kemudian trigger animasi
                    isTextVisible.value = false
                    setTimeout(() => {
                        isTextVisible.value = true
                    }, 50)
                } else {
                    // Reset ketika keluar dari viewport
                    isTextVisible.value = false
                }
            })
        },
        { threshold: 0.3 }
    )

    // Observe the description section after component is mounted
    setTimeout(() => {
        const descSection = document.getElementById('company-description')
        if (descSection && observer.value) {
            observer.value.observe(descSection)
        }
    }, 100)
})

onUnmounted(() => {
    if (intervalTop) clearInterval(intervalTop)
    if (intervalBottom) clearInterval(intervalBottom)
    if (observer.value) {
        observer.value.disconnect()
    }
})

// Social media icons - sama seperti di footer
function getMediaSosialComponent(platform) {
    const iconMap = {
        'Facebook': Facebook,
        'Instagram': Instagram,
        'LinkedIn': Linkedin,
        'Twitter': Twitter,
        'YouTube': Youtube,
        'TikTok': Music,
        'WhatsApp Business': MessageCircle,
        'Telegram': Send,
        'GitHub': Github
    };

    return iconMap[platform] || null;
}
</script>

<template>
    <AppLayout>
        <!-- Extended Modern Hero Section -->
        <div class="relative w-full min-h-screen bg-secondary overflow-hidden font-custom">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0">
                <!-- Floating geometric shapes -->
                <div class="absolute top-20 left-10 w-32 h-32 bg-white/5 rounded-full animate-pulse"></div>
                <div class="absolute top-40 right-20 w-24 h-24 bg-white/10 rounded-lg rotate-45 animate-bounce"></div>
                <div class="absolute bottom-32 left-20 w-40 h-40 bg-primary/20 rounded-full animate-pulse"></div>
                <div class="absolute bottom-20 right-40 w-16 h-16 bg-white/15 rounded-full animate-ping"></div>

                <!-- Grid pattern overlay -->
                <div
                    class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:50px_50px]">
                </div>
            </div>

            <!-- Main Hero Content -->
            <div
                class="relative z-10 w-full max-w-screen-xl mx-auto px-4 sm:px-8 lg:px-16 py-20 min-h-screen flex items-center">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-20 w-full">

                    <!-- Left Content -->
                    <div class="flex-1 text-center lg:text-left space-y-8">
                        <!-- Greeting Badge -->
                        <div
                            class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-sm font-medium text-white">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                            Selamat datang
                        </div>

                        <!-- Main Heading -->
                        <div class="space-y-4">
                            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold text-white leading-tight">
                                <span class="block">Halo,</span>
                                <span
                                    class="block bg-gradient-to-r from-white to-white/80 bg-clip-text text-transparent">
                                    Kenalan Yuk!
                                </span>
                            </h1>
                            <p class="text-lg lg:text-xl text-white/80 font-light leading-relaxed max-w-2xl">
                                Mari berkenalan lebih dekat dengan
                                <span class="font-semibold text-white">
                                    {{ profil_perusahaan?.nama_perusahaan || 'Perusahaan Kami' }}
                                </span>
                                dan temukan cerita di balik inovasi yang kami ciptakan.
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col lg:flex-row gap-4 pt-4">
                            <a href="#company-description"
                                class="inline-flex items-center justify-center px-8 py-4 bg-white text-secondary font-semibold rounded-full hover:bg-black hover:text-white transition-all duration-300 shadow-lg">
                                <span>Jelajahi Cerita Kami</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </a>
                            <button
                                @click="handleContactClick"
                                class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 text-white font-semibold rounded-full hover:bg-white/10 hover:border-white/50 transition-all duration-300">
                                <span>Hubungi Kami</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Content - Logo Display -->
                    <div class="flex-1 flex justify-center lg:justify-end">
                        <div class="relative">
                            <!-- Logo Container with modern styling -->
                            <div class="relative group">
                                <!-- Animated rings -->
                                <div
                                    class="absolute inset-0 rounded-full bg-gradient-to-r from-white/20 to-primary/20 animate-spin [animation-duration:20s]">
                                </div>
                                <div
                                    class="absolute inset-2 rounded-full bg-gradient-to-l from-primary/30 to-white/10 animate-spin [animation-duration:15s] [animation-direction:reverse]">
                                </div>

                                <!-- Logo -->
                                <div
                                    class="relative bg-white/10 backdrop-blur-lg rounded-full p-8 border border-white/20 group-hover:scale-105 transition-all duration-500">
                                    <div v-if="profil_perusahaan?.logo_perusahaan" class="w-48 h-48 lg:w-64 lg:h-64">
                                        <img :src="getImageUrl(profil_perusahaan.logo_perusahaan)"
                                            :alt="profil_perusahaan.nama_perusahaan"
                                            class="w-full h-full object-contain drop-shadow-2xl" />
                                    </div>
                                    <div v-else class="w-48 h-48 lg:w-64 lg:h-64 flex items-center justify-center">
                                        <div class="text-6xl lg:text-8xl font-bold text-white/50">
                                            {{ profil_perusahaan?.nama_perusahaan?.charAt(0) || '?' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating greeting cards -->
                            <div
                                class="absolute -bottom-6 -left-6 bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">Hi!</div>
                                    <div class="text-xs text-white/70">Salam Hangat</div>
                                </div>
                            </div>

                            <div
                                class="absolute -top-6 -right-6 bg-primary/20 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">👋</div>
                                    <div class="text-xs text-white/70">Welcome</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="relative mt-8 flex justify-center">
                <div class="flex flex-col items-center space-y-2 animate-bounce">
                    <span class="text-white/60 text-sm">Scroll untuk melihat lebih</span>
                    <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>

            <!-- Company Description Section with Text Reveal -->
            <div id="company-description"
                class="relative z-10 w-full max-w-screen-xl mx-auto px-4 sm:px-8 lg:px-16 py-20 text-secondary">
                <div class="text-center space-y-8">
                    <!-- Section Badge -->
                    <div
                        class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-sm font-medium text-white">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-3 animate-pulse"></span>
                        Tentang Kami
                    </div>

                    <!-- Company Name with Gradient -->
                    <h2
                        class="text-3xl lg:text-5xl font-bold leading-tight bg-gradient-to-r from-white to-white/90 bg-clip-text text-white">
                        {{ profil_perusahaan?.nama_perusahaan || 'Perusahaan Kami' }}
                    </h2>

                    <!-- Text Reveal Animation -->
                    <div class="max-w-4xl mx-auto pb-12">
                        <div class="overflow-hidden text-lg lg:text-xl font-light leading-relaxed text-white">
                            <span v-for="(char, index) in profil_perusahaan?.deskripsi_perusahaan.split('')" :key="`${char}-${index}`"
                                :class="[
                                    'inline-block transition-all duration-500 ease-out',
                                    isTextVisible
                                        ? 'transform translate-y-0 opacity-100'
                                        : 'transform translate-y-8 opacity-0'
                                ]" :style="{
                                    transitionDelay: isTextVisible ? `${index * 0.02}s` : '0s'
                                }">
                                {{ char == ' ' ? '\u00A0' : char }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Visi & Misi Grid with Petal Design -->
        <div class="w-full px-4 sm:px-8 lg:px-16 py-20 bg-white text-white">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10 max-w-screen-xl mx-auto">
                <!-- Visi - top-left petal -->
                <div class="order-1 flex flex-col justify-center items-start lg:items-end gap-4 bg-secondary py-8 px-10 
                     rounded-lg lg:rounded-tl-[100px] lg:rounded-bl-[100px] lg:rounded-tr-[100px] lg:rounded-br-[20px]
                     transform hover:translate-y-[-5px] transition-all duration-300">
                    <h3 class="text-2xl lg:text-4xl font-semibold font-custom lg:text-right">
                        Visi Kami
                    </h3>
                    <div class="text-base lg:text-lg font-normal font-custom leading-relaxed lg:text-right">
                        <!-- Use v-html to render HTML content -->
                        <div v-html="truncatedVisi" class="prose prose-lg prose-invert max-w-none lg:text-right"></div>
                        <Link v-if="showReadMoreVisi" href="/visi-misi"
                            class="text-blue-400 cursor-pointer hover:underline mt-2 inline-block">
                        ... Baca selengkapnya
                        </Link>
                    </div>
                </div>

                <!-- Gambar atas - top-right petal (Slider 1) -->
                <div class="order-2 overflow-hidden rounded-lg lg:rounded-tr-[100px] lg:rounded-tl-[100px] lg:rounded-br-[100px] lg:rounded-bl-[20px] h-96">
                    <div v-if="thumbnailTop.length" class="flex h-full transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${topIndex * 100}%)` }">
                        <div v-for="(img, i) in thumbnailTop" :key="'top-slide-' + i"
                            class="w-full h-full flex-shrink-0">
                            <img :src="getImageUrl(img)" class="w-full h-full object-cover" />
                        </div>
                    </div>
                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-600">
                        Gambar belum tersedia.
                    </div>
                </div>

                <!-- Gambar bawah - bottom-left petal (Slider 2) -->
                <div class="order-3 overflow-hidden rounded-lg lg:rounded-bl-[100px] lg:rounded-tl-[100px] lg:rounded-br-[100px] lg:rounded-tr-[20px] h-96">
                    <div v-if="thumbnailBottom.length" class="flex h-full transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${bottomIndex * 100}%)` }">
                        <div v-for="(img, i) in thumbnailBottom" :key="'bottom-slide-' + i"
                            class="w-full h-full flex-shrink-0">
                            <img :src="getImageUrl(img)" class="w-full h-full object-cover" />
                        </div>
                    </div>
                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-600">
                        Gambar belum tersedia.
                    </div>
                </div>

                <!-- Misi - bottom-right petal -->
                <div class="order-4 flex flex-col justify-center items-start gap-4 bg-secondary py-8 px-10
                     rounded-lg lg:rounded-br-[100px] lg:rounded-tr-[100px] lg:rounded-bl-[100px] lg:rounded-tl-[20px]
                     transform hover:translate-y-[-5px] transition-all duration-300">
                    <h3 class="text-2xl lg:text-4xl font-semibold font-custom">Misi Kami</h3>
                    <div class="text-base lg:text-lg font-normal font-custom leading-relaxed">
                        <!-- Use v-html to render HTML content -->
                        <div v-html="truncatedMisi" class="prose prose-lg prose-invert max-w-none"></div>
                        <Link v-if="showReadMoreMisi" href="/visi-misi"
                            class="text-blue-400 cursor-pointer hover:underline mt-2 inline-block">
                        ... Baca selengkapnya
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Section -->
        <div class="w-full px-4 sm:px-8 lg:px-16 py-16 bg-secondary text-white relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-bl-full"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-tr-full"></div>

            <div class="max-w-screen-xl mx-auto flex flex-col justify-center items-center gap-10 relative z-10">
                <!-- Modern heading with accent line -->
                <div class="text-center">
                    <h2 class="text-2xl lg:text-4xl font-semibold font-custom">
                        Ikuti Kami di Media Sosial
                    </h2>
                    <div class="w-16 h-1 bg-primary mx-auto mt-3"></div>
                </div>

                <!-- Social media icons -->
                <div v-if="mediaSosial.length > 0" class="flex flex-wrap justify-center gap-6">
                    <a v-for="(platform, index) in mediaSosial" :key="index" :href="platform.link" target="_blank"
                        rel="noopener noreferrer" class="group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 border border-white/20 
                               hover:bg-white hover:text-secondary hover:border-primary transition-all duration-300"
                        :title="platform.name">
                        <component :is="getMediaSosialComponent(platform.name)"
                            class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" />
                    </a>
                </div>

                <!-- Call to action text -->
                <p class="text-sm lg:text-base font-normal font-custom leading-relaxed text-center max-w-2xl px-4 
                          bg-white/5 backdrop-blur-sm py-4 rounded-xl border border-white/10">
                    Bergabunglah dengan kami dan dapatkan update terbaru tentang perusahaan, produk, dan penawaran
                    menarik lainnya.
                </p>
            </div>
        </div>

    </AppLayout>
</template>

<style scoped>
/* Add smooth scrolling for anchor links */
html {
    scroll-behavior: smooth;
}

/* Custom animations */
@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Ensure HTML content in description renders properly */
:deep(.prose) {
    max-width: none;
    color: white;
}

:deep(.prose p) {
    @apply mb-3 leading-relaxed text-white;
}

:deep(.prose strong) {
    @apply font-semibold text-white;
}

:deep(.prose em) {
    @apply italic text-white;
}

:deep(.prose ul) {
    @apply list-disc ml-4 mb-3 text-white;
}

:deep(.prose ol) {
    @apply list-decimal ml-4 mb-3 text-white;
}

:deep(.prose li) {
    @apply mb-1 text-white;
}

:deep(.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6) {
    @apply text-white font-semibold mb-2;
}

:deep(.prose blockquote) {
    @apply border-l-4 border-white/30 pl-4 italic my-3 text-white/90;
}

/* Ensure overflow protection for petal design */
.order-1, .order-4 {
    overflow: hidden;
}

.order-1 .prose,
.order-4 .prose {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 6; /* Limit lines to protect petal design */
}
</style>
