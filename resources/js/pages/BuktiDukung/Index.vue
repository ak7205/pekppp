<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Bukti Dukung', href: route('bukti.index') },
        ],
    },
})

defineProps<{
    period: {
        id: number
        name: string
        is_active: boolean
    } | null
    aspects: {
        id: number
        name: string
        weight: number
        indicators: {
            id: number
            code: string
            name: string
            description: string | null
            score_type: string
            storage_link: string | null
            template_text: string | null
            template_file_url: string | null
            template_pdf_url: string | null
            submissions: {
                id: number
                status: string
                marked_at: string | null
                latest_validation: {
                    status: string
                    note: string | null
                    score: number | null
                } | null
            }[]
        }[]
    }[]
}>()

const showModal    = ref(false)
const activeInd    = ref<any>(null)
const activeSub    = ref<any>(null)

const openDetail = (ind: any) => {
    activeInd.value = ind
    activeSub.value = ind.submissions?.[0] ?? null
    showModal.value = true
}

const toggle = (indicatorId: number) => {
    router.patch(route('bukti.toggle', indicatorId), {}, {
        onSuccess: () => {
            showModal.value = false
        },
    })
}

const getSubmission = (ind: any) => ind.submissions?.[0] ?? null

const isApproved = (ind: any) => getSubmission(ind)?.latest_validation?.status === 'approved'
const isUploaded = (ind: any) => getSubmission(ind)?.status === 'uploaded'
const isRejected = (ind: any) => getSubmission(ind)?.latest_validation?.status === 'rejected'

const statusColor = (ind: any) => {
    if (isApproved(ind)) return 'bg-green-100 text-green-700'
    if (isRejected(ind)) return 'bg-red-100 text-red-600'
    if (isUploaded(ind)) return 'bg-blue-100 text-blue-600'
    return 'bg-gray-100 text-gray-400'
}

const statusLabel = (ind: any) => {
    if (isApproved(ind)) return 'Approved'
    if (isRejected(ind)) return 'Rejected'
    if (isUploaded(ind)) return 'Uploaded'
    return 'Kosong'
}
</script>

<template>
    <Head title="Bukti Dukung" />

    <div class="flex flex-col gap-6 p-6">

        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Bukti Dukung</h1>
                <p class="text-sm text-gray-400 mt-0.5">Klik indikator untuk melengkapi bukti dukung</p>
            </div>
            <span v-if="period"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-green-200 bg-green-50 text-xs text-green-700">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                {{ period.name }}
            </span>
            <span v-else
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-xs text-red-600">
                Tidak ada periode aktif
            </span>
        </div>

        <!-- No period warning -->
        <div v-if="!period" class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
            Belum ada periode penilaian yang aktif.
        </div>

        <!-- Aspek & Indikator -->
        <div v-for="aspect in aspects" :key="aspect.id"
            class="bg-white border border-gray-100 rounded-2xl overflow-hidden">

            <!-- Aspek Header -->
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900">{{ aspect.name }}</p>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">Bobot {{ (aspect.weight * 100).toFixed(0) }}%</span>
                    <span class="text-xs text-gray-400">
                        {{ aspect.indicators.filter(i => isApproved(i)).length }} / {{ aspect.indicators.length }} selesai
                    </span>
                </div>
            </div>

            <!-- Indikator List -->
            <div class="divide-y divide-gray-50">
                <div v-for="ind in aspect.indicators" :key="ind.id"
                    class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition cursor-pointer"
                    @click="openDetail(ind)">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Status icon -->
                        <div :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0',
                            isApproved(ind) ? 'bg-green-100' : isRejected(ind) ? 'bg-red-100' : isUploaded(ind) ? 'bg-blue-100' : 'bg-gray-100'
                        ]">
                            <svg v-if="isApproved(ind)" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg v-else-if="isRejected(ind)" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <svg v-else-if="isUploaded(ind)" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg v-else class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <!-- Info -->
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 mb-0.5">{{ ind.code }}</p>
                            <p class="text-sm text-gray-800 truncate">{{ ind.name }}</p>
                            <!-- Catatan reject -->
                            <p v-if="isRejected(ind)" class="text-xs text-red-500 mt-0.5">
                                ✕ {{ getSubmission(ind)?.latest_validation?.note ?? 'Ditolak' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        <span :class="statusColor(ind)"
                            class="text-xs px-2.5 py-1 rounded-full font-medium">
                            {{ statusLabel(ind) }}
                        </span>
                        <svg v-if="!isApproved(ind)" class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="aspects.length === 0"
            class="bg-white border border-gray-100 rounded-2xl p-10 text-center">
            <p class="text-sm text-gray-400">Belum ada aspek dan indikator.</p>
        </div>

    </div>

    <!-- Modal Detail Indikator -->
    <Modal :show="showModal" :title="activeInd?.code" @close="showModal = false" max-width="max-w-2xl">
        <div v-if="activeInd" class="flex flex-col gap-4">

            <!-- Nama lengkap -->
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 mb-1">Indikator</p>
                <p class="text-sm text-gray-800 font-medium">{{ activeInd.name }}</p>
            </div>

            <!-- Penjelasan bukti dukung -->
            <div v-if="activeInd.description" class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <p class="text-xs font-semibold text-blue-700 mb-2">📄 Dokumen yang Diperlukan</p>
                <p class="text-sm text-blue-800 whitespace-pre-line">{{ activeInd.description }}</p>
            </div>

            <!-- Panduan / template teks -->
            <div v-if="activeInd.template_text" class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                <p class="text-xs font-medium text-amber-700 mb-1">📋 Panduan Pengisian</p>
                <p class="text-sm text-amber-800">{{ activeInd.template_text }}</p>
            </div>

            <!-- Download template file -->
            <a v-if="activeInd.template_file_url" :href="activeInd.template_file_url" target="_blank"
                class="flex items-center gap-3 p-4 bg-indigo-50 border border-indigo-100 rounded-xl hover:bg-indigo-100 transition">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-indigo-700">Download Template</p>
                    <p class="text-xs text-indigo-500">Klik untuk download file template</p>
                </div>
            </a>

            <!-- Link Storage -->
            <div v-if="activeInd.storage_link">
                <p class="text-xs text-gray-500 mb-2">Link penyimpanan dokumen</p>
                <a :href="activeInd.storage_link" target="_blank"
                    class="flex items-center gap-3 p-4 bg-white border border-gray-200 rounded-xl hover:border-orange-300 hover:bg-orange-50 transition">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Buka Storage</p>
                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ activeInd.storage_link }}</p>
                    </div>
                </a>
            </div>

            <!-- Status saat ini -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Status saat ini</p>
                    <span :class="statusColor(activeInd)"
                        class="text-xs px-2.5 py-1 rounded-full font-medium">
                        {{ statusLabel(activeInd) }}
                    </span>
                </div>
                <div v-if="activeSub?.latest_validation?.status === 'rejected'" class="text-right">
                    <p class="text-xs text-gray-400 mb-0.5">Catatan validator</p>
                    <p class="text-xs text-red-500">{{ activeSub?.latest_validation?.note ?? '—' }}</p>
                </div>
            </div>

            <!-- Tombol aksi -->
            <div class="flex justify-end gap-2 pt-2">
                <button @click="showModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Tutup
                </button>
                <span v-if="isApproved(activeInd)"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                    🔒 Sudah Approved
                </span>
                <button v-else-if="period" @click="toggle(activeInd.id)"
                    :class="isUploaded(activeInd)
                        ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        : 'bg-orange-500 text-white hover:bg-orange-600'"
                    class="px-4 py-2 text-sm rounded-lg transition font-medium">
                    {{ isUploaded(activeInd) ? 'Tandai Kosong' : '✓ Tandai Sudah Diupload' }}
                </button>
            </div>

        </div>
    </Modal>

</template>