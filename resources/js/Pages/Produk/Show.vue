<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { usePage, Link } from '@inertiajs/vue3'
import { Home, ChevronRight, ChevronLeft, ShoppingBag, Tag, Star } from 'lucide-vue-next'
import TestimoniTerkirim from '@/Components/Modal/TestimoniTerkirim.vue'

// Props dari route
const { slug } = defineProps({ slug: String })

// Global props dari Inertia
const page = usePage()
const theme = computed(() => page.props.theme)
const user = computed(() => page.props.auth?.user)

// === DATA
const item = ref(null)
const loading = ref(false)
const activeImageIndex = ref(0)
const isLoggedIn = computed(() => !!user.value)
const testimoniList = ref([])
const showTestimoniModal = ref(false)

const newTestimoni = ref({
    isi_testimoni: '',
    rating: 5,
})

// === LIFECYCLE
onMounted(() => {
    fetchProduk()
    document.documentElement.style.setProperty('--color-secondary', theme.value.secondary)
})

// === FUNCTION
async function fetchProduk() {
    try {
        loading.value = true
        const response = await axios.get(`/api/produk/${slug}`)
        item.value = response.data.data
        await fetchTestimoni()
    } catch (err) {
    } finally {
        loading.value = false
    }
}

function getImageUrl(image) {
    return image ? `/storage/${image}` : '/image/placeholder.webp'
}

function prevImage() {
    if (!item.value?.thumbnail_produk) return
    activeImageIndex.value =
        (activeImageIndex.value - 1 + item.value.thumbnail_produk.length) % item.value.thumbnail_produk.length
}

function nextImage() {
    if (!item.value?.thumbnail_produk) return
    activeImageIndex.value =
        (activeImageIndex.value + 1) % item.value.thumbnail_produk.length
}

async function fetchTestimoni() {
    if (!item.value) return
    try {
        const response = await axios.get(`/api/testimoni/produk/${item.value.id_produk}`)
        testimoniList.value = response.data.data
    } catch (err) {
    }
}

async function submitTestimoni() {
    if (!newTestimoni.value.isi_testimoni.trim()) {
        alert('Isi testimoni tidak boleh kosong')
        return
    }

    if (!user.value?.id_user) {
        alert('Silakan login terlebih dahulu.')
        return
    }

    try {
        await axios.post(`/api/testimoni/produk/${item.value.id_produk}`, {
            ...newTestimoni.value,
            id_user: user.value.id_user,
        })

        // Tampilkan modal success
        showTestimoniModal.value = true

        // Reset form
        newTestimoni.value.isi_testimoni = ''
        newTestimoni.value.rating = 5

        // Refresh testimoni list
        await fetchTestimoni()
    } catch (err) {
        alert('Gagal mengirim testimoni')
    }
}

function closeTestimoniModal() {
    showTestimoniModal.value = false
}

function writeAnotherTestimoni() {
    setTimeout(() => {
        const textarea = document.querySelector('textarea[placeholder*="testimoni"]')
        if (textarea) {
            textarea.focus()
            textarea.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
    }, 100)
}

function formatRupiah(value) {
    const numberValue = Number(value);
    if (isNaN(numberValue)) return value;
    return `Rp${numberValue.toLocaleString('id-ID')},00`;
}
</script>

<template>
    <AppLayout>
        <div class="w-full px-4 lg:px-16 py-10 lg:py-20 bg-white flex flex-col items-start gap-20 font-custom">

            <!-- Skeleton Loading -->
            <div v-if="loading" class="flex flex-col lg:flex-row gap-10 lg:gap-20 w-full max-w-7xl mx-auto">
                <!-- Left: Product Image Skeleton -->
                <div class="w-full lg:w-1/2 flex justify-center">
                    <div class="relative w-full max-w-[600px] aspect-[4/3] bg-gray-200 animate-pulse rounded-2xl overflow-hidden">
                        <!-- Pagination Dots Skeleton -->
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
                            <div v-for="i in 3" :key="i" class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>

                <!-- Right: Product Info Skeleton -->
                <div class="w-full lg:w-1/2 flex flex-col gap-8">
                    <!-- Breadcrumbs Skeleton -->
                    <div class="bg-gray-200 h-6 w-3/4 rounded animate-pulse"></div>

                    <!-- Title Skeleton -->
                    <div class="h-12 bg-gray-200 animate-pulse rounded w-3/4"></div>
                    
                    <!-- Price & Category Skeleton -->
                    <div class="flex items-center gap-4">
                        <div class="h-8 bg-gray-200 animate-pulse rounded w-32"></div>
                        <div class="h-6 border-l"></div>
                        <div class="h-8 bg-gray-200 animate-pulse rounded w-40"></div>
                    </div>

                    <!-- Description Skeleton -->
                    <div class="flex flex-col gap-2">
                        <div class="h-4 bg-gray-200 animate-pulse rounded w-full"></div>
                        <div class="h-4 bg-gray-200 animate-pulse rounded w-full"></div>
                        <div class="h-4 bg-gray-200 animate-pulse rounded w-5/6"></div>
                        <div class="h-4 bg-gray-200 animate-pulse rounded w-3/4"></div>
                    </div>

                    <!-- Buy Button Skeleton -->
                    <div class="space-y-4">
                        <div class="h-10 bg-gray-300 animate-pulse rounded-full w-full"></div>
                        <div class="h-3 bg-gray-200 animate-pulse rounded w-40 mx-auto"></div>
                    </div>
                </div>
            </div>

            <!-- Actual Product Detail - Show when not loading -->
            <div v-else class="flex flex-col lg:flex-row gap-10 lg:gap-20 w-full max-w-7xl mx-auto">
                <!-- Left: Product Image -->
                <div class="w-full lg:w-1/2 flex justify-center">
                    <div class="relative w-full max-w-[600px] aspect-[4/3] overflow-hidden rounded-2xl">
                        <img class="absolute inset-0 w-full h-full object-cover"
                            :src="getImageUrl(item?.thumbnail_produk?.[activeImageIndex])" alt="Product Image" />

                        <!-- Nav Arrows -->
                        <div @click="prevImage"
                            class="absolute left-2 top-1/2 -translate-y-1/2 p-2 bg-black/40 hover:bg-black/60 rounded-full cursor-pointer transition">
                            <ChevronLeft class="w-5 h-5 text-white" />
                        </div>
                        <div @click="nextImage"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-black/40 hover:bg-black/60 rounded-full cursor-pointer transition">
                            <ChevronRight class="w-5 h-5 text-white" />
                        </div>

                        <!-- Pagination Dots -->
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
                            <div v-for="(img, i) in item?.thumbnail_produk" :key="i"
                                :class="i === activeImageIndex ? 'w-2.5 h-2.5 rounded-full bg-white scale-110' : 'w-2.5 h-2.5 rounded-full bg-white opacity-30'" />
                        </div>
                    </div>
                </div>

                <!-- Right: Product Info -->
                <div class="w-full lg:w-1/2 flex flex-col gap-8" v-if="item">
                    <!-- Breadcrumbs -->
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 text-sm">
                                <li class="inline-flex items-center">
                                    <Link href="/"
                                        class="inline-flex items-center text-gray-500 hover:text-secondary transition-colors">
                                        <Home class="w-4 h-4 mr-1.5" />
                                        Home
                                    </Link>
                                </li>
                                <li class="inline-flex items-center">
                                    <ChevronRight class="w-4 h-4 text-gray-400 mx-1.5" />
                                    <Link href="/produk"
                                        class="inline-flex items-center text-gray-500 hover:text-secondary transition-colors">
                                        <ShoppingBag class="w-4 h-4 mr-1.5" />
                                        Produk
                                    </Link>
                                </li>
                                <li class="inline-flex items-center">
                                    <ChevronRight class="w-4 h-4 text-gray-400 mx-1.5" />
                                    <span class="text-sm font-medium text-gray-700 truncate max-w-[140px] sm:max-w-[200px] md:max-w-[300px]"
                                        :title="item?.nama_produk">
                                        {{ item?.nama_produk || "Memuat..." }}
                                    </span>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Title & Price -->
                    <h1 class="text-4xl text-secondary font-bold">{{ item.nama_produk }}</h1>

                    <!-- Conditional layout berdasarkan tampilkan_harga -->
                    <div v-if="item.tampilkan_harga" class="flex items-center gap-4">
                        <span class="text-xl font-semibold">{{ formatRupiah(item.harga_produk) }}</span>
                        <div class="flex items-center gap-3">
                            <div class="h-6 border-l" />
                            <span class="text-xl flex items-center gap-1">
                                <Tag class="w-4" /> {{ item.kategori_produk.nama_kategori_produk }}
                            </span>
                        </div>
                    </div>

                    <!-- Layout tanpa harga -->
                    <div v-else class="flex items-center gap-3">
                        <span class="text-xl flex items-center gap-1">
                            <Tag class="w-4" /> {{ item.kategori_produk.nama_kategori_produk }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-base text-gray-700">{{ item.deskripsi_produk }}</p>

                    <!-- Buy Button -->
                    <div class="space-y-4">
                        <a :href="item.link_produk" target="_blank"
                            class="block text-center w-full px-6 py-2.5 bg-secondary hover:bg-black transition duration-500 text-white font-medium rounded-full">
                            Beli di marketplace
                        </a>
                        <p class="text-xs text-center text-gray-500">Anda akan diarahkan ke halaman baru</p>
                    </div>
                </div>
            </div>

            <!-- Rest of the template remains the same -->
            <!-- TESTIMONI LIST -->
            <div v-if="testimoniList.length" class="w-full max-w-3xl mx-auto border-t pt-10">
                <span class="text-sm text-gray-500">Ulasan dari</span>
                <h2 class="text-2xl font-semibold mb-4">{{ item.nama_produk }}</h2>
                <div class="space-y-6">
                    <div v-for="testimoni in testimoniList" :key="testimoni.id_testimoni"
                        class="p-4 border border-gray-200 rounded-xl bg-gray-50 transition hover:bg-gray-100">

                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 gap-2">
                            <div class="flex items-center gap-3">
                                <img v-if="testimoni.user?.foto_profil" :src="`/storage/${testimoni.user.foto_profil}`"
                                    class="w-8 h-8 rounded-full object-cover" alt="Foto Profil" />
                                <div>
                                    <p class="font-bold text-gray-800">{{ testimoni.user?.name || 'Anonim' }}</p>
                                    <p class="text-xs text-gray-500">{{ testimoni.user?.email || '' }}</p>
                                </div>
                            </div>

                            <!-- Bintang Rating -->
                            <div class="flex items-center gap-2 sm:mt-0 mt-2">
                                <div class="flex gap-1">
                                    <Star v-for="i in 5" :key="i"
                                        :class="i <= testimoni.rating ? 'text-secondary' : 'text-gray-300'"
                                        class="w-5 h-5" />
                                </div>
                                <span class="text-sm text-gray-500">{{ testimoni.rating }}/5</span>
                            </div>
                        </div>

                        <p class="text-gray-700">{{ testimoni.isi_testimoni }}</p>
                    </div>
                </div>
            </div>

            <!-- FORM TESTIMONI -->
            <div v-if="isLoggedIn && item" class="w-full max-w-3xl mx-auto mt-10">
                <span class="text-sm text-gray-500">Sudah membeli dan menggunakan {{ item.nama_produk }}?</span>
                <h2 class="text-xl font-semibold mb-3">Tulis pengalamanmu sendiri</h2>
                <form @submit.prevent="submitTestimoni"
                    class="space-y-4 border border-gray-200 rounded-xl bg-gray-50 transition hover:bg-gray-100 p-4">
                    <textarea v-model="newTestimoni.isi_testimoni"
                        class="w-full rounded-md border border-gray-300 bg-white p-3 focus:outline-none focus:ring-1 focus:ring-secondary focus:border-secondary"
                        rows="4" placeholder="Tulis testimoni kamu di sini..." required></textarea>

                    <!-- Rating Star Selector -->
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">Rating:</span>
                        <div class="flex items-center gap-1">
                            <button v-for="i in 5" :key="i" type="button" @click="newTestimoni.rating = i"
                                class="focus:outline-none">
                                <Star :class="i <= newTestimoni.rating ? 'text-secondary' : 'text-gray-300'"
                                    class="w-6 h-6 transition" />
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full rounded-full px-5 py-2 bg-secondary text-white hover:bg-black transition">
                        Kirim
                    </button>
                </form>
            </div>

            <!-- LOGIN WARNING -->
            <div v-else
                class="w-full max-w-3xl mx-auto mt-6 bg-yellow-50 border border-yellow-300 text-yellow-800 p-6 rounded-xl flex items-center gap-4">
                <!-- Ikon atau ilustrasi -->
                <img src="/image/login.svg" alt="Login Illustration" class="w-36 h-36 object-contain" />

                <!-- Pesan -->
                <div class="text-sm leading-relaxed">
                    <p class="font-semibold">Oops! Kamu belum login.</p>
                    <p class="italic text-gray-600">Login terlebih dahulu untuk menulis testimoni dan berbagi
                        pengalamanmu.</p>
                </div>
            </div>
        </div>

        <!-- Testimoni Success Modal -->
        <TestimoniTerkirim :show="showTestimoniModal" @close="closeTestimoniModal"
            @write-another="writeAnotherTestimoni" :auto-close="false" />
    </AppLayout>
</template>