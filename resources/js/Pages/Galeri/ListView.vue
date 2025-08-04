<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
import AOS from "aos";
import "aos/dist/aos.css";
import { ref, onMounted } from "vue";
import { Calendar, ChevronLeft, ChevronRight, Download, Image, Tag } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

const galeries = ref([]);
const categories = ref([]);
const usedCategories = ref([]);
const activeSlideIndex = ref({}); // key by galeri id
const loadingGaleries = ref(true);
const loadingCategories = ref(true);
const searchQuery = ref("");
const selectedCategory = ref(null);
const currentPage = ref(1);
const lastPage = ref(1);
let debounceTimer = null;

// Load galeri saat komponen dimuat
onMounted(() => {
    AOS.init({
        duration: 1000,
        once: false,
        mirror: true,
    });
    fetchData();
    fetchCategories();
    setTimeout(() => {
        loadingGaleries.value = false;
        loadingCategories.value = false;
    }, 4000);
});

async function fetchData() {
    loadingGaleries.value = true;
    loadingCategories.value = true;
    await Promise.all([fetchGaleries(), fetchCategories()]);
    loadingGaleries.value = false;
    loadingCategories.value = false;
}

async function fetchGaleries(query = "", categoryId = null, page = 1) {
    try {
        loadingGaleries.value = true;
        let url =
            query.length > 0 || categoryId !== null
                ? "/api/galeri/search"
                : "/api/galeri";
        const params = { page };
        if (query.length > 0) params.query = query;
        if (categoryId !== null) params.category_id = categoryId;

        const response = await axios.get(url, { params });
        galeries.value = response.data.data || [];
        currentPage.value = response.data.meta?.current_page || 1;
        lastPage.value = response.data.meta?.last_page || 1;

        galeries.value.forEach((galeri) => {
            activeSlideIndex.value[galeri.id_galeri] = 0;
        });
    } catch (error) {
        galeries.value = [];
    } finally {
        loadingGaleries.value = false;
    }
}

async function fetchCategories() {
    try {
        const response = await axios.get("/api/galeri/categories");
        categories.value = response.data.data || [];
        filterUsedCategories();
    } catch (error) {
        categories.value = [];
    }
}

function filterUsedCategories() {
    if (galeries.value.length === 0) {
        usedCategories.value = [];
        return;
    }

    // Ambil ID kategori yang digunakan oleh galeri
    const usedCategoryIds = [
        ...new Set(
            galeries.value
                .map((galeri) => galeri.kategoriGaleri?.id_kategori_galeri)
                .filter((id) => id)
        ),
    ];

    // Filter kategori yang hanya digunakan
    usedCategories.value = categories.value.filter((category) =>
        usedCategoryIds.includes(category.id_kategori_galeri)
    );

    // Reset kategori yang dipilih jika tidak ada dalam kategori yang digunakan
    if (
        selectedCategory.value !== null &&
        !usedCategoryIds.includes(selectedCategory.value)
    ) {
        selectedCategory.value = null;
    }
}

const filterByCategory = (categoryId) => {
    selectedCategory.value = categoryId;
    currentPage.value = 1;
    fetchGaleries(searchQuery.value, categoryId, 1);
};

const handleSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        currentPage.value = 1;
        fetchGaleries(searchQuery.value, selectedCategory.value, 1);
    }, 500);
};

const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    fetchGaleries(searchQuery.value, selectedCategory.value, page);
};

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    return `/storage/${image}`;
}

function prevImage(id) {
    const index = activeSlideIndex.value[id];
    const total =
        galeries.value.find((g) => g.id_galeri === id)?.thumbnail_galeri
            .length || 0;
    activeSlideIndex.value[id] = (index - 1 + total) % total;
}

function nextImage(id) {
    const index = activeSlideIndex.value[id];
    const total =
        galeries.value.find((g) => g.id_galeri === id)?.thumbnail_galeri
            .length || 0;
    activeSlideIndex.value[id] = (index + 1) % total;
}
function stripHtmlTags(html, maxLength = 150) {
    if (!html) return "";

    // Create a temporary div to parse HTML
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;

    // Get text content (strips HTML tags)
    const text = tempDiv.textContent || tempDiv.innerText || "";
    const trimmed = text.trim();

    // Limit text length if needed
    if (trimmed.length <= maxLength) return trimmed;
    return trimmed.substring(0, maxLength) + "...";
}

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}
</script>

<template>
    <AppLayout>
        <div class="bg-secondary text-white font-custom">
            <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-28">
                <div class="flex flex-col gap-20">
                    <!-- Heading & Search Form -->
                    <div class="text-center">
                        <h2 class="text-4xl lg:text-5xl font-normal">
                            Ruang Galeri
                        </h2>
                        <p class="text-lg text-secondary/80 mt-2 mb-10">
                            Kumpulan dokumentasi kegiatan dan acara yang
                            telah dilaksanakan oleh perusahaan kami.
                        </p>

                        <!-- Search form -->
                        <div class="w-full max-w-2xl mx-auto flex flex-col items-center gap-4">
                            <div class="w-full flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                <input v-model="searchQuery" @input="handleSearch" type="text"
                                    placeholder="Cari galeri..."
                                    class="flex-1 px-4 py-3 rounded-xl bg-white/10 text-white placeholder-white/60 outline outline-1 outline-transparent focus:outline-white focus:ring-0 font-custom" />
                                <button @click="fetchGaleries(searchQuery)"
                                    class="px-6 py-2.5 rounded-full bg-white text-black text-base font-medium font-custom hover:bg-gray-100 transition">
                                    Cari
                                </button>
                            </div>
                            <p class="text-xs font-normal font-custom leading-none">
                                Gunakan kata kunci untuk mencari galeri yang
                                Anda inginkan.
                            </p>
                        </div>
                    </div>

                    <!-- Category Filters -->
                    <div v-if="usedCategories.length > 0" class="w-full overflow-x-auto">
                        <div class="flex gap-2 font-custom text-sm whitespace-nowrap">
                            <!-- Skeleton Kategori -->
                            <template v-if="loadingCategories">
                                <div v-for="n in 4" :key="n"
                                    class="px-4 py-2 rounded-xl bg-white/20 animate-pulse w-24 h-9"></div>
                            </template>

                            <!-- Tombol Kategori asli -->
                            <template v-else>
                                <button @click="filterByCategory(null)"
                                    class="px-4 py-2 rounded-xl font-medium transition border" :class="selectedCategory === null
                                        ? 'bg-white text-secondary'
                                        : 'bg-transparent text-white border-white/30 hover:bg-white/10'
                                        ">
                                    Semua
                                </button>

                                <button v-for="category in usedCategories" :key="category.id_kategori_galeri" @click="
                                    filterByCategory(
                                        category.id_kategori_galeri
                                    )
                                    " class="px-4 py-2 rounded-xl font-medium transition border" :class="selectedCategory ===
                                        category.id_kategori_galeri
                                        ? 'bg-white text-secondary'
                                        : 'bg-transparent text-white border-white/30 hover:bg-white/10'
                                        ">
                                    {{ category.nama_kategori_galeri }}
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- List Galeri -->
                    <div class="flex flex-col divide-y divide-white/20">
                        <!-- Skeleton Loading -->
                        <div v-if="loadingGaleries" v-for="i in 3" :key="`skeleton-${i}`"
                            class="flex flex-col lg:flex-row gap-12 items-start lg:items-center py-10">
                            <!-- Kiri: Judul + Deskripsi Skeleton -->
                            <div class="lg:w-1/2 flex flex-col justify-center h-full">
                                <div class="max-w-xl">
                                    <div class="h-8 w-2/3 bg-white/30 animate-pulse rounded-md mb-4"></div>
                                    <div class="h-4 w-full bg-white/20 animate-pulse rounded-md mb-2"></div>
                                    <div class="h-4 w-4/5 bg-white/20 animate-pulse rounded-md mb-2"></div>
                                    <div class="h-4 w-3/4 bg-white/20 animate-pulse rounded-md mb-4"></div>
                                    <div class="h-8 w-32 bg-white/30 animate-pulse rounded-full mt-4"></div>
                                </div>
                            </div>

                            <!-- Kanan: Slider Skeleton -->
                            <div class="lg:w-1/2 flex flex-col gap-6 w-full">
                                <div class="aspect-video w-full overflow-hidden rounded-2xl bg-white/20 animate-pulse">
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-4">
                                        <div class="w-12 h-12 rounded-full bg-white/30 animate-pulse"></div>
                                        <div class="w-12 h-12 rounded-full bg-white/30 animate-pulse"></div>
                                    </div>
                                    <div class="flex gap-2">
                                        <div v-for="dot in 4" :key="dot"
                                            class="w-2 h-2 rounded-full bg-white/30 animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="galeries.length === 0" class="py-20 flex flex-col items-center text-center">
                            <img src="/image/empty.svg" alt="No galleries found" class="w-40 h-40 mb-6" />
                            <h3 class="text-2xl font-semibold mb-2">
                                Tidak ada galeri ditemukan
                            </h3>
                            <p class="text-secondary/70 mb-6 max-w-md">
                                Coba kata kunci pencarian lain atau pilih
                                kategori berbeda.
                            </p>
                        </div>

                        <!-- Actual Galleries -->
                        <div v-else v-for="(galeri, index) in galeries" :key="galeri.id_galeri"
                            class="flex flex-col lg:flex-row gap-12 items-start lg:items-center py-10 group"
                            data-aos="fade-right" :data-aos-delay="index * 100">

                            <!-- Kiri: Judul + Deskripsi -->
                            <div class="lg:w-1/2 flex flex-col justify-center h-full">
                                <div class="max-w-xl space-y-4">
                                    <!-- Badge Kategori -->
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full border border-white/20">
                                            <Tag class="w-4 h-4 inline-block mr-1" />
                                            {{ galeri.kategoriGaleri?.nama_kategori_galeri || 'Tanpa Kategori' }}
                                        </span>
                                        <span
                                            class="flex items-center gap-1.5 text-xs font-medium bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-full text-white">
                                            <Image class="w-4 h-4" />
                                            {{ galeri.thumbnail_galeri?.length || 0 }} foto
                                        </span>
                                    </div>

                                    <!-- Judul -->
                                    <h3
                                        class="text-2xl lg:text-3xl font-normal pb-2 group-hover:text-white/90 transition-colors duration-300">
                                        {{ galeri.judul_galeri }}
                                    </h3>

                                    <!-- Deskripsi -->
                                    <p class="text-base text-white/80 leading-relaxed line-clamp-3">
                                        {{ stripHtmlTags(galeri.deskripsi_galeri, 200) }}
                                    </p>

                                    <!-- Meta Info -->
                                    <div class="flex items-center gap-4 text-xs text-white/70">
                                        <div class="flex items-center gap-1.5">
                                            <Calendar class="w-3 h-3" />
                                            <span>{{ formatDate(galeri.created_at) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5" v-if="galeri.jumlah_unduhan">
                                            <Download class="w-3 h-3" />
                                            <span>{{ galeri.jumlah_unduhan }}× diunduh</span>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <Link :href="`/galeri/${galeri.slug}`"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-3 mt-4 bg-white/30 backdrop-blur-sm text-white font-medium text-sm rounded-full hover:bg-white hover:text-black transition-all duration-300 border border-white/20 group-hover:border-white/40">
                                    <span>Lihat Selengkapnya</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    </Link>
                                </div>
                            </div>

                            <!-- Kanan: Slider -->
                            <div class="lg:w-1/2 flex flex-col gap-6 w-full">
                                <!-- Gambar utama -->
                                <div
                                    class="relative aspect-video w-full overflow-hidden rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-white/20">
                                    <img v-if="galeri.thumbnail_galeri?.length"
                                        :src="getImageUrl(galeri.thumbnail_galeri[activeSlideIndex[galeri.id_galeri]])"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        :alt="`${galeri.judul_galeri} - Gambar ${activeSlideIndex[galeri.id_galeri] + 1}`"
                                        loading="lazy" />
                                </div>

                                <!-- Enhanced Navigation -->
                                <div class="flex justify-between items-center">
                                    <!-- Navigation buttons -->
                                    <div class="flex gap-3">
                                        <button @click="prevImage(galeri.id_galeri)"
                                            :disabled="galeri.thumbnail_galeri?.length <= 1"
                                            class="p-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-full text-white hover:bg-white hover:text-black transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white/20 disabled:hover:text-white">
                                            <ChevronLeft class="w-5 h-5" />
                                        </button>
                                        <button @click="nextImage(galeri.id_galeri)"
                                            :disabled="galeri.thumbnail_galeri?.length <= 1"
                                            class="p-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-full text-white hover:bg-white hover:text-black transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white/20 disabled:hover:text-white">
                                            <ChevronRight class="w-5 h-5" />
                                        </button>
                                    </div>

                                    <!-- Enhanced Dot navigation -->
                                    <div class="flex gap-2 max-w-[200px] overflow-x-auto scrollbar-hide">
                                        <button v-for="(img, imgIndex) in galeri.thumbnail_galeri?.slice(0, 10)"
                                            :key="imgIndex" @click="activeSlideIndex[galeri.id_galeri] = imgIndex"
                                            :class="[
                                                'flex-shrink-0 w-3 h-3 rounded-full transition-all duration-300',
                                                imgIndex === activeSlideIndex[galeri.id_galeri]
                                                    ? 'bg-white'
                                                    : 'bg-white/40 hover:bg-white/60',
                                            ]" :title="`Gambar ${imgIndex + 1}`">
                                        </button>
                                        <!-- Indicator for more images -->
                                        <span v-if="galeri.thumbnail_galeri?.length > 10"
                                            class="flex items-center text-white/60 text-xs font-medium ml-2">
                                            +{{ galeri.thumbnail_galeri.length - 10 }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Thumbnail preview untuk gambar berikutnya -->
                                <div v-if="galeri.thumbnail_galeri?.length > 1"
                                    class="flex gap-2 overflow-x-auto scrollbar-hide">
                                    <button v-for="(img, imgIndex) in galeri.thumbnail_galeri.slice(0, 5)"
                                        :key="`thumb-${imgIndex}`"
                                        @click="activeSlideIndex[galeri.id_galeri] = imgIndex" :class="[
                                            'flex-shrink-0 w-16 h-12 rounded-lg overflow-hidden border-2 transition-all duration-300',
                                            imgIndex === activeSlideIndex[galeri.id_galeri]
                                                ? 'border-white opacity-100'
                                                : 'border-white/30 opacity-60 hover:opacity-80 hover:border-white/50'
                                        ]">
                                        <img :src="getImageUrl(img)" class="w-full h-full object-cover"
                                            :alt="`Thumbnail ${imgIndex + 1}`" loading="lazy" />
                                    </button>
                                    <div v-if="galeri.thumbnail_galeri.length > 5"
                                        class="flex-shrink-0 w-16 h-12 rounded-lg bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center text-white text-xs font-medium">
                                        +{{ galeri.thumbnail_galeri.length - 5 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="!loadingGaleries && lastPage > 1"
                        class="flex justify-center items-center gap-4 mt-10 font-custom text-sm">
                        <!-- Tombol Sebelumnya -->
                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === 1
                                ? 'bg-white/10 text-white/40 cursor-not-allowed border-white/10'
                                : 'bg-transparent text-white border-white/30 hover:bg-white/10'
                                ">
                            Sebelumnya
                        </button>

                        <!-- Indikator halaman -->
                        <div class="px-4 py-2 rounded-xl border border-white text-white font-semibold">
                            {{ currentPage }} / {{ lastPage }}
                        </div>

                        <!-- Tombol Selanjutnya -->
                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                            class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === lastPage
                                ? 'bg-white/10 text-white/40 cursor-not-allowed border-white/10'
                                : 'bg-transparent text-white border-white/30 hover:bg-white/10'
                                ">
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<!-- Tambahkan CSS untuk scrollbar-hide -->
<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
