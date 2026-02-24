<script setup>
import Sidebar from '@/Components/Dashboard/Sidebar.vue';
import { usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { computed } from 'vue';

const user = computed(() => usePage().props.auth.user);
const role = computed(() => user.value?.role);

</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <Sidebar />
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white/80 backdrop-blur-xl shadow-lg border-b border-orange-200/50 p-3 z-20">
                <div class="flex items-center justify-between">
                    <div class="">
                        <h2 class="text-xl font-bold text-slate-800 capitalize">Tableau de bord</h2>
                        <p class="text-sm text-slate-600">Bienvenue dans votre espace {{ role === 'admin' ? 'Administrateur' : role === 'dev' ? 'Développeur' : 'Client' }}</p>
                    </div>
                    <!-- Actions utilisateur -->
                    <div class="flex items-center space-x-4">
                        <!-- Profil utilisateur -->
                        <div class="flex items-center space-x-3 group cursor-pointer">
                        <!-- Avatar -->
                            <div class="w-10 h-10 bg-gradient-to-br from-[#c10e46] to-[#1e002f] rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                                <img v-if="user?.image" :src="`/storage/${user.image}`" alt="Avatar" class="w-full h-full rounded-xl object-cover">
                                <span v-else class="text-white font-semibold text-sm">A</span>
                            </div>
                        <!-- Indicateur de menu -->
                        <ChevronDown class="w-4 h-4 text-slate-500 group-hover:text-pink-600 transition-colors" />
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
