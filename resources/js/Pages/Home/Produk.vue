<script setup>
import { ChevronRight } from 'lucide-vue-next';
import { ref, onMounted } from "vue";
import axios from "axios";
import AOS from "aos";
import "aos/dist/aos.css";
import { nextTick } from "vue";

const produk = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(() => {
    fetchProduk();
    AOS.init({
        duration: 1000,
        once: false,
    });
});

async function fetchProduk() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/produk`);
        const allProduk = response.data.data;
        produk.value = getRandomProduk(allProduk, 4);
    } catch (err) {
        error.value = "Produk not found or an error occurred";
     
    } finally {
        loading.value = false;
        // Refresh AOS to ensure animations work after data change
        await nextTick();
        AOS.refresh();
    }
}

function getRandomProduk(array, count) {
    const shuffled = array.sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";

    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }

    return `/storage/${image}`;
}
</script>

<template>
    <div
        class="w-full px-6 lg:px-16 py-28 bg-white flex flex-col text-black items-center gap-20 overflow-hidden font-custom">
        <!-- Wrapper untuk membatasi lebar -->
        <div class="w-full max-w-screen-xl mx-auto">
            <!-- Judul Section -->
            <div class="text-center max-w-[768px] flex flex-col items-center gap-4 mx-auto" data-aos="fade-up">
                <div class="text-base font-semibold ">Mau lihat lebih jauh?</div>
                <div class="text-5xl font-normal ">Jelajahi produk kami</div>
                <div class="text-lg font-normal ">Lihat list lengkap produk, atau sekadar Window
                    Shopping.</div>

                <!-- Tombol Link ke /produk -->
                <a href="/produk"
                    class="mt-4 inline-flex items-center gap-2 px-6 text-white py-3 bg-secondary font-medium rounded-full hover:bg-black transition duration-200">
                    Lihat Semua Produk
                </a>
            </div>

            <!-- Grid Produk atau Skeleton Loading -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full mt-12" data-aos="fade-up">
                <!-- Skeleton Loading -->
                <div v-if="loading" v-for="n in 4" :key="n"
                    class="relative group p-6 rounded-2xl bg-gray-200 animate-pulse flex flex-col h-96 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-300 to-gray-200 rounded-2xl"></div>
                    <div class="relative z-20 mt-auto flex flex-col gap-2">
                        <div class="h-6 bg-gray-300 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-300 rounded w-full"></div>
                        <div class="h-4 bg-gray-300 rounded w-1/2"></div>
                    </div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="col-span-full text-center py-12">
                    <div class="text-red-500 text-lg">{{ error }}</div>
                </div>

                <!-- Produk Cards -->
                <template v-else>
                    <!-- Loop produk yang ada -->
                    <div v-for="item in produk" :key="item.id_produk"
                        class="relative group p-6 rounded-2xl bg-cover bg-center bg-no-repeat flex flex-col h-96 overflow-hidden"
                        :style="item.thumbnail_produk && item.thumbnail_produk.length > 0
                            ? `background-image: url('${getImageUrl(item.thumbnail_produk)}')`
                            : 'background-color: #ccc'">

                        <!-- Overlay -->
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                        <!-- Konten -->
                        <div
                            class="relative z-20 mt-auto text-white flex flex-col gap-2 opacity-0 translate-y-4 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0">
                            <div class="text-2xl font-normal">{{ item.nama_produk }}</div>
                            <div class="text-sm font-normal leading-snug truncate">{{ item.deskripsi_produk }}</div>
                            <a :href="`/produk/${item.slug}`"
                                class="flex items-center gap-2 text-white font-medium hover:underline">
                                Lihat Selengkapnya
                                <ChevronRight class="w-3" />
                            </a>
                        </div>
                    </div>

                    <!-- Card tambahan jika produk kurang dari 4 -->
                    <div v-if="produk.length > 0 && produk.length < 4"
                        class="p-6 rounded-2xl border bg-gray-100 flex items-center justify-center h-96 text-center col-span-1">
                        <div class="text-black text-lg font-normal">
                            Produk lainnya akan segera hadir!
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>