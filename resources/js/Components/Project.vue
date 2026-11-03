<script setup>
import { Link } from '@inertiajs/vue3';
import { Briefcase } from 'lucide-vue-next';
import { ref } from 'vue'

const props = defineProps({
    projects: Array,
})

// Fonction pour formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'Non défini';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};


// Formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};

// Statut du projet
const getStatusBadge = (status) => {
    const statusMap = {
        'open': { label: 'Ouvert', class: 'bg-green-100 text-green-700' },
        'assigned': { label: 'Attribué', class: 'bg-blue-100 text-blue-700' },
        'closed': { label: 'Fermé', class: 'bg-gray-100 text-gray-700' }
    };
    return statusMap[status] || { label: status, class: 'bg-gray-100 text-gray-700' };
};

// Tronquer la description
const truncateDescription = (description, maxLength = 100) => {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
};

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
                    <span class="text-sm font-semibold tracking-wider text-[#1e002f]">PROJETS DISPONIBLES</span>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4">
                    Découvrez nos <span class="text-[#c10e46]">projets disponibles</span>
                </h2>

                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Découvrez les projets publiés par les clients et proposez vos services
                </p>
            </div>

            <div v-if="projects" class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                <div v-for="project in projects" :key="project.id"
                    class="group bg-white rounded-xl border hover:shadow-xl transition-all duration-300 overflow-hidden"
                    :style="{ borderColor: '#c10e4620' }">
                    <Link :href="`/project/${project.id}`" class="block">
                        <!-- En-tête de la carte -->
                        <div class="p-4 border-b" :style="{ borderColor: '#c10e4620', background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)' }">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2 flex-1">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                        :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                                    >
                                        <Briefcase class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-base truncate group-hover:text-[#c10e46] transition-colors" :style="{ color: '#1e002f' }">
                                            {{ project.title }}
                                        </h3>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ project.category?.title || 'Non catégorisé' }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center flex-shrink-0 ml-2"
                                    :class="getStatusBadge(project.status).class"
                                >
                                    {{ getStatusBadge(project.status).label }}
                                </span>
                            </div>
                        </div>

                        <!-- Corps de la carte -->
                        <div class="p-4 space-y-3">
                            <!-- Client -->
                            <div class="flex items-center gap-2">
                                <img v-if="project.user?.image" :src="`/storage/${project.user?.image}`" alt="Photo de profil" class="w-6 h-6 rounded-full object-cover">
                                <div v-else class="w-6 h-6 rounded-full bg-gradient-to-r from-[#c10e46] to-[#1e002f] flex items-center justify-center text-white text-xs font-bold">
                                    {{  project.user?.name?.charAt(0) || 'C' }}
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Publié par</p>
                                    <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ project.user?.name || 'Client' }}</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Description</p>
                                <p class="text-sm text-gray-700 line-clamp-3">
                                    {{ truncateDescription(project.description, 80) }}
                                </p>
                            </div>

                            <!-- Informations clés -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="flex items-center space-x-2">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    <div>
                                        <p class="text-xs text-gray-500">Publié</p>
                                        <p class="text-xs font-medium" :style="{ color: '#1e002f' }">{{ formatDate(project.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Clock class="w-4 h-4 text-gray-400" />
                                    <div>
                                        <p class="text-xs text-gray-500">Délai</p>
                                        <p class="text-xs font-medium" :style="{ color: '#1e002f' }">{{ project.delai }} jours</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 col-span-2">
                                    <DollarSign class="w-4 h-4 text-gray-400" />
                                    <div>
                                        <p class="text-xs text-gray-500">Budget</p>
                                        <p class="text-sm font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(project.budget) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer avec lien -->
                        <div class="p-3 border-t bg-gray-50" :style="{ borderColor: '#c10e4620' }">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <Eye class="w-4 h-4" />
                                    <span>Voir les détails</span>
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
                    <Link href="/projects"
                          class="inline-flex items-center bg-gradient-to-r from-[#1e002f] to-[#2c0047] hover:from-[#2c0047] hover:to-[#3a0057] text-white px-8 py-4 rounded-full font-semibold transition-all duration-300 transform">
                        Toutes les publications
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
