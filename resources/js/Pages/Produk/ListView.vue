<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";
import { Search, Tag, Wallet, ChevronLeft, ChevronRight } from "lucide-vue-next";
import AOS from "aos";
import "aos/dist/aos.css";


const produk = ref([]);
const searchQuery = ref("");
const searching = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
let debounceTimer = null;
const { props } = usePage();
const isLoading = ref(true);

// State untuk background hero
const heroBackground = ref("/image/placeholder.webp");

onMounted(() => {
    AOS.init({
        duration: 1000,
        once: false,
        mirror: true,
    });
    fetchData();
    fetchKategori();
    fetchLatestProduct();
    setTimeout(() => {
        isLoading.value = false;
    }, 4000);
});

const selectedCategory = ref(null);

async function fetchData() {
    isLoading.value = true;
    await Promise.all([fetchProduk(), fetchKategori()]);
    isLoading.value = false;
}

const filterByCategory = (categoryId) => {
    selectedCategory.value = categoryId;
    currentPage.value = 1;
    fetchProduk(searchQuery.value, categoryId, 1);
};

const fetchProduk = async (query = "", categoryId = null, page = 1) => {
    try {
        searching.value = true;
        let url =
            query.length > 0 || categoryId !== null
                ? "/api/produk/search"
                : "/api/produk";
        const params = { page };
        if (query.length > 0) params.query = query;
        if (categoryId !== null) params.category_id = categoryId;

        const response = await axios.get(url, { params });

        produk.value = response.data.data;
        currentPage.value = response.data.meta?.current_page || 1;
        lastPage.value = response.data.meta?.last_page || 1;

        // Set random background dari produk 
        if (response.data.data.length > 0) {
            const randomIndex = Math.floor(Math.random() * response.data.data.length);
            const randomProduct = response.data.data[randomIndex];
            heroBackground.value = getImageUrl(randomProduct.thumbnail_produk);
        }

    } catch (error) {
        produk.value = [];
        usedCategories.value = [];
    } finally {
        searching.value = false;
    }
};

const goToPage = (page) => {
    if (page < 1 || page > lastPage.value) return;
    fetchProduk(searchQuery.value, selectedCategory.value, page);
};

const handleSearch = () => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        currentPage.value = 1;
        fetchProduk(searchQuery.value, selectedCategory.value, 1);
    }, 500);
};

const categories = ref([]);
const usedCategories = ref([]);

async function fetchKategori() {
    try {
        const response = await axios.get("/api/produk/categories");
        categories.value = response.data.data;
        filterUsedCategories();
    } catch (error) {
    }
}

function filterUsedCategories() {
    if (produk.value.length === 0) {
        usedCategories.value = [];
        return;
    }

    // Ambil ID kategori yang digunakan oleh produk
    const usedCategoryIds = [
        ...new Set(
            produk.value
                .map((product) => product.kategori_produk?.id_kategori_produk)
                .filter((id) => id)
        ),
    ];

    // Filter kategori yang hanya digunakan
    usedCategories.value = categories.value.filter((category) =>
        usedCategoryIds.includes(category.id_kategori_produk)
    );

    // Reset kategori yang dipilih jika tidak ada dalam kategori yang digunakan
    if (
        selectedCategory.value !== null &&
        !usedCategoryIds.includes(selectedCategory.value)
    ) {
        selectedCategory.value = null;
    }
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

const item = ref(null);
const activeImageIndex = ref(0);

const fetchLatestProduct = async () => {
    try {
        const response = await axios.get("/api/produk/latest");
        item.value = response.data.data;
    } catch (error) {
        item.value = null;
    }
};

const nextImage = () => {
    if (!item.value || !item.value.thumbnail_produk) return;
    activeImageIndex.value =
        (activeImageIndex.value + 1) % item.value.thumbnail_produk.length;
};

const prevImage = () => {
    if (!item.value || !item.value.thumbnail_produk) return;
    activeImageIndex.value =
        (activeImageIndex.value - 1 + item.value.thumbnail_produk.length) %
        item.value.thumbnail_produk.length;
};

function formatRupiah(value) {
    const numberValue = Number(value);
    if (isNaN(numberValue)) return value;
    return `Rp${numberValue.toLocaleString("id-ID")},00`;
}
</script>

<template>
    <AppLayout>
        <!-- Hero Section with Search - Updated to match case study style -->
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
                        PRODUK
                    </div>
                    <h1
                        class="text-4xl lg:text-6xl font-normal font-custom leading-tight"
                    >
                        Belanja Praktis,<br />Hasil Maksimal
                    </h1>
                    <p
                        class="text-lg font-normal font-custom leading-relaxed max-w-3xl"
                    >
                        Katalog produk dan jasa. Temukan
                        kebutuhanmu dengan mudah dan cepat hanya dalam satu
                        tempat.
                    </p>
                </div>

                <!-- Search form - Updated to match case study style -->
                <div class="w-full max-w-2xl flex flex-col items-center gap-4">
                    <div class="w-full relative">
                        <input
                            v-model="searchQuery"
                            @input="handleSearch"
                            type="text"
                            placeholder="Cari produk berdasarkan nama, kategori, atau deskripsi..."
                            class="w-full px-6 py-4 rounded-full bg-white/10 backdrop-blur-sm text-white placeholder-white/60 outline outline-1 outline-white/30 focus:outline-white/70 focus:ring-0 font-custom pr-16"
                        />
                        <button
                            @click="fetchProduk(searchQuery)"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white text-black hover:bg-opacity-80 transition"
                        >
                            <Search class="w-5 h-5" />
                        </button>
                    </div>
                    <p
                        class="text-sm font-normal font-custom leading-none opacity-70"
                    >
                        Coba cari menggunakan kata kunci
                    </p>
                </div>
            </div>
        </div>

        <div
            class="max-w-screen-xl mx-auto px-4 lg:px-8 py-8 flex flex-col items-center gap-20 overflow-hidden"
        >
            <!-- Filter Kategori -->
            <div
                v-if="usedCategories.length > 0"
                class="w-full overflow-x-auto mt-6" data-aos="fade-right"
            >
                <div class="flex gap-2 font-custom text-sm whitespace-nowrap">
                    <!-- Skeleton Kategori -->
                    <template v-if="isLoading">
                        <div
                            v-for="n in 4"
                            :key="n"
                            class="px-4 py-2 rounded-xl bg-gray-200 animate-pulse w-24 h-9"
                        ></div>
                    </template>

                    <!-- Tombol Kategori asli -->
                    <template v-else>
                        <button
                            @click="filterByCategory(null)"
                            class="px-4 py-2 rounded-xl font-medium transition border"
                            :class="
                                selectedCategory === null
                                    ? 'bg-black text-white'
                                    : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                            "
                        >
                            Semua
                        </button>

                        <button
                            v-for="category in usedCategories"
                            :key="category.id_kategori_produk"
                            @click="
                                filterByCategory(category.id_kategori_produk)
                            "
                            class="px-4 py-2 rounded-xl font-medium transition border"
                            :class="
                                selectedCategory === category.id_kategori_produk
                                    ? 'bg-black text-white'
                                    : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                            "
                        >
                            {{ category.nama_kategori_produk }}
                        </button>
                    </template>
                </div>
            </div>

            <!-- Grid Produk -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full font-custom"
            >
                <!-- Skeleton Produk -->
                <template v-if="isLoading">
                    <div
                        v-for="n in 6"
                        :key="n"
                        class="relative p-6 rounded-2xl bg-gray-200 animate-pulse flex flex-col h-[480px] overflow-hidden"
                    >
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/20 to-transparent z-10"
                        />
                        <div
                            class="relative z-20 mt-auto text-white flex flex-col gap-4"
                        >
                            <div class="h-6 w-2/3 bg-white/50 rounded"></div>
                            <div class="h-4 w-3/4 bg-white/40 rounded"></div>
                            <div class="h-4 w-1/2 bg-white/40 rounded"></div>
                            <div
                                class="h-8 w-32 bg-white/30 rounded-full mt-4"
                            ></div>
                        </div>
                    </div>
                </template>

                <!-- Produk kosong -->
                <div
                    v-if="!isLoading && produk.length === 0"
                    class="col-span-full flex flex-col items-center justify-center gap-6 py-20 text-center"
                >
                    <div
                        class="flex flex-col lg:flex-row items-center gap-6 text-left"
                    >
                        <img
                            src="/image/empty.svg"
                            alt="Empty State"
                            class="w-40 h-40 lg:w-96 lg:h-96 object-contain"
                        />
                        <div>
                            <h3
                                class="text-xl md:text-2xl font-semibold text-gray-700 font-custom"
                            >
                                Yah, produknya tidak ditemukan
                            </h3>
                            <p
                                class="text-sm md:text-base text-gray-500 font-custom"
                            >
                                Coba kata kunci lain atau pilih kategori
                                berbeda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Produk asli -->
                <template v-else>
                    <div
                        v-for="(item, index) in produk"
                        :key="item.id_produk"
                        class="relative p-6 rounded-2xl bg-cover bg-center bg-no-repeat flex flex-col h-[480px] overflow-hidden transform transition duration-300 hover:scale-105"
                        :style="
                            item.thumbnail_produk &&
                            item.thumbnail_produk.length > 0
                                ? `background-image: url('${getImageUrl(
                                      item.thumbnail_produk
                                  )}')`
                                : 'background-color: #ccc'
                        "
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                    >
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent z-10"
                        />

                        <div
                            class="relative z-20 mt-auto text-white flex flex-col gap-2"
                        >
                            <div class="text-2xl font-normal">
                                {{ item.nama_produk }}
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 text-sm font-normal">
                                    <span class="flex items-center gap-2 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-full border border-white/20">
                                        <Tag class="w-4 h-4" />
                                        <span>{{ item.kategori_produk.nama_kategori_produk }}</span>
                                    </span>
                                </div>
                                <!-- Harga hanya ditampilkan jika tampilkan_harga true -->
                                <div
                                    v-if="item.tampilkan_harga"
                                    class="flex items-center gap-2 text-sm font-normal"
                                >
                                    <span class="flex items-center gap-2 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-full border border-white/20">
                                        <Wallet class="w-4 h-4" />
                                        <span>{{ formatRupiah(item.harga_produk) }}</span>
                                    </span>
                                </div>
                            </div>
                            <a
                                :href="`/produk/${item.slug}`"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2 mt-4 bg-white/30 backdrop-blur-sm text-white font-medium text-sm rounded-full hover:bg-white hover:text-black transition-all duration-300"
                            >
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div
                v-if="lastPage > 1"
                class="flex justify-center items-center gap-4 mt-10 font-custom text-sm"
            >
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

            <div
                v-if="item"
                class="w-full flex flex-col lg:flex-row justify-between items-start lg:items-end" data-aos="fade-up"
            >
                <div class="w-full lg:w-3/4 flex flex-col gap-4">
                    <div
                        class="text-Color-Scheme-1-Text text-base font-semibold font-custom"
                    >
                        Belum puas?
                    </div>
                    <div class="flex flex-col gap-4">
                        <div
                            class="text-Color-Scheme-1-Text text-5xl font-normal font-custom leading-tight"
                        >
                            Produk Terbaru
                        </div>
                        <div
                            class="text-Color-Scheme-1-Text text-lg font-normal font-custom leading-relaxed"
                        >
                            Coba lihat keluaran terbaru kami.
                        </div>
                    </div>
                </div>

                <div class="mt-8 lg:mt-0">
                    <p
                        v-if="item?.created_at"
                        class="px-6 py-2.5 bg-Opacity-Neutral-Darkest-5/5 rounded-full outline outline-1 text-Color-Neutral-Darkest text-base font-medium font-custom"
                    >
                        Rilis:
                        {{
                            new Date(item.created_at).toLocaleDateString(
                                "id-ID",
                                {
                                    day: "numeric",
                                    month: "long",
                                    year: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                }
                            )
                        }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Product Terbaru Detail -->
        <div
            v-if="item"
            class="w-full px-4 lg:px-16 py-10 lg:py-20 bg-white flex flex-col items-start gap-20 font-custom"
        >
            <div
                class="flex flex-col lg:flex-row gap-10 lg:gap-20 w-full max-w-7xl mx-auto"
            >
                <!-- Left: Product Image -->
                <div class="w-full lg:w-1/2 flex justify-center" data-aos="zoom-in-up">
                    <div
                        class="relative w-full max-w-[600px] aspect-[4/3] overflow-hidden rounded-2xl"
                    >
                        <img
                            class="absolute inset-0 w-full h-full object-cover"
                            :src="
                                getImageUrl(
                                    item.thumbnail_produk?.[activeImageIndex]
                                )
                            "
                            alt="Product Image"
                        />

                        <!-- Nav Arrows -->
                        <div
                            @click="prevImage"
                            class="absolute left-2 top-1/2 -translate-y-1/2 p-2 bg-black/40 hover:bg-black/60 rounded-full cursor-pointer transition"
                        >
                            <ChevronLeft class="w-5 h-5 text-white" />
                        </div>
                        <div
                            @click="nextImage"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-black/40 hover:bg-black/60 rounded-full cursor-pointer transition"
                        >
                            <ChevronRight class="w-5 h-5 text-white" />
                        </div>

                        <!-- Pagination Dots -->
                        <div
                            class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2"
                        >
                            <div
                                v-for="(img, i) in item.thumbnail_produk"
                                :key="i"
                                :class="
                                    i === activeImageIndex
                                        ? 'w-2.5 h-2.5 rounded-full bg-white scale-110'
                                        : 'w-2.5 h-2.5 rounded-full bg-white opacity-30'
                                "
                            />
                        </div>
                    </div>
                </div>

                <!-- Right: Product Info -->
                <div class="w-full lg:w-1/2 flex flex-col gap-8" data-aos="zoom-in-up">
                    <!-- Title & Price -->
                    <h1 class="text-4xl text-secondary font-bold">
                        {{ item.nama_produk }}
                    </h1>
                    <div class="flex items-center gap-4">
                        <span
                            v-if="item.tampilkan_harga"
                            class="text-xl font-semibold"
                        >
                            {{ formatRupiah(item.harga_produk) }}
                        </span>
                        <div class="flex items-center gap-3">
                            <!-- Border hanya ditampilkan jika ada harga -->
                            <div
                                v-if="item.tampilkan_harga"
                                class="h-6 border-l"
                            />
                            <span class="text-xl flex items-center gap-1">
                                <Tag class="w-4" />
                                {{ item.kategori_produk.nama_kategori_produk }}
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-base text-gray-700">
                        {{ item.deskripsi_produk }}
                    </p>

                    <!-- Buy Button -->
                    <div class="space-y-4">
                        <a
                            :href="item.link_produk"
                            target="_blank"
                            class="block text-center w-full px-6 py-2.5 bg-secondary hover:bg-black transition duration-500 text-white font-medium rounded-full"
                        >
                            Beli di marketplace
                        </a>
                        <p class="text-xs text-center text-gray-500">
                            Anda akan diarahkan ke halaman baru
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
