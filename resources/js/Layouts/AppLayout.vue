<script setup>
import { ref, onMounted } from "vue";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import Feedback from "@/Components/Feedback.vue";
import { usePage } from "@inertiajs/vue3";

const theme = usePage().props.theme;

onMounted(() => {
    if (theme && theme.secondary) {
        document.documentElement.style.setProperty(
            "--color-secondary",
            theme.secondary
        );
    }
});
</script>

<template>
    <div class="min-w-[390px]">
        <Navbar />
        <main>
            <slot />
        </main>
        <Feedback />
        <Footer />
    </div>
</template>
