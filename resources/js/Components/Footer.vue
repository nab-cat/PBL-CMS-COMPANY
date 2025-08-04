<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    Phone,
    Mail,
    MapPin,
    Facebook,
    Instagram,
    Linkedin,
    Twitter,
    Youtube,
    Github,
    MessageCircle,
    Send, 
    Music
} from "lucide-vue-next";

const profil_perusahaan = ref(null);
const mediaSosial = ref([]);
const loading = ref(false);
const error = ref(null);

const maxKalimat = 1

// Function to strip HTML tags
function stripHtml(html) {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, '');
}

const truncatedDeskripsi = computed(() => {
    if (!profil_perusahaan.value?.deskripsi_perusahaan) return 'Deskripsi perusahaan belum tersedia.'

    // Strip HTML tags first, then split into sentences
    const cleanText = stripHtml(profil_perusahaan.value.deskripsi_perusahaan);
    const kalimat = cleanText.split(/(?<=[.!?])\s+/);
    return kalimat.slice(0, maxKalimat).join(' ');
})

const showReadMore = computed(() => {
    if (!profil_perusahaan.value?.deskripsi_perusahaan) return false
    const cleanText = stripHtml(profil_perusahaan.value.deskripsi_perusahaan);
    return cleanText.split(/(?<=[.!?])\s+/).length > maxKalimat
})

onMounted(() => {
    fetchProfilPerusahaan();
    fetchMediaSosial();
});

async function fetchProfilPerusahaan() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/profil-perusahaan/`);
        profil_perusahaan.value = response.data.data;
        loading.value = false;
    } catch (err) {
        error.value = "Event not found or an error occurred";
        loading.value = false;
    }
}

async function fetchMediaSosial() {
    try {
        const response = await axios.get('/api/media-sosial');
        mediaSosial.value = [];

        // Process the updated response format
        for (const [platform, data] of Object.entries(response.data.data)) {
            if (data.active) {
                mediaSosial.value.push({
                    name: platform,
                    link: data.link
                });
            }
        }
    } catch (err) {
    }
}

function getMediaSosialLink(platform) {
    // This function will be populated with real data from your API
    // For now, it returns placeholder URLs
    return "#"; // In reality, this would return the actual link from your API
}

function getMediaSosialComponent(platform) {
    // Map platform names to Lucide components
    const iconMap = {
        'Facebook': Facebook,
        'Instagram': Instagram,
        'LinkedIn': Linkedin,
        'Twitter': Twitter,
        'YouTube': Youtube,
        'TikTok': Music,
        'WhatsApp Business': MessageCircle,
        'Telegram': Send,
        'GitHub': Github
    };

    return iconMap[platform] || null;
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }

    return `/storage/${image}`;
}

function lihatSelengkapnya() {
    alert(profil_perusahaan.value.sejarah_perusahaan)
}
</script>

<template>
    <footer class="bg-secondary text-white w-full font-custom text-sm">
        <div class="px-5 pt-5 lg:px-10">
            <!-- Tambahan wrapper untuk membatasi lebar -->
            <div class="max-w-screen-xl mx-auto">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-5 lg:gap-10 lg:items-stretch mt-10">
                    <!-- Kolom 1 -->
                    <div v-if="profil_perusahaan" class="lg:w-[120%]">
                        <div class="flex items-center justify-center h-32">
                            <img :src="getImageUrl(profil_perusahaan?.logo_perusahaan)" alt="Logo Perusahaan"
                                class="w-20 object-contain" />
                        </div>
                        <h4 class="font-bold text-center text-lg">{{ profil_perusahaan?.nama_perusahaan }}</h4>
                        <p class="mt-4 text-left">
                            {{ truncatedDeskripsi }}
                            <Link v-if="showReadMore" href="/profil-perusahaan" class="text-blue-400 cursor-pointer">
                            ... Baca selengkapnya
                            </Link>
                        </p>

                        <div class="mt-6">
                            <h4 class="font-bold pb-1">Hubungi Kami</h4>
                            <div class="flex items-center gap-2">
                                <Phone class="w-4" />
                                <span>{{ profil_perusahaan?.telepon_perusahaan || 'Telepon perusahaan belum tersedia.'
                                    }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Mail class="w-4" />
                                <span>{{ profil_perusahaan?.email_perusahaan || 'Email perusahaan belum tersedia.'
                                    }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2 -->
                    <div class="flex flex-col justify-center h-full lg:col-span-2 lg:pl-20">
                        <h4 class="font-bold mb-4">Quick Links</h4>
                        <ul class="grid grid-cols-2 gap-y-2">
                            <li>
                                <Link href="/" class="hover:underline">Beranda</Link>
                            </li>
                            <li>
                                <Link href="/galeri" class="hover:underline">Galeri</Link>
                            </li>
                            <li>
                                <Link href="/profil-perusahaan" class="hover:underline">Tentang Kami</Link>
                            </li>
                            <li>
                                <Link href="/unduhan" class="hover:underline">Unduhan</Link>
                            </li>
                            <li>
                                <Link href="/produk" class="hover:underline">Produk</Link>
                            </li>
                            <li>
                                <Link href="/event" class="hover:underline">Event</Link>
                            </li>
                            <li>
                                <Link href="/artikel" class="hover:underline">Artikel</Link>
                            </li>
                            <li>
                                <Link href="/lowongan" class="hover:underline">Lowongan</Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Kolom 3 -->
                    <div class="space-y-6 flex flex-col justify-center h-full">
                        <div>
                            <h4 class="font-bold mb-4">Our Location</h4>
                            <div class="flex items-start gap-2">
                                <MapPin class="w-10 lg:w-20 self-center" />
                                <span class="leading-relaxed">
                                    {{ profil_perusahaan?.alamat_perusahaan || 'Alamat perusahaan belum tersedia.' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-4">Follow Us</h4>
                            <div v-if="mediaSosial.length > 0" class="flex flex-wrap gap-4">
                                <a v-for="(platform, index) in mediaSosial" :key="index" :href="platform.link"
                                    target="_blank" rel="noopener noreferrer"
                                    class="hover:text-primary transition-colors" :title="platform.name">
                                    <component :is="getMediaSosialComponent(platform.name)" class="w-5 h-5" />
                                </a>
                            </div>
                            <p v-else class="text-sm italic">Belum ada media sosial yang aktif.</p>
                        </div>
                    </div>

                    <!-- Kolom 4: Google Maps -->
                    <div class="flex flex-col justify-center h-full">
                        <div class="w-full aspect-video rounded-lg overflow-hidden">
                            <iframe v-if="profil_perusahaan?.map_embed_perusahaan" class="w-full h-full"
                                :src="profil_perusahaan.map_embed_perusahaan" width="600" height="450" style="border:0;"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                            <div v-else
                                class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-400">
                                <span>Peta lokasi belum tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="bg-white text-black h-10 mt-10">
            <p class="text-center leading-10">
                © 2025 {{ profil_perusahaan?.nama_perusahaan }}.
            </p>
        </div>
    </footer>
</template>