<script setup>
import { Link } from '@inertiajs/vue3';
import { Banknote, HandCoins } from 'lucide-vue-next';
import { ref } from 'vue'

const props = defineProps({
    services: Array,
})

// Tronquer la description si trop longue
const truncateDescription = (description, maxLength = 100) => {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
}

const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};

</script>

<template>
    <!-- Services Section -->
    <section id="services" class="relative py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Éléments décoratifs -->
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-[#1e002f]/5 to-transparent"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#c10e46]/5 rounded-full blur-3xl"></div>
        <div class="absolute top-20 left-20 w-72 h-72 bg-[#1e002f]/5 rounded-full blur-3xl"></div>

        <div class="relative container mx-auto px-4">
            <!-- En-tête de section -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-[#1e002f]/10 px-4 py-2 rounded-full mb-4 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-semibold tracking-wider text-[#1e002f]">NOS PRESTATIONS</span>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4">
                    Découvrez nos <span class="text-[#c10e46]">services exclusifs</span>
                </h2>

                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Des prestations de qualité adaptées à vos besoins, réalisées par des experts passionnés
                </p>
            </div>
            <!-- Grille des services -->
            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                <div v-for="service in services" :key="service.id"
                    class="group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    <Link :href="`/services/${service.slug}`">
                        <!-- Badge prix -->
                        <div class="absolute top-4 right-4 z-20">
                            <span class="bg-gradient-to-r from-[#c10e46] to-[#ff4d7e] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                                {{ service.category?.title || 'Catégorie' }}
                            </span>
                        </div>

                        <!-- En-tête avec image de fond -->
                        <div class="relative h-48 bg-cover bg-center overflow-hidden"
                            :style="{ backgroundImage: `url('/storage/${service.image || '/images/default-service-bg.jpg'}')` }">

                            <!-- Overlay pour améliorer la lisibilité du texte -->
                            <div class="absolute inset-0 bg-black/30"></div>

                            <!-- Informations dans l'en-tête -->
                            <div class="absolute bottom-4 left-6 right-6 z-10">
                                <h3 class="text-xl font-bold text-white mb-1 drop-shadow-lg group-hover:text-pink-200 transition-colors">
                                    {{ service.title || 'Titre non spécifié' }}
                                </h3>
                                <p class="text-sm text-white/90 drop-shadow-md flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ service.category?.title || 'Catégorie' }}
                                </p>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="p-6">
                            <!-- Évaluations / Durée -->
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-sm text-gray-600">
                                    Agence : {{ service.agency?.name || 'Agence non spécifiée' }}
                                </p>
                                <div class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    A propos de ce service
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3 leading-relaxed">
                                {{ truncateDescription(service.description) || 'Aucune description disponible pour le moment.' }}
                            </p>

                            <!-- Informations complémentaires -->
                            <div class="space-y-2 mb-4">
                                <div v-if="service.price" class="flex items-center gap-2 text-bold text-gray-500">
                                    <Banknote class="text-yellow-500" />
                                À partir de <span class="text-[#c10e46] font-bold">{{ formatAmount(service.price) }}</span>
                                </div>
                            </div>

                            <!-- Bouton de devis-->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <HandCoins />
                                    <span>Demandez un devis</span>
                                </div>
                                <svg class="w-5 h-5 text-[#c10e46] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mt-16">
                <div class="inline-flex items-center gap-4 bg-white p-1 rounded-full shadow-lg">
                    <Link href="/services"
                          class="inline-flex items-center bg-gradient-to-r from-[#1e002f] to-[#2c0047] hover:from-[#2c0047] hover:to-[#3a0057] text-white px-8 py-4 rounded-full font-semibold transition-all duration-300 transform">
                        Tous nos services
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
