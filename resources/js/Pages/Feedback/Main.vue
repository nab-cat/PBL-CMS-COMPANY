<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import FeedbackTerkirim from "@/Components/Modal/FeedbackTerkirim.vue";
import { ref, reactive, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import { User, ArrowRight, Send, Check } from "lucide-vue-next";
import SyaratKetentuanFeedback from "@/Components/Modal/SyaratKetentuanFeedback.vue";
import AOS from "aos";
import "aos/dist/aos.css";

const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth.user);
const feedbackList = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const loading = ref(false);

// Modal states
const showSuccessModal = ref(false);
const showTermsModal = ref(false);

const form = reactive({
    name: "",
    email: "",
    subjek_feedback: "",
    isi_feedback: "",
    tingkat_kepuasan: null,
    acceptTerms: false,
});

const emojis = ["😢", "😕", "😐", "😊", "😄"];

onMounted(() => {
    AOS.init({
        duration: 1000,
        once: false,
        mirror: true,
    });
    if (isLoggedIn.value) {
        form.name = page.props.auth.user.name;
        form.email = page.props.auth.user.email;
    }
    fetchFeedback();
});

async function fetchFeedback(pageNumber = 1) {
    try {
        loading.value = true;
        const params = {
            page: pageNumber,
        };
        const res = await axios.get("/api/feedback/", { params });
        feedbackList.value = res.data.data;
        currentPage.value = res.data.meta?.current_page || 1;
        lastPage.value = res.data.meta?.last_page || 1;
    } catch (err) {
    } finally {
        loading.value = false;
    }
}

async function submitForm() {
    if (!form.acceptTerms) {
        alert("Silakan setujui syarat dan ketentuan terlebih dahulu.");
        return;
    }

    if (!isLoggedIn.value || !page.props.auth.user.id_user) {
        alert("Silakan login terlebih dahulu.");
        return;
    }

    try {
        await axios.post("/api/feedback", {
            subjek_feedback: form.subjek_feedback,
            isi_feedback: form.isi_feedback,
            tingkat_kepuasan: form.tingkat_kepuasan,
            id_user: page.props.auth.user.id_user,
        });

        // Show success modal instead of alert
        showSuccessModal.value = true;

        // Reset form
        Object.assign(form, {
            subjek_feedback: "",
            isi_feedback: "",
            tingkat_kepuasan: null,
            acceptTerms: false,
        });

        await fetchFeedback();
    } catch (err) {
        alert("Gagal mengirim feedback.");
    }
}

function getImageUrl(foto_profil) {
    if (!foto_profil) {
        return "/images/default-profile.png";
    }
    return `/storage/${foto_profil}`;
}

function goToPage(pageNumber) {
    if (pageNumber < 1 || pageNumber > lastPage.value) return;
    fetchFeedback(pageNumber);
}

// Modal handlers
const handleCloseModal = () => {
    showSuccessModal.value = false;
};

const handleWriteAnother = () => {
    showSuccessModal.value = false;
    // Scroll to form
    document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
};

const openTermsModal = () => {
    showTermsModal.value = true;
};

const closeTermsModal = () => {
    showTermsModal.value = false;
};
</script>

<template>
    <AppLayout>
        <!-- Hero Section with Form - Updated to match case study CTA style -->
        <section
            class="w-full relative bg-gradient-to-br from-secondary via-secondary to-black px-4 lg:px-16 py-20 overflow-hidden">
            <!-- Content -->
            <div class="relative z-10 max-w-screen-lg mx-auto flex flex-col lg:flex-row gap-10 lg:gap-20 items-center">
                <!-- Left: Illustration -->
                <div class="w-full lg:w-1/2 flex justify-center">
                    <div
                        class="w-full max-w-md bg-white/10 backdrop-blur-sm rounded-2xl p-8 flex items-center justify-center">
                        <img class="w-full h-auto object-contain" src="image/Feedback-white.svg"
                            alt="Feedback Illustration" />
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="w-full lg:w-1/2 flex flex-col gap-8">
                    <!-- Heading -->
                    <div class="flex flex-col gap-6">
                        <div class="text-white/80 text-base font-semibold font-custom uppercase tracking-wider">
                            Hubungi kami lewat
                        </div>
                        <div class="flex flex-col gap-4">
                            <h1 class="text-white text-3xl lg:text-5xl font-normal font-custom leading-tight">
                                Feedback Anda!
                            </h1>
                            <p class="text-white/80 text-base lg:text-lg font-custom leading-relaxed">
                                Kami tumbuh lewat feedback Anda. Beri kami
                                masukan untuk meningkatkan layanan kami.
                            </p>
                        </div>
                    </div>

                    <!-- Jika belum login -->
                    <div v-if="!isLoggedIn"
                        class="flex flex-col gap-6 text-center bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto">
                            <User class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2 font-custom">
                                Login Diperlukan
                            </h3>
                            <p class="text-white/70 font-custom">
                                Mohon login terlebih dahulu untuk memberikan feedback.
                            </p>
                        </div>
                        <a href="/login"
                            class="inline-flex items-center justify-center gap-2 bg-white text-secondary px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 font-custom">
                            <span>Login Sekarang</span>
                            <ArrowRight class="w-5 h-5" />
                        </a>
                    </div>

                    <!-- Jika sudah login -->
                    <form v-else @submit.prevent="submitForm"
                        class="flex flex-col gap-6 bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                        <!-- Form Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div class="flex flex-col gap-2">
                                <label class="text-white text-sm font-medium font-custom">
                                    Nama Lengkap
                                </label>
                                <input v-model="form.name" type="text" readonly  
                                    class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm text-white placeholder-white/60 rounded-xl border border-white/20 focus:outline-none focus:ring-0 focus:ring-white/20 font-custom" />
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col gap-2">
                                <label class="text-white text-sm font-medium font-custom">
                                    Alamat Email
                                </label>
                                <input v-model="form.email" type="email" readonly
                                    class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm text-white placeholder-white/60 rounded-xl border border-white/20 focus:outline-none focus:ring-0 focus:ring-white/20 font-custom" />
                            </div>
                        </div>

                        <!-- Subjek -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium font-custom">
                                Subjek Feedback
                            </label>
                            <input v-model="form.subjek_feedback" type="text"
                                class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm text-white placeholder-white/60 rounded-xl border border-white/20 focus:outline-none focus:ring-0 focus:ring-white/20 font-custom"
                                placeholder="Contoh: Kritik tentang layanan" required />
                        </div>

                        <!-- Message -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium font-custom">
                                Pesan Feedback
                            </label>
                            <textarea v-model="form.isi_feedback" rows="4"
                                class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm text-white placeholder-white/60 rounded-xl border border-white/20 focus:outline-none focus:ring-0 focus:ring-white/20 resize-none font-custom"
                                placeholder="Tulis masukan Anda dengan detail..." required></textarea>
                        </div>

                        <!-- Rating Emoji -->
                        <div class="flex flex-col gap-4">
                            <label class="text-white text-sm font-medium font-custom">
                                Tingkat Kepuasan Anda
                            </label>
                            <div
                                class="flex justify-between bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <label v-for="n in 5" :key="n"
                                    class="cursor-pointer transition-all duration-300 hover:scale-125 flex flex-col items-center gap-2">
                                    <input type="radio" v-model="form.tingkat_kepuasan" :value="n" class="hidden" />
                                    <span class="text-3xl transition-all duration-300" :class="form.tingkat_kepuasan === n
                                        ? 'opacity-100 scale-110'
                                        : 'opacity-50 hover:opacity-80'
                                        ">
                                        {{ emojis[n - 1] }}
                                    </span>
                                    <span class="text-xs text-white/70 font-custom">
                                        {{ ['Buruk', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik'][n - 1] }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Checkbox with Terms Modal -->
                        <div
                            class="flex items-start gap-3 bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                            <div class="relative">
                                <input type="checkbox" v-model="form.acceptTerms" class="sr-only" required />
                                <div @click="form.acceptTerms = !form.acceptTerms"
                                    class="w-5 h-5 mt-0.5 border-2 rounded bg-white/20 flex items-center justify-center cursor-pointer transition-all duration-200"
                                    :class="form.acceptTerms ? 'border-white bg-white/20' : 'border-white/30 hover:border-white/50'">
                                    <Check v-if="form.acceptTerms" class="w-3 h-3 text-white" />
                                </div>
                            </div>
                            <span class="text-white/90 text-sm font-custom leading-relaxed">
                                Saya menerima
                                <button type="button" @click="openTermsModal"
                                    class="underline cursor-pointer hover:text-white transition-colors">
                                    syarat dan ketentuan
                                </button>
                                yang berlaku dan memberikan izin untuk memproses feedback ini.
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="group w-full bg-white text-secondary px-6 py-4 rounded-xl font-semibold hover:bg-secondary hover:text-white transition-all duration-300 transform flex items-center justify-center gap-2 font-custom">
                            <span>Kirim Feedback</span>
                            <Send class="w-5 h-5 group-hover:text-white transition-transform" />
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Feedback List Section -->
        <div class="w-full px-4 lg:px-16 py-20 bg-white text-secondary font-custom">
            <div class="max-w-screen-lg mx-auto">
                <div class="mb-12 text-center">
                    <div class="text-black text-base font-semibold font-custom uppercase tracking-wider mb-4">
                        Testimoni Pengguna
                    </div>
                    <h2 v-if="feedbackList.length !== 0"
                        class="text-black text-3xl lg:text-4xl font-normal font-custom">
                        Feedback dari Pengguna Kami
                    </h2>
                </div>

                <!-- Loading Skeleton -->
                <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div v-for="i in 4" :key="i"
                        class="animate-pulse bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                                <div>
                                    <div class="h-4 bg-gray-300 rounded w-24 mb-2"></div>
                                    <div class="h-3 bg-gray-200 rounded w-32"></div>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-gray-300 rounded"></div>
                        </div>
                        <div class="h-5 bg-gray-300 rounded w-3/4 mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>

                <!-- Tidak ada feedback -->
                <div v-if="!loading && feedbackList.length === 0"
                    class="flex flex-col items-center justify-center gap-6 py-20 text-center">
                    <div class="flex flex-col lg:flex-row items-center gap-6 text-center">
                        <img src="/image/empty.svg" alt="Empty State"
                            class="w-40 h-40 lg:w-64 lg:h-64 object-contain" />
                        <div>
                            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 font-custom mb-2">
                                Belum ada feedback
                            </h3>
                            <p class="text-sm md:text-base text-gray-500 font-custom">
                                Jadilah yang pertama memberikan feedback untuk membantu kami berkembang.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Daftar feedback -->
                <div v-else-if="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div v-for="(item, index) in feedbackList" :key="item.id_feedback"
                        class="bg-gray-50 p-6 rounded-2xl border border-gray-100"
                        data-aos="zoom-in-up" :data-aos-delay="(index * 100)">
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <img :src="getImageUrl(item.user.foto_profil)" alt="Foto Profil"
                                    class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" />
                                <div>
                                    <div class="font-semibold text-black font-custom">
                                        {{ item.user.name }}
                                    </div>
                                    <div class="text-sm text-black font-custom">
                                        {{ item.user.email }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-2xl">
                                {{ emojis[item.tingkat_kepuasan - 1] }}
                            </div>
                        </div>

                        <div class="text-lg font-medium text-black mb-3 font-custom">
                            {{ item.subjek_feedback }}
                        </div>

                        <p class="text-black font-custom leading-relaxed">
                            {{ item.isi_feedback }}
                        </p>

                        <div v-if="item.tanggapan_feedback"
                            class="mt-4 bg-secondary/5 border-l-4 border-secondary p-4 rounded-r-xl">
                            <div class="text-sm text-secondary font-semibold font-custom mb-1">
                                Tanggapan Admin:
                            </div>
                            <p class="text-sm text-secondary/80 font-custom">
                                {{ item.tanggapan_feedback }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1 && !loading"
                class="flex justify-center items-center gap-4 mt-12 font-custom text-sm">
                <!-- Tombol Sebelumnya -->
                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === 1
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                        : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                        ">
                    Sebelumnya
                </button>

                <!-- Indikator halaman -->
                <div class="px-4 py-2 rounded-xl border border-black text-black font-semibold">
                    {{ currentPage }} / {{ lastPage }}
                </div>

                <!-- Tombol Selanjutnya -->
                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                    class="px-4 py-2 rounded-xl font-medium transition border" :class="currentPage === lastPage
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200'
                        : 'bg-white text-black border-gray-300 hover:bg-black hover:text-white'
                        ">
                    Selanjutnya
                </button>
            </div>
        </div>


        <!-- Modals -->
        <FeedbackTerkirim :show="showSuccessModal" @close="handleCloseModal" @write-another="handleWriteAnother" />

        <SyaratKetentuanFeedback :show="showTermsModal" @close="closeTermsModal" />
    </AppLayout>
</template>
