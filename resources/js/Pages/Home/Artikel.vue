<script setup>
import axios from "axios";
import { ref, onMounted, nextTick } from "vue";
import AOS from "aos";
import "aos/dist/aos.css";

const articles = ref([]);
const categories = ref([]);
const selectedCategory = ref(null);
const loading = ref(true);
const searching = ref(false);

onMounted(() => {
    fetchCategories();
    fetchArticles();
    AOS.init({
        duration: 1000,
        once: false,
    });
});

async function fetchCategories() {
    try {
        const response = await axios.get("/api/artikel/categories");
        categories.value = response.data.data;
    } catch (error) {
    }
}

async function fetchArticles() {
    try {
        loading.value = true;
        const params = {};
        if (selectedCategory.value) {
            params.category_id = selectedCategory.value;
        }

        const response = await axios.get("/api/artikel", { params });
        const allArticles = response.data.data.map((item) => {
            const text = stripHtmlTags(item.konten_artikel || "");
            return {
                ...item,
                cleanText: text, // disimpan agar tidak proses ulang untuk preview
            };
        });

        articles.value = getRandomArticles(allArticles, 3);
    } catch (error) {
    } finally {
        loading.value = false;
        searching.value = false;

        // Refresh AOS supaya animasi jalan setelah data berubah
        await nextTick();
        AOS.refresh();
    }
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

function getRandomArticles(array, count) {
    const shuffled = array.sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
}

function formatDate(date) {
    const options = { day: "2-digit", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
}

function stripHtmlTags(html) {
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
}
</script>

<template>
    <div class="w-full px-6 lg:px-16 py-28 flex flex-col gap-20 overflow-hidden font-custom bg-secondary">
        <div class="w-full max-w-screen-xl mx-auto flex flex-col gap-20">
            <!-- Header -->
            <div class="w-full flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6"
                data-aos="fade-down">
                <div class="flex flex-col gap-4 text-white">
                    <div class="text-base font-semibold leading-normal">Artikel</div>
                    <div class="flex flex-col gap-6">
                        <div class="text-5xl font-normal leading-[57.60px]">
                            Wawasan terbaru dari kami
                        </div>
                        <div class="text-lg font-normal leading-relaxed">
                            Baca artikel-artikel pilihan seputar bisnis, inovasi, dan kegiatan
                            terbaru perusahaan.
                        </div>
                    </div>
                </div>
                <a href="/artikel">
                    <div class="px-6 py-2.5 rounded-full outline outline-1 text-white outline-white hover:bg-white hover:text-secondary transition duration-200 flex justify-center items-center gap-2"
                        data-aos="fade-left">
                        <div class="text-base font-medium leading-normal">Lihat semua</div>
                    </div>
                </a>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 w-full">
                <div v-for="article in articles" :key="article.id"
                    class="rounded-2xl border border-Color-Scheme-1-Border/20 flex flex-col overflow-hidden h-full"
                    data-aos="fade-up">
                    <img class="w-full h-72 object-cover flex-shrink-0" :src="getImageUrl(article.thumbnail_artikel)"
                        alt="thumbnail artikel" />
                    <div class="flex-1 p-6 flex flex-col justify-between bg-white">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <div
                                    class="inline-flex items-center rounded-full border border-Color-Scheme-1-Border/20 px-4 py-1 w-fit">
                                    <span class="text-Color-Neutral-Darkest text-base font-semibold leading-tight">
                                        {{ article.kategoriArtikel?.nama_kategori_artikel || "Tanpa Kategori" }}
                                    </span>
                                </div>
                                <a :href="`/artikel/${article.slug}`"
                                    class="text-Color-Scheme-1-Text text-2xl font-normal pb-2 hover:underline">
                                    {{ article.judul_artikel }}
                                </a>
                                <div class="text-Color-Scheme-1-Text text-base font-normal line-clamp-3">
                                    {{ article.cleanText.slice(0, 70) || "Tidak ada ringkasan konten." }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-6">
                            <img class="w-12 h-12 rounded-full" :src="getImageUrl(article.user.foto_profil)"
                                alt="foto profil user" />
                            <div class="flex flex-col">
                                <div class="text-Color-Scheme-1-Text text-sm font-semibold leading-tight">
                                    {{ article.user.name || "Anonim" }}
                                </div>
                                <div class="flex items-center gap-2 text-sm text-Color-Scheme-1-Text">
                                    <span>
                                        Diupload pada
                                        {{
                                            new Date(article.created_at).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "short",
                                        year: "numeric",
                                        })
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dummy Cards -->
                <div v-for="n in 3 - articles.length" :key="'dummy-' + n"
                    class="bg-white rounded-2xl border border-dashed border-gray-300 flex flex-col justify-center items-center text-center p-6 min-h-[28rem] text-gray-400"
                    data-aos="fade-up">
                    <span class="text-xl font-medium">Artikel akan datang</span>
                    <span class="text-sm mt-2">Sedang dipersiapkan untuk Anda</span>
                </div>
            </div>
        </div>
    </div>
</template>
