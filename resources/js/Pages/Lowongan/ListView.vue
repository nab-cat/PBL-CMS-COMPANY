<script setup>
import { ref, onMounted, computed } from "vue";
import { MapPin, Clock, ArrowRight, Briefcase, Search, X } from "lucide-vue-next";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from '@inertiajs/vue3';
import axios from "axios";

// Data state
const lowongan = ref([]);
const allLowongan = ref([]); // Store all job data for filtering
const loading = ref(true);
const error = ref(null);
const searchQuery = ref('');
const selectedJobType = ref(''); // Filter by job type
const selectedStatus = ref(''); // 'open', 'closed', or ''
const isLoading = ref(true);
const currentPage = ref(1);
const lastPage = ref(1);
let debounceTimer = null;

// Fetch job listings with pagination and search
const fetchLowongan = async (page = 1, query = '') => {
    try {
        loading.value = true;

        // Gunakan endpoint yang konsisten untuk pencarian
        const url = query ? '/api/lowongan/search' : '/api/lowongan';
        const params = { page };
        if (query) params.query = query;

        const response = await axios.get(url, { params });

        // Handle response berdasarkan struktur API
        if (response.data && response.data.data) {
            allLowongan.value = response.data.data; // Store all data
            applyFilters(); // Apply current filters

            // Setup pagination if available
            if (response.data.meta) {
                currentPage.value = response.data.meta.current_page;
                lastPage.value = response.data.meta.last_page;
            }
        } else {
            lowongan.value = [];
            allLowongan.value = [];
        }
    } catch (err) {
        lowongan.value = [];
        allLowongan.value = [];
    } finally {
        loading.value = false;
        isLoading.value = false;
    }
};

// Apply filters to the job listings
const applyFilters = () => {
    let filtered = [...allLowongan.value];
    
    // Filter by job type
    if (selectedJobType.value) {
        filtered = filtered.filter(job => job.jenis_lowongan === selectedJobType.value);
    }
    
    // Filter by status (open/closed)
    if (selectedStatus.value) {
        const today = new Date();
        if (selectedStatus.value === 'open') {
            filtered = filtered.filter(job => {
                const openDate = new Date(job.tanggal_dibuka);
                const closeDate = new Date(job.tanggal_ditutup);
                return today >= openDate && today <= closeDate;
            });
        } else if (selectedStatus.value === 'closed') {
            filtered = filtered.filter(job => {
                const openDate = new Date(job.tanggal_dibuka);
                const closeDate = new Date(job.tanggal_ditutup);
                return today < openDate || today > closeDate;
            });
        }
    }
    
    lowongan.value = filtered;
};

// Get unique job types for filter dropdown
const jobTypes = computed(() => {
    const types = [...new Set(allLowongan.value.map(job => job.jenis_lowongan))];
    return types.filter(type => type); // Remove empty values
});

// Handle job type filter change
const handleJobTypeChange = (type) => {
    selectedJobType.value = type;
    currentPage.value = 1;
    applyFilters();
};

// Add status filter handler
const handleStatusChange = (status) => {
    selectedStatus.value = status;
    currentPage.value = 1;
    applyFilters();
};

// Clear all filters
const clearFilters = () => {
    searchQuery.value = '';
    selectedJobType.value = '';
    selectedStatus.value = '';
    currentPage.value = 1;
    fetchLowongan(1, '');
};

// Handle search
const handleSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        currentPage.value = 1; // Reset to first page when searching
        fetchLowongan(1, searchQuery.value);
    }, 500);
};

// Go to specific page
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    fetchLowongan(page, searchQuery.value);
};

// Format date (YYYY-MM-DD to readable format)
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
};

// Check if job is still open
const isJobOpen = (openDate, closeDate) => {
    const today = new Date();
    const open = new Date(openDate);
    const close = new Date(closeDate);
    return today >= open && today <= close;
};

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }

    return `/storage/${image}`;
}

// Function to strip HTML tags
function stripHtml(html) {
    if (!html) return ''
    const tmp = document.createElement('div')
    tmp.innerHTML = html
    return tmp.textContent || tmp.innerText || ''
}

// Function to truncate text
function truncateText(text, length = 200) {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

onMounted(() => {
    fetchLowongan();

    // Set isLoading to false after a delay to show skeleton
    setTimeout(() => {
        isLoading.value = false
    }, 2000);
});
</script>

<template>
    <AppLayout>
        <div class="py-16 lg:py-28 px-4 sm:px-6 lg:px-16 font-custom">
            <div class="max-w-screen-xl mx-auto">
                <!-- Header Section -->
                <div class="flex flex-col justify-center items-center gap-4 mb-16 lg:mb-20 max-w-2xl mx-auto">
                    <div class="inline-flex justify-start items-center">
                        <div class="text-center uppercase text-secondary font-semibold tracking-wider">
                            Karir
                        </div>
                    </div>
                    <div class="flex flex-col justify-start items-center gap-6">
                        <h1 class="text-3xl lg:text-5xl text-center text-typography-dark font-normal leading-tight">
                            Lowongan Tersedia
                        </h1>
                        <p class="text-base lg:text-lg text-center text-typography-dark font-normal leading-relaxed">
                            Temukan posisi yang sesuai dengan keterampilan dan minat Anda untuk bergabung dengan tim
                            kami dan mengembangkan karir profesional Anda.
                        </p>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="w-full max-w-5xl mx-auto mb-16 space-y-8">
                    <!-- Search Bar -->
                    <div class="relative flex items-center max-w-2xl mx-auto">
                        <Search class="absolute left-4 text-gray-400 w-5 h-5 z-10" />
                        <input v-model="searchQuery" @input="handleSearch" type="text"
                            placeholder="Cari posisi yang Anda inginkan..."
                            class="w-full px-12 py-4 rounded-full border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300 font-custom text-base" />
                        <button v-if="searchQuery" @click="() => { searchQuery = ''; handleSearch(); }"
                            class="absolute right-4 p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-all">
                            <X class="w-4 h-4" aria-hidden="true" />
                            <span class="sr-only">Clear search</span>
                        </button>
                    </div>

                    <!-- Filter Section -->
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100">
                        <div class="space-y-6">
                            <!-- Job Type Filter -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-semibold text-gray-800">Jenis Lowongan</h3>
                                    <span class="text-sm text-gray-500">{{ jobTypes.length }} kategori tersedia</span>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button
                                        @click="handleJobTypeChange('')"
                                        class="px-4 py-2.5 rounded-xl font-medium transition-all border whitespace-nowrap"
                                        :class="
                                            selectedJobType === ''
                                                ? 'bg-black text-white border-black shadow-sm'
                                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white hover:border-black'
                                        "
                                    >
                                        Semua Jenis
                                    </button>
                                    <button
                                        v-for="type in jobTypes"
                                        :key="type"
                                        @click="handleJobTypeChange(type)"
                                        class="px-4 py-2.5 rounded-xl font-medium transition-all border whitespace-nowrap"
                                        :class="
                                            selectedJobType === type
                                                ? 'bg-black text-white border-black shadow-sm'
                                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white hover:border-black'
                                        "
                                    >
                                        {{ type }}
                                    </button>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-200"></div>

                            <!-- Status Filter -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-semibold text-gray-800">Status Lowongan</h3>
                                    <span class="text-sm text-gray-500">Filter berdasarkan status</span>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button
                                        @click="handleStatusChange('')"
                                        class="px-4 py-2.5 rounded-xl font-medium transition-all border whitespace-nowrap"
                                        :class="
                                            selectedStatus === ''
                                                ? 'bg-black text-white border-black shadow-sm'
                                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white hover:border-black'
                                        "
                                    >
                                        Semua Status
                                    </button>
                                    <button
                                        @click="handleStatusChange('open')"
                                        class="px-4 py-2.5 rounded-xl font-medium transition-all border whitespace-nowrap"
                                        :class="
                                            selectedStatus === 'open'
                                                ? 'bg-black text-white border-black shadow-sm'
                                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white hover:border-black'
                                        "
                                    >
                                        Dibuka
                                    </button>
                                    <button
                                        @click="handleStatusChange('closed')"
                                        class="px-4 py-2.5 rounded-xl font-medium transition-all border whitespace-nowrap"
                                        :class="
                                            selectedStatus === 'closed'
                                                ? 'bg-black text-white border-black shadow-sm'
                                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white hover:border-black'
                                        "
                                    >
                                        Ditutup
                                    </button>
                                </div>
                            </div>

                            <!-- Clear Filters Button -->
                            <div v-if="searchQuery || selectedJobType || selectedStatus" class="pt-4 border-t border-gray-200">
                                <div class="flex justify-center">
                                    <button 
                                        @click="clearFilters"
                                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-medium text-sm flex items-center gap-2">
                                        <X class="w-4 h-4" />
                                        Hapus Semua Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Summary -->
                <div v-if="!isLoading && !error" class="max-w-4xl mx-auto mb-8">
                    <div class="flex items-center justify-between py-4 px-6 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <BriefcaseBusiness class="w-5 h-5 text-secondary" />
                            <span class="text-base font-medium text-gray-800">
                                {{ lowongan.length }} lowongan ditemukan
                            </span>
                        </div>
                        <div v-if="searchQuery || selectedJobType || selectedStatus" class="text-sm text-gray-600">
                            Hasil filter diterapkan
                        </div>
                    </div>
                </div>

                <!-- Loading State (Skeleton) -->
                <div v-if="isLoading" class="max-w-4xl mx-auto">
                    <!-- Skeleton Job Cards -->
                    <div class="space-y-8">
                        <!-- Skeleton Job Card -->
                        <div v-for="n in 3" :key="n" class="p-6 lg:p-8 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex flex-col gap-6">
                                <!-- Skeleton Thumbnail -->
                                <div class="w-full h-48 rounded-xl bg-gray-200 animate-pulse"></div>

                                <!-- Skeleton Content -->
                                <div class="space-y-6">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="h-8 w-64 bg-gray-200 animate-pulse rounded-md"></div>
                                        <div class="flex gap-2">
                                            <div class="h-6 w-20 bg-gray-200 animate-pulse rounded-full"></div>
                                            <div class="h-6 w-16 bg-gray-200 animate-pulse rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="h-4 bg-gray-200 animate-pulse rounded w-full"></div>
                                        <div class="h-4 bg-gray-200 animate-pulse rounded w-5/6"></div>
                                        <div class="h-4 bg-gray-200 animate-pulse rounded w-4/6"></div>
                                    </div>
                                </div>

                                <!-- Skeleton Footer -->
                                <div class="flex flex-col gap-4">
                                    <div class="flex flex-wrap gap-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-full bg-gray-200 animate-pulse"></div>
                                            <div class="h-5 w-24 bg-gray-200 animate-pulse rounded"></div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-full bg-gray-200 animate-pulse"></div>
                                            <div class="h-5 w-40 bg-gray-200 animate-pulse rounded"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <div class="h-6 w-32 bg-gray-200 animate-pulse rounded-md"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="flex justify-center items-center py-16 px-4">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl text-center max-w-md">
                        <h3 class="font-semibold mb-2">Terjadi Kesalahan</h3>
                        <p class="text-sm">{{ error }}</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="lowongan.length === 0" class="max-w-2xl mx-auto">
                    <div class="flex flex-col items-center justify-center gap-8 py-16 text-center">
                        <div class="flex flex-col items-center gap-6">
                            <img src="/image/empty.svg" alt="Empty State"
                                class="w-32 h-32 lg:w-48 lg:h-48 object-contain opacity-60" />
                            <div class="space-y-3">
                                <h3 class="text-xl lg:text-2xl font-semibold text-gray-700">
                                    {{ (searchQuery || selectedJobType || selectedStatus) ? 'Lowongan tidak ditemukan' : 'Belum ada lowongan tersedia' }}
                                </h3>
                                <p class="text-base text-gray-500 leading-relaxed">
                                    {{ (searchQuery || selectedJobType || selectedStatus) ? 'Coba ubah filter atau kata kunci pencarian untuk hasil yang lebih baik.' : 'Periksa kembali nanti untuk lowongan terbaru yang sesuai dengan minat Anda.' }}
                                </p>
                            </div>
                        </div>
                        <button v-if="searchQuery || selectedJobType || selectedStatus" 
                            @click="clearFilters"
                            class="px-6 py-3 bg-secondary hover:bg-secondary/90 text-white rounded-xl transition-all font-medium flex items-center gap-2">
                            <X class="w-4 h-4" />
                            Reset Filter
                        </button>
                    </div>
                </div>

                <!-- Job Listings -->
                <div v-else class="space-y-8 max-w-4xl mx-auto">
                    <!-- Job Card -->
                    <div v-for="(job, index) in lowongan" :key="job.id_lowongan"
                        class="group p-6 lg:p-8 bg-gray-50 hover:bg-white rounded-2xl border border-gray-100 hover:border-gray-200 transition-all duration-300 hover:scale-[1.02]">
                        
                        <div class="flex flex-col gap-6">
                            <!-- Job Thumbnail -->
                            <div v-if="job.thumbnail_lowongan" class="w-full h-48 rounded-xl overflow-hidden">
                                <img :src="getImageUrl(job.thumbnail_lowongan)" :alt="job.judul_lowongan"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    @error="$event.target.src = '/image/placeholder.webp'" />
                            </div>

                            <!-- Job Header -->
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <h2 class="text-xl lg:text-2xl text-typography-dark font-semibold leading-tight flex-1">
                                        {{ job.judul_lowongan }}
                                    </h2>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1.5 bg-gray-100 rounded-full text-sm font-semibold border border-gray-200">
                                            {{ job.jenis_lowongan }}
                                        </span>
                                        <!-- Status Badge -->
                                        <span :class="`px-3 py-1.5 rounded-full text-sm font-semibold border ${isJobOpen(job.tanggal_dibuka, job.tanggal_ditutup)
                                                ? 'bg-green-50 text-green-700 border-green-200'
                                                : 'bg-red-50 text-red-700 border-red-200'
                                            }`">
                                            {{ isJobOpen(job.tanggal_dibuka, job.tanggal_ditutup) ? 'Dibuka' : 'Ditutup' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Job Description -->
                                <p class="text-gray-600 font-normal leading-relaxed">
                                    {{ truncateText(stripHtml(job.deskripsi_pekerjaan), 180) }}
                                </p>
                            </div>

                            <!-- Job Footer -->
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-wrap gap-6">
                                    <div class="flex items-center gap-3">
                                        <MapPin size="18" class="text-secondary flex-shrink-0" />
                                        <span class="text-gray-700 font-medium">
                                            {{ job.lokasi || "Remote" }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Clock size="18" class="text-secondary flex-shrink-0" />
                                        <span class="text-gray-700 font-medium">
                                            Tutup: {{ formatDate(job.tanggal_ditutup) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <div class="text-sm text-gray-500">
                                        Dibuka: {{ formatDate(job.tanggal_dibuka) }}
                                    </div>
                                    <Link :href="'/lowongan/' + job.slug"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary/90 text-white rounded-lg font-medium transition-all group-hover:translate-x-1">
                                        Lihat Detail
                                        <ArrowRight size="16" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1 && lowongan.length > 0"
                    class="flex justify-center items-center gap-4 mt-10 font-custom text-sm">
                    <!-- Tombol Sebelumnya -->
                    <button
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-xl font-medium transition border"
                        :class="
                            currentPage === 1
                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                        "
                    >
                        Sebelumnya
                    </button>

                    <!-- Indikator halaman -->
                    <div
                        class="px-4 py-2 rounded-xl border border-black text-black font-semibold"
                    >
                        {{ currentPage }} / {{ lastPage }}
                    </div>

                    <!-- Tombol Selanjutnya -->
                    <button
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                        class="px-4 py-2 rounded-xl font-medium transition border"
                        :class="
                            currentPage === lastPage
                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                                : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                        "
                    >
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>