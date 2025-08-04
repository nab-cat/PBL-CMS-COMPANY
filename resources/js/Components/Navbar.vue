<script setup>
import { ChevronDown, Menu, UserCog, LayoutDashboard, X } from "lucide-vue-next";
import MegaMenu from "./MegaMenu/MegaMenu.vue";
import UserMenu from "./MegaMenu/UserMenu.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import { Link, usePage } from "@inertiajs/vue3";

const profil_perusahaan = ref(null);
const showMegaMenu = ref(false);
const showUserMenu = ref(false);
const isMobileMenuOpen = ref(false);
const loading = ref(false);
const error = ref(null);
const isMobile = ref(false);
const featureToggles = ref({});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 12) return "Selamat pagi";
    if (hour >= 12 && hour < 17) return "Selamat siang";
    if (hour >= 17 && hour < 21) return "Selamat sore";
    return "Selamat malam";
});

const toggleMegaMenu = () => {
    showMegaMenu.value = !showMegaMenu.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

function updateIsMobile() {
    isMobile.value = window.innerWidth < 1024;
}

const closeOnClickOutside = (event) => {
    if (!event.target.closest(".user-menu-container")) {
        showUserMenu.value = false;
    }
};

async function fetchProfilPerusahaan() {
    try {
        loading.value = true;
        const response = await axios.get(`/api/profil-perusahaan/navbar`);
        profil_perusahaan.value = response.data.data;
        loading.value = false;
    } catch (err) {
        error.value = "Event not found or an error occurred";
        loading.value = false;
    }
}

async function fetchFeatureToggles() {
    try {
        const res = await axios.get("/api/feature-toggles");
        featureToggles.value = res.data.data || {};
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

const user = computed(() => usePage().props.auth?.user ?? null);
const unreadCount = computed(() => usePage().props.auth?.unreadNotifications || 0);

function getImageUser(imagePath) {
    return imagePath ? `/storage/${imagePath}` : "/image/placeholder.webp";
}

onMounted(() => {
    updateIsMobile();
    fetchProfilPerusahaan();
    fetchFeatureToggles();
    window.addEventListener("resize", updateIsMobile);
    document.addEventListener("click", closeOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", closeOnClickOutside);
});
</script>

<template>
    <!-- Navbar -->
    <nav class="w-full px-6 py-2 lg:px-16 bg-primary shadow-sm fixed top-0 left-0 z-50 font-custom">
        <div class="container mx-auto flex items-center justify-between py-2">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="h-12 w-12 flex items-center justify-center overflow-hidden">
                    <img :src="getImageUrl(profil_perusahaan?.logo_perusahaan)" alt="Logo Perusahaan"
                        class="h-full w-full object-contain" />
                </div>
                <Link href="/" class="text-h4-bold text-typography-dark px-2">
                {{ profil_perusahaan?.nama_perusahaan || "CMS" }}
                </Link>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-8">
                <Link href="/" class="text-typography-dark hover:text-typography-hover2 transition text-lg">Beranda
                </Link>

                <Link v-if="featureToggles.produk_module == 1" href="/produk"
                    class="text-typography-dark hover:text-typography-hover2 transition text-lg">
                Produk
                </Link>

                <Link v-if="featureToggles.feedback_module == 1" href="/feedback"
                    class="text-typography-dark hover:text-typography-hover2 transition text-lg">
                Feedback
                </Link>

                <div class="relative cursor-pointer" @click="toggleMegaMenu">
                    <span
                        class="text-typography-dark hover:text-typography-hover2 transition text-lg flex items-center">
                        Lainnya
                        <ChevronDown 
                            :class="['w-4 transition-transform duration-300 ease-in-out', 
                                    showMegaMenu ? 'rotate-180' : 'rotate-0']" 
                        />
                    </span>
                </div>
            </div>

            <!-- Desktop Login / Foto Profil -->
            <div class="hidden lg:flex items-center space-x-3 relative user-menu-container">
                <template v-if="user">
                    <!-- User Profile -->
                    <div @click="showUserMenu = !showUserMenu" class="cursor-pointer flex items-center space-x-2 group">
                        <div class="flex flex-col items-end text-right">
                            <span
                                class="text-black text-sm font-semibold group-hover:underline"
                                >{{ user.name }}</span
                            >
                            <span class="text-gray-600 text-xs">{{
                                user.email
                            }}</span>
                        </div>
                        <div class="relative">
                            <img
                                :src="getImageUser(user.foto_profil)"
                                alt="Foto Profil"
                                class="w-10 h-10 rounded-full object-cover border border-gray-300"
                            />
                            <!-- Unread Badge -->
                            <span
                                v-if="unreadCount > 0"
                                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"
                            >
                                {{ unreadCount > 99 ? "99+" : unreadCount }}
                            </span>
                        </div>
                    </div>

                    <UserMenu v-if="showUserMenu" />
                </template>
                <template v-else>
                    <a href="/login"
                        class="bg-secondary text-primary px-8 py-2 rounded-xl-figma shadow hover:bg-black transition">
                        Login
                    </a>
                </template>
            </div>

            <!-- Burger icon with animation -->
            <div class="lg:hidden flex items-center">
                <div class="relative w-7 h-7 cursor-pointer" @click="toggleMobileMenu">
                    <!-- Burger Icon -->
                    <Menu 
                        :class="['w-7 h-7 text-black absolute transition-all duration-300 ease-in-out', 
                                isMobileMenuOpen ? 'opacity-0 rotate-180 scale-75' : 'opacity-100 rotate-0 scale-100']" 
                    />
                    <!-- Close Icon -->
                    <X 
                        :class="['w-7 h-7 text-black absolute transition-all duration-300 ease-in-out', 
                                isMobileMenuOpen ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-180 scale-75']" 
                    />
                </div>
            </div>
        </div>

        <!-- Desktop MegaMenu (overlay) -->
        <Transition name="fade-slide">
            <MegaMenu v-if="showMegaMenu && !isMobile" class="fixed left-0 top-[64px] w-screen z-40"
                @click.self="showMegaMenu = false" />
        </Transition>
    </nav>

    <!-- Mobile Menu Dropdown -->
    <Transition name="fade-slide">
        <div v-if="isMobileMenuOpen"
            class="lg:hidden fixed top-[64px] left-0 w-full bg-white px-6 pt-8 pb-8 flex flex-col space-y-4 shadow z-40 font-custom text-black max-h-[calc(100vh-64px)] overflow-y-auto">
            <!-- User Info Mobile -->
            <template v-if="user">
                <a href="#"
                    class="flex items-center space-x-4 pb-4 border-b border-gray-200 hover:bg-gray-50 px-2 py-2 rounded-md transition">
                    <img :src="getImageUser(user.foto_profil)" alt="Foto Profil"
                        class="w-12 h-12 rounded-full object-cover border border-gray-300" />
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-base text-black">{{ greeting }}, {{ user.name }}</span>
                        </div>
                        <span class="text-sm text-gray-500">{{
                            user.email
                            }}</span>
                    </div>
                </a>

                <div class="flex flex-col gap-2 mt-4">
                    <a v-if="user.status_kepegawaian == 'Tetap'" href="/admin"
                        class="flex items-center space-x-3 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md transition">
                        <LayoutDashboard class="w-5 h-5 text-gray-700" />
                        <span class="text-sm font-medium text-gray-800">Admin Panel</span>
                    </a>

                    <a href="/admin/profile"
                        class="flex items-center space-x-3 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md transition">
                        <UserCog class="w-5 h-5 text-gray-700" />
                        <span class="text-sm font-medium text-gray-800">Edit Profil</span>
                    </a>

                    <a href="/notifications"
                        class="flex items-center space-x-3 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md transition">
                        <Bell class="w-5 h-5 text-gray-700" />
                        <span class="text-sm font-medium text-gray-800">Notifikasi</span>
                        <span
                            v-if="unreadCount > 0"
                            class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full ml-auto"
                        >
                            {{ unreadCount > 99 ? "99+" : unreadCount }}
                        </span>
                    </a>
                </div>
            </template>

            <Link href="/" class="text-2xl py-1">Beranda</Link>
            <Link v-if="featureToggles.produk_module == 1" href="/produk" class="text-2xl py-1">Produk</Link>
            <Link v-if="featureToggles.feedback_module == 1" href="/feedback" class="text-2xl py-1">Feedback</Link>

            <div class="text-2xl py-1 flex justify-between items-center cursor-pointer" @click="toggleMegaMenu">
                <span>Lainnya</span>
                <ChevronDown 
                    :class="['w-5 h-5 transition-transform duration-300 ease-in-out', 
                            showMegaMenu && isMobile ? 'rotate-180' : 'rotate-0']" 
                />
            </div>

            <div v-if="showMegaMenu && isMobile" class="pt-4">
                <MegaMenu class="w-full bg-primary rounded-xl p-4" />
            </div>

            <template v-if="user">
                <a href="/logout"
                    class="mt-4 bg-red-500 text-primary px-6 py-2 rounded-xl-figma text-center shadow hover:bg-red-600 transition w-full">
                    Logout
                </a>
            </template>
            <template v-else>
                <a href="/login"
                    class="mt-4 bg-secondary text-primary px-6 py-2 rounded-xl-figma text-center shadow hover:bg-black transition">
                    Login
                </a>
            </template>
        </div>
    </Transition>

    <!-- Spacer -->
    <div class="pt-16"></div>
</template>

<style scoped>
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: opacity 0.3s, transform 0.3s;
}

.fade-slide-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.fade-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
}

.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Additional smooth transition for burger menu */
.burger-container {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
