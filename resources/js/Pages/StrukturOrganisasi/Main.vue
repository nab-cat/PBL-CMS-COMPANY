<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { Link } from "@inertiajs/vue3";

const struktur_organisasi = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(() => {
    fetchStrukturOrganisasi();
});

async function fetchStrukturOrganisasi() {
    try {
        loading.value = true;
        // Add timestamp to prevent browser caching
        const timestamp = new Date().getTime();
        const response = await axios.get(
            `/api/struktur-organisasi?_t=${timestamp}`
        );
        struktur_organisasi.value = response.data.data;
        loading.value = false;
    } catch (err) {
        error.value =
            "Gagal memuat data struktur organisasi atau terjadi kesalahan";
        loading.value = false;
    }
}

const teamMembers = computed(() => {
    return struktur_organisasi.value.map((item) => ({
        image:
            item.user?.foto_profil ||
            `https://ui-avatars.com/api/?name=${encodeURIComponent(
                item.user?.name || "User"
            )}&color=7F9CF5&background=EBF4FF`,
        name: item.user?.name || "Nama tidak tersedia",
        jobTitle: item.jabatan || "Jabatan tidak tersedia",
        description: item.deskripsi || "",
    }));
});
</script>

<template>
    <AppLayout>
        <div
            class="max-w-screen-xl mx-auto px-6 md:px-10 py-20 md:py-28 font-custom text-black"
        >
            <!-- Header Section with modern design -->
            <div class="text-center mb-16 md:mb-24 max-w-3xl mx-auto">
                <span
                    class="inline-block px-4 py-1 bg-secondary/10 text-secondary rounded-full text-sm font-medium mb-4"
                    >Tim Profesional</span
                >
                <h2
                    class="text-4xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent"
                >
                    Struktur Organisasi
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Mereka yang berdedikasi untuk memberikan pelayanan terbaik
                    dan memastikan perusahaan kami terus berkembang dengan
                    inovasi.
                </p>
            </div>

            <!-- Loading and Error States with modern styling -->
            <div v-if="loading" class="text-center py-20">
                <div
                    class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-secondary border-t-transparent mb-3"
                ></div>
                <p class="text-gray-500 font-medium">
                    Memuat data struktur organisasi...
                </p>
            </div>

            <div
                v-else-if="error"
                class="bg-red-50 text-red-600 p-6 rounded-xl text-center border border-red-100 max-w-lg mx-auto"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10 mx-auto mb-3 text-red-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <p class="font-medium">{{ error }}</p>
            </div>

            <!-- Modern Team Members Grid -->
            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 md:gap-12"
            >
                <div
                    v-for="(member, index) in teamMembers"
                    :key="index"
                    class="group relative"
                >
                    <!-- Double card effect with subtle border instead of shadow -->
                    <div
                        class="absolute inset-1 -right-2 -bottom-2 rounded-2xl border border-gray-200 bg-gray-50"
                    ></div>

                    <!-- Main card -->
                    <div
                        class="relative bg-white rounded-2xl p-6 border border-gray-200 transition-all duration-300 flex flex-col items-center text-center h-96"
                    >
                        <!-- Modern Profile Image - No border by default, appears on hover -->
                        <div class="mb-6">
                            <div
                                class="w-32 h-32 rounded-full overflow-hidden border border-gray-100 transition-all duration-300 group-hover:ring-4 group-hover:ring-secondary"
                            >
                                <img
                                    :src="member.image"
                                    :alt="`${member.name}'s photo`"
                                    class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-300"
                                    @error="
                                        $event.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                            member.name
                                        )}&color=7F9CF5&background=EBF4FF`
                                    "
                                />
                            </div>
                        </div>

                        <!-- Member Info with modern typography -->
                        <div
                            class="flex flex-col items-center space-y-2 flex-1"
                        >
                            <h3
                                class="text-xl font-bold text-gray-800 group-hover:text-secondary transition-colors duration-300"
                            >
                                {{ member.name }}
                            </h3>
                            <p
                                class="px-3 py-1 bg-secondary/10 text-secondary rounded-full text-sm font-medium"
                            >
                                {{ member.jobTitle }}
                            </p>
                            <div class="flex-1 flex items-start mt-3 w-full">
                                <p
                                    class="text-gray-600 text-sm leading-relaxed line-clamp-4 text-center"
                                >
                                    {{ member.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Recruitment Section -->
            <div class="mt-24 relative">
                <!-- Double card effect with subtle border instead of shadow -->
                <div
                    class="absolute inset-1 -right-2 -bottom-2 rounded-3xl border border-gray-200 bg-gray-50"
                ></div>

                <!-- Main content -->
                <div
                    class="relative bg-white rounded-3xl p-8 md:p-12 border border-gray-200 flex flex-col md:flex-row items-center"
                >
                    <div class="md:w-2/3 mb-8 md:mb-0 md:pr-12">
                        <span
                            class="inline-block px-4 py-1 bg-secondary/10 text-secondary rounded-full text-sm font-medium mb-4"
                            >Lowongan</span
                        >
                        <h3
                            class="text-3xl md:text-4xl font-bold mb-5 bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent"
                        >
                            Jadilah Bagian dari Tim Kami
                        </h3>
                        <p class="text-lg leading-relaxed mb-8 text-gray-600">
                            Kami selalu mencari talenta-talenta terbaik yang
                            ingin berkembang dan berkolaborasi dalam lingkungan
                            yang inovatif. Temukan peluang karir yang sesuai
                            dengan passion Anda bersama kami.
                        </p>
                        <Link
                            class="px-8 py-3.5 rounded-full bg-secondary text-white font-medium hover:bg-black transition-all duration-300 flex items-center"
                            href="/lowongan"
                        >
                            <span>Lihat Lowongan</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 ml-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                                />
                            </svg>
                        </Link>
                    </div>
                    <div class="md:w-1/3">
                        <img
                            src="/image/recruitment.svg"
                            alt="Recruitment"
                            class="w-full max-w-xs mx-auto transform hover:scale-105 transition-transform duration-500"
                            onerror="this.src='/image/placeholder.webp';"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
