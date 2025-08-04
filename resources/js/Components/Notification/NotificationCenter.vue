<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import {
    Bell,
    Check,
    X,
    MailCheck,
    UserCheck,
    AlertCircle,
    Info,
    AlertTriangle,
} from "lucide-vue-next";

const page = usePage();
const showDropdown = ref(false);

// Get notifications from Inertia props
const notifications = computed(
    () => page.props.notifications || { unread: [], recent: [] }
);
const unreadCount = computed(() => page.props.auth?.unreadNotifications || 0);

// Icon mapping
const iconMap = {
    "user-check": UserCheck,
    "mail-check": MailCheck,
    bell: Bell,
    "alert-circle": AlertCircle,
    info: Info,
    "alert-triangle": AlertTriangle,
    "briefcase": Briefcase,
    "check-circle": CheckCircle,
    "x-circle": XCircle,
};

// Color mapping based on notification type
const getTypeColor = (type) => {
    const colors = {
        registration_success: "text-green-600 bg-green-50 border-green-200",
        email_verified: "text-blue-600 bg-blue-50 border-blue-200",
        success: "text-green-600 bg-green-50 border-green-200",
        error: "text-red-600 bg-red-50 border-red-200",
        warning: "text-yellow-600 bg-yellow-50 border-yellow-200",
        info: "text-blue-600 bg-blue-50 border-blue-200",
    };
    return colors[type] || colors.info;
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

// Handle notification click
const handleNotificationClick = (notification) => {
    // Mark as read if unread
    if (!notification.read_at) {
        markAsRead(notification.id);
    }

    // Navigate to action URL if exists (full page load)
    if (notification.action_url) {
        window.location.href = notification.action_url;
    }

    showDropdown.value = false;
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

// Close dropdown when clicking outside
onMounted(() => {
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".notification-dropdown")) {
            showDropdown.value = false;
        }
    });
});
</script>

<template>
    <div class="relative notification-dropdown">
        <!-- Notification Bell Button -->
        <button
            @click="showDropdown = !showDropdown"
            class="relative flex items-center justify-center text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full"
        >
            <Bell class="w-4 h-4" />

            <!-- Unread Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"
            >
                {{ unreadCount > 99 ? "99+" : unreadCount }}
            </span>
        </button>

        <!-- Notification Dropdown -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="showDropdown"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            >
                <!-- Header -->
                <div
                    class="px-4 py-3 border-b border-gray-200 flex items-center justify-between"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Notifikasi
                    </h3>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        Tandai Semua Dibaca
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                    <!-- Unread Notifications -->
                    <div v-if="notifications.unread.length > 0">
                        <div
                            class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50"
                        >
                            Belum Dibaca ({{ notifications.unread.length }})
                        </div>
                        <div
                            v-for="notification in notifications.unread"
                            :key="notification.id"
                            class="px-4 py-3 border-l-4 border-blue-500 bg-blue-25"
                        >
                            <div class="flex items-start space-x-3">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <component
                                        :is="iconMap[notification.icon] || Bell"
                                        class="w-5 h-5 text-blue-600"
                                    />
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ notification.title }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ notification.message }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ timeAgo(notification.created_at) }}
                                    </p>
                                </div>

                                <!-- Unread indicator -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-2 h-2 bg-blue-600 rounded-full"
                                    ></div>
                                </div>
                            </div>
                            <!-- 'Sudah Dibaca' button -->
                            <div class="mt-2 text-right">
                                <button
                                    @click.prevent="markAsRead(notification.id)"
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    Sudah Dibaca
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Notifications -->
                    <div v-if="notifications.recent.length > 0">
                        <div
                            class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50"
                        >
                            Terbaru
                        </div>
                        <div
                            v-for="notification in notifications.recent"
                            :key="notification.id"
                            class="px-4 py-3 hover:bg-gray-50"
                            :class="{ 'opacity-60': notification.read_at }"
                        >
                            <div class="flex items-start space-x-3">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <component
                                        :is="iconMap[notification.icon] || Bell"
                                        class="w-5 h-5 text-gray-400"
                                    />
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ notification.title }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
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
                        v-if="
                            notifications.unread.length === 0 &&
                            notifications.recent.length === 0
                        "
                        class="px-4 py-8 text-center"
                    >
                        <Bell class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                        <p class="text-gray-500 text-sm">
                            Tidak ada notifikasi
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200">
                    <button
                        @click="router.visit('/notifications')"
                        class="w-full text-center text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        Lihat Semua Notifikasi
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
