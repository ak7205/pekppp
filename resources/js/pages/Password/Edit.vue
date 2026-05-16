<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: route('dashboard') },
            { title: 'Ganti Password', href: route('mypassword.edit') },
        ],
    },
})

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.transform(data => ({
        ...data,
        _method: 'PUT',
    })).post(route('mypassword.update'), {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Ganti Password" />

    <div class="flex flex-col gap-6 p-6 max-w-lg">

        <div>
            <h1 class="text-lg font-semibold text-gray-900">Ganti Password</h1>
            <p class="text-sm text-gray-400 mt-0.5">Pastikan password baru minimal 8 karakter</p>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <form @submit.prevent="submit" class="flex flex-col gap-4">

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Password Saat Ini</label>
                    <input
                        v-model="form.current_password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    <p v-if="form.errors.current_password" class="text-xs text-red-500 mt-1">{{ form.errors.current_password }}</p>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Password Baru</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Konfirmasi Password Baru</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="••••••••"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Password' }}
                    </button>
                </div>

            </form>
        </div>

    </div>
</template>