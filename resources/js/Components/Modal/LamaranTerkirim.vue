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

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
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
                    <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Lamaran Berhasil Dikirim!
                    </h3>

                    <!-- Description -->
                    <div class="text-sm text-gray-600 mb-6 space-y-2">
                        <p>Terima kasih telah mengirimkan lamaran Anda.</p>
                        <p>Tim kami akan meninjau lamaran Anda dan akan menghubungi jika memenuhi kualifikasi.</p>
                        <p class="text-xs bg-blue-50 text-blue-700 p-2 rounded-lg border border-blue-200 mt-2">
                            <span class="font-medium">Info:</span> Anda dapat melihat status lamaran Anda di halaman profil.
                        </p>
                    </div>

                    <!-- Action Button -->
                    <div class="flex">
                        <button @click="closeModal"
                            class="w-full bg-secondary hover:bg-black text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>