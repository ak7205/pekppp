<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Periode Penilaian', href: route('periode.index') },
        ],
    },
})

defineProps<{
    periods: {
        id: number
        name: string
        start_date: string
        end_date: string
        is_active: boolean
    }[]
}>()

const showModal = ref(false)

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
})

const submit = () => {
    form.post(route('periode.store'), {
        onSuccess: () => {
            form.reset()
            showModal.value = false
        },
    })
}

const activate = (id: number) => {
    if (confirm('Yakin ingin mengaktifkan periode ini? Periode aktif sebelumnya akan dinonaktifkan.')) {
        router.patch(route('periode.activate', id))
    }
}

const destroy = (id: number) => {
    if (confirm('Yakin ingin menghapus periode ini?')) {
        router.delete(route('periode.destroy', id))
    }
}

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    })
}
</script>

<template>
    <Head title="Periode Penilaian" />

    <div class="flex flex-col gap-6 p-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Periode Penilaian</h1>
                <p class="text-sm text-gray-400 mt-0.5">Kelola periode penilaian PEKPPP</p>
            </div>
            <button @click="showModal = true"
                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-xl transition">
                + Tambah Periode
            </button>
        </div>

        <!-- Tabel Periode -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <div v-if="periods.length === 0" class="text-center py-10 text-sm text-gray-400">
                Belum ada periode. Klik tombol di atas untuk menambahkan.
            </div>
            <table v-else class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-normal pb-3">Nama Periode</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3">Tanggal Mulai</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3">Tanggal Selesai</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-24">Status</th>
                        <th class="text-right text-xs text-gray-400 font-normal pb-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="period in periods" :key="period.id"
                        class="border-b border-gray-50 last:border-0">
                        <td class="py-3 text-sm text-gray-800 font-medium">{{ period.name }}</td>
                        <td class="py-3 text-sm text-gray-500">{{ formatDate(period.start_date) }}</td>
                        <td class="py-3 text-sm text-gray-500">{{ formatDate(period.end_date) }}</td>
                        <td class="py-3">
                            <span v-if="period.is_active"
                                class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                            </span>
                            <span v-else class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-400">
                                Nonaktif
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button v-if="!period.is_active" @click="activate(period.id)"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition font-medium">
                                    Aktifkan
                                </button>
                                <button v-if="!period.is_active" @click="destroy(period.id)"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition font-medium">
                                    Hapus
                                </button>
                                <span v-if="period.is_active" class="text-xs text-gray-300">Periode aktif</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal Tambah Periode -->
    <Modal :show="showModal" title="Tambah Periode Baru" @close="showModal = false">
        <form @submit.prevent="submit" class="flex flex-col gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Nama Periode</label>
                <input v-model="form.name" type="text" placeholder="Contoh: Penilaian 2024"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Tanggal Mulai</label>
                    <input v-model="form.start_date" type="date"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    <p v-if="form.errors.start_date" class="text-xs text-red-500 mt-1">{{ form.errors.start_date }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Tanggal Selesai</label>
                    <input v-model="form.end_date" type="date"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    <p v-if="form.errors.end_date" class="text-xs text-red-500 mt-1">{{ form.errors.end_date }}</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ form.processing ? 'Menyimpan...' : 'Tambah Periode' }}
                </button>
            </div>
        </form>
    </Modal>

</template>