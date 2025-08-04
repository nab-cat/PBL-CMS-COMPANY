<script setup>
import AOS, { init } from "aos";
import "aos/dist/aos.css";
import { School } from "lucide-vue-next";

AOS.init();
</script>

<template>
    <div class="bg-gray-100 p-6 space-y-10 font-custom">
        <h1 class="text-3xl font-bold text-center">Belajar Tailwind CSS</h1>
        <p class="text-gray-600 text-center mb-10">
            Contoh penggunaan Tailwind CSS dalam Vue.js
        </p>

        <!-- Layout: Flex dan Grid -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Layout</h2>
            <div class="flex justify-between">
                <div class="bg-blue-200 p-4 rounded">Item 1</div>
                <div class="bg-blue-400 p-4 rounded">Item 2</div>
                <div class="bg-blue-600 p-4 rounded">Item 3</div>
            </div>
            <!-- Penjelasan: flex untuk horizontal layout, justify-between untuk jarak antar item -->
        </section>

        <!-- Spacing: Padding dan Margin -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Spacing</h2>
            <div class="m-4 p-6 bg-green-200 rounded">
                Ini box dengan margin 4 dan padding 6
            </div>
        </section>

        <!-- Typography: Ukuran dan Style Teks -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Typography</h2>
            <p class="text-sm text-gray-600">Teks kecil abu-abu</p>
            <p class="text-lg font-semibold text-blue-700">
                Teks besar dan tebal biru
            </p>
        </section>

        <!-- Background dan Border -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Background & Border</h2>
            <div
                class="bg-gradient-to-r from-blue-400 to-purple-600 text-white p-6 rounded-lg border-4 border-white"
            >
                Box dengan gradient background dan border putih
            </div>
        </section>

        <!-- Sizing: Width dan Height -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Sizing</h2>
            <div
                class="w-32 h-32 bg-yellow-300 flex items-center justify-center"
            >
                128x128 px
            </div>
        </section>

        <!-- Effects: Shadow, Opacity, Blur -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Effects</h2>
            <div class="shadow-lg p-6 bg-white rounded opacity-80">
                Box dengan shadow besar dan sedikit transparan
            </div>
            <div class="mt-4 backdrop-blur-md bg-white/30 p-6 rounded">
                Blur latar belakang di belakang box ini
            </div>
        </section>

        <!-- Transition & Animation -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Transition & Animation</h2>
            <button
                class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-700 transition-colors duration-300"
            >
                Hover aku!
            </button>

            <div
                class="mt-6 w-16 h-16 bg-green-400 rounded-full animate-bounce"
            ></div>
            <!-- Penjelasan: Button ini punya efek hover halus, kotak hijau ini bouncing otomatis -->
        </section>

        <!-- Responsive & State Variants -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Responsive & State</h2>
            <div class="hidden md:block bg-red-400 p-6 rounded">
                Ini hanya muncul di layar medium ke atas (responsive)
            </div>

            <button
                class="mt-4 px-4 py-2 bg-indigo-500 text-white rounded focus:ring-4"
            >
                Fokusin aku!
            </button>
        </section>

        <!-- Responsive Text -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Responsive Text</h2>
            <p class="text-sm md:text-lg lg:text-2xl">
                Teks ini kecil di HP, sedang di tablet, besar di desktop.
            </p>
        </section>

        <!-- Hover dan Focus -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Hover dan Focus</h2>
            <input
                type="text"
                placeholder="Fokusin aku"
                class="border border-gray-300 rounded px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-300"
            />
        </section>

        <!--Dark Mode -->
        <section class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h2
                class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100"
            >
                Dark Mode
            </h2>
            <p class="text-gray-600 dark:text-gray-300">
                Aku berubah kalau dark mode aktif.
            </p>
        </section>

        <!-- AOS Animation -->
        <button
            class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-700 transition-all duration-300 ease-in-out"
        >
            Hover Aku
        </button>

        <div
            class="w-8 h-8 border-4 border-dashed rounded-full animate-spin border-blue-500"
        ></div>

        <div data-aos="fade-up" class="bg-white p-6 rounded shadow">
            Ini bakal fade-up saat discroll
        </div>

        <section class="text-center p-10 space-y-6">
            <h1 class="text-4xl font-bold" data-aos="fade-up">
                Selamat Datang di Website Kami
            </h1>

            <p class="text-gray-600" data-aos="fade">
                Temukan berbagai artikel menarik dan event seru di sini.
            </p>

            <div class="flex justify-center space-x-6 mt-8">
                <div class="bg-white p-6 rounded shadow-md" data-aos="zoom-in">
                    Card 1
                </div>
                <div
                    class="bg-white p-6 rounded shadow-md"
                    data-aos="zoom-in"
                    data-aos-delay="100"
                >
                    Card 2
                </div>
                <div
                    class="bg-white p-6 rounded shadow-md"
                    data-aos="zoom-in"
                    data-aos-delay="200"
                >
                    Card 3
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 p-10 bg-gray-100">
            <!-- Card 1 -->
            <div
                class="relative group overflow-hidden rounded-lg shadow-lg bg-white"
            >
                <img
                    src="/image/placeholder.webp"
                    alt="Gambar"
                    class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-500"
                />

                <!-- Overlay -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition duration-500 flex flex-col justify-end p-6"
                >
                    <!-- Lucide Icon -->
                    <School class="text-white w-8 h-8 mb-4" />

                    <h3 class="text-white text-xl font-bold">Judul Card</h3>
                    <p class="text-white text-sm mt-1">
                        Deskripsi singkat tentang card ini.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div
                class="relative group overflow-hidden rounded-lg shadow-lg bg-white"
            >
                <img
                    src="/image/placeholder.webp"
                    alt="Gambar"
                    class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-500"
                />

                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition duration-500 flex flex-col justify-end p-6"
                >
                    <Target class="text-white w-8 h-8 mb-4" />
                    <h3 class="text-white text-xl font-bold">Judul Card 2</h3>
                    <p class="text-white text-sm mt-1">
                        Deskripsi untuk card kedua.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div
                class="relative group overflow-hidden rounded-lg shadow-lg bg-white"
            >
                <img
                    src="/image/placeholder.webp"
                    alt="Gambar"
                    class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-500"
                />

                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition duration-500 flex flex-col justify-end p-6"
                >
                    <Rocket class="text-white w-8 h-8 mb-4" />
                    <h3 class="text-white text-xl font-bold">Judul Card 3</h3>
                    <p class="text-white text-sm mt-1">
                        Deskripsi untuk card ketiga.
                    </p>
                </div>
            </div>
        </section>

    </div>
</template>
