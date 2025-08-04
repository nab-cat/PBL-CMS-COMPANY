<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import AOS from 'aos'
import 'aos/dist/aos.css'

const profil_perusahaan = ref(null)
const artikel = ref(null)
const produk = ref(null)
const galeri = ref(null)
const event = ref(null)
const items = ref([])
const currentSlide = ref(0)
const sliderDuration = ref(5000)
const featureToggles = ref({})


let slideInterval = null

onMounted(async () => {
    await fetchFeatureToggles()
    await fetchProfilPerusahaan()
    await fetchKontenSlider()
    AOS.init({
        duration: 1000,
        once: false,
    });
})

onBeforeUnmount(() => {
    stopAutoSlide()
})

async function fetchFeatureToggles() {
    try {
        const response = await axios.get('/api/feature-toggles')
        featureToggles.value = response.data.data || {}
    } catch (err) {
    }
}

async function fetchProfilPerusahaan() {
    try {
        const response = await axios.get(`/api/profil-perusahaan`)
        profil_perusahaan.value = response.data.data
    } catch (err) {
       
    }
}

async function fetchKontenSlider() {
    try {
        const response = await axios.get("/api/konten-slider")
        const data = response.data.data

        if (!Array.isArray(data)) return

        // Set slider duration
        if (data.length > 0 && data[0].durasi_slider) {
            const durasi = parseInt(data[0].durasi_slider)
            if (!isNaN(durasi) && durasi > 0) {
                sliderDuration.value = durasi * 1000
            }
        }

        // Extract content sesuai toggle aktif
        artikel.value = featureToggles.value.artikel_module ? data.find(i => i?.artikel)?.artikel ?? null : null
        produk.value = featureToggles.value.produk_module ? data.find(i => i?.produk)?.produk ?? null : null
        galeri.value = featureToggles.value.galeri_module ? data.find(i => i?.galeri)?.galeri ?? null : null
        event.value = featureToggles.value.event_module ? data.find(i => i?.event)?.event ?? null : null

        // Only push enabled modules with valid data
        items.value = [
            artikel.value && {
                data: artikel.value,
                title: 'Artikel',
                description: 'Artikel terbaru dari kami'
            },
            produk.value && {
                data: produk.value,
                title: 'Produk',
                description: 'Produk unggulan yang kami tawarkan'
            },
            galeri.value && {
                data: galeri.value,
                title: 'Galeri',
                description: 'Koleksi foto dan video menarik'
            },
            event.value && {
                data: event.value,
                title: 'Event',
                description: 'Event dan kegiatan yang akan datang'
            }
        ].filter(Boolean) // filter yang null

        // Start slider only if there is item
        if (items.value.length > 0) {
            startAutoSlide()
        }

    } catch (err) {
        
    }
}


function getThumbnail(data, type) {
    if (!data) return "https://placehold.co/600x400?text=Coming+Soon"

    const basePath = "/storage/"
    const thumbnailMap = {
        artikel: data.thumbnail_artikel,
        produk: data.thumbnail_produk,
        galeri: data.thumbnail_galeri,
        event: data.thumbnail_event,
    }

    const thumbnail = thumbnailMap[type]

    if (thumbnail && Array.isArray(thumbnail) && thumbnail.length > 0 && thumbnail[0]) {
        return basePath + thumbnail[0]
    }

    return "https://placehold.co/600x400?text=Coming+Soon"
}

function getTitle(data) {
    if (!data) return "Coming Soon!"
    return (
        data.judul_artikel ||
        data.nama_produk ||
        data.judul_galeri ||
        data.nama_event ||
        "Tanpa Judul"
    )
}

function startAutoSlide() {
    stopAutoSlide()
    if (items.value.length === 0) return

    slideInterval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % items.value.length
    }, sliderDuration.value)
}

function stopAutoSlide() {
    if (slideInterval) {
        clearInterval(slideInterval)
        slideInterval = null
    }
}

function goToSlide(index) {
    currentSlide.value = index
}
</script>

<template>
    <div id="KontenSlider" class="w-full bg-secondary text-white py-28 px-6 lg:px-16 overflow-hidden font-custom">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up">
            <div class="text-base font-semibold font-custom">Jadi</div>
            <div class="text-5xl font-normal font-custom mb-4">Kenapa sih harus ke sini?</div>
            <div class="text-lg font-normal font-custom leading-relaxed">
                Karena {{ profil_perusahaan?.nama_perusahaan || "CMS" }} ini bukan cuma informatif, tapi
                juga nyaman dan seru buat dijelajahi!
            </div>
        </div>

        <!-- Slider -->
        <div class="relative max-w-screen-xl mx-auto overflow-hidden">
            <div v-if="items.length > 0">
                <!-- Slider content as-is -->
                <div class="flex transition-transform duration-500 ease-in-out"
                    :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                    <div v-for="(item, index) in items" :key="index"
                        class="min-w-full flex flex-col-reverse lg:flex-row items-center justify-between bg-white/5 border border-white/10 rounded-2xl overflow-hidden">

                        <!-- Text -->
                        <div class="w-full lg:w-1/2 p-8">
                            <div class="text-white text-2xl font-normal font-custom">{{ item.description }}</div>
                        </div>

                        <!-- Image -->
                        <div class="w-full lg:w-1/2 relative group h-80 overflow-hidden">
                            <img :src="getThumbnail(item.data, item.title.toLowerCase())" :alt="getTitle(item.data)"
                                class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105 blur-sm group-hover:blur-none" />

                            <div
                                class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                            <div
                                class="absolute bottom-4 left-4 right-4 z-20 text-white opacity-0 translate-y-4 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0">
                                <div class="text-4xl font-normal leading-tight truncate">{{ getTitle(item.data) }}</div>
                                <a :href="item.data?.slug ? `/${item.title.toLowerCase()}/${item.data.slug}` : '/'"
                                    class="inline-block mt-2 text-sm underline hover:no-underline">
                                    Lihat Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center gap-2 mt-6">
                    <button v-for="(item, index) in items" :key="'dot-' + index"
                        class="w-3 h-3 rounded-full transition-colors duration-200"
                        :class="currentSlide === index ? 'bg-white' : 'bg-white/40 hover:bg-white/60'"
                        @click="goToSlide(index)" :aria-label="`Go to slide ${index + 1}: ${item.title}`" />
                </div>
            </div>

            <!-- Fallback jika semua module tidak aktif -->
            <div v-else class="bg-white/5 border border-white/10 rounded-2xl p-8 text-center text-white/70">
                <div class="text-3xl font-normal mb-2">Tidak Ada Konten</div>
                <p class="text-sm">
                    Belum ada konten yang tersedia. Silakan hubungi admin untuk informasi lebih lanjut.
                </p>
            </div>
        </div>
    </div>
</template>