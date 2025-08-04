<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Download, Search } from "lucide-vue-next"; // add Search icon
import AOS from "aos";

const unduhanList = ref([]);
const categories = ref([]);
const usedCategories = ref([]);
const mostDownloaded = ref([]);
const search = ref("");
const selectedCategory = ref(null);
// Add pagination state
const currentPage = ref(1);
const lastPage = ref(1);
const isLoading = ref(true);
const isSearching = ref(false); // Separate loading state for search results
let debounceTimer = null;

const fetchUnduhan = async () => {
    isSearching.value = true; // Use separate loading state for search results
    const params = { page: currentPage.value };
    if (search.value) params.query = search.value;
    if (selectedCategory.value) params.category_id = selectedCategory.value;

    try {
        const { data } = await axios.get("/api/unduhan/search", { params });
        unduhanList.value = data.data;
        // Update pagination info
        currentPage.value = data.meta?.current_page || 1;
        lastPage.value = data.meta?.last_page || 1;
    } catch (error) {
        unduhanList.value = [];
        usedCategories.value = [];
    } finally {
        isSearching.value = false; // End search loading state
    }
};

const fetchCategories = async () => {
    try {
        const { data } = await axios.get("/api/unduhan/categories");
        categories.value = data.data;
        filterUsedCategories();
    } catch (error) {
        categories.value = [];
    }
};

const fetchMostDownloaded = async () => {
    try {
        const { data } = await axios.get("/api/unduhan/most-downloaded");
        mostDownloaded.value = data.data;
    } catch (error) {
        mostDownloaded.value = [];
    }
};

function filterUsedCategories() {
    if (unduhanList.value.length === 0) {
        usedCategories.value = [];
        return;
    }

    // Ambil ID kategori yang digunakan oleh unduhan
    const usedCategoryIds = [
        ...new Set(
            unduhanList.value
                .map((unduhan) => unduhan.kategori?.id)
                .filter((id) => id)
        ),
    ];

    // Filter kategori yang hanya digunakan
    usedCategories.value = categories.value.filter((category) =>
        usedCategoryIds.includes(category.id_kategori_unduhan)
    );

    // Reset kategori yang dipilih jika tidak ada dalam kategori yang digunakan
    if (
        selectedCategory.value !== null &&
        !usedCategoryIds.includes(selectedCategory.value)
    ) {
        selectedCategory.value = null;
    }
}

const filterByCategory = (id) => {
    selectedCategory.value = id;
    currentPage.value = 1;
    fetchUnduhan();
};

// Add page navigation
const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    currentPage.value = page;
    fetchUnduhan();
};

// Add debounced search
const handleSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        currentPage.value = 1;
        fetchUnduhan();
    }, 500);
};

const downloadFile = async (item) => {
    try {
        // Panggil endpoint yang mengirim file binary
        const response = await axios.get(`/api/unduhan/download/${item.id}`, {
            responseType: "blob", // penting untuk file binary
        });

        // Update local count
        item.jumlah_unduhan++;

        // Buat URL objek dari blob
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;

        // Tentukan nama file unduhan
        const extension = item.lokasi_file.split(".").pop();
        link.setAttribute("download", item.nama_unduhan + "." + extension);

        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch { }
};
const getFileIcon = (ext) => {
    switch (ext) {
        case "doc":
        case "docx":
            return "/image/unduhan/doc.svg";
        case "xls":
        case "xlsx":
            return "/image/unduhan/xls.svg";
        case "pdf":
            return "/image/unduhan/pdf.svg";
        case "ppt":
        case "pptx":
            return "/image/unduhan/slides.svg";
        default:
            return "/image/unduhan/file.svg";
    }
};

const stripToHtml = (html) => {
    if (!html) return "";

    // Create a temporary div to parse HTML
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;

    // Return text content (strips HTML tags)
    return tempDiv.textContent || tempDiv.innerText || "";
};

onMounted(() => {
    AOS.init({
        duration: 1000,
        once: false,
        mirror: true,
    });
    fetchData();
    fetchCategories();
    fetchMostDownloaded();

    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});

async function fetchData() {
    isLoading.value = true;
    await Promise.all([fetchUnduhan(), fetchCategories()]);
    isLoading.value = false;
}
</script>

<template>
    <AppLayout>
        <div
            class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-16 py-28 text-black font-custom flex flex-col gap-20 overflow-hidden">
            <div class="w-full flex flex-col gap-4">
                <div class="text-base font-semibold uppercase text-secondary">Unduhan</div>
                <div class="flex flex-col items-start gap-4">
                    <h1 class="text-4xl sm:text-5xl font-normal leading-tight">
                        Download File Sesuai Kebutuhanmu
                    </h1>
                    <p class="text-lg leading-relaxed">
                        Temukan dokumen, e-book, panduan, atau file penting
                        lainnya di sini.
                    </p>
                </div>
            </div>

            <!-- Categories with horizontal scrolling and search - Always show unless initial loading -->
            <div v-if="!isLoading || categories.length > 0" class="w-full flex flex-col gap-4">
                <div class="flex flex-wrap justify-between items-center">
                    <h2 class="text-xl font-semibold mb-2">Kategori</h2>
                    <div class="flex items-center gap-2 relative">
                        <input v-model="search" @input="handleSearch" type="text" placeholder="Cari file..."
                            class="px-3 py-2 rounded-xl text-sm border border-gray-300 focus:ring-gray-800 focus:border-gray-800" />
                        <button @click="fetchUnduhan"
                            class="px-3 py-2 rounded-xl bg-gray-800 text-white text-sm flex items-center gap-1">
                            <Search class="w-4 h-4" />
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Horizontal scrollable categories -->
                <div class="w-full overflow-x-auto">
                    <div class="flex gap-2 whitespace-nowrap py-1">
                        <!-- Skeleton loading for categories -->
                        <template v-if="isLoading && !categories.length">
                            <div v-for="n in 4" :key="n"
                                class="px-4 py-2 rounded-xl bg-gray-200 animate-pulse w-24 h-9"></div>
                        </template>

                        <!-- Actual categories -->
                        <template v-else>
                            <button class="px-4 py-2 rounded-xl text-sm font-medium transition border" :class="selectedCategory === null
                                    ? 'bg-gray-800 text-white border-gray-800'
                                    : 'bg-white text-gray-800 border-gray-300'
                                " @click="filterByCategory(null)">
                                Semua
                            </button>
                            <!-- Show all categories, not just used ones when searching -->
                            <button v-for="cat in (search || selectedCategory !== null ? categories : usedCategories)" 
                                :key="cat.id_kategori_unduhan"
                                class="px-4 py-2 rounded-xl text-sm font-medium transition border" 
                                :class="selectedCategory === cat.id_kategori_unduhan
                                        ? 'bg-gray-800 text-white border-gray-800'
                                        : 'bg-white text-gray-800 border-gray-300'
                                    " 
                                @click="filterByCategory(cat.id_kategori_unduhan)">
                                {{ cat.nama_kategori_unduhan }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Unduhan List -->
            <div class="w-full flex flex-col gap-8">
                <!-- Loading skeleton -->
                <template v-if="isSearching">
                    <div v-for="n in 3" :key="`skeleton-${n}`"
                        class="bg-white border border-gray-200 rounded-2xl p-8 animate-pulse">
                        <div class="flex flex-col lg:flex-row gap-8">
                            <div class="w-full lg:w-48 flex flex-col gap-4">
                                <div class="h-20 w-20 bg-gray-200 rounded-xl mx-auto lg:mx-0"></div>
                                <div class="h-4 w-20 bg-gray-200 rounded mx-auto lg:mx-0"></div>
                            </div>
                            <div class="flex-1 flex flex-col gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-6 w-24 bg-gray-200 rounded-full"></div>
                                    <div class="h-5 w-16 bg-gray-200 rounded"></div>
                                </div>
                                <div class="h-8 w-3/4 bg-gray-200 rounded"></div>
                                <div class="h-4 w-full bg-gray-200 rounded"></div>
                                <div class="h-4 w-2/3 bg-gray-200 rounded"></div>
                                <div class="h-10 w-40 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty state - only when no results found but not initial loading -->
                <div v-else-if="unduhanList.length === 0 && !isLoading"
                    class="flex flex-col items-center justify-center gap-6 py-20 text-center">
                    <div class="flex flex-col lg:flex-row items-center gap-6 text-left">
                        <img src="/image/empty.svg" alt="Empty State"
                            class="w-40 h-40 lg:w-96 lg:h-96 object-contain" />
                        <div>
                            <h3 class="text-xl md:text-2xl font-semibold text-gray-700">
                                {{ search || selectedCategory !== null ? 'Yah, file tidak ditemukan' : 'Belum ada file unduhan' }}
                            </h3>
                            <p class="text-sm md:text-base text-gray-500">
                                {{ search || selectedCategory !== null 
                                    ? 'Coba ubah kata kunci pencarian atau pilih kategori lain.'
                                    : 'File unduhan akan muncul di sini setelah ditambahkan.'
                                }}
                            </p>
                            <!-- Reset filter button when searching -->
                            <button v-if="search || selectedCategory !== null"
                                @click="resetFilters"
                                class="mt-4 px-4 py-2 bg-gray-800 text-white rounded-xl text-sm font-medium hover:bg-gray-700 transition-colors">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actual unduhan list -->
                <div v-else-if="unduhanList.length > 0" v-for="item in unduhanList" :key="item.id"
                    class="bg-white border border-gray-200 rounded-2xl p-8 hover:border-gray-300 transition-colors duration-300 "
                    data-aos="fade-right" :data-aos-delay="item * 100">
                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- File Info Card -->
                        <div class="w-full lg:w-48 flex flex-col items-center lg:items-start gap-4">
                            <!-- Large File Icon -->
                            <div class="relative">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                                    <img :src="getFileIcon(item.jenis_file)" alt="File icon" class="w-10 h-10" />
                                </div>
                                <!-- File format badge -->
                                <div
                                    class="absolute -bottom-2 -right-2 bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded-md">
                                    {{ item.jenis_file?.toUpperCase() || 'FILE' }}
                                </div>
                            </div>

                            <!-- File size -->
                            <div class="text-center lg:text-left">
                                <p class="text-sm font-medium text-gray-600">Ukuran File</p>
                                <p class="text-lg font-semibold text-gray-800">
                                    {{ item.ukuran_file || 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 flex flex-col gap-6">
                            <!-- Meta info -->
                            <div class="flex flex-wrap items-center gap-4">
                                <span
                                    class="px-3 py-1.5 bg-gray-100 rounded-full border text-sm font-semibold text-gray-700">
                                    {{ item.kategori?.nama_kategori_unduhan || "Tanpa Kategori" }}
                                </span>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <Download class="w-4 h-4" />
                                    <span class="font-medium">{{ item.jumlah_unduhan }}x diunduh</span>
                                </div>
                            </div>

                            <!-- Title and description -->
                            <div class="flex flex-col gap-3">
                                <h2 class="text-2xl lg:text-3xl font-semibold text-gray-900 leading-tight">
                                    {{ item.nama_unduhan }}
                                </h2>
                                <p class="text-gray-600 leading-relaxed line-clamp-3">
                                    {{ stripToHtml(item.deskripsi) }}
                                </p>
                            </div>

                            <!-- Download button -->
                            <div class="flex items-center justify-between">
                                <button @click="downloadFile(item)"
                                    class="bg-white hover:bg-gray-800 text-black hover:text-white border-2 border-gray-200 px-6 py-3 rounded-full font-medium transition-colors duration-300 flex items-center gap-2">
                                    <Download class="w-5 h-5" />
                                    Unduh Sekarang
                                </button>

                                <!-- Additional file info -->
                                <div class="hidden md:flex flex-col text-right text-sm text-gray-500">
                                    <span>Format: {{ item.jenis_file?.toUpperCase() || 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1" class="flex justify-center items-center gap-4 mt-10 font-custom text-sm">
                <!-- Previous Button -->
                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === 1
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                        ">
                    Sebelumnya
                </button>

                <!-- Page indicator -->
                <div class="px-4 py-2 rounded-xl border border-black text-black font-semibold">
                    {{ currentPage }} / {{ lastPage }}
                </div>

                <!-- Next Button -->
                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                    class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === lastPage
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                        ">
                    Selanjutnya
                </button>
            </div>
        </div>
    </AppLayout>
</template>
