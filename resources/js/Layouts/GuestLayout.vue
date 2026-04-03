<script setup>
import Toast from '@/Components/Toastify/Toast.vue';
</script>

<template>
    <div
    class="flex min-h-screen flex-col items-center bg-[url('@/assets/images/bg-header.jpg')] bg-cover bg-fixed bg-center pt-6 sm:justify-center sm:pt-0"
    >
    <Toast />
        <div
        class="md:mt-6 w-full overflow-hidden bg-white px-8 py-6 shadow-md md:max-w-xl sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
