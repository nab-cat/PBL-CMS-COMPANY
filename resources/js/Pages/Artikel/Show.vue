<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
import { ref, onMounted, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import CopyLink from "@/Components/Modal/CopyLink.vue";
import TestimoniTerkirim from "@/Components/Modal/TestimoniTerkirim.vue";

const article = ref(null);
const loading = ref(true);
const error = ref(null);
const readingTime = ref("");
const showCopyModal = ref(false);
const page = usePage();
const user = computed(() => page.props.auth?.user);

const testimoniList = ref([]);
const isLoggedIn = computed(() => !!user.value);
const showTestimoniModal = ref(false);

const newTestimoni = ref({
    isi_testimoni: "",
    rating: 5,
});

const props = defineProps({
    slug: String,
});

const relatedArticles = ref([]);

async function fetchRelatedArticles() {
    try {
        const response = await axios.get("/api/artikel/featured");
        relatedArticles.value = response.data.data;
    } catch (err) {
    }
}

onMounted(() => {
    fetchArticle();
    fetchRelatedArticles();
});

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

// fungsi untuk mengambil data artikel dari API
async function fetchArticle() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/artikel/${props.slug}`);
        article.value = response.data.data;

        // Calculate reading time (roughly 200 words per minute)
        const wordCount = article.value.konten_artikel
            .replace(/<[^>]*>/g, "")
            .split(/\s+/).length;
        const minutes = Math.ceil(wordCount / 200);
        readingTime.value = `${minutes} menit durasi baca`;

        // Fetch testimonials after article is loaded
        await fetchTestimoni();

        loading.value = false;
    } catch (err) {
        error.value = "Article not found or an error occurred";
        loading.value = false;
    }
}

// fungsi untuk mendapatkan URL gambar
function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }

    return `/storage/${image}`;
}

// fungsi untuk memformat tanggal
function formatDate(date) {
    if (!date) return "";

    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}
function getArticleExcerpt(content, maxLength = 80) {
    if (!content) return "";
    const plainText = content.replace(/<[^>]*>/g, "");
    return plainText.length > maxLength
        ? plainText.substring(0, maxLength).trim() + "..."
        : plainText.trim();
}
async function fetchTestimoni() {
    if (!article.value) return;
    try {
        const response = await axios.get(
            `/api/testimoni/artikel/${article.value.id_artikel}`
        );
        testimoniList.value = response.data.data;
    } catch (err) {
    }
}
async function submitTestimoni() {
    if (!newTestimoni.value.isi_testimoni.trim()) {
        alert("Isi testimoni tidak boleh kosong");
        return;
    }

    if (!user.value?.id_user) {
        alert("Silakan login terlebih dahulu.");
        return;
    }

    try {
        await axios.post(`/api/testimoni/artikel/${article.value.id_artikel}`, {
            ...newTestimoni.value,
            id_user: user.value.id_user,
        });

        // Tampilkan modal success
        showTestimoniModal.value = true;

        // Reset form
        newTestimoni.value.isi_testimoni = "";
        newTestimoni.value.rating = 5;

        // Refresh testimoni list
        await fetchTestimoni();
    } catch (err) {
        alert("Gagal mengirim testimoni");
    }
}
function closeTestimoniModal() {
    showTestimoniModal.value = false;
}

function writeAnotherTestimoni() {
    setTimeout(() => {
        const textarea = document.querySelector(
            'textarea[placeholder*="testimoni"]'
        );
        if (textarea) {
            textarea.focus();
            textarea.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    }, 100);
}
</script>

<template>
    <AppLayout>
        <div
            class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-16 py-20 font-custom text-black"
        >
            <!-- Skeleton Loading -->
            <div v-if="loading" class="flex flex-col lg:flex-row gap-16">
                <!-- Bagian Kiri: Artikel Skeleton -->
                <div class="flex-1 flex flex-col gap-10">
                    <!-- Breadcrumb Skeleton -->
                    <div class="flex flex-col gap-12">
                        <div
                            class="bg-gray-200 h-6 w-3/4 rounded animate-pulse"
                        ></div>
                        <div class="flex flex-col gap-4">
                            <div
                                class="bg-gray-200 h-8 w-48 rounded-full animate-pulse"
                            ></div>
                            <div
                                class="bg-gray-300 h-14 w-full rounded animate-pulse"
                            ></div>
                        </div>
                    </div>

                    <!-- Featured Image Skeleton -->
                    <div class="flex flex-col gap-8">
                        <div
                            class="bg-gray-200 h-[300px] sm:h-[400px] lg:h-[600px] rounded-2xl w-full animate-pulse"
                        ></div>

                        <!-- Author Info Box Skeleton -->
                        <div
                            class="bg-gray-100 rounded-xl w-full p-6 border border-gray-100"
                        >
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="bg-gray-200 w-14 h-14 rounded-full animate-pulse"
                                ></div>
                                <div class="flex-1">
                                    <div
                                        class="bg-gray-200 h-6 w-40 rounded animate-pulse mb-2"
                                    ></div>
                                    <div
                                        class="bg-gray-200 h-4 w-32 rounded animate-pulse"
                                    ></div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between pt-4 border-t border-gray-200"
                            >
                                <div
                                    class="bg-gray-200 h-5 w-24 rounded animate-pulse"
                                ></div>
                                <div
                                    class="bg-gray-200 h-8 w-24 rounded animate-pulse"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Skeleton -->
                    <div class="flex flex-col gap-4">
                        <div
                            class="bg-gray-200 h-6 w-full rounded animate-pulse"
                        ></div>
                        <div
                            class="bg-gray-200 h-6 w-full rounded animate-pulse"
                        ></div>
                        <div
                            class="bg-gray-200 h-6 w-3/4 rounded animate-pulse"
                        ></div>
                        <div
                            class="bg-gray-200 h-6 w-5/6 rounded animate-pulse"
                        ></div>
                        <div
                            class="bg-gray-200 h-6 w-full rounded animate-pulse"
                        ></div>
                        <div
                            class="bg-gray-200 h-6 w-4/5 rounded animate-pulse"
                        ></div>
                        <div
                            class="bg-gray-200 h-6 w-full rounded animate-pulse"
                        ></div>
                    </div>
                </div>

                <!-- Bagian Kanan: Artikel Lainnya Skeleton -->
                <div class="w-full lg:w-80 flex-shrink-0">
                    <div class="bg-gray-300 h-8 w-48 rounded animate-pulse mb-4"></div>
                    <div class="flex flex-col gap-4">
                        <div
                            v-for="i in 4"
                            :key="i"
                            class="flex flex-col gap-3 p-4 bg-white rounded-xl border border-gray-100"
                        >
                            <!-- Image skeleton -->
                            <div class="bg-gray-200 w-full h-32 rounded-lg animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actual Content (display when not loading) -->
            <div v-else class="flex flex-col lg:flex-row gap-16">
                <!-- Bagian Kiri: Artikel -->
                <div class="flex-1 flex flex-col gap-10">
                    <!-- Breadcrumb -->
                    <div class="flex flex-col gap-12">
                        <div>
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 text-sm">
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
                                        <ChevronRight class="w-4 h-4 text-gray-400 mx-1.5" />
                                        <Link
                                            href="/artikel"
                                            class="inline-flex items-center text-gray-500 hover:text-secondary transition-colors"
                                        >
                                            <FileText class="w-4 h-4 mr-1.5" />
                                            Artikel
                                        </Link>
                                    </li>
                                    <li class="inline-flex items-center">
                                        <ChevronRight class="w-4 h-4 text-gray-400 mx-1.5" />
                                        <span
                                            class="text-sm font-medium text-gray-700 truncate max-w-[140px] sm:max-w-[200px] md:max-w-[300px]"
                                            :title="article?.judul_artikel"
                                        >
                                            {{ article?.judul_artikel || "Memuat..." }}
                                        </span>
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="px-3 py-1 rounded-full border text-sm font-semibold bg-black/5 text-black"
                                >
                                    {{
                                        article?.kategoriArtikel
                                            ?.nama_kategori_artikel ||
                                        "Tanpa Kategori"
                                    }}
                                </div>
                                <div class="text-sm font-semibold text-black flex gap-2 items-center">
                                    <Clock class="w-4 h-4" />
                                    {{ readingTime }}
                                </div>
                            </div>
                            <h1
                                class="text-4xl sm:text-5xl font-normal leading-tight"
                            >
                                {{
                                    article?.judul_artikel ||
                                    "Artikel tidak ditemukan"
                                }}
                            </h1>
                        </div>
                    </div>

                    <!-- Gambar & Info Penulis -->
                    <div class="flex flex-col gap-8">
                        <img
                            class="w-full h-[300px] sm:h-[400px] lg:h-[600px] rounded-2xl object-cover"
                            :src="getImageUrl(article?.thumbnail_artikel)"
                            :alt="article?.judul_artikel"
                        />
                        <div
                            class="flex justify-between items-start flex-wrap gap-8"
                        >
                            <!-- Info Penulis - Clean Layout -->
                            <div
                                class="bg-gray-50 rounded-xl w-full p-6 border border-gray-100"
                            >
                                <!-- Author Profile -->
                                <div class="flex items-center gap-4 mb-4">
                                    <img
                                        class="w-14 h-14 rounded-full object-cover ring-2 ring-white shadow-sm"
                                        :src="
                                            getImageUrl(
                                                article?.user?.foto_profil
                                            )
                                        "
                                        alt="Foto Penulis"
                                    />
                                    <div class="flex-1">
                                        <h4
                                            class="font-semibold text-lg text-black"
                                        >
                                            {{
                                                article?.user?.name || "Anonim"
                                            }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            {{
                                                formatDate(article?.created_at)
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Stats & Actions -->
                                <div
                                    class="flex items-center justify-between pt-4 border-t border-gray-200"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-600"
                                    >
                                        <LucideEye class="w-4 h-4" />
                                        <span class="text-sm font-medium"
                                            >{{ article?.jumlah_view || 0 }}×
                                            dilihat</span
                                        >
                                    </div>

                                    <button
                                        class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 text-sm font-medium"
                                        @click="copyLink"
                                    >
                                        <Copy class="w-4 h-4" />
                                        <span class="hidden sm:inline"
                                            >Salin Link</span
                                        >
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Konten Artikel -->
                    <div
                        class="prose prose-lg max-w-none text-black"
                        v-html="article?.konten_artikel"
                    ></div>

                    <!-- TESTIMONI LIST -->
                    <div
                        v-if="testimoniList.length"
                        class="w-full mt-16 border-t pt-10"
                    >
                        <span class="text-sm text-gray-500">Ulasan untuk</span>
                        <h2 class="text-2xl font-semibold mb-4">
                            {{ article?.judul_artikel }}
                        </h2>
                        <div class="space-y-6">
                            <div
                                v-for="testimoni in testimoniList"
                                :key="testimoni.id_testimoni_artikel"
                                class="p-4 border border-gray-200 rounded-xl bg-gray-50 transition hover:bg-gray-100"
                            >
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 gap-2"
                                >
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="testimoni.user?.foto_profil"
                                            :src="`/storage/${testimoni.user.foto_profil}`"
                                            class="w-8 h-8 rounded-full object-cover"
                                            alt="Foto Profil"
                                        />
                                        <div>
                                            <p class="font-bold text-gray-800">
                                                {{
                                                    testimoni.user?.name ||
                                                    "Anonim"
                                                }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{
                                                    testimoni.user?.email || ""
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bintang Rating -->
                                    <div
                                        class="flex items-center gap-2 sm:mt-0 mt-2"
                                    >
                                        <div class="flex gap-1">
                                            <Star
                                                v-for="i in 5"
                                                :key="i"
                                                :class="
                                                    i <= testimoni.rating
                                                        ? 'text-secondary'
                                                        : 'text-gray-300'
                                                "
                                                class="w-5 h-5"
                                            />
                                        </div>
                                        <span class="text-sm text-gray-500"
                                            >{{ testimoni.rating }}/5</span
                                        >
                                    </div>
                                </div>

                                <p class="text-gray-700">
                                    {{ testimoni.isi_testimoni }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FORM TESTIMONI -->
                    <div v-if="isLoggedIn && article" class="w-full mt-10">
                        <span class="text-sm text-gray-500"
                            >Sudah membaca artikel ini?</span
                        >
                        <h2 class="text-xl font-semibold mb-3">
                            Berikan ulasanmu
                        </h2>
                        <form
                            @submit.prevent="submitTestimoni"
                            class="space-y-4 border border-gray-200 rounded-xl bg-gray-50 transition hover:bg-gray-100 p-4"
                        >
                            <textarea
                                v-model="newTestimoni.isi_testimoni"
                                class="w-full rounded-md border border-gray-300 bg-white p-3 focus:outline-none focus:ring-1 focus:ring-secondary focus:border-secondary"
                                rows="4"
                                placeholder="Tulis testimoni kamu di sini..."
                                required
                            ></textarea>

                            <!-- Rating Star Selector -->
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-700"
                                    >Rating:</span
                                >
                                <div class="flex items-center gap-1">
                                    <button
                                        v-for="i in 5"
                                        :key="i"
                                        type="button"
                                        @click="newTestimoni.rating = i"
                                        class="focus:outline-none"
                                    >
                                        <Star
                                            :class="
                                                i <= newTestimoni.rating
                                                    ? 'text-secondary'
                                                    : 'text-gray-300'
                                            "
                                            class="w-6 h-6 transition"
                                        />
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-full px-5 py-2 bg-secondary text-white hover:bg-black transition"
                            >
                                Kirim
                            </button>
                        </form>
                    </div>

                    <!-- LOGIN WARNING -->
                    <div
                        v-else-if="!isLoggedIn && article"
                        class="w-full mt-10 bg-yellow-50 border border-yellow-300 text-yellow-800 p-6 rounded-xl flex items-center gap-4"
                    >
                        <!-- Ikon atau ilustrasi -->
                        <img
                            src="/image/login.svg"
                            alt="Login Illustration"
                            class="w-36 h-36 object-contain"
                        />

                        <!-- Pesan -->
                        <div class="text-sm leading-relaxed">
                            <p class="font-semibold">Oops! Kamu belum login.</p>
                            <p class="italic text-gray-600">
                                Login terlebih dahulu untuk menulis testimoni
                                dan berbagi pendapatmu tentang artikel ini.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bagian Kanan: Artikel Lainnya -->
                <div class="w-full lg:w-80 flex-shrink-0">
                    <h2 class="text-xl font-semibold mb-4">
                        Artikel Teratas Lainnya
                    </h2>
                    <div class="flex flex-col gap-4">
                        <div
                            v-for="item in relatedArticles"
                            :key="item.id_artikel"
                            class="flex flex-col gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-gray-200 transition-all duration-200 cursor-pointer"
                            @click="$inertia.visit(`/artikel/${item.slug}`)"
                        >
                            <img
                                class="w-full h-32 rounded-lg object-cover"
                                :src="getImageUrl(item.thumbnail_artikel)"
                                :alt="item.judul_artikel"
                            />
                            <div class="flex flex-col gap-2">
                                <!-- Category Badge -->
                                <span class="px-2 py-1 bg-gray-100 rounded-full text-xs font-medium text-gray-700 w-fit">
                                    {{ item.kategoriArtikel?.nama_kategori_artikel || 'Tanpa Kategori' }}
                                </span>
                                
                                <!-- Title -->
                                <h3 class="font-semibold text-sm leading-snug line-clamp-2">
                                    {{ item.judul_artikel }}
                                </h3>
                                
                                <!-- Excerpt -->
                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                    {{ getArticleExcerpt(item.konten_artikel, 60) }}
                                </p>
                                
                                <!-- Meta Info -->
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <LucideEye class="w-3 h-3" />
                                        <span>{{ item.jumlah_view || 0 }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <LucideCalendar class="w-3 h-3" />
                                        <span>{{ new Date(item.created_at).toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'short',
                                        }) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Author Info -->
                                <div class="flex items-center gap-2 pt-1">
                                    <img
                                        class="w-5 h-5 rounded-full object-cover"
                                        :src="getImageUrl(item.user?.foto_profil)"
                                        :alt="item.user?.name || 'Author'"
                                    />
                                    <span class="text-xs text-gray-600 font-medium">
                                        {{ item.user?.name || 'Anonim' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <CopyLink
            :show="showCopyModal"
            @close="closeCopyModal"
            :auto-close="true"
            :auto-close-delay="3000"
        />
        <TestimoniTerkirim
            :show="showTestimoniModal"
            @close="closeTestimoniModal"
            @write-another="writeAnotherTestimoni"
            :auto-close="false"
        />
    </AppLayout>
</template>
