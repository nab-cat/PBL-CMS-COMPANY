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

const emit = defineEmits(['close', 'login']);

const closeModal = () => {
    emit('close');
};

const redirectToLogin = () => {
    window.location.href = '/admin/login';
    closeModal();
};
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
                    <!-- Info Icon -->
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Login Diperlukan
                    </h3>

                    <!-- Description -->
                    <div class="text-sm text-gray-600 mb-6">
                        <p>Silakan login terlebih dahulu untuk mendaftar event.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button @click="closeModal"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Nanti Saja
                        </button>
                        <button @click="redirectToLogin"
                            class="flex-1 bg-secondary hover:bg-secondary/90 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Login Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>