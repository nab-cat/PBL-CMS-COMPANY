<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { nextTick } from 'vue';
import { Calendar, ArrowRight } from 'lucide-vue-next';

const events = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(() => {
    fetchEvent();
    AOS.init({
        duration: 1000,
        once: false,
    });
});

async function fetchEvent() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/event`);
        const allEvent = response.data.data;
        events.value = getRandomEvent(allEvent, 3);
    } catch (err) {
        error.value = "Event not found or an error occurred";
    } finally {
        loading.value = false;
        // Refresh AOS to ensure animations work after data change
        await nextTick();
        AOS.refresh();  
    }
}

function getRandomEvent(array, count) {
    const shuffled = [...array].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

// Function to format date
function formatDate(dateString) {
    if (!dateString) return { day: '--', month: 'N/A' };
    
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = date.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase();
    
    return { day, month };
}

// Function to strip HTML tags
function stripHtml(html) {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}
</script>

<template>
    <div
        class="w-full px-6 lg:px-16 py-28 bg-secondary text-white flex flex-col items-center overflow-hidden font-custom">
        <div class="w-full max-w-screen-xl mx-auto flex flex-col lg:flex-row items-center lg:items-start gap-10">

            <!-- Left Side: Header dan Panah -->
            <div class="w-full lg:w-1/4 flex flex-col items-center lg:items-start text-center lg:text-left gap-4" data-aos="fade-right">
                <div class="text-Color-Scheme-1-Text text-base font-semibold">
                    Coba lihat!
                </div>
                <h2 class="text-4xl lg:text-6xl font-custom font-thin lg:font-normal text-white">
                    Event Akan Datang
                </h2>
                <div class="relative w-full h-10 overflow-hidden hidden lg:block">
                    <img src="image/arrow-right.png" alt="Panah"
                        class="absolute top-0 left-0 w-full h-10 animate-panah-berulang" />
                </div>
            </div>

            <!-- Right Side: Event Cards -->
            <div class="w-full lg:w-3/4 flex flex-col lg:flex-row lg:flex-nowrap gap-8" data-aos="fade-down">
                <div v-for="event in events" :key="event.slug"
                    class="relative group rounded-2xl overflow-hidden w-full lg:w-1/3">

                    <!-- Gambar Event -->
                    <img :src="getImageUrl(event.thumbnail_event)" alt=""
                        class="w-full h-72 lg:h-96 object-cover rounded-2xl transition-transform duration-500 group-hover:scale-105" />

                    <!-- White Date Block (Top Right) -->
                    <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm rounded-lg p-3 shadow-lg border border-white/20">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-800">
                                {{ formatDate(event.waktu_start_event).day }}
                            </div>
                            <div class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                {{ formatDate(event.waktu_start_event).month }}
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Modern Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out" />
                    
                    <!-- Modern Content Container -->
                    <div class="absolute inset-0 flex flex-col justify-end p-6 opacity-0 group-hover:opacity-100 transform translate-y-6 group-hover:translate-y-0 transition-all duration-500 ease-out">
                        <!-- Category/Tag -->
                        <div class="mb-3">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium text-white/90 tracking-wide">
                                <Calendar class="w-3 h-3" />
                                Event
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-xl lg:text-2xl font-bold text-white mb-3 leading-tight">
                            {{ event.nama_event }}
                        </h3>

                        <!-- Description (if available) -->
                        <p v-if="event.deskripsi_event" class="text-white/80 text-sm leading-relaxed mb-4 line-clamp-2">
                            {{ stripHtml(event.deskripsi_event) }}
                        </p>

                        <!-- Modern CTA Button -->
                        <a :href="`/event/${event.slug}`"
                           class="inline-flex items-center gap-2 text-white font-medium text-sm group/btn hover:gap-3 transition-all duration-300">
                            <span>Lihat Detail</span>
                            <div class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover/btn:bg-white/30 transition-all duration-300">
                                <ArrowRight class="w-3 h-3" />
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes panah-berulang {
    0% {
        opacity: 1;
        transform: translateX(-300px);
    }

    15% {
        opacity: 1;
        transform: translateX(0);
    }

    25% {
        opacity: 1;
        transform: translateX(0);
    }

    85% {
        opacity: 1;
        transform: translateX(0px);
    }

    100% {
        opacity: 1;
        transform: translateX(500px);
    }
}

.animate-panah-berulang {
    animation: panah-berulang 3s ease-in-out infinite;
}
</style>