<script setup>
import axios from "axios";
import { ref, onMounted, computed, onUnmounted } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    Home,
    Image,
    ChevronRight,
    Download,
    Copy,
    Eye,
    Info,
    X,
} from "lucide-vue-next";
import CopyLink from "@/Components/Modal/CopyLink.vue";
import ZoomGaleri from "@/Components/Modal/ZoomGaleri.vue";
import MetaGambarGaleri from "@/Components/Modal/MetaGambarGaleri.vue";

const gallery = ref(null);
const loading = ref(true);
const error = ref(null);
const activeImageIndex = ref(0);
const showCopyModal = ref(false);
const showImageModal = ref(false);
const showMetaModal = ref(false);
const imageMetadata = ref({});
const loadingMeta = ref(false);

const props = defineProps({
    slug: String,
});

// Computed property for current image metadata
const currentImageMeta = computed(() => {
    if (!gallery.value?.thumbnail_galeri?.[activeImageIndex.value]) return null;
    return imageMetadata.value[activeImageIndex.value] || null;
});

onMounted(() => {
    fetchGallery();
    document.addEventListener("keydown", handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeyDown);
});

// Handle keyboard navigation
function handleKeyDown(event) {
    if (
        !gallery.value?.thumbnail_galeri?.length ||
        gallery.value.thumbnail_galeri.length <= 1
    )
        return;

    switch (event.key) {
        case "ArrowLeft":
            event.preventDefault();
            prevImage();
            break;
        case "ArrowRight":
            event.preventDefault();
            nextImage();
            break;
        case "Escape":
            if (showImageModal.value) {
                closeImageModal();
            } else if (showMetaModal.value) {
                closeMetaModal();
            }
            break;
    }
}

async function fetchGallery() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/galeri/${props.slug}`);
        gallery.value = response.data.data;
        loading.value = false;

        // Load metadata for all images after gallery is loaded
        await loadImageMetadata();
    } catch (err) {
        error.value = "Gallery not found or an error occurred";
        loading.value = false;
    }
}

async function loadImageMetadata() {
    if (!gallery.value?.thumbnail_galeri?.length) return;

    try {
        loadingMeta.value = true;

        // Prepare image paths for API
        const imagePaths = gallery.value.thumbnail_galeri
            .map((img) => {
                if (typeof img === "object" && img !== null) {
                    return img[0] || "";
                }
                return img || "";
            })
            .filter((path) => path !== "");

        const response = await axios.post("/api/image-meta/bulk", {
            images: imagePaths,
        });

        if (response.data.status === "success") {
            imageMetadata.value = response.data.data;
        }
    } catch (err) {
    } finally {
        loadingMeta.value = false;
    }
}

function fallbackCopy(text) {
    const ta = document.createElement("textarea");
    ta.value = text;
    ta.style.position = "fixed";
    ta.style.opacity = "0";
    document.body.appendChild(ta);
    ta.select();
    document.execCommand("copy");
    document.body.removeChild(ta);
    showCopyModal.value = true;
}

async function copyLink() {
    const url = window.location.href;
    try {
        // Only call writeText if the API exists and is a function
        if (
            navigator.clipboard &&
            typeof navigator.clipboard.writeText === "function"
        ) {
            await navigator.clipboard.writeText(url);
        } else {
            throw new Error("Clipboard API not available");
        }
        showCopyModal.value = true;
    } catch (err) {
        console.warn("Clipboard write failed, using fallback:", err);
        fallbackCopy(url);
    }
}

function closeCopyModal() {
    showCopyModal.value = false;
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        const imagePath = image[0];
        if (!imagePath) return "/image/placeholder.webp";

        // All gallery images are stored in storage/app/public and accessed via /storage/
        return `/storage/${imagePath}`;
    }

    // Handle string paths
    if (typeof image === "string") {
        // All gallery images are stored in storage/app/public and accessed via /storage/
        return `/storage/${image}`;
    }

    return "/image/placeholder.webp";
}

function formatDate(date) {
    if (!date) return "";

    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

async function downloadGallery(galleryId) {
    try {
        const response = await axios.get(`/api/galeri/download/${galleryId}`);

        // Update the download count locally
        if (gallery.value) {
            gallery.value.jumlah_unduhan++;
        }

        // Get the current image
        const currentImage =
            gallery.value.thumbnail_galeri[activeImageIndex.value];
        if (currentImage) {
            let imagePath;

            if (typeof currentImage === "object" && currentImage !== null) {
                imagePath = currentImage[0];
            } else {
                imagePath = currentImage;
            }

            if (imagePath) {
                // Create download URL based on path type
                let downloadUrl;
                if (imagePath.startsWith("galeri-thumbnails/")) {
                    downloadUrl = `/storage/${imagePath}`;
                }

                // Create a download link
                const link = document.createElement("a");
                link.href = downloadUrl;
                link.download = `gallery-${galleryId}-image-${
                    activeImageIndex.value + 1
                }.jpg`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    } catch (error) {}
}

function setActiveImage(index) {
    activeImageIndex.value = index;
}

function openImageModal() {
    showImageModal.value = true;
}

function closeImageModal() {
    showImageModal.value = false;
}

function openMetaModal() {
    showMetaModal.value = true;
}

function closeMetaModal() {
    showMetaModal.value = false;
}

function nextImage() {
    if (gallery.value?.thumbnail_galeri?.length > 1) {
        activeImageIndex.value =
            (activeImageIndex.value + 1) %
            gallery.value.thumbnail_galeri.length;
    }
}

function prevImage() {
    if (gallery.value?.thumbnail_galeri?.length > 1) {
        activeImageIndex.value =
            activeImageIndex.value === 0
                ? gallery.value.thumbnail_galeri.length - 1
                : activeImageIndex.value - 1;
    }
}
</script>

<template>
    <AppLayout>
        <div
            class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-16 py-20 font-custom text-black"
        >
            <!-- Loading Skeleton -->
            <div v-if="loading" class="flex flex-col gap-20">
                <!-- Skeleton Breadcrumb - Simplified to single rectangle -->
                <div
                    class="w-full max-w-md h-5 bg-gray-200 animate-pulse rounded"
                ></div>

                <!-- Skeleton Judul & Kategori -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-24 h-8 bg-gray-200 animate-pulse rounded-full"
                        ></div>
                        <div
                            class="w-20 h-6 bg-gray-200 animate-pulse rounded"
                        ></div>
                    </div>
                    <div
                        class="w-3/4 h-12 bg-gray-200 animate-pulse rounded"
                    ></div>
                </div>

                <!-- Skeleton Gambar Utama -->
                <div
                    class="relative rounded-2xl overflow-hidden shadow-sm aspect-[16/9] bg-gray-200 animate-pulse"
                ></div>

                <!-- Skeleton Thumbnails -->
                <div class="flex overflow-x-auto gap-4 py-4">
                    <div
                        class="w-20 aspect-square rounded-lg bg-gray-200 animate-pulse flex-shrink-0"
                    ></div>
                    <div
                        class="w-20 aspect-square rounded-lg bg-gray-200 animate-pulse flex-shrink-0"
                    ></div>
                    <div
                        class="w-20 aspect-square rounded-lg bg-gray-200 animate-pulse flex-shrink-0"
                    ></div>
                    <div
                        class="w-20 aspect-square rounded-lg bg-gray-200 animate-pulse flex-shrink-0"
                    ></div>
                </div>

                <!-- Skeleton Info Penulis -->
                <div
                    class="bg-gray-50 rounded-xl w-full p-6 border border-gray-100"
                >
                    <!-- Skeleton Author Profile -->
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-14 h-14 rounded-full bg-gray-200 animate-pulse"
                        ></div>
                        <div class="flex-1">
                            <div
                                class="w-40 h-6 bg-gray-200 animate-pulse rounded mb-2"
                            ></div>
                            <div
                                class="w-32 h-4 bg-gray-200 animate-pulse rounded"
                            ></div>
                        </div>
                    </div>

                    <!-- Skeleton Stats & Actions -->
                    <div
                        class="flex items-center justify-between pt-4 border-t border-gray-200"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-4 h-4 bg-gray-200 animate-pulse rounded"
                                ></div>
                                <div
                                    class="w-24 h-4 bg-gray-200 animate-pulse rounded"
                                ></div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div
                                class="w-24 h-10 bg-gray-200 animate-pulse rounded-lg"
                            ></div>
                            <div
                                class="w-32 h-10 bg-gray-200 animate-pulse rounded-lg"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Skeleton Deskripsi -->
                <div>
                    <div
                        class="w-32 h-6 bg-gray-200 animate-pulse rounded mb-4"
                    ></div>
                    <div class="space-y-3">
                        <div
                            class="w-full h-4 bg-gray-200 animate-pulse rounded"
                        ></div>
                        <div
                            class="w-full h-4 bg-gray-200 animate-pulse rounded"
                        ></div>
                        <div
                            class="w-full h-4 bg-gray-200 animate-pulse rounded"
                        ></div>
                        <div
                            class="w-5/6 h-4 bg-gray-200 animate-pulse rounded"
                        ></div>
                        <div
                            class="w-3/4 h-4 bg-gray-200 animate-pulse rounded"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Actual Content (Existing) -->
            <div v-else-if="!error" class="flex flex-col gap-20">
                <!-- Breadcrumb -->
                <div class="flex flex-col gap-12">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol
                                class="inline-flex items-center space-x-1 text-sm"
                            >
                                <li class="inline-flex items-center">
                                    <Link
                                        href="/"
                                        class="inline-flex items-center text-gray-500 hover:text-secondary transition-colors"
                                    >
                                        <Home class="w-4 h-4 mr-1.5" />
                                        Home
                                    </Link>
                                </li>
                                <li class="inline-flex items-center">
                                    <ChevronRight
                                        class="w-4 h-4 text-gray-400 mx-1.5"
                                    />
                                    <Link
                                        href="/galeri"
                                        class="inline-flex items-center text-gray-500 hover:text-secondary transition-colors"
                                    >
                                        <Image class="w-4 h-4 mr-1.5" />
                                        Galeri
                                    </Link>
                                </li>
                                <li class="inline-flex items-center">
                                    <ChevronRight
                                        class="w-4 h-4 text-gray-400 mx-1.5"
                                    />
                                    <span
                                        class="text-sm font-medium text-gray-700 truncate max-w-[140px] sm:max-w-[200px] md:max-w-[300px]"
                                        :title="gallery?.judul_galeri"
                                    >
                                        {{
                                            gallery?.judul_galeri || "Memuat..."
                                        }}
                                    </span>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Judul & Kategori -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4 flex-wrap">
                            <div
                                class="px-3 py-1 rounded-full border text-sm font-semibold bg-black/5 text-black"
                            >
                                {{
                                    gallery?.kategoriGaleri
                                        ?.nama_kategori_galeri ||
                                    "Tanpa Kategori"
                                }}
                            </div>
                            <div
                                class="text-sm font-semibold text-black flex items-center gap-2"
                            >
                                <Image class="w-4 h-4" />
                                {{
                                    gallery?.thumbnail_galeri?.length || 0
                                }}
                                gambar
                            </div>
                        </div>
                        <h1
                            class="text-4xl sm:text-5xl font-normal leading-tight"
                        >
                            {{
                                gallery?.judul_galeri || "Judul tidak tersedia"
                            }}
                        </h1>
                    </div>
                </div>

                <!-- Gambar Utama - Fixed button events -->
                <div
                    class="relative rounded-2xl overflow-hidden shadow-sm aspect-[16/9] bg-white group"
                >
                    <img
                        :src="
                            getImageUrl(
                                gallery?.thumbnail_galeri?.[activeImageIndex]
                            )
                        "
                        :alt="gallery?.judul_galeri"
                        class="w-full h-full object-cover"
                    />

                    <!-- Action buttons - Fixed with proper event handlers -->
                    <div class="absolute top-4 right-4 flex gap-2 z-20">
                        <button
                            @click.stop="openImageModal"
                            class="p-3 bg-white/90 backdrop-blur-sm rounded-full text-black hover:bg-white transition-all duration-200 shadow-lg opacity-100 md:opacity-0 md:group-hover:opacity-100"
                            title="Lihat gambar penuh"
                            type="button"
                        >
                            <Eye class="w-5 h-5" />
                        </button>
                        <button
                            @click.stop="openMetaModal"
                            class="p-3 bg-white/90 backdrop-blur-sm rounded-full text-black hover:bg-white transition-all duration-200 shadow-lg opacity-100 md:opacity-0 md:group-hover:opacity-100"
                            title="Info gambar"
                            type="button"
                        >
                            <Info class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Navigation arrows - Fixed z-index -->
                    <div
                        v-if="gallery?.thumbnail_galeri?.length > 1"
                        class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-4 z-10"
                    >
                        <!-- Desktop arrows (hover only) -->
                        <button
                            @click.stop="prevImage"
                            class="p-3 bg-white/80 backdrop-blur-sm rounded-full text-black hover:bg-white transition-all duration-200 shadow-lg hidden md:block opacity-0 group-hover:opacity-100"
                            title="Gambar sebelumnya"
                            type="button"
                        >
                            <ChevronRight class="w-5 h-5 rotate-180" />
                        </button>
                        <button
                            @click.stop="nextImage"
                            class="p-3 bg-white/80 backdrop-blur-sm rounded-full text-black hover:bg-white transition-all duration-200 shadow-lg hidden md:block opacity-0 group-hover:opacity-100"
                            title="Gambar selanjutnya"
                            type="button"
                        >
                            <ChevronRight class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Mobile navigation - Fixed z-index -->
                    <div
                        v-if="gallery?.thumbnail_galeri?.length > 1"
                        class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 md:hidden z-10"
                    >
                        <button
                            @click.stop="prevImage"
                            class="p-2 bg-white/90 backdrop-blur-sm rounded-full text-black hover:bg-white transition-colors shadow-lg"
                            title="Gambar sebelumnya"
                            type="button"
                        >
                            <ChevronRight class="w-4 h-4 rotate-180" />
                        </button>
                        <span
                            class="px-3 py-2 bg-white/90 backdrop-blur-sm rounded-full text-black text-sm font-medium"
                        >
                            {{ activeImageIndex + 1 }} /
                            {{ gallery?.thumbnail_galeri?.length }}
                        </span>
                        <button
                            @click.stop="nextImage"
                            class="p-2 bg-white/90 backdrop-blur-sm rounded-full text-black hover:bg-white transition-colors shadow-lg"
                            title="Gambar selanjutnya"
                            type="button"
                        >
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Thumbnails -->
                <div
                    v-if="gallery?.thumbnail_galeri?.length > 1"
                    class="flex overflow-x-auto gap-4 py-4"
                >
                    <div
                        v-for="(img, i) in gallery.thumbnail_galeri"
                        :key="i"
                        class="w-20 aspect-square rounded-lg cursor-pointer border-2 overflow-hidden flex-shrink-0"
                        :class="{
                            'border-secondary': activeImageIndex === i,
                            'border-transparent': activeImageIndex !== i,
                        }"
                        @click="setActiveImage(i)"
                        :title="`Gambar ${i + 1}`"
                    >
                        <img
                            :src="getImageUrl(img)"
                            alt="Thumbnail"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>

                <!-- Info Penulis dengan style dari Artikel -->
                <div
                    class="bg-gray-50 rounded-xl w-full p-6 border border-gray-100"
                >
                    <!-- Author Profile -->
                    <div class="flex items-center gap-4 mb-4">
                        <img
                            class="w-14 h-14 rounded-full object-cover ring-2 ring-white shadow-sm"
                            :src="getImageUrl(gallery?.user?.foto_profil)"
                            alt="Foto Penulis"
                        />
                        <div class="flex-1">
                            <h4 class="font-semibold text-lg text-black">
                                {{ gallery?.user?.name || "Anonim" }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ formatDate(gallery?.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Stats & Actions -->
                    <div
                        class="flex items-center justify-between pt-4 border-t border-gray-200"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-gray-600">
                                <Download class="w-4 h-4" />
                                <span class="text-sm font-medium"
                                    >{{ gallery?.jumlah_unduhan || 0 }}×
                                    diunduh</span
                                >
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button
                                class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 text-sm font-medium"
                                @click="copyLink"
                            >
                                <Copy class="w-4 h-4" />
                                <span class="hidden sm:inline">Salin Link</span>
                            </button>

                            <button
                                @click="downloadGallery(gallery.id_galeri)"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg bg-secondary text-white hover:bg-black transition text-sm font-medium"
                            >
                                <Download class="w-4 h-4" />
                                <span class="hidden sm:inline"
                                    >Unduh Gambar</span
                                >
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi dengan heading kecil -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Deskripsi</h3>
                    <div
                        class="prose max-w-none"
                        v-html="gallery?.deskripsi_galeri"
                    ></div>
                </div>
            </div>

            <!-- Error State -->
            <div v-else class="flex flex-col items-center justify-center py-20">
                <div class="text-center">
                    <Image class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                    <h2 class="text-2xl font-medium text-gray-900 mb-2">
                        Gallery tidak ditemukan
                    </h2>
                    <p class="text-gray-600 mb-8">
                        {{ error }}
                    </p>
                    <Link
                        href="/galeri"
                        class="inline-flex items-center px-6 py-3 bg-secondary text-white rounded-xl hover:bg-black transition"
                    >
                        Kembali ke Galeri
                    </Link>
                </div>
            </div>
        </div>

        <!-- Modal Components - Fixed positioning -->
        <Teleport to="body">
            <!-- Copy Link Modal -->
            <CopyLink
                :show="showCopyModal"
                @close="closeCopyModal"
                :auto-close="true"
                :auto-close-delay="3000"
            />

            <!-- Zoom Modal Component -->
            <ZoomGaleri
                :show="showImageModal"
                :images="gallery?.thumbnail_galeri"
                :active-index="activeImageIndex"
                :current-image-url="
                    getImageUrl(gallery?.thumbnail_galeri?.[activeImageIndex])
                "
                :gallery-title="gallery?.judul_galeri"
                @close="closeImageModal"
                @next="nextImage"
                @prev="prevImage"
                @update:active-index="setActiveImage"
            />

            <!-- Metadata Modal Component -->
            <MetaGambarGaleri
                :show="showMetaModal"
                :metadata="currentImageMeta"
                :loading-meta="loadingMeta"
                :current-image-url="
                    getImageUrl(gallery?.thumbnail_galeri?.[activeImageIndex])
                "
                :gallery-title="gallery?.judul_galeri"
                @close="closeMetaModal"
            />
        </Teleport>
    </AppLayout>
</template>
