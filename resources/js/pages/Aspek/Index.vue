<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Pengaturan Aspek', href: route('aspek.index') },
        ],
    },
})

defineProps<{
    aspects: {
        id: number
        name: string
        weight: number
        order: number
        indicators: {
            id: number
            code: string
            name: string
            weight: number
            score_type: string
            storage_link: string | null
            template_text: string | null
            template_file_url: string | null
            template_pdf_url: string | null
        }[]
    }[]
}>()

// showImportModal
const showImportModal = ref(false)
const formImport = useForm({ file: null as File | null })
const fileImportRef = ref<HTMLInputElement | null>(null)

const onImportFile = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) formImport.file = file
}

const submitImport = () => {
    formImport.post(route('aspek.import'), {
        forceFormData: true,
        onSuccess: () => {
            showImportModal.value = false
            formImport.reset()
        },
    })
}

// Modal states
const showAddAspekModal   = ref(false)
const showEditAspekModal  = ref(false)
const showAddIndModal     = ref(false)
const showEditIndModal    = ref(false)

const selectedAspekId    = ref<number | null>(null)
const editingAspek       = ref<any>(null)
const editingIndikator   = ref<any>(null)

// Forms
const formAspek = useForm({ name: '', weight: '', order: '' })
const formEditAspek = useForm({ name: '', weight: '', order: '' })

const submitAspek = () => {
    formAspek.post(route('aspek.store'), {
        onSuccess: () => { formAspek.reset(); showAddAspekModal.value = false },
    })
}

const openEditAspek = (aspect: any) => {
    editingAspek.value = aspect
    formEditAspek.name   = aspect.name
    formEditAspek.weight = aspect.weight
    formEditAspek.order  = aspect.order
    showEditAspekModal.value = true
}

const submitEditAspek = () => {
    if (!editingAspek.value) return
    formEditAspek.patch(route('aspek.update', editingAspek.value.id), {
        onSuccess: () => { showEditAspekModal.value = false; editingAspek.value = null },
    })
}

const destroyAspek = (id: number) => {
    if (confirm('Yakin hapus aspek ini? Semua indikator di dalamnya ikut terhapus.')) {
        router.delete(route('aspek.destroy', id))
    }
}

const openAddIndikator = (aspekId: number) => {
    selectedAspekId.value = aspekId
    formIndikator.reset()
    showAddIndModal.value = true
}

const submitIndikator = () => {
    if (!selectedAspekId.value) return
    formIndikator.post(route('aspek.indikator.store', selectedAspekId.value), {
        onSuccess: () => { formIndikator.reset(); showAddIndModal.value = false },
    })
}

const openEditIndikator = (ind: any) => {
    editingIndikator.value = ind
    formEditIndikator.code              = ind.code
    formEditIndikator.name              = ind.name
    formEditIndikator.weight            = ind.weight
    formEditIndikator.score_type        = ind.score_type
    formEditIndikator.storage_link      = ind.storage_link ?? ''
    formEditIndikator.template_text     = ind.template_text ?? ''
    formEditIndikator.template_file_url = ind.template_file_url ?? ''
    formEditIndikator.template_pdf_url  = ind.template_pdf_url ?? ''
    formEditIndikator.order             = ind.order
    formEditIndikator.description       = ind.description ?? ''
    showEditIndModal.value = true
}

const formIndikator = useForm({
    code: '', name: '', description: '', weight: '', score_type: 'likert',
    storage_link: '', template_text: '', template_file_url: '', template_pdf_url: '', order: '',
})

const formEditIndikator = useForm({
    code: '', name: '', description: '', weight: '', score_type: 'likert',
    storage_link: '', template_text: '', template_file_url: '', template_pdf_url: '', order: '',
})

const submitEditIndikator = () => {
    if (!editingIndikator.value) return
    formEditIndikator.patch(route('indikator.update', editingIndikator.value.id), {
        onSuccess: () => { showEditIndModal.value = false; editingIndikator.value = null },
    })
}

const destroyIndikator = (id: number) => {
    if (confirm('Yakin hapus indikator ini?')) {
        router.delete(route('indikator.destroy', id))
    }
}
</script>

<template>
    <Head title="Pengaturan Aspek" />

    <div class="flex flex-col gap-6 p-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Pengaturan Aspek & Indikator</h1>
                <p class="text-sm text-gray-400 mt-0.5">Kelola aspek dan indikator PEKPPP</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Export -->
                <a :href="route('aspek.export')"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
                <!-- Import -->
                <button @click="showImportModal = true"
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Excel
                </button>
                <!-- Tambah Aspek -->
                <button @click="showAddAspekModal = true"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-xl transition">
                    + Tambah Aspek
                </button>
            </div>
        </div>

        <!-- Daftar Aspek -->
        <div v-for="aspect in aspects" :key="aspect.id"
            class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ aspect.name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Bobot {{ (aspect.weight * 100).toFixed(0) }}% · Aspek {{ aspect.order }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openAddIndikator(aspect.id)"
                        class="text-xs px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition font-medium">
                        + Indikator
                    </button>
                    <button @click="openEditAspek(aspect)"
                        class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition font-medium">
                        Edit
                    </button>
                    <button @click="destroyAspek(aspect.id)"
                        class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition font-medium">
                        Hapus
                    </button>
                </div>
            </div>

            <div v-if="aspect.indicators.length === 0" class="text-center py-8 text-xs text-gray-400">
                Belum ada indikator.
            </div>
            <table v-else class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-normal px-5 py-3 w-20">Kode</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3">Nama Indikator</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3 w-20">Bobot</th>
                        <th class="text-left text-xs text-gray-400 font-normal py-3 w-32">Tipe Nilai</th>
                        <th class="text-right text-xs text-gray-400 font-normal py-3 px-5 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ind in aspect.indicators" :key="ind.id"
                        class="border-b border-gray-50 last:border-0">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ ind.code }}</td>
                        <td class="py-3 pr-4 text-xs text-gray-700">{{ ind.name }}</td>
                        <td class="py-3 text-xs text-gray-400">{{ (ind.weight * 100).toFixed(1) }}%</td>
                        <td class="py-3">
                            <span :class="ind.score_type === 'likert' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600'"
                                class="text-xs px-2 py-0.5 rounded-full">
                                {{ ind.score_type === 'likert' ? 'Likert 1-5' : 'Memenuhi/Tidak' }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEditIndikator(ind)"
                                    class="text-xs px-2 py-1 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                                    Edit
                                </button>
                                <button @click="destroyIndikator(ind.id)"
                                    class="text-xs px-2 py-1 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="aspects.length === 0" class="bg-white border border-gray-100 rounded-2xl p-10 text-center">
            <p class="text-sm text-gray-400">Belum ada aspek.</p>
        </div>

    </div>

    <!-- Modal Tambah Aspek -->
    <Modal :show="showAddAspekModal" title="Tambah Aspek Baru" @close="showAddAspekModal = false">
        <form @submit.prevent="submitAspek" class="flex flex-col gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Nama Aspek</label>
                <input v-model="formAspek.name" type="text" placeholder="Contoh: Kebijakan Pelayanan"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                <p v-if="formAspek.errors.name" class="text-xs text-red-500 mt-1">{{ formAspek.errors.name }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Bobot (desimal)</label>
                    <input v-model="formAspek.weight" type="number" step="0.01" placeholder="0.24"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Urutan</label>
                    <input v-model="formAspek.order" type="number" placeholder="1"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showAddAspekModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" :disabled="formAspek.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ formAspek.processing ? 'Menyimpan...' : 'Tambah Aspek' }}
                </button>
            </div>
        </form>
    </Modal>

    <!-- Modal Edit Aspek -->
    <Modal :show="showEditAspekModal" title="Edit Aspek" @close="showEditAspekModal = false">
        <form @submit.prevent="submitEditAspek" class="flex flex-col gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Nama Aspek</label>
                <input v-model="formEditAspek.name" type="text"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Bobot (desimal)</label>
                    <input v-model="formEditAspek.weight" type="number" step="0.01"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Urutan</label>
                    <input v-model="formEditAspek.order" type="number"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showEditAspekModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" :disabled="formEditAspek.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ formEditAspek.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </div>
        </form>
    </Modal>

    <!-- Modal Tambah Indikator -->
    <Modal :show="showAddIndModal" title="Tambah Indikator" @close="showAddIndModal = false" max-width="max-w-2xl">
        <form @submit.prevent="submitIndikator" class="flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Kode</label>
                    <input v-model="formIndikator.code" type="text" placeholder="1.a.Ak"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Bobot (desimal)</label>
                    <input v-model="formIndikator.weight" type="number" step="0.001" placeholder="0.111"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Nama Indikator</label>
                <input v-model="formIndikator.name" type="text" placeholder="Tersedia Standar Pelayanan..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
            </div>
            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1.5">Penjelasan Bukti Dukung</label>
                <textarea v-model="formIndikator.description" rows="3"
                    placeholder="Jelaskan dokumen apa saja yang harus dilampirkan sebagai bukti dukung..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Tipe Penilaian</label>
                    <select v-model="formIndikator.score_type"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="likert">Likert (1-5)</option>
                        <option value="status">Status (Memenuhi/Tidak)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Urutan</label>
                    <input v-model="formIndikator.order" type="number" placeholder="1"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Link Storage</label>
                <input v-model="formIndikator.storage_link" type="url" placeholder="https://drive.google.com/..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Catatan / Template Teks</label>
                <textarea v-model="formIndikator.template_text" rows="2" placeholder="Panduan pengisian..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showAddIndModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" :disabled="formIndikator.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ formIndikator.processing ? 'Menyimpan...' : 'Tambah Indikator' }}
                </button>
            </div>
        </form>
    </Modal>

    <!-- Modal Edit Indikator -->
    <Modal :show="showEditIndModal" title="Edit Indikator" @close="showEditIndModal = false" max-width="max-w-2xl">
        <form @submit.prevent="submitEditIndikator" class="flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Kode</label>
                    <input v-model="formEditIndikator.code" type="text"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Bobot (desimal)</label>
                    <input v-model="formEditIndikator.weight" type="number" step="0.001"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Nama Indikator</label>
                <input v-model="formEditIndikator.name" type="text"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Tipe Penilaian</label>
                    <select v-model="formEditIndikator.score_type"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="likert">Likert (1-5)</option>
                        <option value="status">Status (Memenuhi/Tidak)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Urutan</label>
                    <input v-model="formEditIndikator.order" type="number"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>
            </div>
            <div class="col-span-2">
                <label class="block text-xs text-gray-500 mb-1.5">Penjelasan Bukti Dukung</label>
                <textarea v-model="formEditIndikator.description" rows="3"
                    placeholder="Jelaskan dokumen apa saja yang harus dilampirkan..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Link Storage</label>
                <input v-model="formEditIndikator.storage_link" type="url"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Catatan / Template Teks</label>
                <textarea v-model="formEditIndikator.template_text" rows="2"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showEditIndModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" :disabled="formEditIndikator.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ formEditIndikator.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </div>
        </form>
    </Modal>

    <!-- Modal Import -->
    <Modal :show="showImportModal" title="Import Aspek & Indikator" @close="showImportModal = false">
        <div class="mb-4 p-3 bg-amber-50 rounded-xl border border-amber-100">
            <p class="text-xs text-amber-700 font-medium mb-1">Format file Excel:</p>
            <p class="text-xs text-amber-600">Kolom: nama_aspek, bobot_aspek, urutan_aspek, kode_indikator, nama_indikator, bobot_indikator, tipe_nilai, link_storage, catatan, urutan</p>
            <p class="text-xs text-red-500 mt-1">⚠ Kolom bobot harus diisi angka desimal langsung (contoh: 0.24), bukan formula Excel (contoh: =24/100).</p>
            <a :href="route('aspek.export')" class="text-xs text-indigo-600 hover:underline mt-1 inline-block">
                Download template dari data yang ada →
            </a>
        </div>
        <form @submit.prevent="submitImport" class="flex flex-col gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">File Excel (.xlsx)</label>
                <input ref="fileImportRef" type="file" accept=".xlsx,.xls" @change="onImportFile"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                <p v-if="formImport.errors.file" class="text-xs text-red-500 mt-1">{{ formImport.errors.file }}</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showImportModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" :disabled="formImport.processing || !formImport.file"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ formImport.processing ? 'Mengimport...' : 'Import' }}
                </button>
            </div>
        </form>
    </Modal>

</template>