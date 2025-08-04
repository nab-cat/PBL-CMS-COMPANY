<script setup>
import { computed, ref, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { User, LayoutDashboard, LogOut, Bell, Check, ChevronRight } from "lucide-vue-next";

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const activeTab = ref("profile"); // 'profile' or 'notifications'
const theme = usePage().props.theme;

onMounted(() => {
    if (theme && theme.secondary) {
        document.documentElement.style.setProperty(
            "--color-secondary",
            theme.secondary
        );
    }
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 12) return "Selamat pagi";
    if (hour >= 12 && hour < 17) return "Selamat siang";
    if (hour >= 17 && hour < 21) return "Selamat sore";
    return "Selamat malam";
});

const canAccessPanel = computed(() => {
    const allowedStatuses = ["Tetap", "Kontrak", "Magang"];
    return allowedStatuses.includes(user.value?.status_kepegawaian ?? "");
});

// Notification functionality integrated from NotificationCenter
const notifications = computed(
    () => page.props.notifications || { unread: [], recent: [] }
);
const unreadCount = computed(() => page.props.auth?.unreadNotifications || 0);

// Icon mapping
const iconMap = {
    "user-check": User,
    "mail-check": Bell,
    bell: Bell,
    "alert-circle": Bell,
    info: Bell,
    "alert-triangle": Bell,
};

// Mark notification as read
const markAsRead = (notificationId) => {
    router.post(
        `/notifications/${notificationId}/read`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ["notifications", "auth"],
        }
    );
};

// Mark all as read
const markAllAsRead = () => {
    router.post(
        "/notifications/read-all",
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ["notifications", "auth"],
        }
    );
};

// Format time ago
const timeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "Baru saja";
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} menit lalu`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
    return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
};

// Handle notification click
const handleNotificationClick = (notification) => {
    // Mark as read if unread
    if (!notification.read_at) {
        markAsRead(notification.id);
    }

    // Navigate to action URL if exists
    if (notification.action_url) {
        window.location.href = notification.action_url;
    }
};
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-1"
    >
        <div
            class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-lg z-40 font-custom max-h-[80vh] overflow-hidden"
        >
            <!-- User Profile Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="mb-2">
                    <p class="text-xs text-gray-500">{{ greeting }},</p>
                    <p class="text-base font-semibold text-gray-800">
                        {{ user?.name ?? "Pengguna" }}
                    </p>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200">
                <button
                    @click="activeTab = 'profile'"
                    class="flex-1 py-2 px-4 text-sm font-medium"
                    :class="activeTab == 'profile' ? 'border-b-2' : 'text-gray-500 hover:text-gray-700'"
                    :style="activeTab == 'profile' ? { color: theme?.secondary || '#3B82F6', borderBottomColor: theme?.secondary || '#3B82F6' } : {}"
                >
                    Profil
                </button>
                <button
                    @click="activeTab = 'notifications'"
                    class="flex-1 py-2 px-4 text-sm font-medium relative"
                    :class="activeTab == 'notifications' ? 'border-b-2' : 'text-gray-500 hover:text-gray-700'"
                    :style="activeTab == 'notifications' ? { color: theme?.secondary || '#3B82F6', borderBottomColor: theme?.secondary || '#3B82F6' } : {}"
                >
                    Notifikasi
                    <span
                        v-if="unreadCount > 0"
                        class="absolute top-1 right-1 inline-flex items-center justify-center px-1 py-0.5 text-[10px] font-bold leading-none text-white rounded-full"
                        :style="{ backgroundColor: theme?.secondary || '#EF4444' }"
                    >
                        {{ unreadCount > 99 ? "99+" : unreadCount }}
                    </span>
                </button>
            </div>

            <!-- Profile Content -->
            <div v-if="activeTab == 'profile'" class="p-4">
                <ul class="space-y-3 text-sm text-black">
                    <li class="flex items-center gap-2">
                        <User class="w-4 h-4" />
                        <a href="/admin/profile" class="hover:underline">Ubah Profil</a>
                    </li>
                    <li v-if="canAccessPanel" class="flex items-center gap-2">
                        <LayoutDashboard class="w-4 h-4" />
                        <a href="/admin" class="hover:underline">Panel Admin</a>
                    </li>
                    <li class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200">
                        <LogOut class="w-4 h-4 text-red-600" />
                        <a href="/logout" class="text-red-600 hover:underline">Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Notifications Content -->
            <div v-else-if="activeTab == 'notifications'" class="overflow-y-auto max-h-[60vh]">
                <!-- Notifications Header with Mark All as Read button -->
                <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
                    <span class="text-xs font-semibold text-gray-500">NOTIFIKASI</span>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="text-xs hover:underline"
                        :style="{ color: theme?.secondary || '#3B82F6' }"
                    >
                        Tandai Semua Dibaca
                    </button>
                </div>

                <!-- Unread Notifications -->
                <div v-if="notifications.unread.length > 0">
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 bg-gray-50">
                        Belum Dibaca ({{ notifications.unread.length }})
                    </div>
                    <div
                        v-for="notification in notifications.unread"
                        :key="notification.id"
                        class="px-4 py-3 border-l-4 cursor-pointer hover:bg-gray-100 transition-colors"
                        :style="{ 
                            borderLeftColor: theme?.secondary || '#3B82F6',
                            backgroundColor: `${theme?.secondary || '#3B82F6'}08`
                        }"
                        @click="handleNotificationClick(notification)"
                    >
                        <div class="flex items-start">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mr-3">
                                <component
                                    :is="iconMap[notification.icon] || Bell"
                                    class="w-5 h-5"
                                    :style="{ color: theme?.secondary || '#3B82F6' }"
                                />
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ notification.title }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ notification.message }}
                                </p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500">
                                        {{ timeAgo(notification.created_at) }}
                                    </span>
                                    <button
                                        @click.stop="markAsRead(notification.id)"
                                        class="text-xs font-medium hover:opacity-80"
                                        :style="{ color: theme?.secondary || '#3B82F6' }"
                                    >
                                        <Check class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Notifications -->
                <div v-if="notifications.recent.length > 0">
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 bg-gray-50">
                        Terbaru
                    </div>
                    <div
                        v-for="notification in notifications.recent"
                        :key="notification.id"
                        class="px-4 py-3 hover:bg-gray-50 cursor-pointer"
                        :class="{ 'opacity-75': notification.read_at }"
                        @click="handleNotificationClick(notification)"
                    >
                        <div class="flex items-start">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mr-3">
                                <component
                                    :is="iconMap[notification.icon] || Bell"
                                    class="w-5 h-5 text-gray-400"
                                />
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ notification.title }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ notification.message }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ timeAgo(notification.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="notifications.unread.length == 0 && notifications.recent.length == 0"
                    class="px-4 py-8 text-center"
                >
                    <Bell class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-500 text-sm">
                        Tidak ada notifikasi
                    </p>
                </div>

                <!-- View All Link -->
                <div class="p-3 border-t border-gray-200">
                    <a
                        href="/notifications"
                        class="block w-full text-center font-medium text-sm py-2 rounded-lg transition-colors hover:bg-gray-100"
                        :style="{ 
                            color: theme?.secondary || '#3B82F6'
                        }"
                    >
                        <div class="flex items-center justify-center">
                            <span>Lihat Semua Notifikasi</span>
                            <ChevronRight class="w-4 h-4 ml-1" />
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </transition>
</template>
