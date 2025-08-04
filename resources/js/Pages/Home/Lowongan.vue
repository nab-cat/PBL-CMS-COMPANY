<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { AlarmClock, Wallet } from 'lucide-vue-next'
import AOS from 'aos'
import 'aos/dist/aos.css'

const lowongan = ref([])
const profil_perusahaan = ref(null)
const loading = ref(false)
const error = ref(null)
const currentIndex = ref(0)

onMounted(() => {
    fetchLowongan()
    fetchProfilPerusahaan()
    setInterval(() => {
        if (lowongan.value.length > 0) {
            currentIndex.value = (currentIndex.value + 1) % lowongan.value.length
        }
    }, 4000)
})

async function fetchLowongan() {
    try {
        loading.value = true
        const response = await axios.get('/api/lowongan')
        const allData = response.data.data
        lowongan.value = getRandomItems(allData, 3)
    } catch (err) {
        error.value = 'Lowongan tidak ditemukan atau terjadi kesalahan.'
       
    } finally {
        loading.value = false
    }
}

async function fetchProfilPerusahaan() {
    try {
        loading.value = true
        const response = await axios.get(`/api/profil-perusahaan/navbar`)
        profil_perusahaan.value = response.data.data
    } catch (err) {
        error.value = "Profil perusahaan tidak ditemukan atau terjadi kesalahan"
        
    } finally {
        loading.value = false
    }
}

function getRandomItems(array, count) {
    const shuffled = [...array].sort(() => 0.5 - Math.random())
    return shuffled.slice(0, count)
}

function getImage(image) {
    if (!image) return '/image/placeholder.webp'
    if (typeof image === 'object' && image !== null) {
        return image[0] ? `/storage/${image[0]}` : '/image/placeholder.webp'
    }
    return `/storage/${image}`
}

function formatTanggal(tanggal) {
    const options = { day: '2-digit', month: 'long', year: 'numeric' }
    return new Date(tanggal).toLocaleDateString('id-ID', options)
}

function formatGaji(angka) {
    return parseInt(angka).toLocaleString('id-ID')
}

// Function to strip HTML tags for preview
function stripHtml(html) {
    if (!html) return ''
    const tmp = document.createElement('div')
    tmp.innerHTML = html
    return tmp.textContent || tmp.innerText || ''
}

// Function to truncate text
function truncateText(text, length = 150) {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<template>
    <div
        class="w-full px-6 py-20 lg:px-16 lg:py-28 bg-Color-Scheme-1-Background flex flex-col gap-20 items-center font-custom">
        <!-- Header -->
        <div class="w-full max-w-2xl flex flex-col gap-4 items-center text-center" data-aos="fade-right">
            <h4 class="text-base font-semibold text-Color-Scheme-1-Text">
                {{ profil_perusahaan?.nama_perusahaan || "Memuat" }}
            </h4>
            <h2 class="text-5xl font-normal text-Color-Scheme-1-Text leading-[1.2]">Peluang Karier Eksklusif</h2>
            <p class="text-lg text-Color-Scheme-1-Text leading-relaxed">
                Kami membuka kesempatan untuk kamu yang ingin berkembang dan berkontribusi dalam tim kami. Yuk, daftar
                sekarang!
            </p>
        </div>

        <!-- Content -->
        <div class="flex flex-col lg:flex-row w-full max-w-7xl gap-12" data-aos="fade-right">
            <!-- List Lowongan -->
            <div class="flex-1 flex flex-col gap-12">
                <template v-if="lowongan.length > 0">
                    <div v-for="low in lowongan" :key="low.id"
                        class="border-t border-Color-Scheme-1-Border/20 pt-8 flex flex-col gap-6">
                        <!-- Title & Department -->
                        <div class="flex flex-wrap items-center gap-4">
                            <h3 class="text-2xl text-Color-Scheme-1-Text">{{ low.judul_lowongan }}</h3>
                            <span
                                class="px-2.5 border py-1 bg-Opacity-Neutral-Darkest-5/5 rounded-full text-sm font-semibold text-Color-Neutral-Darkest">
                                {{ low.jenis_lowongan }}
                            </span>
                        </div>

                        <!-- Deskripsi (Strip HTML for preview) -->
                        <p class="text-base text-Color-Scheme-1-Text">
                            {{ truncateText(stripHtml(low.deskripsi_pekerjaan)) }}
                        </p>

                        <!-- Info -->
                        <div class="flex flex-wrap gap-6">
                            <div class="flex items-center gap-3">
                                <AlarmClock class="w-6 h-6 text-Color-Scheme-1-Text" />
                                <span class="text-lg text-Color-Scheme-1-Text">
                                    Ditutup pada {{ formatTanggal(low.tanggal_ditutup) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Wallet class="w-6 h-6 text-Color-Scheme-1-Text" />
                                <span class="text-lg text-Color-Scheme-1-Text">
                                    Rp.{{ formatGaji(low.gaji) }}
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Apply -->
                        <a :href="`/lowongan/${low.slug}`"
                            class="inline-block w-fit px-5 py-2 bg-secondary rounded-full text-base font-medium text-white">
                            Apply Now
                        </a>
                    </div>
                </template>

                <template v-else>
                    <div class="text-center text-lg text-Color-Scheme-1-Text">
                        Maaf, belum ada lowongan yang tersedia saat ini.
                    </div>
                </template>
            </div>

            <!-- Gambar Slider -->
            <div class="flex-1 max-w-full" data-aos="fade-up">
                <div class="relative h-full w-full aspect-[1/1] overflow-hidden rounded-2xl">
                    <template v-if="lowongan.length > 0">
                        <div class="flex transition-transform duration-700 ease-in-out h-full"
                            :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                            <div v-for="(item, index) in lowongan" :key="'lowongan-slide-' + index"
                                class="w-full h-full flex-shrink-0 bg-cover bg-center"
                                :style="{ backgroundImage: `url(${getImage(item.thumbnail_lowongan)})` }">
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex items-center justify-center h-full bg-gray-100 text-gray-500 text-xl">
                            Tidak ada gambar lowongan
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
