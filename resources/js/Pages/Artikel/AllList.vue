<script setup>
import AOS from "aos";
import "aos/dist/aos.css";
import axios from "axios";
import { ref, onMounted, nextTick } from "vue";
import { LucideSearch, LucideEye } from "lucide-vue-next";

const articles = ref([]);
const categories = ref([]);
const usedCategories = ref([]);
const selectedCategory = ref(null);
const searchQuery = ref("");
const loading = ref(true);
const searching = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
let debounceTimer = null;

onMounted(() => {
    fetchData();
    AOS.init({
        duration: 1000,
        once: false,
        mirror: true,
        disable: false,
        startEvent: 'DOMContentLoaded',
        initClassName: 'aos-init',
        animatedClassName: 'aos-animate',
        useClassNames: false,
        disableMutationObserver: false,
        debounceDelay: 50,
        throttleDelay: 99,
        offset: 120,
        delay: 0,
        easing: 'ease',
    });
});

async function fetchData() {
    loading.value = true;
    await Promise.all([fetchArticles(), fetchCategories()]);
    loading.value = false;
}

async function fetchCategories() {
    try {
        const response = await axios.get("/api/artikel/categories");
        categories.value = response.data.data;
        filterUsedCategories();
    } catch (error) {
    }
}

async function fetchArticles(page = 1) {
    try {
        loading.value = true;
        const params = {
            page,
        };
        if (selectedCategory.value) {
            params.category_id = selectedCategory.value;
        }
        const response = await axios.get("/api/artikel", { params });

        articles.value = response.data.data;
        currentPage.value = response.data.meta?.current_page || 1;
        lastPage.value = response.data.meta?.last_page || 1;

        // Refresh AOS setelah konten dimuat
        await nextTick();
        AOS.refresh();
    } catch (error) {
        articles.value = [];
        usedCategories.value = [];
    } finally {
        loading.value = false;
        searching.value = false;
    }
}

async function searchArticles(query) {
    try {
        loading.value = true;
        const params = { query };
        if (selectedCategory.value) {
            params.category_id = selectedCategory.value;
        }
        const response = await axios.get("/api/artikel/search", { params });
        articles.value = response.data.data || [];

        // Refresh AOS setelah pencarian
        await nextTick();
        AOS.refresh();
    } catch (error) {
        articles.value = [];
        usedCategories.value = [];
    } finally {
        loading.value = false;
        searching.value = false;
    }
}

function filterByCategory(categoryId) {
    selectedCategory.value = categoryId;
    if (searchQuery.value.length > 0) {
        searchArticles(searchQuery.value);
    } else {
        fetchArticles();
    }
}

function handleSearch() {
    if (debounceTimer) clearTimeout(debounceTimer);
    searching.value = true;
    debounceTimer = setTimeout(() => {
        if (searchQuery.value.length === 0) {
            fetchArticles();
        } else {
            searchArticles(searchQuery.value);
        }
    }, 500);
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

// Bersihkan tag HTML untuk ditampilkan dalam preview
function stripHtmlTags(html) {
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
}

function goToPage(page) {
    if (page < 1 || page > lastPage.value) return;
    fetchArticles(page);
}

function filterUsedCategories() {
    if (articles.value.length === 0) {
        usedCategories.value = [];
        return;
    }

    // Ambil ID kategori yang digunakan oleh artikel
    const usedCategoryIds = [
        ...new Set(
            articles.value
                .map((article) => article.kategoriArtikel?.id_kategori_artikel)
                .filter((id) => id)
        ),
    ];

    // Filter kategori yang hanya digunakan
    usedCategories.value = categories.value.filter((category) =>
        usedCategoryIds.includes(category.id_kategori_artikel)
    );

    // Reset kategori yang dipilih jika tidak ada dalam kategori yang digunakan
    if (
        selectedCategory.value !== null &&
        !usedCategoryIds.includes(selectedCategory.value)
    ) {
        selectedCategory.value = null;
    }
}
</script>

<template>
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-6 py-20 font-custom text-black">
        <div class="flex flex-col lg:grid lg:grid-cols-4 gap-10">
            <!-- Judul "Semua Artikel" -->
            <div class="order-1 lg:order-1 lg:col-span-4">
                <h2 class="text-4xl mb-4">Semua Artikel</h2>
                <p class="text-lg font-normal leading-relaxed">
                    Temukan berbagai artikel menarik yang membahas topik-topik
                    terkini, tips, dan wawasan dari berbagai bidang. Jelajahi
                    pengetahuan baru dan inspirasi dari tulisan-tulisan kami.
                </p>
            </div>

            <!-- Sidebar: Search & Kategori -->
            <aside class="order-2 lg:order-1 lg:col-span-1 w-full flex flex-col gap-6 bg-gray-50 p-6 rounded-2xl">
                <div class="relative w-full">
                    <LucideSearch class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="Cari artikel..."
                        class="w-full px-10 py-2 border rounded-lg bg-white" />
                </div>
                <div class="flex flex-col gap-2">
                    <button :class="[
                        'text-left px-4 py-2 rounded-xl border transition-all duration-200',
                        selectedCategory === null
                            ? 'bg-black text-white'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white',
                    ]" @click="filterByCategory(null)">
                        Semua Kategori
                    </button>
                    <button v-for="cat in usedCategories" :key="cat.id_kategori_artikel" :class="[
                        'text-left px-4 py-2 rounded-xl border transition-all duration-200',
                        selectedCategory === cat.id_kategori_artikel
                            ? 'bg-black text-white'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white',
                    ]" @click="filterByCategory(cat.id_kategori_artikel)">
                        {{ cat.nama_kategori_artikel }}
                    </button>
                </div>
            </aside>

            <!-- Daftar Artikel -->
            <div class="order-3 lg:order-2 lg:col-span-3 flex flex-col gap-6 bg-gray-50 p-6 rounded-2xl">
                <!-- Skeleton -->
                <div v-if="loading" class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                    <div v-for="i in 4" :key="i" class="animate-pulse flex flex-col gap-4 bg-white p-6 rounded-2xl">
                        <div class="bg-gray-300 rounded-2xl h-64 w-full"></div>
                        <div class="h-5 bg-gray-300 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>

                <!-- Tidak ada artikel -->
                <div v-else-if="articles.length === 0"
                    class="col-span-full flex flex-col items-center justify-center gap-6 py-20 text-center bg-white rounded-2xl">
                    <div class="flex flex-col lg:flex-row items-center gap-6 text-left">
                        <img src="/image/empty.svg" alt="Empty State"
                            class="w-40 h-40 lg:w-96 lg:h-96 object-contain" />
                        <div>
                            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 font-custom">
                                Yah, artikelnya tidak ditemukan
                            </h3>
                            <p class="text-sm md:text-base text-gray-500 font-custom">
                                Coba kata kunci lain atau pilih kategori
                                berbeda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Daftar artikel -->
                <div v-if="!loading && articles.length > 0" class="flex flex-col gap-6">
                    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2">
                        <article v-for="(post, index) in articles" :key="index"
                            class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-sm transition duration-300"
                            data-aos="fade-up" :data-aos-delay="index * 100">
                            <img :src="getImageUrl(post.thumbnail_artikel)" alt="Blog post image"
                                class="rounded-2xl h-64 w-full object-cover" />
                            <div class="flex items-center gap-4">
                                <span class="px-2.5 py-1 bg-gray-100 rounded-full border text-sm font-semibold">
                                    {{
                                        post.kategoriArtikel
                                            ?.nama_kategori_artikel ||
                                        "Tanpa Kategori"
                                    }}
                                </span>
                                <span class="flex items-center gap-1 text-sm font-semibold text-gray-600">
                                    <LucideEye class="w-4 h-4" />
                                    {{ post.jumlah_view ?? "—" }}× dilihat
                                </span>
                            </div>
                            <a :href="`/artikel/${post.slug}`" class="hover:underline group">
                                <h3
                                    class="text-2xl leading-snug group-hover:text-gray-700 transition-colors duration-200">
                                    {{ post.judul_artikel }}
                                </h3>
                            </a>
                            <p class="text-base leading-normal text-gray-600">
                                {{
                                    stripHtmlTags(post.konten_artikel).slice(
                                        0,
                                        70
                                    )
                                }}...
                            </p>
                            <div class="flex items-center gap-4 mt-2 pt-4 border-t border-gray-100">
                                <img class="w-12 h-12 rounded-full object-cover"
                                    :src="getImageUrl(post.user.foto_profil, true)" alt="Foto profil" />
                                <div class="flex flex-col">
                                    <div class="text-sm font-semibold leading-tight">
                                        {{ post.user.name || "Anonim" }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span>{{
                                            new Date(post.created_at).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "short",
                                        year: "numeric",
                                        })
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="!loading && lastPage > 1"
                    class="flex justify-center items-center gap-4 mt-6 text-xs font-custom bg-white p-4 rounded-2xl">
                    <!-- Tombol Sebelumnya -->
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === 1
                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                            ">
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
                            : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                            ">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
