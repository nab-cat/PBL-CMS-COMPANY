<script setup>
import { Link } from '@inertiajs/vue3'

</script>

<template>
    <div class="flex justify-center items-center max-w-screen-xl mx-auto h-auto py-16 font-custom">
        <div class="flex flex-col items-center w-full max-w-screen-lg px-24">
            <!-- Heading -->
            <h1 class="text-3xl sm:text-4xl md:text-4xl lg:text-5xl text-shadow text-center"
                style="line-height: 1.3 !important;">
                Temukan siapa kami dan ke mana kami melangkah.
            </h1>

            <div class="pt-6">
                <!-- Button -->
                <Link href="/sejarah-perusahaan"
                    class="relative flex items-center justify-center gap-2 px-6 py-4 sm:px-8 sm:py-5 bg-black text-white rounded-xl text-base sm:text-lg md:text-xl font-bold hover:opacity-90 transition duration-200">
                <span>Pelajari lebih lanjut</span>
                <ChevronRight class="w-5 h-5 sm:w-6 sm:h-6" />
                </Link>
            </div>
        </div>
    </div>
</template>