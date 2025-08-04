<script setup>
import { computed, onMounted } from "vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import {
    Bell,
    Check,
    MailCheck,
    UserCheck,
    AlertCircle,
    Info,
    AlertTriangle,
    ArrowLeft,
} from "lucide-vue-next";

const theme = usePage().props.theme;

onMounted(() => {
    if (theme && theme.secondary) {
        document.documentElement.style.setProperty(
            "--color-secondary",
            theme.secondary
        );
    }
});

const page = usePage();
const notifications = computed(
    () => page.props.notifications || { unread: [], recent: [] }
);

// Icon mapping
const iconMap = {
    "user-check": UserCheck,
    "mail-check": MailCheck,
    bell: Bell,
    "alert-circle": AlertCircle,
    info: Info,
    "alert-triangle": AlertTriangle,
};

// Color mapping based on notification type
const getTypeColor = (type) => {
    const colors = {
        registration_success: "border-l-green-500 bg-green-50",
        email_verified: "border-l-blue-500 bg-blue-50",
        success: "border-l-green-500 bg-green-50",
        error: "border-l-red-500 bg-red-50",
        warning: "border-l-yellow-500 bg-yellow-50",
        info: "border-l-blue-500 bg-blue-50",
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

    // Navigate to action URL if exists
    if (notification.action_url) {
        router.visit(notification.action_url);
    }
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

// Format full date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Get all notifications combined
const allNotifications = computed(() => {
    const unread = notifications.value.unread.map((n) => ({
        ...n,
        isUnread: true,
    }));
    const recent = notifications.value.recent
        .filter((n) => n.read_at)
        .map((n) => ({ ...n, isUnread: false }));

    return [...unread, ...recent].sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
    );
});
</script>

<template>
    <Head title="Notifikasi" />

    <Navbar />
    <div class="min-w-[390px py-8 font-custom">
        <div class="lg:mx-20 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div
                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between"
                >
                    <div class="flex items-center space-x-4">
                        <button
                            @click="router.visit('/')"
                            class="p-2 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            <ArrowLeft class="w-5 h-5" />
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                Notifikasi
                            </h1>
                            <p class="text-gray-600">
                                Kelola semua notifikasi Anda
                            </p>
                        </div>
                    </div>

                    <button
                        v-if="notifications.unread.length > 0"
                        @click="markAllAsRead"
                        class="mt-4 lg:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <Check class="w-4 h-4 mr-2" />
                        Tandai Semua Dibaca
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="space-y-4">
                <!-- Unread Section -->
                <div v-if="notifications.unread.length > 0" class="space-y-4">
                    <h2
                        class="text-lg font-semibold text-gray-900 flex items-center"
                    >
                        <div
                            class="w-3 h-3 bg-blue-500 rounded-full mr-3"
                        ></div>
                        Belum Dibaca ({{ notifications.unread.length }})
                    </h2>

                    <div
                        v-for="notification in notifications.unread"
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        :class="[
                            'bg-white rounded-lg shadow-sm border-l-4 p-6 cursor-pointer hover:shadow-md transition-shadow',
                            getTypeColor(notification.type),
                        ]"
                    >
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <component
                                    :is="iconMap[notification.icon] || Bell"
                                    class="w-6 h-6 text-blue-600"
                                />
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3
                                            class="text-base font-medium text-gray-900 mb-1"
                                        >
                                            {{ notification.title }}
                                        </h3>
                                        <p class="text-sm text-gray-700 mb-2">
                                            {{ notification.message }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{
                                                formatDate(
                                                    notification.created_at
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <!-- Unread indicator -->
                                    <div class="flex-shrink-0 ml-4">
                                        <div
                                            class="w-3 h-3 bg-blue-500 rounded-full"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div
                                    v-if="notification.action_url"
                                    class="mt-3"
                                >
                                    <button
                                        class="text-blue-600 text-sm font-medium hover:text-blue-700"
                                    >
                                        {{
                                            notification.action_text ||
                                            "Lihat Detail"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Section -->
                <div v-if="notifications.recent.length > 0" class="space-y-4">
                    <h2
                        class="text-lg font-semibold text-gray-900 flex items-center"
                    >
                        <div
                            class="w-3 h-3 bg-gray-300 rounded-full mr-3"
                        ></div>
                        Terbaru
                    </h2>

                    <div
                        v-for="notification in notifications.recent"
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        class="bg-white rounded-lg shadow-sm border-l-4 border-l-gray-300 p-6 cursor-pointer hover:shadow-md transition-shadow opacity-75"
                    >
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <component
                                    :is="iconMap[notification.icon] || Bell"
                                    class="w-6 h-6 text-gray-400"
                                />
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-base font-medium text-gray-900 mb-1"
                                >
                                    {{ notification.title }}
                                </h3>
                                <p class="text-sm text-gray-700 mb-2">
                                    {{ notification.message }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(notification.created_at) }}
                                </p>

                                <!-- Action Button -->
                                <div
                                    v-if="notification.action_url"
                                    class="mt-3"
                                >
                                    <button
                                        class="text-gray-600 text-sm font-medium hover:text-gray-700"
                                    >
                                        {{
                                            notification.action_text ||
                                            "Lihat Detail"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="allNotifications.length === 0"
                    class="text-center py-12"
                >
                    <Bell class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Tidak ada notifikasi
                    </h3>
                    <p class="text-gray-500">
                        Anda akan menerima notifikasi di sini ketika ada
                        aktivitas baru.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
