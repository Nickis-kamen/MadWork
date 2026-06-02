<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';

const searchQuery = ref('');
const user = computed(() => usePage().props.auth.user)
const isAgency = computed(() => user.value?.role === 'agency')

const submitSearch = () => {
    if (searchQuery.value.trim() !== '') {
        if(user.value){
            if (isAgency.value) {
                router.get('/projects', { search: searchQuery.value });
            } else {
                router.get('/services', { search: searchQuery.value });
            }
        }else{
            router.get('/services', { search: searchQuery.value });
        }
    }
};

</script>
<template>
    <header class="h-screen bg-[url('@/assets/images/bg-header.jpg')] bg-cover bg-fixed bg-center flex items-center justify-center">
        <div class="text-center text-white px-4">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Bienvenue sur MadWork</h1>
            <p class="text-lg md:text-2xl mb-14">Votre plateforme de confiance pour des solutions digitales innovantes</p>
            <form @submit.prevent="submitSearch" class="flex items-center justify-center space-x-4">
                <input v-model="searchQuery" class="text-black rounded-xl" type="search" placeholder="Que recherchez-vous ?">
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 text-sm rounded-xl font-semibold transition-all duration-200 hover:shadow-md focus:outline-none">
                    Recherche
                </button>
            </form>
        </div>
    </header>
</template>
