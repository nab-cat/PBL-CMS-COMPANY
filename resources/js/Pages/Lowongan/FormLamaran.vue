<script setup>
import { ref } from "vue";
import axios from "axios";
import { Send, Loader, Eye, X, FileText, File, Archive } from "lucide-vue-next";

const props = defineProps({
    lowongan: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["success"]);

const isApplying = ref(false);
const previewFile = ref(null);
const previewType = ref("");
const showPreview = ref(false);
const formData = ref({
    nama_lengkap: props.user?.name || "",
    email: props.user?.email || "",
    surat_lamaran: null,
    cv: null,
    portfolio: null,
    pesan_pelamar: "",
});

// File preview URLs
const filePreviewUrls = ref({
    surat_lamaran: null,
    cv: null,
    portfolio: null,
});

function handleFileUpload(fieldName) {
    return (event) => {
        const file = event.target.files[0];
        if (file) {
            formData.value[fieldName] = file;

            // Create preview URL
            if (filePreviewUrls.value[fieldName]) {
                URL.revokeObjectURL(filePreviewUrls.value[fieldName]);
            }
            filePreviewUrls.value[fieldName] = URL.createObjectURL(file);
        }
    };
}

function openPreview(fieldName) {
    const file = formData.value[fieldName];
    if (file) {
        previewFile.value = filePreviewUrls.value[fieldName];
        previewType.value = file.type;
        showPreview.value = true;
    }
}

function closePreview() {
    showPreview.value = false;
    previewFile.value = null;
    previewType.value = "";
}

function removeFile(fieldName) {
    formData.value[fieldName] = null;
    if (filePreviewUrls.value[fieldName]) {
        URL.revokeObjectURL(filePreviewUrls.value[fieldName]);
        filePreviewUrls.value[fieldName] = null;
    }
    document.getElementById(fieldName).value = "";
}

function getFileIcon(file) {
    if (!file) return FileText;
    const extension = file.name.split(".").pop().toLowerCase();
    switch (extension) {
        case "pdf":
            return FileText;
        case "doc":
        case "docx":
            return File;
        case "zip":
            return Archive;
        default:
            return FileText;
    }
}

async function submitApplication() {
    if (
        !formData.value.nama_lengkap ||
        !formData.value.email ||
        !formData.value.surat_lamaran ||
        !formData.value.cv
    ) {
        alert("Silakan lengkapi semua field yang diperlukan");
        return;
    }

    try {
        isApplying.value = true;

        const submitData = new FormData();
        submitData.append("id_lowongan", props.lowongan.id_lowongan);

        // Remove id_user parameter - backend will use authenticated user
        if (!props.user?.id_user) {
            alert("Anda harus login terlebih dahulu untuk melamar");
            return;
        }

        submitData.append("surat_lamaran", formData.value.surat_lamaran);
        submitData.append("cv", formData.value.cv);

        if (formData.value.portfolio) {
            submitData.append("portfolio", formData.value.portfolio);
        }

        submitData.append("pesan_pelamar", formData.value.pesan_pelamar);

        const response = await axios.post("/api/lamaran", submitData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        // Cleanup preview URLs
        Object.values(filePreviewUrls.value).forEach((url) => {
            if (url) URL.revokeObjectURL(url);
        });

        // Reset form
        formData.value = {
            nama_lengkap: props.user?.name || "",
            email: props.user?.email || "",
            surat_lamaran: null,
            cv: null,
            portfolio: null,
            pesan_pelamar: "",
        };

        filePreviewUrls.value = {
            surat_lamaran: null,
            cv: null,
            portfolio: null,
        };

        // Reset file inputs
        document.getElementById("surat_lamaran").value = "";
        document.getElementById("cv").value = "";
        if (document.getElementById("portfolio")) {
            document.getElementById("portfolio").value = "";
        }

        // Emit success event for parent component with response data
        emit("success", response.data);
    } catch (err) {
        alert(
            "Gagal mengirim lamaran: " +
                (err.response?.data?.message || err.message)
        );
    } finally {
        isApplying.value = false;
    }
}
</script>

<template>
    <form @submit.prevent="submitApplication" class="space-y-4">
        <!-- Nama Lengkap -->
        <div>
            <label
                for="nama_lengkap"
                class="block text-sm font-medium text-white mb-2"
            >
                Nama Lengkap <span class="text-red-400">*</span>
            </label>
            <input
                id="nama_lengkap"
                v-model="formData.nama_lengkap"
                type="text"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:bg-white/15 focus:border-white/30"
                placeholder="Masukkan nama lengkap Anda"
                required
            />
        </div>

        <!-- Email -->
        <div>
            <label
                for="email"
                class="block text-sm font-medium text-white mb-2"
            >
                Email <span class="text-red-400">*</span>
            </label>
            <input
                id="email"
                v-model="formData.email"
                type="email"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:bg-white/15 focus:border-white/30"
                placeholder="contoh@email.com"
                required
            />
        </div>

        <!-- Surat Lamaran -->
        <div>
            <label
                for="surat_lamaran"
                class="block text-sm font-medium text-white mb-2"
            >
                Surat Lamaran <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <input
                    id="surat_lamaran"
                    type="file"
                    @change="handleFileUpload('surat_lamaran')($event)"
                    accept=".pdf,.doc,.docx"
                    class="w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/20 file:text-white hover:file:bg-white/30 file:cursor-pointer cursor-pointer focus:outline-none focus:bg-white/15 focus:border-white/30"
                    required
                />
            </div>
            <!-- File Preview Card -->
            <div
                v-if="formData.surat_lamaran"
                class="mt-3 p-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg">
                            <component
                                :is="getFileIcon(formData.surat_lamaran)"
                                class="w-5 h-5 text-white"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ formData.surat_lamaran.name }}
                            </p>
                            <p class="text-xs text-white/60">
                                {{
                                    (
                                        formData.surat_lamaran.size /
                                        1024 /
                                        1024
                                    ).toFixed(2)
                                }}
                                MB
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            @click="openPreview('surat_lamaran')"
                            class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                            title="Preview file"
                        >
                            <Eye class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            @click="removeFile('surat_lamaran')"
                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                            title="Hapus file"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CV Upload -->
        <div>
            <label for="cv" class="block text-sm font-medium text-white mb-2">
                CV/Resume <span class="text-red-400">*</span>
            </label>
            <input
                id="cv"
                type="file"
                @change="handleFileUpload('cv')($event)"
                accept=".pdf,.doc,.docx"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/20 file:text-white hover:file:bg-white/30 file:cursor-pointer cursor-pointer focus:outline-none focus:bg-white/15 focus:border-white/30"
                required
            />
            <!-- File Preview Card -->
            <div
                v-if="formData.cv"
                class="mt-3 p-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg">
                            <component
                                :is="getFileIcon(formData.cv)"
                                class="w-5 h-5 text-white"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ formData.cv.name }}
                            </p>
                            <p class="text-xs text-white/60">
                                {{
                                    (formData.cv.size / 1024 / 1024).toFixed(2)
                                }}
                                MB
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            @click="openPreview('cv')"
                            class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                            title="Preview file"
                        >
                            <Eye class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            @click="removeFile('cv')"
                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                            title="Hapus file"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Portfolio (optional) -->
        <div>
            <label
                for="portfolio"
                class="block text-sm font-medium text-white mb-2"
            >
                Portfolio
                <span class="text-white/60 text-xs font-normal"
                    >(opsional)</span
                >
            </label>
            <input
                id="portfolio"
                type="file"
                @change="handleFileUpload('portfolio')($event)"
                accept=".pdf,.doc,.docx,.zip"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/20 file:text-white hover:file:bg-white/30 file:cursor-pointer cursor-pointer focus:outline-none focus:bg-white/15 focus:border-white/30"
            />
            <!-- File Preview Card -->
            <div
                v-if="formData.portfolio"
                class="mt-3 p-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg">
                            <component
                                :is="getFileIcon(formData.portfolio)"
                                class="w-5 h-5 text-white"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ formData.portfolio.name }}
                            </p>
                            <p class="text-xs text-white/60">
                                {{
                                    (
                                        formData.portfolio.size /
                                        1024 /
                                        1024
                                    ).toFixed(2)
                                }}
                                MB
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            @click="openPreview('portfolio')"
                            class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                            title="Preview file"
                        >
                            <Eye class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            @click="removeFile('portfolio')"
                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                            title="Hapus file"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pesan Pelamar -->
        <div>
            <label
                for="pesan_pelamar"
                class="block text-sm font-medium text-white mb-2"
            >
                Pesan Lamaran
                <span class="text-white/60 text-xs font-normal"
                    >(opsional)</span
                >
            </label>
            <textarea
                id="pesan_pelamar"
                v-model="formData.pesan_pelamar"
                rows="4"
                class="w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:bg-white/15 focus:border-white/30 resize-none"
                placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini..."
            ></textarea>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            :disabled="isApplying"
            class="w-full bg-white text-black hover:bg-white/90 transition-colors duration-300 py-3.5 px-6 rounded-lg font-semibold flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
        >
            <Send v-if="!isApplying" class="w-5 h-5" />
            <Loader v-else class="animate-spin h-5 w-5" />
            {{ isApplying ? "Mengirim Lamaran..." : "Kirim Lamaran" }}
        </button>

        <!-- Required Fields Info -->
        <div class="flex items-center gap-2 pt-2">
            <div class="w-1 h-1 bg-red-400 rounded-full"></div>
            <p class="text-xs text-white/60">
                Field bertanda <span class="text-red-400">*</span> wajib diisi
            </p>
        </div>
    </form>

    <!-- File Preview Modal -->
    <div
        v-if="showPreview"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    >
        <div
            class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl"
        >
            <div
                class="flex items-center justify-between p-6 border-b border-gray-200"
            >
                <h3 class="text-lg font-semibold text-gray-900">
                    Preview File
                </h3>
                <button
                    @click="closePreview"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6 h-[70vh] overflow-auto">
                <!-- PDF Preview -->
                <iframe
                    v-if="previewType === 'application/pdf'"
                    :src="previewFile"
                    class="w-full h-full border-0 rounded-lg"
                    title="PDF Preview"
                ></iframe>

                <!-- DOC/DOCX Preview (fallback message) -->
                <div
                    v-else-if="
                        previewType.includes('document') ||
                        previewType.includes('word')
                    "
                    class="flex flex-col items-center justify-center h-full text-gray-500"
                >
                    <div class="p-6 bg-gray-50 rounded-full mb-6">
                        <FileText class="w-16 h-16 text-gray-400" />
                    </div>
                    <p class="text-xl font-semibold mb-2 text-gray-700">
                        Preview Tidak Tersedia
                    </p>
                    <p class="text-sm text-center text-gray-600 mb-6 max-w-md">
                        File Word/DOC tidak dapat di-preview di browser. Silakan
                        download untuk melihat isi file.
                    </p>
                    <a
                        :href="previewFile"
                        :download="true"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        Download File
                    </a>
                </div>

                <!-- Other file types -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center h-full text-gray-500"
                >
                    <div class="p-6 bg-gray-50 rounded-full mb-6">
                        <FileText class="w-16 h-16 text-gray-400" />
                    </div>
                    <p class="text-xl font-semibold mb-2 text-gray-700">
                        Preview Tidak Tersedia
                    </p>
                    <p class="text-sm text-center text-gray-600">
                        Tipe file ini tidak dapat di-preview di browser.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
