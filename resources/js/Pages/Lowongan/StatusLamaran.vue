<script setup>
import { ref } from "vue";
import axios from "axios";
import { RefreshCw, CheckCircle } from "lucide-vue-next";

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
    formatTanggal: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits(["update:application"]);
const isRefreshing = ref(false);

async function refreshApplicationStatus() {
    if (!props.application || !props.application.id_lamaran) {
        return;
    }

    try {
        isRefreshing.value = true;

        const response = await axios.get(
            `/api/lamaran/${props.application.id_lamaran}`,
            {
                // Add cache busting parameter to ensure we get fresh data
                params: {
                    _: Date.now(),
                },
            }
        );

        if (
            response.data &&
            response.data.status === "success" &&
            response.data.data
        ) {
            emit("update:application", response.data.data);

            // Show success message if status changed
            if (
                response.data.data.status_lamaran !==
                props.application.status_lamaran
            ) {
                console.log(
                    "Status updated:",
                    response.data.data.status_lamaran
                );
            }
        }
    } catch (err) {
    } finally {
        isRefreshing.value = false;
    }
}
</script>

<template>
    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg p-5">
        <div class="flex items-center justify-center mb-4">
            <div class="p-2 rounded-full bg-white/20">
                <CheckCircle class="w-6 h-6 text-white" />
            </div>
        </div>

        <h4 class="text-center text-lg font-medium text-white mb-2">
            Anda Sudah Melamar
        </h4>
        <p class="text-center text-white/70 mb-4">
            Anda telah mengirimkan lamaran untuk posisi ini pada
            {{ formatTanggal(application.created_at) }}.
        </p>

        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-white/60">Status Lamaran:</p>
                <button
                    @click="refreshApplicationStatus"
                    :disabled="isRefreshing"
                    class="text-xs text-white/70 hover:text-white flex items-center gap-1 transition-colors disabled:opacity-50"
                >
                    <RefreshCw
                        :class="{ 'animate-spin': isRefreshing }"
                        class="w-3 h-3"
                    />
                    {{ isRefreshing ? "Memuat..." : "Perbarui" }}
                </button>
            </div>
            <div class="flex justify-center">
                <span
                    :class="{
                        'px-3 py-1.5 rounded-full text-sm font-medium backdrop-blur-sm border': true,
                        'bg-blue-500/20 text-blue-200 border-blue-400/30':
                            application.status_lamaran === 'Diproses',
                        'bg-green-500/20 text-green-200 border-green-400/30':
                            application.status_lamaran === 'Diterima',
                        'bg-red-500/20 text-red-200 border-red-400/30':
                            application.status_lamaran === 'Ditolak',
                        'bg-yellow-500/20 text-yellow-200 border-yellow-400/30':
                            application.status_lamaran === 'Menunggu',
                    }"
                >
                    {{ application.status_lamaran }}
                </span>
            </div>
        </div>

        <div class="text-center">
            <p class="text-sm text-white/60">
                Tim kami akan menghubungi Anda melalui email jika ada
                perkembangan.
            </p>
        </div>
    </div>
</template>
