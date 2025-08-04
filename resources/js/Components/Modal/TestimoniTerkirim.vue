<script setup>
import { watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    autoClose: {
        type: Boolean,
        default: false
    },
    autoCloseDelay: {
        type: Number,
        default: 5000
    }
});

const emit = defineEmits(['close', 'write-another']);

const closeModal = () => {
    emit('close');
};

const writeAnother = () => {
    emit('write-another');
    closeModal();
};

// Auto close functionality
watch(() => props.show, (newValue) => {
    if (newValue && props.autoClose) {
        setTimeout(() => {
            closeModal();
        }, props.autoCloseDelay);
    }
});
</script>
<template>
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 font-custom">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 mx-4">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Testimoni Berhasil Dikirim!
                    </h3>

                    <!-- Description -->
                    <div class="text-sm text-gray-600 mb-6 space-y-2">
                        <p>Terima kasih telah berbagi pengalaman Anda!</p>
                        <p class="text-xs bg-yellow-50 text-yellow-700 p-2 rounded-lg border border-yellow-200">
                            <span class="font-medium">Info:</span> Testimoni Anda sedang dalam proses persetujuan dan
                            akan ditampilkan setelah diverifikasi.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="closeModal"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Tutup
                        </button>
                        <button @click="writeAnother"
                            class="flex-1 bg-secondary hover:bg-black text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Tulis Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
