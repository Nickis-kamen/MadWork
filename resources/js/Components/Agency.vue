<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'

const props = defineProps({
    agencies: Array,
})

</script>

<template>
    <!-- Agencies Section -->
    <section id="agences" class="relative py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Éléments décoratifs -->
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-[#1e002f]/5 to-transparent"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#c10e46]/5 rounded-full blur-3xl"></div>
        <div class="absolute top-20 left-20 w-72 h-72 bg-[#1e002f]/5 rounded-full blur-3xl"></div>

        <div class="relative container mx-auto px-4">
            <!-- En-tête de section -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-[#1e002f]/10 px-4 py-2 rounded-full mb-4 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-semibold tracking-wider text-[#1e002f]">AGENCES PARTENAIRES</span>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4">
                    Découvrez nos <span class="text-[#c10e46]">agences expertes</span>
                </h2>

                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Des partenaires de confiance sélectionnés pour leur expertise et leur savoir-faire unique
                </p>
            </div>

            <!-- Grille des agences -->
            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                <div v-for="agency in agencies" :key="agency.id"
                    class="group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    <Link :href="`/agencies/${agency.slug}`">

                        <!-- En-tête avec image de fond (agency.image) -->
                        <div class="relative h-48 bg-cover bg-center overflow-hidden"
                            :style="{ backgroundImage: `url('/storage/${agency.image || '/images/default-agency-bg.jpg'}')` }">

                            <!-- Overlay pour améliorer la lisibilité du texte si nécessaire -->
                            <div class="absolute inset-0 bg-black/30"></div>

                            <!-- Informations dans l'en-tête -->
                            <div class="absolute bottom-4 left-6 right-6 z-10">
                                <h3 class="text-xl font-bold text-white mb-1 drop-shadow-lg group-hover:text-pink-200 transition-colors">
                                    {{ agency.name || 'Nom non spécifié' }}
                                </h3>
                                <p class="text-sm text-white/90 drop-shadow-md flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    {{ agency.email || 'Email non spécifié' }}
                                </p>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-xs bg-red-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{ agency.specialite || 'Agence' }}
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3 leading-relaxed">
                                {{ agency.description || 'Aucune description disponible pour le moment.' }}
                            </p>

                            <!-- Informations complémentaires -->
                            <div class="space-y-2 mb-4">
                                <div v-if="agency.address" class="flex items-start gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ agency.address }}</span>
                                </div>
                                <div v-if="agency.phone" class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ agency.phone }}</span>
                                </div>
                            </div>

                            <!-- Bouton -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Membre depuis {{ agency.created_at ? new Date(agency.created_at).getFullYear() : '2024' }}</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mt-16">
                <div class="inline-flex items-center gap-4 bg-white p-1 rounded-full shadow-lg">
                    <Link href="/agences"
                          class="inline-flex items-center bg-gradient-to-r from-[#1e002f] to-[#2c0047] hover:from-[#2c0047] hover:to-[#3a0057] text-white px-8 py-4 rounded-full font-semibold transition-all duration-300 transform">
                        Toutes les agences
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
</style>
