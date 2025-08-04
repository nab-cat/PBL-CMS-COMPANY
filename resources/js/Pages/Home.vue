<script setup>
import AppLayout from "../Layouts/AppLayout.vue";
import Hero from "../Pages/Home/Hero.vue";
import KontenSlider from "./Home/KontenSlider.vue";
import Produk from "@/Pages/Home/Produk.vue";
import Artikel from "@/Pages/Home/Artikel.vue";
import CallToAction from "../Pages/Home/CallToAction.vue";
import Galeri from "../Pages/Home/Galeri.vue";
import Event from "@/Pages/Home/Event.vue";
import Lowongan from "@/Pages/Home/Lowongan.vue";
import Mitra from "@/Pages/Home/Mitra.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import Testimoni from "./Home/Testimoni.vue";

const featureToggles = ref({});

onMounted(async () => {
    const response = await axios.get('/api/feature-toggles');
    featureToggles.value = response.data.data;
});
</script>


<template>
    <AppLayout>
        <Hero />
        <div v-if="featureToggles.kontenslider_module">
            <KontenSlider />
        </div>
        <div v-if="featureToggles.produk_module">
            <Produk />
        </div>
        <div v-if="featureToggles.artikel_module">
            <Artikel />
        </div>
        <div v-if="featureToggles.galeri_module">
            <Galeri />
        </div>
        <div v-if="featureToggles.event_module">
            <Event />
        </div>
        <div v-if="featureToggles.lowongan_module">
            <Lowongan />
        </div>
        <div v-if="featureToggles.testimoni_module">
            <Testimoni/>
        </div>
        <div v-if="featureToggles.mitra_module">
            <Mitra /> 
        </div>
        <CallToAction />
    </AppLayout>
</template>
