<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Validasi', href: route('validasi.index') },
        ],
    },
})

defineProps<{
    period: { id: number; name: string } | null
    aspects: {
        id: number
        name: string
        weight: number
        indicators: {
            id: number
            code: string
            name: string
            score_type: string
            submissions: {
                id: number
                status: string
                marked_at: string | null
                operator: { name: string } | null
                latest_validation: {
                    status: string
                    note: string | null
                    score: number | null
                } | null
            }[]
        }[]
    }[]
}>()

const showModal     = ref(false)
const activeSubmission = ref<any>(null)
const activeScoreType  = ref<string>('likert')

const form = useForm({
    status: 'approved',
    note:   '',
    score:  null as number | null,
})

const openReview = (submission: any, scoreType: string) => {
    activeSubmission.value = submission
    activeScoreType.value  = scoreType
    form.status = submission.latest_validation?.status ?? 'approved'
    form.note   = submission.latest_validation?.note ?? ''
    form.score  = submission.latest_validation?.score ?? null
    showModal.value = true
}

const submitReview = () => {
    if (!activeSubmission.value) return
    form.post(route('validasi.review', activeSubmission.value.id), {
        onSuccess: () => {
            showModal.value = false
            form.reset()
            activeSubmission.value = null
        },
    })
}

const getSubmission = (indicator: any) => indicator.submissions?.[0] ?? null

const validasiClass = (status: string | null) => {
    const map: Record<string, string> = {
        approved: 'bg-green-50 text-green-700',
        rejected: 'bg-red-50 text-red-600',
    }
    return map[status ?? ''] ?? ''
}
</script>

<template>
    <Head title="Validasi" />

    <div class="flex flex-col gap-6 p-6">

        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Validasi Bukti Dukung</h1>
                <p class="text-sm text-gray-400 mt-0.5">Review dan beri nilai bukti dukung yang sudah diupload</p>
            </div>
            <span v-if="period"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-green-200 bg-green-50 text-xs text-green-700">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                {{ period.name }}
            </span>
        </div>

        <!-- Aspek & Indikator -->
        <div v-for="aspect in aspects" :key="aspect.id"
            class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-800">{{ aspect.name }}</p>
                <span class="text-xs text-gray-400">Bobot {{ (aspect.weight * 100).toFixed(0) }}%</span>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-normal px-5 py-3 w-20">Kode</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3">Indikator</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3 w-24">Status</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3 w-28">Validasi</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3 w-16">Nilai</th>
                        <th class="text-right text-xs text-gray-400 font-normal py-3 px-5 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ind in aspect.indicators" :key="ind.id"
                        class="border-b border-gray-50 last:border-0">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ ind.code }}</td>
                        <td class="py-3 pr-4 text-sm text-gray-800">{{ ind.name }}</td>
                        <td class="py-3">
                            <span v-if="getSubmission(ind)?.status === 'uploaded'"
                                class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-medium">
                                Uploaded
                            </span>
                            <span v-else class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-400">
                                Kosong
                            </span>
                        </td>
                        <td class="py-3">
                            <span v-if="getSubmission(ind)?.latest_validation"
                                :class="validasiClass(getSubmission(ind)?.latest_validation?.status ?? '')"
                                class="text-xs px-2 py-0.5 rounded-full font-medium">
                                {{ getSubmission(ind)?.latest_validation?.status === 'approved' ? 'Approved' : 'Rejected' }}
                            </span>
                            <span v-else class="text-xs text-gray-300">—</span>
                        </td>
                        <td class="py-3 text-xs text-gray-500">
                            {{ getSubmission(ind)?.latest_validation?.score ?? '—' }}
                        </td>
                        <td class="py-3 px-5 text-right">
                            <button
                                v-if="getSubmission(ind)?.status === 'uploaded'"
                                @click="openReview(getSubmission(ind), ind.score_type)"
                                class="text-xs px-3 py-1.5 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition font-medium">
                                Review
                            </button>
                            <span v-else class="text-xs text-gray-300">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="aspects.length === 0"
            class="bg-white border border-gray-100 rounded-2xl p-10 text-center">
            <p class="text-sm text-gray-400">Belum ada aspek dan indikator.</p>
        </div>

    </div>

    <!-- Modal Review -->
    <Modal :show="showModal" title="Form Validasi" @close="showModal = false">
        <form @submit.prevent="submitReview" class="flex flex-col gap-4">

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Keputusan</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                        @click="form.status = 'approved'"
                        :class="form.status === 'approved'
                            ? 'bg-green-500 text-white border-green-500'
                            : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                        class="py-2.5 rounded-xl border text-sm font-medium transition">
                        ✓ Approve
                    </button>
                    <button type="button"
                        @click="form.status = 'rejected'"
                        :class="form.status === 'rejected'
                            ? 'bg-red-500 text-white border-red-500'
                            : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                        class="py-2.5 rounded-xl border text-sm font-medium transition">
                        ✕ Reject
                    </button>
                </div>
            </div>

            <!-- Nilai (hanya saat approve) -->
            <div v-if="form.status === 'approved'">
                <label class="block text-xs text-gray-500 mb-1.5">Nilai Penilaian Mandiri</label>
                <div class="grid grid-cols-5 gap-2">
                    <button v-for="n in [1,2,3,4,5]" :key="n"
                        type="button"
                        @click="form.score = n"
                        :class="form.score === n
                            ? 'bg-orange-500 text-white border-orange-500'
                            : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                        class="py-2.5 rounded-xl border text-sm font-semibold transition">
                        {{ n }}
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">
                    {{ form.score === 1 ? 'Sangat Kurang' : form.score === 2 ? 'Kurang' : form.score === 3 ? 'Cukup' : form.score === 4 ? 'Baik' : form.score === 5 ? 'Sangat Baik' : 'Pilih nilai' }}
                </p>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Catatan <span class="text-gray-300">(opsional)</span></label>
                <textarea v-model="form.note" rows="3"
                    placeholder="Catatan untuk operator..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" :disabled="form.processing || (form.status === 'approved' && !form.score)"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Validasi' }}
                </button>
            </div>

        </form>
    </Modal>

</template>