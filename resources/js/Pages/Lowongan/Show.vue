<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed, onBeforeUnmount } from "vue";
import axios from "axios";
import { usePage, Link } from "@inertiajs/vue3";
import LamaranTerkirim from "@/Components/Modal/LamaranTerkirim.vue";
import LowonganDetail from "@/Pages/Lowongan/LowonganDetail.vue";
import FormLamaran from "@/Pages/Lowongan/FormLamaran.vue";
import StatusLamaran from "@/Pages/Lowongan/StatusLamaran.vue";
import LoginOverlay from "@/Pages/Lowongan/LoginOverlay.vue";
import LowonganTutup from "@/Pages/Lowongan/LowonganTutup.vue";
import TestimoniTerkirim from "@/Components/Modal/TestimoniTerkirim.vue";
// Menambahkan import icons dari lucide
import { Wallet, RefreshCw, Loader, FrownIcon, Star, Home, ChevronRight, Briefcase } from "lucide-vue-next";

// Props dari route
const { slug } = defineProps({ slug: String });

// Global props dari Inertia
const page = usePage();
const theme = computed(() => page.props.theme);
const user = computed(() => page.props.auth?.user);

// === DATA
const item = ref(null);
const loading = ref(false);
const isLoggedIn = computed(() => !!user.value);
const showSuccessModal = ref(false);
const userApplication = ref(null);
const checkingApplication = ref(false);

// Testimoni data
const testimoniList = ref([]);
const showTestimoniModal = ref(false);
const newTestimoni = ref({
    isi_testimoni: "",
    rating: 5,
});

// === LIFECYCLE
onMounted(() => {
    fetchLowongan();
    document.documentElement.style.setProperty(
        "--color-secondary",
        theme.value.secondary
    );

    if (isLoggedIn.value && slug) {
        checkUserApplication();
    }

    // Refresh status saat tab menjadi aktif kembali
    const refreshOnVisibilityChange = () => {
        if (document.visibilityState === "visible" && userApplication.value) {
            refreshApplicationStatus();
        }
    };

    document.addEventListener("visibilitychange", refreshOnVisibilityChange);

    // Cleanup event listener
    onBeforeUnmount(() => {
        document.removeEventListener(
            "visibilitychange",
            refreshOnVisibilityChange
        );
    });
});

// === FUNCTIONS
async function checkUserApplication() {
    if (!user.value?.id_user) return;

    try {
        checkingApplication.value = true;
        // Add cache busting parameter to ensure fresh data
        const response = await axios.get(
            `/api/lamaran/user/${user.value.id_user}`,
            {
                params: {
                    _: Date.now(),
                },
            }
        );

        // Cek apakah user sudah melamar untuk lowongan ini
        if (response.data && Array.isArray(response.data.data)) {
            const existingApplication = response.data.data.find(
                (application) =>
                    application.lowongan.id_lowongan === item.value?.id_lowongan
            );

            if (existingApplication) {
                userApplication.value = existingApplication;
            } else {
                userApplication.value = null;
            }
        }
    } catch (err) {
        userApplication.value = null;
    } finally {
        checkingApplication.value = false;
    }
}

async function fetchLowongan() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/lowongan/${slug}`);

        if (response.data) {
            // LowonganViewResource structure (probably the whole response is the lowongan)
            if (response.data.id_lowongan) {
                item.value = response.data;
                // Periksa lamaran user setelah data lowongan didapat
                if (isLoggedIn.value) {
                    checkUserApplication();
                }
                // Fetch testimoni setelah data lowongan didapat
                await fetchTestimoni();
            }
            // Response might be wrapped in a data property
            else if (response.data.data && response.data.data.id_lowongan) {
                item.value = response.data.data;
                // Periksa lamaran user setelah data lowongan didapat
                if (isLoggedIn.value) {
                    checkUserApplication();
                }
                // Fetch testimoni setelah data lowongan didapat
                await fetchTestimoni();
            }
            // Handle error response that might have a message
            else if (response.data.message) {
                item.value = null;
            } else {
                item.value = null;
            }
        } else {
            item.value = null;
        }
    } catch (err) {
        item.value = null;
    } finally {
        loading.value = false;
    }
}

function handleApplicationSuccess() {
    showSuccessModal.value = true;
    // Force refresh application status after successful submission
    setTimeout(() => {
        checkUserApplication();
    }, 1000); // Give some time for the server to process
}

function closeModal() {
    showSuccessModal.value = false;
}

function updateUserApplication(newApplication) {
    userApplication.value = newApplication;
}

function formatTanggal(tanggal) {
    if (!tanggal) return "";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(tanggal).toLocaleDateString("id-ID", options);
}

function formatRupiah(value) {
    if (!value) return "Tidak disebutkan";
    const numberValue = Number(value);
    if (isNaN(numberValue)) return value;
    return `Rp${numberValue.toLocaleString("id-ID")},00`;
}

function isLowonganActive(tanggalDibuka, tanggalDitutup) {
    const now = new Date();
    const dibuka = new Date(tanggalDibuka);
    const ditutup = new Date(tanggalDitutup);

    return now >= dibuka && now <= ditutup;
}

// Testimoni functions
async function fetchTestimoni() {
    if (!item.value) return;
    try {
        const response = await axios.get(
            `/api/testimoni/lowongan/${item.value.id_lowongan}`
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
        await axios.post(`/api/testimoni/lowongan/${item.value.id_lowongan}`, {
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
            class="w-full px-4 lg:px-16 py-10 lg:py-20 bg-white flex flex-col items-start gap-10 font-custom"
        >
            <!-- Skeleton Loading -->
            <div
                v-if="loading"
                class="flex flex-col gap-10 w-full max-w-7xl mx-auto"
            >
                <!-- Skeleton template here (unchanged) -->
            </div>

            <!-- Actual Lowongan Detail - Show when not loading -->
            <LowonganDetail
                v-else-if="item"
                :lowongan="item"
                :formatTanggal="formatTanggal"
                :formatRupiah="formatRupiah"
                :isLowonganActive="isLowonganActive"
            >
                <!-- Right Column Slot -->
                <div class="w-full lg:w-1/3">
                    <div
                        class="sticky top-24 bg-gradient-to-br from-secondary via-secondary to-black border border-gray-100 rounded-xl p-6 shadow-sm max-h-[calc(100vh-4rem)] lg:max-h-[calc(100vh-6rem)] overflow-y-auto"
                    >
                        <h3 class="text-lg font-semibold mb-4 text-white">
                            Lamar Posisi Ini
                        </h3>

                        <div
                            v-if="
                                isLowonganActive(
                                    item.tanggal_dibuka,
                                    item.tanggal_ditutup
                                )
                            "
                        >
                            <!-- Login Overlay - Show when user is not logged in -->
                            <LoginOverlay v-if="!isLoggedIn" />

                            <!-- Checking Application Status - Menggunakan Lucide Loader -->
                            <div
                                v-else-if="checkingApplication"
                                class="py-8 flex flex-col items-center justify-center"
                            >
                                <Loader
                                    class="animate-spin h-8 w-8 text-white mb-4"
                                />
                                <p class="text-gray-200">
                                    Memeriksa status lamaran...
                                </p>
                            </div>

                            <!-- Already Applied - Display Application Status -->
                            <StatusLamaran
                                v-else-if="userApplication"
                                :application="userApplication"
                                :formatTanggal="formatTanggal"
                                @update:application="updateUserApplication"
                            />

                            <!-- Application Form - Show when user is logged in and hasn't applied -->
                            <FormLamaran
                                v-else
                                :lowongan="item"
                                :user="user"
                                @success="handleApplicationSuccess"
                            />
                        </div>

                        <!-- Show when lowongan is not active -->
                        <LowonganTutup v-else />
                    </div>
                </div>
            </LowonganDetail>

            <!-- No Data Found - Menggunakan Lucide FrownIcon -->
            <div v-else class="w-full max-w-3xl mx-auto text-center py-12">
                <FrownIcon class="mx-auto h-16 w-16 text-gray-400" />
                <h3 class="mt-2 text-xl font-medium text-gray-900">
                    Lowongan tidak ditemukan
                </h3>
                <p class="mt-1 text-gray-500">
                    Lowongan mungkin sudah tidak tersedia atau telah dihapus.
                </p>
                <div class="mt-6">
                    <Link
                        href="/lowongan"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-secondary hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary"
                    >
                        Kembali ke Daftar Lowongan
                    </Link>
                </div>
            </div>

            <!-- TESTIMONI LIST -->
            <div
                v-if="testimoniList.length && item"
                class="w-full max-w-3xl mx-auto border-t pt-10"
            >
                <span class="text-sm text-gray-500">Ulasan dari</span>
                <h2 class="text-2xl font-semibold mb-4">
                    {{ item.judul_lowongan }}
                </h2>
                <div class="space-y-6">
                    <div
                        v-for="testimoni in testimoniList"
                        :key="testimoni.id_testimoni_lowongan"
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
                                        {{ testimoni.user?.name || "Anonim" }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ testimoni.user?.email || "" }}
                                    </p>
                                </div>
                            </div>

                            <!-- Bintang Rating -->
                            <div class="flex items-center gap-2 sm:mt-0 mt-2">
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
            <div
                v-if="isLoggedIn && item"
                class="w-full max-w-3xl mx-auto mt-10"
            >
                <span class="text-sm text-gray-500"
                    >Sudah pernah bekerja di posisi
                    {{ item.judul_lowongan }}?</span
                >
                <h2 class="text-xl font-semibold mb-3">
                    Tulis pengalamanmu sendiri
                </h2>
                <form
                    @submit.prevent="submitTestimoni"
                    class="space-y-4 border border-gray-200 rounded-xl bg-gray-50 transition hover:bg-gray-100 p-4"
                >
                    <textarea
                        v-model="newTestimoni.isi_testimoni"
                        class="w-full rounded-md border border-gray-300 bg-white p-3 focus:outline-none focus:ring-1 focus:ring-secondary focus:border-secondary"
                        rows="4"
                        placeholder="Tulis testimoni kamu tentang pengalaman kerja di posisi ini..."
                        required
                    ></textarea>

                    <!-- Rating Star Selector -->
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-700">Rating:</span>
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
                v-else-if="item"
                class="w-full max-w-3xl mx-auto mt-6 bg-yellow-50 border border-yellow-300 text-yellow-800 p-6 rounded-xl flex items-center gap-4"
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
                        Login terlebih dahulu untuk menulis testimoni dan
                        berbagi pengalamanmu.
                    </p>
                </div>
            </div>
        </div>

        <!-- Success Modal Component -->
        <LamaranTerkirim
            :show="showSuccessModal"
            :auto-close="false"
            @close="closeModal"
        />

        <!-- Testimoni Success Modal -->
        <TestimoniTerkirim
            :show="showTestimoniModal"
            @close="closeTestimoniModal"
            @write-another="writeAnotherTestimoni"
            :auto-close="false"
        />
    </AppLayout>
</template>
