<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { megaMenuCache } from "./MegaMenuStore";

const articles = ref([]);
const isLoading = ref(true);

onMounted(() => {
    fetchArticles();
});

async function fetchArticles() {
    try {
        if (megaMenuCache.isValid("articles")) {
            articles.value = megaMenuCache.articles;
        } else {
            const response = await axios.get("/api/artikel/most-viewed");
            articles.value = response.data.data;
            megaMenuCache.setCache("articles", response.data.data);
        }
    } catch (error) {
    } finally {
        isLoading.value = false;
    }
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

// Function to strip HTML tags
function stripHtml(html) {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// Function to truncate text
function truncateText(text, length = 80) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}
</script>

<template>
    <div>
        <div class="font-bold text-h6-bold mb-4 text-secondary">
            Artikel Populer
        </div>
        <div class="flex flex-col gap-3">
            <!-- Loading Skeleton -->
            <div
                v-if="isLoading"
                v-for="i in 1"
                :key="i"
                class="flex gap-3 bg-white rounded-lg border border-gray-200 p-3 items-center animate-pulse"
            >
                <div class="w-12 h-12 bg-gray-300 rounded-lg flex-shrink-0"></div>
                <div class="flex flex-col gap-2 w-full">
                    <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                    <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                </div>
            </div>

            <!-- Real Data -->
            <div
                v-else-if="articles.length"
                v-for="artikel in articles"
                :key="artikel.id_artikel"
                class="flex gap-3 bg-white rounded-lg border border-gray-200 hover:scale-105 transition p-3 items-center"
            >
                <img
                    :src="getImageUrl(artikel.thumbnail_artikel)"
                    :alt="artikel.judul_artikel"
                    class="w-12 h-12 object-cover rounded-lg flex-shrink-0"
                />
                <div class="flex flex-col overflow-hidden">
                    <a
                        :href="`/artikel/${artikel.slug}`"
                        class="text-h6-bold text-secondary truncate hover:underline"
                    >
                        {{ artikel.judul_artikel }}
                    </a>
                    <span class="text-xs text-typography-dark line-clamp-2 mt-1">
                        {{ truncateText(stripHtml(artikel.konten_artikel)) }}
                    </span>
                </div>
            </div>

            <!-- Empty Data -->
            <div
                v-else
                class="text-typography-dark text-xs italic text-center py-2"
            >
                Tidak ada artikel populer.
            </div>
        </div>
    </div>
</template>
