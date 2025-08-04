<script setup>
// Bagian script tidak perlu diubah, sudah bagus.
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import MiniArtikel from "./MiniArtikel.vue";
import MiniEvent from "./MiniEvent.vue";
import MiniLowongan from "./MiniLowongan.vue";
import { Link } from "@inertiajs/vue3";

defineOptions({ name: "MegaMenu" });

const featureToggles = ref({});

const tentangKamiSection = {
    title: "Tentang Kami",
    links: [
        { href: "/profil-perusahaan", icon: "Building2", label: "Profil Perusahaan" },
        { href: "/visi-misi", icon: "Binoculars", label: "Visi Misi Perusahaan" },
        { href: "/sejarah-perusahaan", icon: "ScrollText", label: "Sejarah Perusahaan" },
        { href: "/struktur-organisasi", icon: "Users", label: "Struktur Organisasi" },
    ],
};

const otherSections = [
    {
        title: "Informasi",
        links: [
            { href: "/artikel", icon: "FileText", label: "Artikel", toggleKey: "artikel_module" },
            { href: "/case-study", icon: "BookOpenCheck", label: "Case Study", toggleKey: "case_study_module" },
            { href: "/galeri", icon: "Image", label: "Galeri", toggleKey: "galeri_module" },
            { href: "/unduhan", icon: "Download", label: "Unduhan", toggleKey: "unduhan_module" },
        ],
    },
    {
        title: "Kegiatan",
        links: [
            { href: "/event", icon: "Calendar", label: "Event", toggleKey: "event_module" },
            { href: "/lowongan", icon: "BriefcaseBusiness", label: "Lowongan", toggleKey: "lowongan_module" },
        ],
    },
];

const filteredOtherSections = computed(() => {
    return otherSections
        .map((section) => {
            return {
                ...section,
                links: section.links.filter(
                    (link) => featureToggles.value[link.toggleKey] == 1
                ),
            };
        })
        .filter((section) => section.links.length > 0);
});

const filteredMenuSections = computed(() => [
    tentangKamiSection,
    ...filteredOtherSections.value,
]);

async function fetchFeatureToggles() {
    try {
        const res = await axios.get("/api/feature-toggles");
        featureToggles.value = res.data.data || {};
    } catch (err) {
        featureToggles.value = {};
    }
}

onMounted(() => {
    fetchFeatureToggles();
});
</script>

<template>
    <div
        name="mega-menu"
        class="w-full relative z-30 lg:fixed lg:top-[64px] lg:z-50 bg-primary shadow-xl max-h-[calc(100vh-64px)] overflow-y-auto"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                <div class="lg:col-span-8 xl:col-span-9">
                    <div
                        class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12"
                    >
                        <div
                            v-for="(section, index) in filteredMenuSections"
                            :key="section.title"
                            class="space-y-6"
                            :class="{
                                'lg:col-span-1':
                                    index == 0 &&
                                    filteredMenuSections.length == 3,
                            }"
                        >
                            <div class="border-b border-secondary/20 pb-3">
                                <h3
                                    class="font-bold text-lg text-secondary uppercase tracking-wide"
                                >
                                    {{ section.title }}
                                </h3>
                            </div>
                            <nav class="space-y-4">
                                <Link
                                    v-for="link in section.links"
                                    :key="link.href"
                                    :href="link.href"
                                    class="group flex items-center gap-3 text-h5 font-medium text-typography-dark hover:text-typography-hover2 transition-all duration-200 hover:translate-x-1"
                                >
                                    <div
                                        class="flex-shrink-0 w-7 h-7 flex items-center justify-center"
                                    >
                                        <component
                                            :is="link.icon"
                                            class="w-6 h-6 group-hover:scale-100 transition-transform duration-200"
                                        />
                                    </div>
                                    <span class="leading-relaxed">{{
                                        link.label
                                    }}</span>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 xl:col-span-3">
                    <div
                        class="lg:border-l lg:border-secondary/20 lg:pl-8 space-y-8"
                    >
                        <div
                            v-if="
                                featureToggles.artikel_module == 1 ||
                                featureToggles.event_module == 1 ||
                                featureToggles.lowongan_module == 1
                            "
                            class="border-b border-secondary/20 pb-3 lg:border-b-0 lg:pb-0"
                        >
                            <h3
                                class="font-bold text-lg text-secondary uppercase tracking-wide lg:mb-6"
                            >
                                Konten Terbaru
                            </h3>
                        </div>
                        <div class="space-y-6">
                            <MiniArtikel
                                v-if="featureToggles.artikel_module == 1"
                            />
                            <MiniEvent
                                v-if="featureToggles.event_module == 1"
                            />
                            <MiniLowongan
                                v-if="featureToggles.lowongan_module == 1"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>