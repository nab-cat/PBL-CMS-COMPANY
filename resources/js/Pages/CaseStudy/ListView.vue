<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { Search, Calendar, ArrowRight, ChevronRight } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

// State variables
const caseStudies = ref([]);
const featuredCase = ref(null);
const searchQuery = ref("");
const isLoading = ref(true);
const currentPage = ref(1);
const lastPage = ref(1);
const allMitra = ref([]);
const companyProfile = ref(null);
let debounceTimer = null;

// State untuk background hero
const heroBackground = ref("/image/placeholder.webp");

// Fetch data when component mounts
onMounted(() => {
    fetchCaseStudies();
    fetchLatestCaseStudy();
    fetchAllMitra();
    fetchCompanyProfile();
});

// Fetch all case studies
async function fetchCaseStudies(query = "", page = 1) {
    isLoading.value = true;
    try {
        let url = query ? "/api/case-study/search" : "/api/case-study";
        const params = { page };
        if (query) params.query = query;

        const response = await axios.get(url, { params });
        caseStudies.value = response.data.data;
        currentPage.value = response.data.meta?.current_page || 1;
        lastPage.value = response.data.meta?.last_page || 1;

        // Set random background dari case studies
        if (response.data.data.length > 0) {
            const randomIndex = Math.floor(
                Math.random() * response.data.data.length
            );
            const randomCase = response.data.data[randomIndex];
            heroBackground.value = getImageUrl(randomCase.thumbnail_case_study);
        }
    } catch (error) {
        caseStudies.value = [];
    } finally {
        isLoading.value = false;
    }
}

// Fetch latest case study
async function fetchLatestCaseStudy() {
    try {
        const response = await axios.get("/api/case-study/latest");
        featuredCase.value = response.data.data;
    } catch (error) {
        featuredCase.value = null;
    }
}

// Fetch all active mitra
async function fetchAllMitra() {
    try {
        const response = await axios.get("/api/case-study/mitra");
        allMitra.value = response.data.data;
    } catch (error) {
        allMitra.value = [];
    }
}

// Fetch company profile
async function fetchCompanyProfile() {
    try {
        const response = await axios.get("/api/profil-perusahaan");
        companyProfile.value = response.data.data;
    } catch (error) {
        companyProfile.value = null;
    }
}

// Handle search with debounce
const handleSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        currentPage.value = 1;
        fetchCaseStudies(searchQuery.value, 1);
    }, 500);
};

// Navigate to a specific page
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    fetchCaseStudies(searchQuery.value, page);
};

// Helper function to get image URL
function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

// Format date to Indonesian format
function formatDate(dateString) {
    if (!dateString) return "";

    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(dateString).toLocaleDateString("id-ID", options);
}

// Computed properties untuk statistik dinamis
const dynamicStats = computed(() => {
    const currentYear = new Date().getFullYear();
    const thisYearCases = caseStudies.value.filter(
        (cs) => new Date(cs.created_at).getFullYear() === currentYear
    );

    return {
        totalCases: caseStudies.value.length,
        uniquePartners: allMitra.value.length,
        thisYearCases: thisYearCases.length,
    };
});
</script>

<template>
    <AppLayout>
        <!-- Hero Section with Search -->
        <div
            class="relative w-full bg-cover bg-center bg-no-repeat px-4 lg:px-16 py-28 flex flex-col justify-start items-center gap-16 overflow-hidden"
            :style="{
                backgroundImage: `linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('${heroBackground}')`,
            }"
        >
            <!-- Content -->
            <div
                class="relative z-10 w-full max-w-4xl flex flex-col justify-start items-center gap-10 text-white text-center"
            >
                <!-- Heading -->
                <div class="flex flex-col items-center gap-6">
                    <div
                        class="text-base font-semibold font-custom uppercase tracking-wider"
                    >
                        Studi Kasus
                    </div>
                    <h1
                        class="text-4xl lg:text-6xl font-normal font-custom leading-tight"
                    >
                        Kisah Sukses yang<br />Menginspirasi Perubahan
                    </h1>
                    <p
                        class="text-lg font-normal font-custom leading-relaxed max-w-3xl"
                    >
                        Jelajahi bagaimana kami membantu klien dari berbagai
                        industri mengubah tantangan menjadi peluang dengan
                        solusi inovatif dan hasil yang terukur.
                    </p>
                </div>

                <!-- Search form -->
                <div class="w-full max-w-2xl flex flex-col items-center gap-4">
                    <div class="w-full relative">
                        <input
                            v-model="searchQuery"
                            @input="handleSearch"
                            type="text"
                            placeholder="Cari studi kasus berdasarkan industri, tantangan, atau solusi..."
                            class="w-full px-6 py-4 rounded-full bg-white/10 backdrop-blur-sm text-white placeholder-white/60 outline outline-1 outline-white/30 focus:outline-white/70 focus:ring-0 font-custom pr-16"
                        />
                        <button
                            @click="handleSearch"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white text-black hover:bg-opacity-80 transition"
                        >
                            <Search class="w-5 h-5" />
                        </button>
                    </div>
                    <p
                        class="text-sm font-normal font-custom leading-none opacity-70"
                    >
                        Coba cari "keuangan", "kesehatan", atau "transformasi
                        digital"
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div
            class="max-w-screen-xl mx-auto px-4 lg:px-8 py-16 flex flex-col items-center gap-20 overflow-hidden"
        >
            <!-- Case Studies List -->
            <div class="w-full">
                <div class="mb-12">
                    <h2 class="text-3xl font-normal font-custom mb-8">
                        Jelajahi Semua Studi Kasus
                    </h2>
                </div>

                <!-- No Case Studies -->
                <div
                    v-if="!isLoading && caseStudies.length === 0"
                    class="text-center py-12"
                >
                    <div class="flex flex-col items-center gap-1 font-custom">
                        <img
                            src="/image/empty.svg"
                            alt="Tidak ada data"
                            class="w-40 h-40 object-contain"
                        />
                        <h3 class="text-xl font-medium text-gray-700">
                            Tidak ada studi kasus yang ditemukan
                        </h3>
                        <p class="text-gray-500">
                            Coba ubah kata kunci pencarian Anda
                        </p>
                    </div>
                </div>

                <!-- Case Studies Grid -->
                <div
                    v-if="!isLoading && caseStudies.length > 0"
                    class="grid grid-cols-1 lg:grid-cols-3 gap-8"
                >
                    <div
                        v-for="item in caseStudies"
                        :key="item.case_study_id"
                        class="group bg-white rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:border-secondary"
                    >
                        <!-- Image -->
                        <div class="h-64 overflow-hidden">
                            <img
                                :src="getImageUrl(item.thumbnail_case_study)"
                                :alt="item.judul_case_study"
                                class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
                            />
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="px-3 py-1 bg-gray-200 text-secondary rounded-full text-sm font-semibold font-custom"
                                >
                                    {{ item.mitra?.nama_mitra || "Mitra" }}
                                </span>
                            </div>

                            <Link
                                :href="`/case-study/${item.slug_case_study}`"
                                class="group-hover:text-secondary"
                            >
                                <h3
                                    class="text-xl font-custom font-medium transition-colors"
                                >
                                    {{ item.judul_case_study }}
                                </h3>
                            </Link>

                            <p class="text-gray-600 line-clamp-2 font-custom">
                                {{ item.deskripsi_case_study }}
                            </p>

                            <div
                                class="mt-2 text-sm text-gray-500 font-custom flex items-center gap-2"
                            >
                                <Calendar class="w-4 h-4" />
                                {{ formatDate(item.created_at) }}
                            </div>

                            <Link
                                :href="`/case-study/${item.slug_case_study}`"
                                class="mt-4 text-secondary font-medium flex items-center group-hover:underline font-custom"
                            >
                                Lihat studi kasus
                                <ArrowRight class="w-4 h-4 ml-1" />
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Loading State for Grid -->
                <div
                    v-if="isLoading"
                    class="grid grid-cols-1 lg:grid-cols-3 gap-8"
                >
                    <div
                        v-for="i in 6"
                        :key="i"
                        class="bg-white rounded-2xl overflow-hidden border border-gray-100"
                    >
                        <!-- Skeleton Image -->
                        <div class="h-64 bg-gray-200 animate-pulse"></div>

                        <!-- Skeleton Content -->
                        <div class="p-6 flex flex-col gap-4">
                            <div
                                class="h-6 bg-gray-200 rounded w-32 animate-pulse"
                            ></div>
                            <div
                                class="h-8 bg-gray-300 rounded w-full animate-pulse"
                            ></div>
                            <div
                                class="h-4 bg-gray-200 rounded w-full animate-pulse"
                            ></div>
                            <div
                                class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"
                            ></div>
                            <div
                                class="h-4 bg-gray-200 rounded w-24 mt-2 animate-pulse"
                            ></div>
                            <div
                                class="h-6 bg-gray-200 rounded w-40 mt-4 animate-pulse"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="!isLoading && lastPage > 1"
                    class="flex justify-center items-center gap-4 mt-16 font-custom"
                >
                    <button
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-full font-medium transition border"
                        :class="
                            currentPage === 1
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-black border-gray-300 hover:bg-secondary hover:text-white hover:border-secondary'
                        "
                    >
                        Sebelumnya
                    </button>

                    <div class="flex gap-2">
                        <button
                            v-for="page in Math.min(3, lastPage)"
                            :key="page"
                            @click="goToPage(page)"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-medium transition"
                            :class="
                                currentPage === page
                                    ? 'bg-secondary text-white'
                                    : 'bg-white text-black border border-gray-300 hover:border-secondary hover:text-secondary'
                            "
                        >
                            {{ page }}
                        </button>
                        <span v-if="lastPage > 3" class="flex items-center px-2"
                            >...</span
                        >
                        <button
                            v-if="lastPage > 3"
                            @click="goToPage(lastPage)"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-medium transition"
                            :class="
                                currentPage === lastPage
                                    ? 'bg-secondary text-white'
                                    : 'bg-white text-black border border-gray-300 hover:border-secondary hover:text-secondary'
                            "
                        >
                            {{ lastPage }}
                        </button>
                    </div>

                    <button
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                        class="px-4 py-2 rounded-full font-medium transition border"
                        :class="
                            currentPage === lastPage
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-black border-gray-300 hover:bg-secondary hover:text-white hover:border-secondary'
                        "
                    >
                        Selanjutnya
                    </button>
                </div>
            </div>

            <!-- Featured Case Study -->
            <section v-if="featuredCase" class="w-full">
                <div class="w-full mb-12 flex flex-col gap-4">
                    <div
                        class="text-secondary text-base font-semibold font-custom uppercase tracking-wider"
                    >
                        Studi Kasus Unggulan
                    </div>
                    <h2
                        class="text-3xl lg:text-5xl font-normal font-custom leading-tight"
                    >
                        Kisah Sukses Terbaru Kami
                    </h2>
                </div>

                <div
                    class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8 bg-gray-50 rounded-3xl overflow-hidden min-h-[400px] lg:min-h-[500px]"
                >
                    <div class="relative aspect-[4/3] lg:aspect-auto lg:h-full">
                        <img
                            :src="
                                getImageUrl(featuredCase.thumbnail_case_study)
                            "
                            :alt="featuredCase.judul_case_study"
                            class="absolute inset-0 w-full h-full object-cover transition duration-500 hover:scale-105"
                        />
                    </div>
                    <div class="flex flex-col justify-center p-8 lg:p-12 gap-6">
                        <div class="flex items-center gap-4">
                            <span
                                class="px-3 py-1.5 bg-gray-200 text-secondary rounded-full text-sm font-semibold font-custom"
                            >
                                {{ featuredCase.mitra?.nama_mitra || "Mitra" }}
                            </span>
                        </div>
                        <h3
                            class="text-2xl lg:text-4xl font-custom font-normal"
                        >
                            {{ featuredCase.judul_case_study }}
                        </h3>
                        <p
                            class="text-base lg:text-lg font-custom text-gray-600"
                        >
                            {{ featuredCase.deskripsi_case_study }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <div class="flex items-center gap-2 text-gray-700">
                                <Calendar class="w-4 h-4" />
                                <span class="font-custom">{{
                                    formatDate(featuredCase.created_at)
                                }}</span>
                            </div>
                        </div>
                        <Link
                            :href="`/case-study/${featuredCase.slug_case_study}`"
                            class="mt-6 inline-flex items-center justify-center gap-2 px-6 py-3 bg-secondary text-white font-medium rounded-full hover:bg-black transition-all duration-300 w-fit font-custom"
                        >
                            Baca Studi Kasus
                            <ArrowRight class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section
                class="w-full relative bg-gradient-to-br from-secondary via-secondary to-black rounded-3xl p-8 lg:p-16 text-center overflow-hidden"
            >
                <!-- Content -->
                <div
                    class="relative z-10 max-w-4xl mx-auto flex flex-col items-center gap-8"
                >
                    <!-- Icon -->
                    <div
                        class="w-72 h-72 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center"
                    >
                        <img
                            src="/image/mitra.svg"
                            alt="Success"
                            class="object-contain"
                        />
                    </div>

                    <!-- Heading -->
                    <div class="text-center">
                        <h2
                            class="text-3xl lg:text-5xl font-normal font-custom text-white leading-tight mb-4"
                        >
                            Siap untuk membuat kisah sukses Anda sendiri?
                        </h2>
                        <p
                            class="text-lg lg:text-xl text-white/80 font-custom max-w-2xl mx-auto"
                        >
                            Mari bekerja sama untuk mengubah tantangan bisnis
                            Anda menjadi kisah sukses yang terukur dan
                            menginspirasi.
                        </p>
                    </div>

                    <!-- Stats -->
                    <div
                        class="grid grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-2xl"
                    >
                        <div class="text-center">
                            <div
                                class="text-2xl lg:text-3xl font-bold text-white font-custom"
                            >
                                {{ dynamicStats.totalCases }}+
                            </div>
                            <div class="text-sm text-white/70 font-custom">
                                Studi Kasus
                            </div>
                        </div>
                        <div class="text-center">
                            <div
                                class="text-2xl lg:text-3xl font-bold text-white font-custom"
                            >
                                {{ dynamicStats.uniquePartners }}+
                            </div>
                            <div class="text-sm text-white/70 font-custom">
                                Mitra Terpercaya
                            </div>
                        </div>
                        <div class="text-center col-span-2 lg:col-span-1">
                            <div
                                class="text-2xl lg:text-3xl font-bold text-white font-custom"
                            >
                                {{ dynamicStats.thisYearCases }}+
                            </div>
                            <div class="text-sm text-white/70 font-custom">
                                Proyek Tahun Ini
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 mt-4 font-custom"
                    >
                        <a
                            v-if="companyProfile?.email_perusahaan"
                            :href="`mailto:${companyProfile.email_perusahaan}?subject=Inquiry - Case Study Collaboration`"
                            class="group px-8 py-4 bg-white text-secondary rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2"
                        >
                            <span>Hubungi Kami</span>
                            <ArrowRight
                                class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                            />
                        </a>
                        <Link
                            v-else
                            href="/profil-perusahaan"
                            class="group px-8 py-4 bg-white text-secondary rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2"
                        >
                            <span>Hubungi Kami</span>
                            <ArrowRight
                                class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                            />
                        </Link>
                        <Link
                            href="/profil-perusahaan"
                            class="px-8 py-4 bg-transparent text-white border-2 border-white/30 rounded-full font-semibold hover:border-white hover:bg-white/10 transition-all duration-300 backdrop-blur-sm"
                        >
                            Lihat Profil Kami
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
