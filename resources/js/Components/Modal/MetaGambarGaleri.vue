<template>
    <!-- Image Metadata Modal -->
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 font-custom"
        @click="$emit('close')"
    >
        <div
            class="bg-white rounded-xl w-full max-w-lg flex flex-col shadow-2xl relative"
            style="max-height: 90vh;"
            @click.stop
        >
            <!-- Header - Fixed -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-semibold text-gray-900 pr-8">Info Gambar</h3>
                <button
                    @click="$emit('close')"
                    class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all duration-200 flex-shrink-0 z-10"
                    title="Tutup"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Content - Scrollable -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6 space-y-4">
                    <!-- Loading State -->
                    <div v-if="loadingMeta" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-secondary"></div>
                        <p class="mt-3 text-sm text-gray-600">Memuat informasi gambar...</p>
                    </div>

                    <!-- Metadata Display -->
                    <div v-else-if="metadata && !metadata.error" class="space-y-6">
                        <!-- Image Preview -->
                        <div class="relative rounded-lg overflow-hidden bg-gray-100 shadow-sm">
                            <img
                                :src="currentImageUrl"
                                :alt="galleryTitle"
                                class="w-full max-h-48 object-contain bg-gray-50"
                                style="aspect-ratio: 16/9;"
                            />
                        </div>

                        <!-- Basic Info Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-medium text-gray-600 text-xs uppercase tracking-wide">Dimensi</dt>
                                <dd class="text-gray-900 mt-1 font-semibold">{{ metadata.resolution }}</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-medium text-gray-600 text-xs uppercase tracking-wide">Ukuran File</dt>
                                <dd class="text-gray-900 mt-1 font-semibold">{{ metadata.size_formatted }}</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-medium text-gray-600 text-xs uppercase tracking-wide">Format</dt>
                                <dd class="text-gray-900 mt-1 font-semibold">{{ metadata.type }}</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="font-medium text-gray-600 text-xs uppercase tracking-wide">Aspek Rasio</dt>
                                <dd class="text-gray-900 mt-1 font-semibold">{{ metadata.aspect_ratio }}:1</dd>
                            </div>
                        </div>

                        <!-- File Info Section -->
                        <div v-if="metadata.bits || metadata.channels || metadata.file_created || metadata.file_modified" 
                             class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-3 text-sm">Informasi File</h4>
                            <div class="space-y-3 text-sm">
                                <div v-if="metadata.bits" class="flex justify-between">
                                    <dt class="text-gray-600">Kedalaman Bit</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.bits }} bit</dd>
                                </div>
                                <div v-if="metadata.channels" class="flex justify-between">
                                    <dt class="text-gray-600">Channel</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.channels }}</dd>
                                </div>
                                <div v-if="metadata.file_created" class="flex justify-between">
                                    <dt class="text-gray-600">Dibuat</dt>
                                    <dd class="text-gray-900 font-medium">{{ formatDate(metadata.file_created) }}</dd>
                                </div>
                                <div v-if="metadata.file_modified" class="flex justify-between">
                                    <dt class="text-gray-600">Dimodifikasi</dt>
                                    <dd class="text-gray-900 font-medium">{{ formatDate(metadata.file_modified) }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- EXIF Data Section -->
                        <div v-if="metadata.exif && Object.keys(metadata.exif).length > 0" 
                             class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-3 text-sm">Informasi Kamera</h4>
                            <div class="space-y-3 text-sm">
                                <div v-if="metadata.exif.camera_make" class="flex justify-between">
                                    <dt class="text-gray-600">Merek Kamera</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.exif.camera_make }}</dd>
                                </div>
                                <div v-if="metadata.exif.camera_model" class="flex justify-between">
                                    <dt class="text-gray-600">Model Kamera</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.exif.camera_model }}</dd>
                                </div>
                                <div v-if="metadata.exif.date_taken" class="flex justify-between">
                                    <dt class="text-gray-600">Tanggal Diambil</dt>
                                    <dd class="text-gray-900 font-medium">{{ formatDate(metadata.exif.date_taken) }}</dd>
                                </div>
                                <div v-if="metadata.exif.iso" class="flex justify-between">
                                    <dt class="text-gray-600">ISO</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.exif.iso }}</dd>
                                </div>
                                <div v-if="metadata.exif.focal_length" class="flex justify-between">
                                    <dt class="text-gray-600">Focal Length</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.exif.focal_length }}</dd>
                                </div>
                                <div v-if="metadata.exif.aperture" class="flex justify-between">
                                    <dt class="text-gray-600">Aperture</dt>
                                    <dd class="text-gray-900 font-medium">f/{{ metadata.exif.aperture }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Info Section -->
                        <div v-if="metadata.technical && Object.keys(metadata.technical).length > 0" 
                             class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-3 text-sm">Informasi Teknis</h4>
                            <div class="space-y-3 text-sm">
                                <div v-if="metadata.technical.compression" class="flex justify-between">
                                    <dt class="text-gray-600">Kompresi</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.technical.compression }}</dd>
                                </div>
                                <div v-if="metadata.technical.color_space" class="flex justify-between">
                                    <dt class="text-gray-600">Color Space</dt>
                                    <dd class="text-gray-900 font-medium">{{ metadata.technical.color_space }}</dd>
                                </div>
                                <div v-if="metadata.file_hash" class="flex flex-col gap-1">
                                    <dt class="text-gray-600">File Hash</dt>
                                    <dd class="text-gray-900 font-mono text-xs break-all bg-gray-50 p-2 rounded">
                                        {{ metadata.file_hash }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="metadata?.error" class="text-center py-12">
                        <div class="text-gray-400 mb-3">
                            <Info class="w-12 h-12 mx-auto" />
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ metadata.message || "Tidak dapat memuat informasi gambar" }}
                        </p>
                    </div>

                    <!-- No Data State -->
                    <div v-else class="text-center py-12">
                        <div class="text-gray-400 mb-3">
                            <Info class="w-12 h-12 mx-auto" />
                        </div>
                        <p class="text-sm text-gray-600">Informasi gambar tidak tersedia</p>
                    </div>
                </div>
            </div>

            <!-- Footer - Fixed -->
            <div class="border-t border-gray-200 p-4 sm:p-6 flex-shrink-0">
                <div class="flex justify-end">
                    <button
                        @click="$emit('close')"
                        class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200 focus:ring-2 focus:ring-gray-300 focus:outline-none"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { X, Info } from "lucide-vue-next";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    metadata: {
        type: Object,
        default: () => ({}),
    },
    loadingMeta: {
        type: Boolean,
        default: false,
    },
    currentImageUrl: {
        type: String,
        default: "",
    },
    galleryTitle: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["close"]);

function formatDate(date) {
    if (!date) return "";

    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}
</script>
