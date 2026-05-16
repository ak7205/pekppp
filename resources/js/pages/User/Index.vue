<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Manajemen User', href: route('users.index') },
        ],
    },
})

defineProps<{
    users: {
        id: number
        name: string
        email: string
        is_active: boolean
        roles: { name: string }[]
    }[]
    roles: { id: number; name: string }[]
}>()

const showAddModal = ref(false)
const showEditModal = ref(false)
const editingUser = ref<any>(null)

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'operator',
})

const editForm = useForm({
    role: '',
})

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            form.reset()
            showAddModal.value = false
        },
    })
}

const openEdit = (user: any) => {
    editingUser.value = user
    editForm.role = user.roles[0]?.name ?? 'operator'
    showEditModal.value = true
}

const submitEdit = () => {
    if (!editingUser.value) return
    editForm.patch(route('users.role', editingUser.value.id), {
        onSuccess: () => {
            showEditModal.value = false
            editingUser.value = null
        },
    })
}

const toggleActive = (userId: number) => {
    router.patch(route('users.toggle', userId))
}

const destroy = (userId: number) => {
    if (confirm('Yakin ingin menghapus user ini?')) {
        router.delete(route('users.destroy', userId))
    }
}

const roleBadgeClass = (role: string) => {
    const map: Record<string, string> = {
        admin:     'bg-purple-50 text-purple-700',
        operator:  'bg-green-50 text-green-700',
        validator: 'bg-amber-50 text-amber-700',
        viewer:    'bg-gray-100 text-gray-500',
    }
    return map[role] ?? 'bg-gray-100 text-gray-500'
}
</script>

<template>
    <Head title="Manajemen User" />

    <div class="flex flex-col gap-6 p-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Manajemen User</h1>
                <p class="text-sm text-gray-400 mt-0.5">Kelola akun dan role pengguna aplikasi</p>
            </div>
            <button @click="showAddModal = true"
                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-xl transition">
                + Tambah User
            </button>
        </div>

        <!-- Tabel User -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-normal pb-3">Nama</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3">Email</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-28">Role</th>
                        <th class="text-left text-xs text-gray-400 font-normal pb-3 w-24">Status</th>
                        <th class="text-right text-xs text-gray-400 font-normal pb-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-b border-gray-50 last:border-0">
                        <td class="py-3 text-sm text-gray-800 font-medium">{{ user.name }}</td>
                        <td class="py-3 text-sm text-gray-500">{{ user.email }}</td>
                        <td class="py-3">
                            <span :class="roleBadgeClass(user.roles[0]?.name ?? '')"
                                class="text-xs px-2 py-0.5 rounded-full font-medium">
                                {{ user.roles[0]?.name ?? '—' }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span :class="user.is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'"
                                class="text-xs px-2 py-0.5 rounded-full font-medium">
                                {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit(user)"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition font-medium">
                                    Edit
                                </button>
                                <button @click="toggleActive(user.id)"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition font-medium">
                                    {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                                <button @click="destroy(user.id)"
                                    class="text-xs px-3 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition font-medium">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal Tambah User -->
    <Modal :show="showAddModal" title="Tambah User Baru" @close="showAddModal = false">
        <form @submit.prevent="submit" class="flex flex-col gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Nama</label>
                <input v-model="form.name" type="text" placeholder="Nama lengkap"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Email</label>
                <input v-model="form.email" type="text" placeholder="email@instansi.go.id"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Password</label>
                <input v-model="form.password" type="password" placeholder="Minimal 8 karakter"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Role</label>
                <select v-model="form.role"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="admin">admin</option>
                    <option value="operator">operator</option>
                    <option value="validator">validator</option>
                    <option value="viewer">viewer</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showAddModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ form.processing ? 'Menyimpan...' : 'Tambah User' }}
                </button>
            </div>
        </form>
    </Modal>

    <!-- Modal Edit Role -->
    <Modal :show="showEditModal" title="Edit Role User" @close="showEditModal = false">
        <div class="mb-4 p-3 bg-gray-50 rounded-xl">
            <p class="text-sm font-medium text-gray-900">{{ editingUser?.name }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ editingUser?.email }}</p>
        </div>
        <form @submit.prevent="submitEdit" class="flex flex-col gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Role</label>
                <select v-model="editForm.role"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="admin">admin</option>
                    <option value="operator">operator</option>
                    <option value="validator">validator</option>
                    <option value="viewer">viewer</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showEditModal = false"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" :disabled="editForm.processing"
                    class="px-4 py-2 text-sm rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                    {{ editForm.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </div>
        </form>
    </Modal>

</template>