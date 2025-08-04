<script setup>
import { X, FileText } from 'lucide-vue-next';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <Transition 
        enter-active-class="transition-all duration-300 ease-out" 
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100" 
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 scale-100" 
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 font-custom">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[85vh] flex flex-col overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <FileText class="w-5 h-5 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 font-custom">
                            Syarat dan Ketentuan Feedback
                        </h3>
                    </div>
                    <button 
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                    >
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <!-- Content - Scrollable -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="space-y-6 text-gray-700">
                        <!-- Pendahuluan -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 font-custom">
                                1. Ketentuan Umum
                            </h4>
                            <p class="text-sm leading-relaxed font-custom">
                                Dengan memberikan feedback, Anda setuju untuk mematuhi syarat dan ketentuan berikut. 
                                Feedback yang Anda berikan akan membantu kami meningkatkan kualitas layanan.
                            </p>
                        </div>

                        <!-- Tata Tertib -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 font-custom">
                                2. Tata Tertib Feedback
                            </h4>
                            <ul class="space-y-2 text-sm leading-relaxed font-custom">
                                <li class="flex items-start gap-2">
                                    <span class="text-green-500 mt-1">✓</span>
                                    <span>Gunakan bahasa yang sopan, santun, dan tidak menyinggung pihak lain</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-green-500 mt-1">✓</span>
                                    <span>Berikan feedback yang konstruktif dan relevan dengan layanan kami</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-green-500 mt-1">✓</span>
                                    <span>Hindari penggunaan kata-kata kasar, sara, atau diskriminatif</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-green-500 mt-1">✓</span>
                                    <span>Jangan mencantumkan informasi pribadi seperti nomor telepon atau alamat lengkap</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-green-500 mt-1">✓</span>
                                    <span>Berikan detail yang jelas untuk membantu kami memahami masalah atau saran Anda</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Larangan -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 font-custom">
                                3. Hal yang Dilarang
                            </h4>
                            <ul class="space-y-2 text-sm leading-relaxed font-custom">
                                <li class="flex items-start gap-2">
                                    <span class="text-red-500 mt-1">X</span>
                                    <span>Spam atau feedback yang berulang-ulang dengan konten yang sama</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-red-500 mt-1">X</span>
                                    <span>Konten yang mengandung unsur politik, pornografi, atau kekerasan</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-red-500 mt-1">X</span>
                                    <span>Promosi produk atau layanan dari pihak ketiga</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-red-500 mt-1">X</span>
                                    <span>Tuduhan palsu atau fitnah terhadap perusahaan atau individu</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-red-500 mt-1">X</span>
                                    <span>Menggunakan identitas palsu atau menyamar sebagai orang lain</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Privasi -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 font-custom">
                                4. Privasi dan Kerahasiaan
                            </h4>
                            <p class="text-sm leading-relaxed font-custom">
                                Informasi yang Anda berikan akan dijaga kerahasiaannya dan hanya digunakan untuk 
                                keperluan internal perusahaan. Kami tidak akan membagikan data pribadi Anda kepada 
                                pihak ketiga tanpa persetujuan Anda.
                            </p>
                        </div>

                        <!-- Moderasi -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 font-custom">
                                5. Moderasi Konten
                            </h4>
                            <p class="text-sm leading-relaxed font-custom">
                                Kami berhak untuk meninjau, mengedit, atau menghapus feedback yang melanggar ketentuan 
                                ini tanpa pemberitahuan sebelumnya. Feedback yang sesuai ketentuan akan ditampilkan 
                                setelah proses verifikasi.
                            </p>
                        </div>

                        <!-- Tanggung Jawab -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 font-custom">
                                6. Tanggung Jawab
                            </h4>
                            <p class="text-sm leading-relaxed font-custom">
                                Anda bertanggung jawab penuh atas konten feedback yang diberikan. Perusahaan tidak 
                                bertanggung jawab atas kerugian yang timbul akibat feedback yang tidak sesuai ketentuan.
                            </p>
                        </div>

                        <!-- Penutup -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                            <p class="text-sm text-blue-700 font-custom leading-6">
                                <span class="font-bold">Catatan:</span> 
                                Ketentuan ini dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya. 
                                Dengan memberikan feedback, Anda dianggap telah membaca dan menyetujui seluruh ketentuan di atas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer - Fixed -->
                <div class="flex justify-end gap-3 p-6 border-t border-gray-100 bg-white flex-shrink-0">
                    <button 
                        @click="closeModal"
                        class="px-6 py-3 bg-secondary hover:bg-black text-white font-medium rounded-xl transition-all duration-200 transform font-custom"
                    >
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>