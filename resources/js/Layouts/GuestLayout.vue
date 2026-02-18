<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Nav from '@/Components/Nav.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
    class="flex min-h-screen flex-col items-center bg-[url('@/assets/images/bg-header.jpg')] bg-cover bg-fixed bg-center pt-6 sm:justify-center sm:pt-0"
    >
    <Nav />

        <div
        class="md:mt-6 w-full overflow-hidden bg-white px-8 py-6 shadow-md md:max-w-lg sm:max-w-md sm:rounded-lg"
        >
            <div class="flex justify-center">
                <Link href="/">
                    <ApplicationLogo class=""/>
                </Link>
            </div>
            <slot />
        </div>
    </div>
</template>
