<template>
    <div class="relative font-custom antialiased">
        <main class="relative flex flex-col justify-center bg-secondary overflow-hidden">
            <div class="w-full max-w-7xl mx-auto px-4 md:px-6 py-32">

                <!-- Header Section -->
                <div class="text-center mb-20" data-aos="zoom-in-up">
                    <div class="inline-flex items-center px-4 py-2 bg-primary/10 rounded-full mb-6">
                        <span class="text-primary text-sm font-medium tracking-wide uppercase">Partnership</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                        Ingin bermitra bersama kami?
                    </h2>
                    <p class="text-white text-xl max-w-3xl mx-auto leading-relaxed">
                        Bergabunglah dengan ekosistem mitra terpercaya yang telah berkembang bersama kami
                    </p>
                </div>

                <!-- Mitra Grid -->
                <div class="mb-16">
                    <!-- Grid Container with better spacing -->
                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6" data-aos="zoom-in-up">
                        <!-- Existing Mitra -->
                        <div v-for="mitra in mitraList" :key="mitra.nama"
                            class="group relative aspect-square flex items-center justify-center p-4 md:p-6 bg-white/3 rounded-3xl border border-white/5 hover:bg-white/8 hover:border-white/15 transition-all duration-500 ease-out">
                            <!-- Animated background -->
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-primary/5 to-accent/5 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                            </div>

                            <img :src="getImageUrl(mitra.logo)" :alt="mitra.nama" :title="mitra.nama"
                                class="relative h-8 md:h-10 w-auto max-w-full object-contain filter brightness-75 contrast-125 group-hover:brightness-100 group-hover:contrast-100 transition-all duration-500"
                                @error="handleImageError" />
                        </div>

                        <!-- Call to Action Card -->
                        <div
                            class="group relative aspect-square flex flex-col items-center justify-center p-4 md:p-6 bg-gradient-to-br from-primary/8 to-accent/8 rounded-3xl border-2 border-dashed border-primary/30 hover:border-primary/50 transition-all duration-500 cursor-pointer overflow-hidden" 
                            data-aos="zoom-in-up"
                            @click="handleContactClick">
                            <!-- Animated background pattern -->
                            <div
                                class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                                <div
                                    class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-primary/20 to-transparent">
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 w-full h-full bg-gradient-to-tl from-accent/20 to-transparent">
                                </div>
                            </div>

                            <div class="relative text-center">
                                <div
                                    class="w-8 h-8 md:w-10 md:h-10 mx-auto mb-2 flex items-center justify-center rounded-2xl bg-primary/15 group-hover:bg-primary/25 transition-all duration-500 group-hover:scale-110">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <p
                                    class="text-xs md:text-sm font-medium text-white/70 group-hover:text-white/90 transition-colors duration-500">
                                    Logo Anda di sini?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Bottom CTA Section -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-full max-w-2xl mx-auto">
                        <!-- Background with subtle animation -->
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary/10 via-accent/10 to-primary/10 rounded-3xl blur-xl animate-pulse">
                        </div>

                        <div
                            class="relative flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-6 px-6 py-6 sm:px-8 sm:py-4 bg-white/5 backdrop-blur-sm rounded-3xl border border-white/10 hover:border-white/20 transition-all duration-500 w-full">
                            <!-- Text Section -->
                            <div class="flex items-center justify-center sm:justify-start gap-3 flex-1">
                                <div class="w-2 h-2 bg-primary rounded-full animate-pulse flex-shrink-0"></div>
                                <span
                                    class="text-white/80 text-base sm:text-lg font-medium leading-relaxed text-center sm:text-left">
                                    Tertarik untuk bermitra dengan kami?
                                </span>
                            </div>

                            <!-- Button Section -->
                            <div class="w-full sm:w-auto flex-shrink-0">
                                <button
                                    @click="handleContactClick"
                                    class="group/btn relative w-full sm:w-auto px-8 py-3 bg-white text-secondary hover:bg-secondary hover:text-white font-semibold rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95">
                                    <span class="relative z-10">Hubungi Kami</span>
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r rounded-2xl opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300">
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AOS from 'aos'
import 'aos/dist/aos.css'

const mitraList = ref([])
const isLoading = ref(true)
const companyProfile = ref(null)

// Fetch mitra data from API
const fetchMitraData = async () => {
    try {
        const response = await axios.get('/api/mitra')
        mitraList.value = response.data.data || response.data
        isLoading.value = false
    } catch (error) {
      
        isLoading.value = false
        // Fallback to default logos if API fails
        mitraList.value = [
            { nama: 'Facebook', logo: 'facebook.svg' },
            { nama: 'Disney', logo: 'disney.svg' },
            { nama: 'Airbnb', logo: 'airbnb.svg' },
            { nama: 'Apple', logo: 'apple.svg' },
            { nama: 'Spark', logo: 'spark.svg' },
            { nama: 'Samsung', logo: 'samsung.svg' },
            { nama: 'Quora', logo: 'quora.svg' },
            { nama: 'Sass', logo: 'sass.svg' }
        ]
    }
}

// Fetch company profile
const fetchCompanyProfile = async () => {
    try {
        const response = await axios.get("/api/profil-perusahaan");
        companyProfile.value = response.data.data;
    } catch (error) {
   
        companyProfile.value = null;
    }
}

// Handle image loading errors
const handleImageError = (event) => {
    event.target.src = '/image/placeholder.webp'
}

// Function to get proper image URL
function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }

    return `/storage/${image}`;
}

// Handle contact button click
const handleContactClick = () => {
    if (companyProfile.value?.email_perusahaan) {
        window.location.href = `mailto:${companyProfile.value.email_perusahaan}?subject=Inquiry - Partnership Collaboration`;
    } else {
        // Fallback to contact page if email not available
        window.location.href = '/kontak';
    }
}

onMounted(async () => {
    await fetchMitraData()
    await fetchCompanyProfile()
    AOS.init({
        duration: 1000,
        once: false,
    });
    AOS.refresh();
})
</script>

<style scoped>
/* Enhanced hover effects */
.group:hover {
    transform: translateY(-1px);
}

/* Smooth animations with better easing */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom animation for pulsing elements */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Subtle background pattern animation */
@keyframes float {

    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }

    50% {
        transform: translateY(-10px) rotate(180deg);
    }
}
</style>