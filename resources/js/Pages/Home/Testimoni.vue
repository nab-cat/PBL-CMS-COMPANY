<template>
    <div
        class="w-full px-4 sm:px-8 lg:px-16 py-16 sm:py-20 lg:py-28 bg-white font-custom"
    >
        <div class="max-w-screen-xl mx-auto">
            <!-- Header Section -->
            <div class="w-full mb-12 lg:mb-16" data-aos="fade-up">
                <div class="max-w-2xl">
                    <h2
                        class="text-3xl sm:text-4xl lg:text-5xl font-normal leading-tight mb-4"
                    >
                        Kumpulan Testimoni
                    </h2>
                    <p
                        class="text-base sm:text-lg font-normal leading-relaxed text-gray-600"
                    >
                        Apa kata mereka tentang layanan kami
                    </p>
                </div>
            </div>

            <!-- Testimonials Section -->
            <div class="w-full">
                <!-- Loading State -->
                <div
                    v-if="loading"
                    class="w-full p-8 lg:p-12 rounded-2xl border border-gray-200 flex justify-center items-center"
                >
                    <div class="text-lg font-normal">
                        Loading testimonials...
                    </div>
                </div>

                <!-- Error State -->
                <div
                    v-else-if="error"
                    class="w-full p-8 lg:p-12 rounded-2xl border border-red-200 bg-red-50 flex justify-center items-center"
                >
                    <div class="text-lg font-normal text-red-600">
                        {{ error }}
                    </div>
                </div>

                <!-- Testimonials Container -->
                <div v-else class="w-full" data-aos="fade-zoom-out">
                    <!-- Show message if no testimonials -->
                    <div
                        v-if="availableTestimonials.length === 0"
                        class="w-full p-8 lg:p-12 rounded-2xl border border-gray-200 bg-gray-50 flex justify-center items-center"
                    >
                        <div class="text-center text-gray-500">
                            <div class="text-lg mb-2">Belum ada testimoni</div>
                            <div class="text-sm">
                                Testimoni akan muncul di sini ketika sudah ada
                            </div>
                        </div>
                    </div>

                    <!-- Slider Container -->
                    <div v-else class="relative overflow-hidden rounded-2xl">
                        <div
                            class="flex transition-transform duration-500 ease-in-out"
                            :style="{
                                transform: `translateX(-${
                                    currentSlide * 100
                                }%)`,
                            }"
                        >
                            <!-- Dynamic Testimonial Slides -->
                            <div
                                v-for="(
                                    testimonial, index
                                ) in availableTestimonials"
                                :key="testimonial.type"
                                class="w-full flex-shrink-0"
                            >
                                <div
                                    class="p-6 sm:p-8 lg:p-10 border border-gray-200 bg-white h-full flex flex-col gap-6"
                                >
                                    <!-- Header Badge -->
                                    <div
                                        class="inline-flex w-fit px-4 py-2 rounded-lg"
                                        :class="testimonial.badgeColor"
                                    >
                                        <span class="text-sm font-semibold">{{
                                            testimonial.badge
                                        }}</span>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 flex flex-col gap-6">
                                        <div class="space-y-6">
                                            <!-- Testimonial Text -->
                                            <blockquote
                                                class="text-lg lg:text-xl font-normal leading-relaxed text-gray-800"
                                            >
                                                "{{ testimonial.content }}"
                                            </blockquote>

                                            <!-- Rating -->
                                            <div
                                                class="flex items-center gap-1"
                                            >
                                                <Star
                                                    v-for="i in 5"
                                                    :key="i"
                                                    :class="[
                                                        'w-5 h-5',
                                                        i <= testimonial.rating
                                                            ? 'text-yellow-400 fill-yellow-400'
                                                            : 'text-gray-300',
                                                    ]"
                                                />
                                            </div>

                                            <!-- User Info -->
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <img
                                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100"
                                                    :src="
                                                        testimonial.user
                                                            ?.foto_profil
                                                            ? `/storage/${testimonial.user.foto_profil}`
                                                            : 'https://placehold.co/48x48'
                                                    "
                                                    :alt="
                                                        testimonial.user
                                                            ?.name || 'User'
                                                    "
                                                />
                                                <div>
                                                    <div
                                                        class="font-semibold text-gray-900"
                                                    >
                                                        {{
                                                            testimonial.user
                                                                ?.name ||
                                                            "Anonim"
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-sm text-gray-500"
                                                    >
                                                        {{
                                                            formatDate(
                                                                testimonial.createdAt
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div
                                        v-if="testimonial.slug"
                                        class="pt-4 border-t border-gray-100"
                                    >
                                        <Link
                                            :href="
                                                getTestimoniLink(
                                                    testimonial.type
                                                )
                                            "
                                            class="inline-flex items-center gap-2 font-medium transition-colors"
                                            :class="testimonial.linkColor"
                                        >
                                            <span
                                                >Lihat
                                                {{ testimonial.type }} yang
                                                dikomentari</span
                                            >
                                            <ArrowRight class="w-4 h-4" />
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div
                        v-if="totalSlides > 1"
                        class="flex items-center justify-between mt-8"
                    >
                        <!-- Dots Indicator -->
                        <div class="flex items-center gap-2">
                            <button
                                v-for="(_, index) in totalSlides"
                                :key="index"
                                @click="goToSlide(index)"
                                :class="[
                                    'w-3 h-3 rounded-full transition-all duration-200',
                                    currentSlide === index
                                        ? 'bg-secondary scale-110'
                                        : 'bg-gray-300 hover:bg-gray-400',
                                ]"
                            />
                        </div>

                        <!-- Arrow Navigation -->
                        <div class="flex items-center gap-3">
                            <button
                                @click="prevSlide"
                                class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <ChevronLeft class="w-5 h-5" />
                            </button>
                            <button
                                @click="nextSlide"
                                class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <ChevronRight class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { ArrowRight, ChevronLeft, ChevronRight, Star } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import AOS from "aos";
import "aos/dist/aos.css";

// Reactive data
const currentSlide = ref(0);
const loading = ref(true);
const error = ref(null);
const testimoniData = ref({});

// Computed properties
const availableTestimonials = computed(() => {
    const testimonials = [];

    // Check artikel
    if (testimoniData.value.isi_testimoni_artikel) {
        testimonials.push({
            type: "artikel",
            badge: "ARTIKEL",
            badgeColor: "bg-blue-50 text-blue-700",
            linkColor: "text-blue-600 hover:text-blue-700",
            content: testimoniData.value.isi_testimoni_artikel,
            rating: testimoniData.value.rating_artikel,
            user: testimoniData.value.user_artikel,
            slug: testimoniData.value.artikel_slug,
            createdAt: testimoniData.value.created_at,
        });
    }

    // Check produk
    if (testimoniData.value.isi_testimoni_produk) {
        testimonials.push({
            type: "produk",
            badge: "PRODUK",
            badgeColor: "bg-green-50 text-green-700",
            linkColor: "text-green-600 hover:text-green-700",
            content: testimoniData.value.isi_testimoni_produk,
            rating: testimoniData.value.rating_produk,
            user: testimoniData.value.user_produk,
            slug: testimoniData.value.produk_slug,
            createdAt: testimoniData.value.created_at,
        });
    }

    // Check event
    if (testimoniData.value.isi_testimoni_event) {
        testimonials.push({
            type: "event",
            badge: "EVENT",
            badgeColor: "bg-purple-50 text-purple-700",
            linkColor: "text-purple-600 hover:text-purple-700",
            content: testimoniData.value.isi_testimoni_event,
            rating: testimoniData.value.rating_event,
            user: testimoniData.value.user_event,
            slug: testimoniData.value.event_slug,
            createdAt: testimoniData.value.created_at,
        });
    }

    // Check lowongan
    if (testimoniData.value.isi_testimoni_lowongan) {
        testimonials.push({
            type: "lowongan",
            badge: "LOWONGAN",
            badgeColor: "bg-orange-50 text-orange-700",
            linkColor: "text-orange-600 hover:text-orange-700",
            content: testimoniData.value.isi_testimoni_lowongan,
            rating: testimoniData.value.rating_lowongan,
            user: testimoniData.value.user_lowongan,
            slug: testimoniData.value.lowongan_slug,
            createdAt: testimoniData.value.created_at,
        });
    }

    return testimonials;
});

const totalSlides = computed(() => availableTestimonials.value.length);

// Methods
const nextSlide = () => {
    if (totalSlides.value > 0) {
        currentSlide.value = (currentSlide.value + 1) % totalSlides.value;
    }
};

const prevSlide = () => {
    if (totalSlides.value > 0) {
        currentSlide.value =
            currentSlide.value === 0
                ? totalSlides.value - 1
                : currentSlide.value - 1;
    }
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    return new Date(dateString).toLocaleDateString("id-ID", options);
};

const getTestimoniLink = (type) => {
    let slug = null;
    let routeName = null;

    switch (type) {
        case "artikel":
            slug = testimoniData.value.artikel_slug;
            routeName = "artikel.show";
            break;
        case "produk":
            slug = testimoniData.value.produk_slug;
            routeName = "produk.show";
            break;
        case "event":
            slug = testimoniData.value.event_slug;
            routeName = "event.show";
            break;
        case "lowongan":
            slug = testimoniData.value.lowongan_slug;
            routeName = "lowongan.show";
            break;
    }

    if (slug && routeName) {
        try {
            return route(routeName, { slug });
        } catch (error) {
            return `/${type}/${slug}`;
        }
    }

    return null;
};

// Fetch testimonials from API
const fetchTestimonials = async () => {
    try {
        loading.value = true;
        error.value = null;

        const response = await axios.get("/api/testimoni");

        if (response.data.status === "success") {
            testimoniData.value = response.data.data;
        } else {
            throw new Error("Failed to fetch testimonials");
        }
    } catch (err) {
        error.value = "Gagal memuat testimoni. Silakan coba lagi.";
    } finally {
        loading.value = false;
    }
};

// Lifecycle
onMounted(() => {
    fetchTestimonials();
    AOS.init({
        duration: 1000,
        once: false,
    });

    // Auto-slide every 5 seconds
    setInterval(() => {
        if (!loading.value && !error.value && totalSlides.value > 1) {
            nextSlide();
        }
    }, 5000);
});
</script>
