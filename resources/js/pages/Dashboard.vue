<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
        ],
    },
})

const props = defineProps<{
    period: { id: number; name: string; is_active: boolean } | null
    indeksTotal: number
    totalIndicators: number
    totalUploaded: number
    totalApproved: number
    totalRejected: number
    totalPending: number
    aspectData: { nama: string; bobot: number; progres: number; nilai: number }[]
    progressTable: {
        aspek: string
        bobotAspek: number
        nilaiAspek: number
        indicators: {
            kode: string
            nama: string
            status: string
            validasi: string | null
            score: number | null
            operator: string | null
            storage_link: string | null
        }[]
    }[]
    userContributions: { name: string; total: number }[]
}>()

const badgeClass = (status: string) => {
    const map: Record<string, string> = {
        'Approved': 'bg-green-50 text-green-700',
        'Rejected': 'bg-red-50 text-red-600',
        'Menunggu': 'bg-yellow-50 text-yellow-700',
        'Kosong':   'bg-gray-100 text-gray-400',
    }
    return map[status] ?? 'bg-gray-100 text-gray-400'
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-6">

        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ period ? period.name : 'Belum ada periode aktif' }} · Akuntabilitas Pelayanan Publik
                </p>
            </div>
            <span v-if="period"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-green-200 bg-green-50 text-xs text-green-700">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Periode aktif
            </span>
            <span v-else
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-xs text-red-600">
                Tidak ada periode aktif
            </span>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl p-5 bg-orange-500 text-white shadow-md">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center mb-4">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-2xl font-bold mb-1">{{ indeksTotal }}</p>
                <p class="text-sm text-white/80">Indeks Pelayanan Publik (IPP)</p>
            </div>
            <div class="rounded-2xl p-5 bg-gray-800 text-white shadow-md">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center mb-4">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-2xl font-bold mb-1">{{ totalUploaded }}</p>
                <p class="text-sm text-white/80">Dokumen Terupload</p>
            </div>
            <div class="rounded-2xl p-5 bg-gray-800 text-white shadow-md">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center mb-4">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-2xl font-bold mb-1">{{ totalApproved }}</p>
                <p class="text-sm text-white/80">Dokumen Approved</p>
            </div>
            <div class="rounded-2xl p-5 bg-gray-800 text-white shadow-md">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center mb-4">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-2xl font-bold mb-1">{{ totalPending }}</p>
                <p class="text-sm text-white/80">Dokumen perlu Review</p>
            </div>
        </div>

<!-- Progres per Aspek -->
<div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-md">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm font-semibold text-gray-900">Progres per aspek</p>
        <p class="text-xl font-bold text-gray-900">
            {{ indeksTotal }} <span class="text-sm text-gray-400 font-normal">/ 5</span>
        </p>
    </div>

    <div class="flex items-end justify-around gap-2 h-40">
        <div v-for="aspek in aspectData" :key="aspek.nama"
            class="flex flex-col items-center gap-2 flex-1">
            <!-- Bar container -->
            <div class="relative w-full flex items-end justify-center gap-1 h-32">
                <!-- Background bar (100%) -->
                <div class="w-3 rounded-full bg-gray-200 h-full"></div>
                <!-- Progress bar (terpenuhi) -->
                <div
                    class="w-3 rounded-full bg-orange-500 transition-all duration-700 ease-out absolute bottom-0 left-1/2 -translate-x-1/2"
                    :style="{ height: aspek.progres + '%' }"
                ></div>
            </div>
            <!-- Label -->
            <p class="text-xs font-semibold text-gray-700 text-center leading-tight" style="font-size:10px">
                Aspek {{ aspectData.indexOf(aspek) + 1 }}
            </p>
            <p class="text-xs text-gray-400 text-center leading-tight" style="font-size:10px">
                {{ (aspek.bobot * 100).toFixed(0) }}%
            </p>
        </div>
    </div>

    <!-- Legend -->
    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-50">
        <div class="flex items-center gap-1.5">
            <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
            <span class="text-xs text-gray-400">Terpenuhi</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div>
            <span class="text-xs text-gray-400">Total</span>
        </div>
    </div>
</div>

        <!-- Progress Keseluruhan -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-md">
            <p class="text-sm font-semibold text-gray-900 mb-4">Progress Keseluruhan</p>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-20">Kode</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3">Indikator</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-24">Status</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-16">Nilai</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-32">Operator</th>
                        <th class="text-right text-xs text-gray-400 font-normal pb-3 w-20">Lihat</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="aspek in progressTable" :key="aspek.aspek">
                        <!-- Baris aspek header -->
                        <tr class="bg-gray-50">
                            <td colspan="6" class="px-3 py-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-700">{{ aspek.aspek }}</span>
                                    <span class="text-xs font-bold text-gray-800">
                                        {{ Number.isInteger(aspek.nilaiAspek * 10) ? aspek.nilaiAspek.toFixed(0) : aspek.nilaiAspek.toFixed(1) }}
                                        / {{ (aspek.bobotAspek * 100).toFixed(0) }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <!-- Baris indikator -->
                        <tr v-for="ind in aspek.indicators" :key="ind.kode"
                            class="border-b border-gray-50 last:border-0">
                            <td class="py-3 text-xs text-gray-400">{{ ind.kode }}</td>
                            <td class="py-3 text-xs text-gray-800 pr-4">{{ ind.nama }}</td>
                            <td class="py-3">
                                <div v-if="ind.validasi === 'approved'"
                                    class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div v-else-if="ind.validasi === 'rejected'"
                                    class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div v-else-if="ind.status === 'uploaded'"
                                    class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div v-else
                                    class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </div>
                            </td>
                            <td class="py-3 text-xs text-gray-700 font-medium">
                                {{ ind.score !== null ? ind.score : '—' }}
                            </td>
                            <td class="py-3 text-xs text-gray-500">{{ ind.operator ?? '—' }}</td>
                            <td class="py-3 text-right">
                                <a v-if="ind.storage_link" :href="ind.storage_link" target="_blank"
                                    class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition font-medium">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Buka
                                </a>
                                <span v-else class="text-xs text-gray-300">—</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Kontribusi per User -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="user in userContributions" :key="user.name"
                class="bg-white border border-gray-100 rounded-2xl p-5 shadow-md">
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mb-3">
                    <span class="text-sm font-bold text-orange-500">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ user.total }}</p>
                <p class="text-xs text-gray-400">Dokumen diupload</p>
                <p class="text-sm font-semibold text-gray-700 mt-2">{{ user.name }}</p>
            </div>
        </div>

    </div>
</template>