<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Profil', href: route('profile.edit') },
        ],
    },
})

defineProps<{
    mustVerifyEmail: boolean
    status?: string
}>()

const page = usePage()
const user = computed(() => page.props.auth.user)

const form = useForm({
    name: user.value.name,
    email: user.value.email,
})

const submit = () => {
    form.patch(route('profile.update'))
}
</script>

<template>
    <Head title="Profil" />

    <div class="flex flex-col gap-6 p-6 max-w-lg">

        <div>
            <h1 class="text-lg font-semibold text-gray-900">Profil</h1>
            <p class="text-sm text-gray-400 mt-0.5">Perbarui nama dan email akun Anda</p>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <form @submit.prevent="submit" class="flex flex-col gap-4">

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Nama</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Nama lengkap"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="email@instansi.go.id"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}
                    </button>
                </div>

            </form>
        </div>

    </div>
</template>