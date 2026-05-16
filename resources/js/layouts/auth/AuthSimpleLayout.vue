<script setup>
defineProps({
    title: String,
    description: String,
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <slot />
    </div>
</template>