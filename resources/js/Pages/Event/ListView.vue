<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
import { ref, onMounted } from "vue";
import { MapPin, Clock, Search, UserCheck2Icon } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

const events = ref([]);
const featuredEvent = ref(null);
const loading = ref(true);
const searchQuery = ref('');
const searching = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
let debounceTimer = null;

onMounted(() => {
    fetchEvents();

    // Set a timeout to ensure skeleton animation is shown for at least 1.5 seconds
    setTimeout(() => {
        loading.value = false;
    }, 1500);
});

// Handle search with debounce
const handleSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        currentPage.value = 1;
        fetchEvents();
    }, 500);
};

// Go to specific page
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    currentPage.value = page;
    fetchEvents();
};

// Ambil semua event
async function fetchEvents() {
    try {
        searching.value = true;

        // Determine which API endpoint to use based on if we're searching
        const url = searchQuery.value ? "/api/event/search" : "/api/event";
        const params = { page: currentPage.value };
        if (searchQuery.value) params.query = searchQuery.value;

        const [eventsResponse, newestResponse] = await Promise.all([
            axios.get(url, { params }),
            axios.get("/api/event/newest"),
        ]);

        events.value = eventsResponse.data.data;
        featuredEvent.value = newestResponse.data.data;

        // Set pagination info
        currentPage.value = eventsResponse.data.meta?.current_page || 1;
        lastPage.value = eventsResponse.data.meta?.last_page || 1;

        // Filter featured from list if we have a featured event
        if (featuredEvent.value) {
            events.value = events.value.filter(e => e.id_event !== featuredEvent.value?.id_event);
        }

    } catch (error) {
        events.value = [];
    } finally {
        searching.value = false;
    }
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

function formatDate(date) {
    if (!date) return "";
    const d = new Date(date);
    return d.toLocaleDateString("id-ID", { year: "numeric", month: "long", day: "numeric" });
}

function getDay(date) {
    return new Date(date).toLocaleDateString("id-ID", { weekday: "short" });
}

function getDate(date) {
    return new Date(date).getDate();
}

function getMonthYear(date) {
    return new Date(date).toLocaleDateString("id-ID", { month: "short", year: "numeric" });
}

function formatTimeRange(start, end) {
    const startTime = new Date(start);
    const endTime = new Date(end);

    const startStr = startTime.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
    });

    const endStr = endTime.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
    });

    return `${startStr} - ${endStr}`;
}

function stripHtmlTags(html) {
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
}
</script>

<template>
    <AppLayout>
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-16 py-28 bg-background text-black font-custom">
            <div class="flex flex-col gap-20 overflow-hidden">
                <!-- Header -->
                <div class="w-full mb-12 flex flex-col gap-4">
                    <div class="text-secondary text-base font-semibold font-custom uppercase tracking-wider">
                        Event
                    </div>
                    <h2 class="text-3xl lg:text-5xl font-normal font-custom leading-tight">
                        Temukan Berbagai Event Menarik
                    </h2>
                    <p class="text-lg font-normal leading-relaxed text-gray-600 max-w-2xl">
                        Coba ikuti berbagai event-event menarik yang kami selenggarakan
                    </p>
                </div>

                <!-- Featured Event - Skeleton Loading -->
                <div v-if="loading" class="flex flex-col gap-8">
                    <h2 class="text-4xl font-normal">Event Terbaru</h2>
                    <div class="flex flex-col lg:flex-row gap-12">
                        <!-- Image skeleton -->
                        <div class="relative flex-1 animate-pulse">
                            <div class="w-full h-96 bg-gray-200 rounded-2xl"></div>
                            <div
                                class="absolute left-4 top-4 bg-white text-black rounded-2xl px-3 py-2 text-center border border-gray-200">
                                <div class="w-8 h-4 bg-gray-200 mb-1 rounded"></div>
                                <div class="w-10 h-8 bg-gray-200 mb-1 rounded"></div>
                                <div class="w-16 h-4 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                        <!-- Content skeleton -->
                        <div class="flex-1 flex flex-col gap-6 animate-pulse">
                            <div class="flex flex-col gap-2">
                                <div class="h-10 w-3/4 bg-gray-200 rounded"></div>
                                <div class="h-6 w-1/2 bg-gray-200 rounded mt-2"></div>
                                <div class="h-6 w-1/3 bg-gray-200 rounded mt-2"></div>
                                <div class="h-24 w-full bg-gray-200 rounded mt-4"></div>
                            </div>
                            <div class="w-32 h-10 bg-gray-200 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Featured Event - Actual Content -->
                <div v-else-if="featuredEvent" class="flex flex-col gap-8">
                    <h2 class="text-4xl font-normal">Event Terbaru</h2>
                    <div
                        class="flex flex-col lg:flex-row gap-12 lg:items-center p-6 bg-gradient-to-br from-secondary via-secondary to-black rounded-2xl">
                        <div class="relative flex-1">
                            <img class="w-full h-96 object-cover rounded-2xl"
                                :src="getImageUrl(featuredEvent.thumbnail_event)" alt="Event Image" />
                            <div
                                class="absolute left-4 top-4 bg-white text-black rounded-2xl px-3 py-2 text-center border border-gray-200">
                                <div class="text-sm font-medium text-secondary">{{
                                    getDay(featuredEvent.waktu_start_event) }}</div>
                                <div class="text-3xl font-bold text-gray-900">{{
                                    getDate(featuredEvent.waktu_start_event) }}</div>
                                <div class="text-sm font-medium text-gray-600">{{
                                    getMonthYear(featuredEvent.waktu_start_event) }}</div>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-6">
                            <div class="flex flex-col gap-3">
                                <span class="text-sm font-semibold text-white uppercase tracking-wide">Event
                                    Unggulan</span>
                                <h3 class="text-3xl font-semibold text-white leading-tight">{{ featuredEvent.nama_event
                                }}</h3>
                                <div class="flex flex-col gap-2">
                                    <p class="text-base text-gray-300 flex items-center gap-2">
                                        <MapPin class="w-5 h-5 text-white flex-shrink-0" />
                                        <span class="font-medium">{{ featuredEvent.lokasi_event }}</span>
                                    </p>
                                    <p class="text-base text-gray-300 flex items-center gap-2">
                                        <Clock class="w-5 h-5 text-white flex-shrink-0" />
                                        <span class="font-medium">{{ formatTimeRange(featuredEvent.waktu_start_event,
                                            featuredEvent.waktu_end_event) }} WIB</span>
                                    </p>
                                </div>
                                <p class="text-base text-white leading-relaxed line-clamp-4">
                                    {{ stripHtmlTags(featuredEvent.deskripsi_event) }}
                                </p>
                            </div>
                            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                                <Link :href="`/event/${featuredEvent.slug}`"
                                    class="bg-white text-secondary hover:text-white px-8 py-3 rounded-full font-medium text-center hover:bg-black transition-colors duration-300 w-full lg:w-auto">
                                Lihat Detail Event
                                </Link>
                                <div class="flex items-center gap-2 text-sm text-white">
                                    <UserCheck class="w-4 h-4" />
                                    <span>{{ featuredEvent.jumlah_pendaftar || 0 }} orang terdaftar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Events - Skeleton Loading -->
                <div v-if="loading" class="flex flex-col gap-10">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <h2 class="text-4xl font-normal">Event Lainnya</h2>
                        <div class="w-full md:w-auto md:max-w-md">
                            <div class="relative">
                                <div class="h-12 bg-gray-200 rounded-full w-64 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-6">
                        <!-- Skeleton items -->
                        <div v-for="i in 3" :key="i"
                            class="flex flex-col lg:flex-row gap-8 p-6 bg-white border border-gray-200 rounded-2xl animate-pulse">
                            <div class="relative w-full h-64 lg:w-72 lg:h-48 flex-shrink-0 bg-gray-200 rounded-xl">
                                <div
                                    class="absolute left-4 top-4 bg-white rounded-xl p-2 w-16 h-20 border border-gray-200">
                                </div>
                            </div>
                            <div class="flex-1 flex flex-col gap-4 justify-between">
                                <div class="flex flex-col gap-3">
                                    <div class="h-8 bg-gray-200 w-3/4 rounded"></div>
                                    <div class="h-5 bg-gray-200 w-1/2 rounded"></div>
                                    <div class="h-5 bg-gray-200 w-1/3 rounded"></div>
                                    <div class="h-20 bg-gray-200 w-full rounded"></div>
                                </div>
                                <div class="h-10 w-32 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar - Always show when not loading -->
                <div v-if="!loading" class="flex flex-wrap justify-between items-center gap-4">
                    <h2 class="text-4xl font-normal">
                        {{ searchQuery ? `Hasil Pencarian "${searchQuery}"` : 'Event Lainnya' }}
                    </h2>
                    <div class="w-full">
                        <div class="relative flex items-center">
                            <input
                                v-model="searchQuery" 
                                @input="handleSearch"
                                type="text"
                                placeholder="Cari event berdasarkan nama, lokasi, tanggal, atau deskripsi..."
                                class="w-full px-5 py-3 pr-12 rounded-full border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300 bg-white text-base"
                            />
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <button
                                    @click="fetchEvents"
                                    class="p-2 rounded-full hover:bg-gray-100 transition-colors"
                                    :disabled="searching"
                                >
                                    <Search class="w-5 h-5 text-gray-500" :class="{ 'animate-spin': searching }" />
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            Contoh: <span class="font-medium">"Jakarta", "Webinar", "2024", "Teknologi"</span>
                        </div>
                    </div>
                </div>

                <!-- No events found -->
                <div v-if="events.length === 0 && !loading && !searching" class="flex flex-col gap-8">
                    <div class="flex flex-col items-center justify-center gap-6 py-20 text-center">
                        <div class="flex flex-col lg:flex-row items-center gap-6">
                            <img src="/image/empty.svg" alt="Empty State"
                                class="w-40 h-40 lg:w-64 lg:h-64 object-contain opacity-70" />
                            <div class="text-center lg:text-left">
                                <h3 class="text-2xl font-semibold text-gray-700 font-custom mb-2">
                                    {{ searchQuery ? 'Event tidak ditemukan' : 'Belum ada event lainnya' }}
                                </h3>
                                <p class="text-base text-gray-500 font-custom mb-4">
                                    {{ searchQuery
                                        ? 'Coba gunakan kata kunci yang berbeda atau periksa ejaan Anda.'
                                        : 'Silakan periksa kembali nanti untuk event-event menarik.'
                                    }}
                                </p>
                                <button v-if="searchQuery" @click="clearSearch"
                                    class="bg-secondary text-white px-6 py-2 rounded-full font-medium hover:bg-black transition-colors">
                                    Lihat Semua Event
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading state for search -->
                <div v-if="searching && !loading" class="flex flex-col gap-6">
                    <div v-for="i in 3" :key="i"
                        class="flex flex-col lg:flex-row gap-8 p-6 bg-white border border-gray-200 rounded-2xl animate-pulse">
                        <div class="relative w-full h-64 lg:w-72 lg:h-48 flex-shrink-0 bg-gray-200 rounded-xl">
                            <div class="absolute left-4 top-4 bg-white rounded-xl p-2 w-16 h-20 border border-gray-200">
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-4 justify-between">
                            <div class="flex flex-col gap-3">
                                <div class="h-8 bg-gray-200 w-3/4 rounded"></div>
                                <div class="h-5 bg-gray-200 w-1/2 rounded"></div>
                                <div class="h-5 bg-gray-200 w-1/3 rounded"></div>
                                <div class="h-20 bg-gray-200 w-full rounded"></div>
                            </div>
                            <div class="h-10 w-32 bg-gray-200 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Other Events - Actual Content -->
                <div v-if="events.length && !searching" class="flex flex-col gap-6">
                    <div v-for="(event, index) in events" :key="event.id_event"
                        class="flex flex-col lg:flex-row gap-8 p-6 bg-white border border-gray-200 rounded-2xl hover:border-gray-300 transition-colors duration-300">
                        <div class="relative w-full h-64 lg:w-72 lg:h-48 flex-shrink-0">
                            <img class="w-full h-full object-cover rounded-xl border border-gray-100"
                                :src="getImageUrl(event.thumbnail_event)" alt="Event Thumbnail" />
                            <div
                                class="absolute left-4 top-4 bg-white text-black rounded-xl px-3 py-2 text-center border border-gray-200">
                                <div class="text-xs font-medium text-secondary">{{ getDay(event.waktu_start_event) }}
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ getDate(event.waktu_start_event) }}
                                </div>
                                <div class="text-xs font-medium text-gray-600">{{ getMonthYear(event.waktu_start_event)
                                }}</div>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-4">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ event.nama_event }}</h3>
                            <div class="flex flex-col gap-2">
                                <p class="text-sm text-gray-700 flex items-center gap-1.5">
                                    <MapPin class="w-4 h-4 text-secondary" />
                                    {{ event.lokasi_event }}
                                </p>
                                <p class="text-sm text-gray-700 flex items-center gap-1.5">
                                    <Clock class="w-4 h-4 text-secondary" />
                                    {{ formatTimeRange(event.waktu_start_event, event.waktu_end_event) }} WIB
                                </p>
                            </div>
                            <p class="text-base text-gray-600 line-clamp-3">{{ stripHtmlTags(event.deskripsi_event) }}
                            </p>
                        </div>
                        <div class="flex lg:items-start">
                            <Link :href="`/event/${event.slug}`"
                                class="bg-secondary text-white px-5 py-2 h-10 rounded-full text-base font-medium hover:bg-black hover:text-white transition">
                            Lihat Detail
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1 && !loading"
                    class="flex justify-center items-center gap-4 mt-10 font-custom text-sm">
                    <!-- Tombol Sebelumnya -->
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === 1
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'">
                        Sebelumnya
                    </button>

                    <!-- Indikator halaman -->
                    <div class="px-4 py-2 rounded-xl border border-black text-black font-semibold">
                        {{ currentPage }} / {{ lastPage }}
                    </div>

                    <!-- Tombol Selanjutnya -->
                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                        class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === lastPage
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>