<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="w-full max-w-sm">

            <!-- Logo & Brand -->
            <div class="flex flex-col items-center mb-8">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mb-4">
                    <svg width="28" height="28" viewBox="0 0 20 20" fill="none">
                        <rect x="2" y="2" width="7" height="7" rx="1.5" fill="#7F77DD"/>
                        <rect x="11" y="2" width="7" height="7" rx="1.5" fill="#AFA9EC"/>
                        <rect x="2" y="11" width="7" height="7" rx="1.5" fill="#AFA9EC"/>
                        <rect x="11" y="11" width="7" height="7" rx="1.5" fill="#534AB7"/>
                    </svg>
                </div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">PEKPPP</h1>
                <p class="text-sm text-gray-400 mt-1">Sistem Bukti Dukung</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-8 py-8">
                <h2 class="text-base font-medium text-gray-800 mb-6">Masuk ke akun Anda</h2>

                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Email -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1.5">Email</label>
                        <input
                            v-model="form.email"
                            type="text"
                            placeholder="Username atau email"
                            autofocus
                            autocomplete="email"
                            class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1.5">Password</label>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-10"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            id="remember"
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label for="remember" class="text-sm text-gray-500">Ingat saya</label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2"
                    >
                        {{ form.processing ? 'Memproses...' : 'Masuk' }}
                    </button>

                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                PEKPPP · Akuntabilitas Pelayanan Publik
            </p>
        </div>
    </div>
</template>