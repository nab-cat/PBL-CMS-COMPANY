<template>
    <div class="relative font-custom antialiased">
        <main class="relative flex flex-col justify-center bg-secondary overflow-hidden">
            <div class="w-full max-w-7xl mx-auto px-4 md:px-6 py-32">

                <!-- Header Section -->
                <div class="text-center mb-24" data-aos="fade-up">
                    <div class="inline-flex items-center px-6 py-3 bg-white/10 rounded-full mb-8 border border-white/20">
                        <span class="text-white text-sm font-semibold tracking-wider uppercase">Mitra Kami</span>
                    </div>
                    <h2 class="text-5xl md:text-6xl lg:text-7xl font-light text-white mb-8 leading-tight">
                        Mitra <span class="font-bold text-primary">Terpercaya</span>
                    </h2>
                    <p class="text-white/70 text-xl max-w-4xl mx-auto leading-relaxed font-light">
                        Bergabung dengan ekosistem mitra yang telah mempercayai kami dalam perjalanan transformasi digital mereka
                    </p>
                </div>

                <!-- Mitra Grid -->
                <div class="mb-20">
                    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12 lg:gap-16" data-aos="zoom-in-up">
                        <!-- Mitra Logos -->
                        <div v-for="(mitra, index) in mitraList" :key="mitra.nama"
                            class="relative flex items-center justify-center p-8 md:p-10 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 hover:border-white/30 transition-colors duration-300">
                            
                            <!-- Logo container -->
                            <div class="w-24 h-24 md:w-32 md:h-32 lg:w-40 lg:h-40 flex items-center justify-center">
                                <img :src="getImageUrl(mitra.logo)" 
                                     :alt="mitra.nama" 
                                     :title="mitra.nama"
                                     class="max-w-full max-h-full object-contain filter brightness-90 contrast-110"
                                     @error="handleImageError" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center" data-aos="fade-up">
                    <div class="bg-white/5 rounded-3xl border border-white/20 p-8 md:p-10 max-w-3xl mx-auto">
                        <!-- Content -->
                        <div class="flex flex-col items-center gap-6">
                            <!-- Icon -->
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>

                            <!-- Text -->
                            <div class="text-center space-y-4">
                                <h3 class="text-2xl md:text-3xl font-bold text-white">
                                    Ingin Bergabung?
                                </h3>
                                <p class="text-white/70 text-lg leading-relaxed max-w-lg">
                                    Mari berkolaborasi dan ciptakan solusi inovatif bersama kami
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <button
                                @click="handleContactClick"
                                class="inline-flex items-center gap-3 px-8 py-4 hover:bg-black hover:text-white font-semibold rounded-2xl bg-white text-black transition-colors duration-300">
                                <span>Mulai Kemitraan</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </button>
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
        console.error('Error fetching mitra data:', error)
        isLoading.value = false
        // Fallback to default logos if API fails
        mitraList.value = [
            { nama: 'Facebook', logo: 'facebook.svg' },
            { nama: 'Disney', logo: 'disney.svg' },
            { nama: 'Airbnb', logo: 'airbnb.svg' },
            { nama: 'Apple', logo: 'apple.svg' },
            { nama: 'Spark', logo: 'spark.svg' },
            { nama: 'Samsung', logo: 'samsung.svg' }
        ]
    }
}

// Fetch company profile
const fetchCompanyProfile = async () => {
    try {
        const response = await axios.get("/api/profil-perusahaan");
        companyProfile.value = response.data.data;
    } catch (error) {
        console.error("Error fetching company profile:", error);
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
/* Clean, minimal styling with no animations */
* {
    transition-property: color, background-color, border-color;
    transition-timing-function: ease;
    transition-duration: 300ms;
}
</style>