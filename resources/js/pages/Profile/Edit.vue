<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

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
const user = computed(() => page.props.auth.user as any)

const form = useForm({
    name: user.value.name,
    email: user.value.email,
})

const avatarForm = useForm({
    avatar: null as File | null,
})

const previewUrl = ref<string | null>(
    user.value.avatar ? user.value.avatar : null
)

const fileInput = ref<HTMLInputElement | null>(null)

const compressImage = (file: File, size = 400, quality = 0.8): Promise<File> => {
    return new Promise((resolve) => {
        const reader = new FileReader()
        reader.onload = (e) => {
            const img = new Image()
            img.onload = () => {
                const canvas = document.createElement('canvas')
                canvas.width = size
                canvas.height = size

                const ctx = canvas.getContext('2d')!

                // Crop ke square dari tengah
                const minSide = Math.min(img.width, img.height)
                const sx = (img.width - minSide) / 2
                const sy = (img.height - minSide) / 2

                ctx.drawImage(img, sx, sy, minSide, minSide, 0, 0, size, size)

                canvas.toBlob((blob) => {
                    if (!blob) return resolve(file)
                    const compressed = new File([blob], 'avatar.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    })
                    resolve(compressed)
                }, 'image/jpeg', quality)
            }
            img.src = e.target?.result as string
        }
        reader.readAsDataURL(file)
    })
}

const onFileChange = async (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    
    // Preview langsung
    previewUrl.value = URL.createObjectURL(file)
    
    // Compress sebelum simpan ke form
    const compressed = await compressImage(file)
    avatarForm.avatar = compressed
}

const submitAvatar = () => {
    avatarForm.post(route('avatar.update'), {
        forceFormData: true,
        onSuccess: () => {
            // refresh page props
        },
    })
}

const submit = () => {
    form.patch(route('profile.update'))
}

const initials = computed(() => {
    return user.value.name
        ?.split(' ')
        .map((n: string) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase() ?? 'U'
}
)
</script>

<template>
    <Head title="Profil" />

    <div class="flex flex-col gap-6 p-6 max-w-lg">

        <div>
            <h1 class="text-lg font-semibold text-gray-900">Profil</h1>
            <p class="text-sm text-gray-400 mt-0.5">Perbarui informasi dan foto profil akun Anda</p>
        </div>

        <!-- Avatar -->
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-sm font-medium text-gray-900 mb-4">Foto Profil</p>
            <div class="flex items-center gap-5">
                <!-- Preview -->
                <div class="w-20 h-20 rounded-full overflow-hidden bg-orange-100 flex items-center justify-center flex-shrink-0">
                    <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover" alt="Avatar" />
                    <span v-else class="text-xl font-bold text-orange-500">{{ initials }}</span>
                </div>
                <!-- Upload -->
                <div class="flex flex-col gap-2">
                    <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
                    <button @click="fileInput?.click()"
                        class="text-sm px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 transition font-medium">
                        Pilih Foto
                    </button>
                    <p class="text-xs text-gray-400">JPG, PNG, WEBP. Maks 2MB.</p>
                    <button v-if="avatarForm.avatar" @click="submitAvatar" :disabled="avatarForm.processing"
                        class="text-sm px-4 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-50 transition font-medium">
                        {{ avatarForm.processing ? 'Menyimpan...' : 'Simpan Foto' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Profil -->
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <form @submit.prevent="submit" class="flex flex-col gap-4">

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Nama</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Nama lengkap"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                    />
                    <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Email</label>
                    <input
                        v-model="form.email"
                        type="text"
                        placeholder="email@instansi.go.id"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                    />
                    <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}
                    </button>
                </div>

            </form>
        </div>

    </div>
</template>