<script setup lang="ts">
defineProps<{
    show: boolean
    title: string
    maxWidth?: string
}>()

const emit = defineEmits(['close'])
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="emit('close')"></div>
                <!-- Modal -->
                <div :class="['relative bg-white rounded-2xl shadow-xl w-full transition duration-200', maxWidth ?? 'max-w-lg']">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">{{ title }}</h2>
                        <button @click="emit('close')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Content -->
                    <div class="px-6 py-5">
                        <slot />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>