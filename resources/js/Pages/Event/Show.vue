<script setup>
import axios from "axios";
import { ref, onMounted, computed, watch, onUnmounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    Home,
    Calendar,
    MapPin,
    ChevronRight,
    Pencil,
    User,
    CheckCircle,
    Copy,
    UserPlus,
    UserMinus,
    LogIn,
    Star // Add Star icon for ratings
} from "lucide-vue-next";
import AppLayout from "@/Layouts/AppLayout.vue";
import CopyLink from "@/Components/Modal/CopyLink.vue";
import EventTimer from "@/Pages/Event/EventTimer.vue";
import PendaftaranBerhasil from "@/Components/Modal/PendaftaranBerhasil.vue";
import PembatalanBerhasil from "@/Components/Modal/PembatalanBerhasil.vue";
import KonfirmasiPembatalan from "@/Components/Modal/KonfirmasiPembatalan.vue";
import LoginDiperlukan from "@/Components/Modal/LoginDiperlukan.vue";
import TestimoniTerkirim from "@/Components/Modal/TestimoniTerkirim.vue"; // Add this component

// Add these new state variables for testimonials
const testimoniList = ref([]);
const showTestimoniModal = ref(false);
const newTestimoni = ref({
    isi_testimoni: "",
    rating: 5,
});

const event = ref(null);
const loading = ref(true);
const error = ref(null);
const registering = ref(false);
const page = usePage();
const showCopyModal = ref(false);
const showRegistrationSuccessModal = ref(false);
const showCancellationSuccessModal = ref(false);
const showCancellationConfirmModal = ref(false);
const showErrorModal = ref(false);
const showLoginRequiredModal = ref(false);
const errorMessage = ref('');

const props = defineProps({
    slug: String,
});

onMounted(() => {
    fetchEvent();
});

// Add this function to fetch testimonials
async function fetchTestimoni() {
    if (!event.value) return;
    try {
        const response = await axios.get(
            `/api/testimoni/event/${event.value.id_event}`
        );
        testimoniList.value = response.data.data;
    } catch (err) {
    }
}

async function fetchEvent() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/event/${props.slug}`, {
            withCredentials: true
        });
        event.value = response.data.data;

        // If user is authenticated, check registration status separately
        if (isAuthenticated.value && event.value) {
            await checkRegistrationStatus();
        }

        // Fetch testimonials after event is loaded
        await fetchTestimoni();
    } catch (err) {
        error.value = "Event not found or an error occurred";
    } finally {
        loading.value = false;
    }
}

// Add this new function
async function checkRegistrationStatus() {
    if (!isAuthenticated.value || !event.value) return;

    try {
        const response = await axios.get(`/api/event/${props.slug}/check-registration`, {
            withCredentials: true
        });
        if (response.data && typeof response.data.is_registered !== 'undefined') {
            event.value.is_registered = response.data.is_registered;
        }
    } catch (err) {
    }
}

function getImageUrl(image) {
    if (!image) return "/image/placeholder.webp";
    if (typeof image === "object" && image !== null) {
        return image[0] ? `/storage/${image[0]}` : "/image/placeholder.webp";
    }
    return `/storage/${image}`;
}

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function formatTime(date) {
    if (!date) return "";
    return new Date(date).toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });
}

const timeRemaining = ref({
    total: 0,
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
});

let countdownInterval;

watch(event, () => {
    if (event.value) {
        clearInterval(countdownInterval);
        startCountdown(new Date(event.value.waktu_start_event));
    }
});

function startCountdown(targetDate) {
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance <= 0) {
            clearInterval(countdownInterval);
            timeRemaining.value = {
                total: 0,
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0,
            };
            return;
        }

        timeRemaining.value = {
            total: distance,
            days: Math.floor(distance / (1000 * 60 * 60 * 24)),
            hours: Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            ),
            minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
            seconds: Math.floor((distance % (1000 * 60)) / 1000),
        };
    }

    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

onUnmounted(() => {
    clearInterval(countdownInterval);
});

// Check if user is authenticated
const isAuthenticated = computed(() => {
    return page.props.auth && page.props.auth.user;
});

// Check if event has finished
const isEventFinished = computed(() => {
    if (!event.value || !event.value.waktu_end_event) return false;
    const endTime = new Date(event.value.waktu_end_event).getTime();
    const now = new Date().getTime();
    return now > endTime;
});

// Register for event - updated to use modals
async function registerForEvent() {
    if (!isAuthenticated.value) {
        showLoginRequiredModal.value = true;
        return;
    }

    try {
        registering.value = true;
        const response = await axios.post(`/api/event/${props.slug}/register`);

        if (response.data.status === "success") {
            // Update event data
            event.value.jumlah_pendaftar = response.data.jumlah_pendaftar;
            event.value.is_registered = response.data.is_registered;
            // Show success modal instead of alert
            showRegistrationSuccessModal.value = true;
        }
    } catch (err) {
        if (err.response && err.response.data) {
            errorMessage.value = err.response.data.message || "Gagal mendaftar event";
        } else {
            errorMessage.value = "Terjadi kesalahan saat mendaftar event";
        }
        showErrorModal.value = true;
    } finally {
        registering.value = false;
    }
}

// Unregister from event - updated to use modals
async function unregisterFromEvent() {
    if (!isAuthenticated.value) {
        return;
    }

    // Show confirmation modal instead of confirm()
    showCancellationConfirmModal.value = true;
}

// Actual unregister function to call after confirmation
async function doUnregister() {
    try {
        registering.value = true;
        const response = await axios.delete(
            `/api/event/${props.slug}/register`
        );

        if (response.data.status === "success") {
            // Update event data
            event.value.jumlah_pendaftar = response.data.jumlah_pendaftar;
            event.value.is_registered = response.data.is_registered;
            // Show success modal instead of alert
            showCancellationSuccessModal.value = true;
        }
    } catch (err) {
        if (err.response && err.response.data) {
            errorMessage.value = err.response.data.message || "Gagal membatalkan pendaftaran";
        } else {
            errorMessage.value = "Terjadi kesalahan saat membatalkan pendaftaran";
        }
        showErrorModal.value = true;
    } finally {
        registering.value = false;
    }
}

// Copy event link
async function copyEventLink() {
    const url = window.location.href;
    try {
        if (navigator.clipboard && typeof navigator.clipboard.writeText === "function") {
            await navigator.clipboard.writeText(url);
            showCopyModal.value = true;
        } else {
            throw new Error("Clipboard API not available");
        }
    } catch (err) {
        console.warn("Clipboard write failed, using fallback:", err);
        fallbackCopy(url);
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

function closeCopyModal() {
    showCopyModal.value = false;
}

// Add function to submit testimonials
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
        await axios.post(`/api/testimoni/event/${event.value.id_event}`, {
            ...newTestimoni.value,
            id_user: user.value.id_user,
        });

        // Show success modal
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

// Add the user computed property to get current user info
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <AppLayout>
        <div class="bg-white min-h-screen py-6 px-4 md:px-8 text-black font-custom">
            <div class="max-w-6xl mx-auto px-2 sm:px-4 space-y-10">
                <!-- Breadcrumb -->
                <div class="mt-6">
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
                                <Link href="/event"
                                    class="inline-flex items-center text-gray-500 hover:text-secondary transition-colors">
                                <Calendar class="w-4 h-4 mr-1.5" />
                                Event
                                </Link>
                            </li>
                            <li class="inline-flex items-center">
                                <ChevronRight class="w-4 h-4 text-gray-400 mx-1.5" />
                                <span
                                    class="text-sm font-medium text-gray-700 truncate max-w-[140px] sm:max-w-[200px] md:max-w-[300px]"
                                    :title="event?.nama_event">
                                    {{ event?.nama_event || "Memuat..." }}
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- Loading/Error -->
                <div v-if="loading" class="space-y-10">
                    <!-- Skeleton for Featured Image -->
                    <div class="w-full aspect-[3/1] bg-gray-200 animate-pulse rounded-xl"></div>

                    <!-- Skeleton for Title and Date -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-6 bg-gray-200 animate-pulse rounded-full"></div>
                        </div>
                        <div class="w-3/4 h-8 bg-gray-200 animate-pulse rounded"></div>
                        <div class="w-1/2 h-4 bg-gray-200 animate-pulse rounded"></div>
                    </div>

                    <!-- Skeleton for Info Card -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <div class="flex flex-col md:flex-row justify-between gap-6 mb-6">
                            <div class="w-32 h-6 bg-gray-200 animate-pulse rounded"></div>
                            <div class="flex items-center gap-3">
                                <div class="w-24 h-8 bg-gray-200 animate-pulse rounded-lg"></div>
                                <div class="w-28 h-8 bg-gray-200 animate-pulse rounded-lg"></div>
                            </div>
                        </div>
                        <div class="flex flex-col md:grid md:grid-cols-3 gap-y-4 md:gap-y-6 md:gap-x-6">
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-gray-200 animate-pulse rounded"></div>
                                <div class="space-y-2 flex-1">
                                    <div class="w-24 h-4 bg-gray-200 animate-pulse rounded"></div>
                                    <div class="w-32 h-3 bg-gray-200 animate-pulse rounded"></div>
                                    <div class="w-28 h-3 bg-gray-200 animate-pulse rounded"></div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-gray-200 animate-pulse rounded"></div>
                                <div class="space-y-2 flex-1">
                                    <div class="w-16 h-4 bg-gray-200 animate-pulse rounded"></div>
                                    <div class="w-24 h-3 bg-gray-200 animate-pulse rounded"></div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-gray-200 animate-pulse rounded"></div>
                                <div class="space-y-2 flex-1">
                                    <div class="w-20 h-4 bg-gray-200 animate-pulse rounded"></div>
                                    <div class="w-28 h-3 bg-gray-200 animate-pulse rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Skeleton for Countdown -->
                    <div class="space-y-4">
                        <div class="w-64 h-6 bg-gray-200 animate-pulse rounded mx-auto"></div>
                        <div class="flex justify-center gap-4">
                            <div class="w-16 h-12 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-16 h-12 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-16 h-12 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-16 h-12 bg-gray-200 animate-pulse rounded"></div>
                        </div>
                    </div>

                    <!-- Skeleton for Description -->
                    <div class="space-y-3">
                        <div class="w-40 h-6 bg-gray-200 animate-pulse rounded"></div>
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 space-y-3">
                            <div class="w-full h-4 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-5/6 h-4 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-4/5 h-4 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-full h-4 bg-gray-200 animate-pulse rounded"></div>
                            <div class="w-3/4 h-4 bg-gray-200 animate-pulse rounded"></div>
                        </div>
                    </div>
                </div>
                <div v-else-if="error" class="text-center py-20 text-red-600">
                    {{ error }}
                </div>

                <!-- Detail -->
                <div v-else class="space-y-10">
                    <!-- Featured Image -->
                    <div class="w-full aspect-[3/1] bg-gray-100">
                        <img :src="getImageUrl(event.thumbnail_event)" :alt="event.nama_event"
                            class="w-full h-full object-cover rounded-xl" />
                    </div>

                    <!-- Title and Date -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-4">
                            <div class="px-3 py-1 rounded-full border text-sm font-semibold bg-black/5 text-black">
                                Event
                            </div>
                        </div>
                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-semibold">
                            {{ event.nama_event }}
                        </h1>
                        <div class="text-sm text-gray-600">
                            {{ formatDate(event.waktu_start_event) }} |
                            {{ formatTime(event.waktu_start_event) }} -
                            {{ formatTime(event.waktu_end_event) }}
                        </div>
                    </div>

                    <!-- Info Blocks in Card with Registration -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <div class="flex flex-col md:flex-row justify-between gap-6 mb-6">
                            <div>
                                <h3 class="font-semibold text-lg mb-4">Informasi Event</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 text-sm font-medium"
                                    @click="copyEventLink">
                                    <Copy class="w-4 h-4" />
                                    <span class="hidden sm:inline">Salin Link</span>
                                </button>

                                <!-- Registration buttons with icons -->
                                <div>
                                    <button v-if="!event.is_registered" @click="registerForEvent"
                                        :disabled="registering || !isAuthenticated || isEventFinished"
                                        class="flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium">
                                        <UserPlus class="w-4 h-4" />
                                        <span>{{ registering ? "Mendaftar..." : isEventFinished ? "Event Selesai" :
                                            "Daftar Event" }}</span>
                                    </button>
                                    <button v-else @click="unregisterFromEvent"
                                        :disabled="registering || isEventFinished"
                                        class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium">
                                        <UserMinus class="w-4 h-4" />
                                        <span>{{ registering ? "Membatalkan..." : "Batal Daftar" }}</span>
                                    </button>
                                </div>
                                <a v-if="!isAuthenticated" href="/login"
                                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                                    <LogIn class="w-4 h-4" />
                                    <span>Login</span>
                                </a>
                            </div>
                        </div>

                        <div class="flex flex-col md:grid md:grid-cols-3 gap-y-4 md:gap-y-6 md:gap-x-6">
                            <div class="flex items-start gap-3 pb-4 md:pb-0 border-b md:border-b-0 border-gray-200">
                                <Calendar class="w-5 h-5 text-secondary mt-1 flex-shrink-0" />
                                <div>
                                    <div class="font-medium">Tanggal & Waktu</div>
                                    <div>
                                        {{ formatDate(event.waktu_start_event) }}
                                    </div>
                                    <div>
                                        {{ formatTime(event.waktu_start_event) }} -
                                        {{ formatTime(event.waktu_end_event) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 py-4 md:py-0 border-b md:border-b-0 border-gray-200">
                                <MapPin class="w-5 h-5 text-secondary mt-1 flex-shrink-0" />
                                <div>
                                    <div class="font-medium">Lokasi</div>
                                    <a :href="event.link_lokasi_event" target="_blank"
                                        class="text-secondary hover:underline">
                                        {{ event.lokasi_event }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 pt-4 md:pt-0">
                                <UserCheck class="w-5 h-5 text-secondary mt-1 flex-shrink-0" />
                                <div>
                                    <div class="font-medium">Pendaftar</div>
                                    <div class="text-sm text-gray-600">
                                        {{ event.jumlah_pendaftar || 0 }} orang
                                        terdaftar
                                    </div>
                                    <div v-if="event.is_registered" class="flex items-center gap-2 text-secondary mt-1">
                                        <CheckCircle class="w-4 h-4" />
                                        <span class="text-xs font-medium">Anda sudah terdaftar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Countdown -->
                    <div class="space-y-4">
                        <h3 class="text-center font-semibold text-lg sm:text-xl text-black">
                            Event akan dimulai dalam:
                        </h3>
                        <EventTimer :target-date="event.waktu_start_event" finished-text="Event telah selesai" />
                    </div>

                    <!-- Deskripsi Event -->
                    <div class="space-y-3">
                        <h2 class="text-xl font-semibold">Deskripsi Event</h2>
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                            <div
                                class="prose max-w-none text-black prose-headings:text-black prose-p:text-black prose-strong:text-black prose-ul:text-black prose-ol:text-black prose-li:text-black prose-a:text-secondary prose-a:hover:text-secondary/80">
                                <div v-html="event.deskripsi_event"></div>
                            </div>
                        </div>
                    </div>

                    <!-- TESTIMONI LIST -->
                    <div v-if="testimoniList.length" class="w-full mt-16 border-t pt-10">
                        <span class="text-sm text-gray-500">Ulasan untuk</span>
                        <h2 class="text-2xl font-semibold mb-4">
                            {{ event?.nama_event }}
                        </h2>
                        <div class="space-y-6">
                            <div v-for="testimoni in testimoniList" :key="testimoni.id_testimoni_event"
                                class="p-4 border border-gray-200 rounded-xl bg-gray-50 transition hover:bg-gray-100">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 gap-2">
                                    <div class="flex items-center gap-3">
                                        <img v-if="testimoni.user?.foto_profil"
                                            :src="`/storage/${testimoni.user.foto_profil}`"
                                            class="w-8 h-8 rounded-full object-cover" alt="Foto Profil" />
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
                                    <div class="flex items-center gap-2 sm:mt-0 mt-2">
                                        <div class="flex gap-1">
                                            <Star v-for="i in 5" :key="i" :class="i <= testimoni.rating
                                                    ? 'text-secondary'
                                                    : 'text-gray-300'
                                                " class="w-5 h-5" />
                                        </div>
                                        <span class="text-sm text-gray-500">{{ testimoni.rating }}/5</span>
                                    </div>
                                </div>

                                <p class="text-gray-700">
                                    {{ testimoni.isi_testimoni }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FORM TESTIMONI -->
                    <div v-if="isAuthenticated && event" class="w-full mt-10">
                        <span class="text-sm text-gray-500">Sudah mengikuti event ini?</span>
                        <h2 class="text-xl font-semibold mb-3">
                            Berikan ulasanmu
                        </h2>
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
                                        <Star :class="i <= newTestimoni.rating
                                                ? 'text-secondary'
                                                : 'text-gray-300'
                                            " class="w-6 h-6 transition" />
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
                    <div v-else-if="!isAuthenticated && event"
                        class="w-full mt-10 bg-yellow-50 border border-yellow-300 text-yellow-800 p-6 rounded-xl flex items-center gap-4">
                        <!-- Ikon atau ilustrasi -->
                        <img src="/image/login.svg" alt="Login Illustration" class="w-36 h-36 object-contain" />

                        <!-- Pesan -->
                        <div class="text-sm leading-relaxed">
                            <p class="font-semibold">Oops! Kamu belum login.</p>
                            <p class="italic text-gray-600">
                                Login terlebih dahulu untuk menulis testimoni
                                dan berbagi pendapatmu tentang event ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Add all modals at the end of the template -->
    <CopyLink :show="showCopyModal" @close="closeCopyModal" :auto-close="true" :auto-close-delay="3000" />

    <PendaftaranBerhasil :show="showRegistrationSuccessModal" @close="showRegistrationSuccessModal = false"
        :auto-close="true" :auto-close-delay="5000" />

    <PembatalanBerhasil :show="showCancellationSuccessModal" @close="showCancellationSuccessModal = false"
        :auto-close="true" :auto-close-delay="5000" />

    <KonfirmasiPembatalan :show="showCancellationConfirmModal" @close="showCancellationConfirmModal = false"
        @confirm="doUnregister" />

    <GagalModal :show="showErrorModal" :message="errorMessage" @close="showErrorModal = false" />

    <LoginDiperlukan :show="showLoginRequiredModal" @close="showLoginRequiredModal = false" />

    <TestimoniTerkirim :show="showTestimoniModal" @close="closeTestimoniModal" @write-another="writeAnotherTestimoni"
        :auto-close="false" />
</template>
