<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { LucideEye, LucideCalendar, LucideUser } from "lucide-vue-next";

const featuredArticles = ref([]);
const loading = ref(true);

onMounted(() => {
    fetchFeaturedArticles();
});

async function fetchFeaturedArticles() {
    try {
        const response = await axios.get("/api/artikel/featured");
        featuredArticles.value = response.data.data || [];
    } catch (error) {
    } finally {
        loading.value = false;
    }
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

// Truncate text
function truncateText(text, limit = 150) {
    if (text.length <= limit) return text;
    return text.substring(0, limit) + "...";
}
</script>

<template>
    <div
        class="w-full px-4 sm:px-6 lg:px-6 py-28 max-w-screen-xl mx-auto flex flex-col gap-20 overflow-hidden font-custom">
        <!-- Heading Section -->
        <div class="w-full mb-12 flex flex-col gap-4">
            <div class="text-secondary text-base font-semibold font-custom uppercase tracking-wider">
                Artikel
            </div>
            <h2 class="text-3xl lg:text-5xl font-normal font-custom leading-tight">
                Temukan Berbagai Artikel Menarik
            </h2>
            <p class="text-lg font-normal leading-relaxed text-gray-600 max-w-2xl">
                Mulai petualangan pengetahuan dengan artikel-artikel penuh ide segar dan cerita menarik yang
                membangkitkan semangat.
            </p>
        </div>

        <!-- Featured Blog Section -->
        <div class="flex flex-col gap-12" v-if="!loading">
            <div class="flex items-center justify-between">
                <h2 v-if="featuredArticles.length" class="text-3xl lg:text-4xl font-normal text-gray-900">
                    Artikel Terpopuler
                </h2>
                <div class="hidden lg:block w-24 h-1 bg-gradient-to-r from-secondary to-gray-300 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" v-if="featuredArticles.length">
                <!-- Large Featured Blog -->
                <div class="lg:col-span-7 group" v-if="featuredArticles[0]">
                    <div class="relative overflow-hidden rounded-2xl ">
                        <img class="w-full h-96 lg:h-[500px] object-cover group-hover:scale-105 transition-transform duration-700"
                            :src="getImageUrl(featuredArticles[0].thumbnail_artikel)" alt="thumbnail" />

                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                            <div class="flex items-center gap-4 mb-4">
                                <span
                                    class="px-3 py-1.5 bg-secondary/90 backdrop-blur-sm text-white text-sm font-semibold rounded-full border border-white/20">
                                    {{ featuredArticles[0].kategoriArtikel?.nama_kategori_artikel || 'Tanpa Kategori' }}
                                </span>
                                <span
                                    class="flex items-center gap-2 text-sm font-medium bg-black/30 px-3 py-1.5 rounded-full backdrop-blur-sm">
                                    <LucideEye class="w-4 h-4" />
                                    {{ featuredArticles[0].jumlah_view ?? '0' }}× dilihat
                                </span>
                            </div>

                            <a :href="`/artikel/${featuredArticles[0].slug}`" class="group/link">
                                <h3
                                    class="text-2xl lg:text-3xl font-bold mb-3 group-hover/link:text-white transition-colors duration-300">
                                    {{ featuredArticles[0].judul_artikel }}
                                </h3>
                            </a>

                            <p class="text-gray-200 text-base leading-relaxed mb-4 line-clamp-2">
                                {{ truncateText(stripHtmlTags(featuredArticles[0].konten_artikel)) }}
                            </p>

                            <!-- Author Info -->
                            <div class="flex items-center gap-3">
                                <img class="w-10 h-10 rounded-full object-cover border-2 border-white/30"
                                    :src="getImageUrl(featuredArticles[0].user.foto_profil)" />
                                <div class="flex flex-col">
                                    <div class="text-sm font-semibold">
                                        {{ featuredArticles[0].user.name || 'Anonim' }}
                                    </div>
                                    <div class="text-xs text-gray-300">
                                        {{ new Date(featuredArticles[0].created_at).toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric',
                                        }) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Articles -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <h3 class="lg:hidden text-2xl font-semibold text-gray-900 border-l-4 border-secondary pl-4">
                        Artikel Populer Lainnya
                    </h3>

                    <div class="space-y-6">
                        <div v-for="(post, i) in featuredArticles.slice(1, 4)" :key="i"
                            class="group relative overflow-hidden rounded-xl">

                            <!-- Background Image -->
                            <img class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500"
                                :src="getImageUrl(post.thumbnail_artikel)" alt="thumbnail" />

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent">
                            </div>

                            <!-- Content Overlay -->
                            <div class="absolute bottom-0 left-0 right-0 p-5">
                                <div class="flex items-center gap-3 mb-3">
                                    <span
                                        class="px-2.5 py-1 bg-secondary/80 backdrop-blur-sm text-white text-xs font-semibold rounded-full border border-white/20">
                                        {{ post.kategoriArtikel?.nama_kategori_artikel || 'Tanpa Kategori' }}
                                    </span>
                                    <span
                                        class="flex items-center gap-1.5 text-xs font-medium bg-black/40 px-2.5 py-1 rounded-full backdrop-blur-sm text-white">
                                        <LucideEye class="w-3 h-3" />
                                        {{ post.jumlah_view ?? '0' }}
                                    </span>
                                </div>

                                <a :href="`/artikel/${post.slug}`" class="block group/title mb-2">
                                    <h3
                                        class="text-lg font-bold text-white group-hover/title:text-gray-200 transition-colors duration-300 line-clamp-2">
                                        {{ post.judul_artikel }}
                                    </h3>
                                </a>

                                <p class="text-gray-200 text-sm leading-relaxed mb-3 line-clamp-2">
                                    {{ truncateText(stripHtmlTags(post.konten_artikel), 80) }}
                                </p>

                                <!-- Author & Date -->
                                <div class="flex items-center gap-2 text-xs text-gray-300">
                                    <LucideUser class="w-3 h-3" />
                                    <span>{{ post.user.name || 'Anonim' }}</span>
                                    <span>•</span>
                                    <LucideCalendar class="w-3 h-3" />
                                    <span>{{ new Date(post.created_at).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'short',
                                        year: 'numeric',
                                    }) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Articles State -->
            <div v-else class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <LucideUser class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                <p class="text-gray-600">Artikel terpopuler akan muncul di sini setelah ada yang dipublikasikan.</p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else class="flex flex-col gap-12">
            <div class="flex items-center justify-between">
                <div class="h-10 bg-gray-200 rounded-lg w-64 animate-pulse"></div>
                <div class="hidden lg:block w-24 h-1 bg-gray-200 rounded-full animate-pulse"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Large Featured Skeleton -->
                <div class="lg:col-span-7 animate-pulse">
                    <div class="bg-gray-200 rounded-2xl h-96 lg:h-[500px] w-full"></div>
                </div>

                <!-- Side Articles Skeleton -->
                <div class="lg:col-span-5 space-y-6">
                    <div v-for="i in 3" :key="i" class="bg-gray-200 rounded-xl h-64 animate-pulse">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>