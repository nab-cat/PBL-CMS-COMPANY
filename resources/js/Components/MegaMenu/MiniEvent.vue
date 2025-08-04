<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { megaMenuCache } from "./MegaMenuStore";

const event = ref(null); // satu objek saja
const isLoading = ref(true);

onMounted(() => {
    fetchEvent();
});

async function fetchEvent() {
    try {
        if (megaMenuCache.isValid("events")) {
            event.value = megaMenuCache.events;
            // Even if from cache, we are done loading
        } else {
            const response = await axios.get("/api/event/navbar");
            event.value = response.data.data;
            megaMenuCache.setCache("events", response.data.data);
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
            Event Terbaru
        </div>

        <!-- Loading skeleton -->
        <div
            v-if="isLoading"
            class="flex gap-3 bg-white rounded-lg transition p-3 items-center animate-pulse"
        >
            <div class="w-12 h-12 bg-gray-300 rounded-lg flex-shrink-0"></div>
            <div class="flex flex-col gap-2 overflow-hidden w-full">
                <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-full"></div>
            </div>
        </div>

        <!-- Real Data: Event pertama -->
        <div
            v-else-if="event"
            class="flex gap-3 bg-white rounded-lg border border-gray-200 hover:scale-105 transition-transform duration-200 p-3 items-center"
        >
            <img
                :src="getImageUrl(event.thumbnail_event)"
                :alt="event.nama_event"
                class="w-12 h-12 object-cover rounded-lg flex-shrink-0"
            />
            <div class="flex flex-col overflow-hidden">
                <a
                    :href="`/event/${event.slug}`"
                    class="text-h6-bold text-secondary truncate hover:underline"
                >
                    {{ event.nama_event }}
                </a>
                <span class="text-xs text-typography-dark line-clamp-2 mt-1">
                    {{ truncateText(stripHtml(event.deskripsi_event)) }}
                </span>
            </div>
        </div>

        <!-- Empty Data -->
        <div
            v-else
            class="text-typography-dark text-xs italic text-center py-2"
        >
            Tidak ada event terbaru.
        </div>
    </div>
</template>
