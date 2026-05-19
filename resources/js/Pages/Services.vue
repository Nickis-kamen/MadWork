<script setup>
import Nav from '@/Components/Nav.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Banknote, HandCoins, Search, X } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue'

const props = defineProps({
    services: Object, // Changé en Object pour la pagination
    agencies: Array,
    categories: Array,
    filters: Object,
});

// État local pour les filtres
const searchTerm = ref(props.filters?.search || '');
const selectedAgency = ref(props.filters?.agency_id || '');
const selectedCategory = ref(props.filters?.category_id || '');
const currentPage = ref(props.filters?.page || 1);

// Debounce pour la recherche
let searchTimeout = null;

// Fonction pour appliquer les filtres
const applyFilters = () => {
    const query = {};

    if (searchTerm.value) query.search = searchTerm.value;
    if (selectedAgency.value) query.agency_id = selectedAgency.value;
    if (selectedCategory.value) query.category_id = selectedCategory.value;
    if (currentPage.value > 1) query.page = currentPage.value;

    router.get('/services', query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// Watch avec debounce pour la recherche
watch(searchTerm, (newValue) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage.value = 1;
        applyFilters();
    }, 500);
});

watch(selectedAgency, () => {
    currentPage.value = 1;
    applyFilters();
});

watch(selectedCategory, () => {
    currentPage.value = 1;
    applyFilters();
});

// Réinitialiser les filtres
const resetFilters = () => {
    searchTerm.value = '';
    selectedAgency.value = '';
    selectedCategory.value = '';
    currentPage.value = 1;
    applyFilters();
};

// Vérifier si des filtres sont actifs
const hasActiveFilters = computed(() => {
    return searchTerm.value || selectedAgency.value || selectedCategory.value;
});

// Tronquer la description
const truncateDescription = (description, maxLength = 100) => {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
};

// Changement de page
const changePage = (page) => {
    if (page < 1 || page > props.services.last_page) return;
    currentPage.value = page;
    applyFilters();
};

const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>

<template>
    <Head title="Services"/>
    <Nav />
    <!-- Services Section -->
    <div id="services" class="relative py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="relative container mx-auto px-4">
            <!-- En-tête de section -->
            <h2 class="text-3xl text-center md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4 mt-8">
                Découvrez nos <span class="text-[#c10e46]">services</span>
            </h2>

            <!-- Barre de recherche et filtres -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Champ de recherche -->
                        <div class="flex-1 relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Rechercher un service..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent transition-all"
                            />
                        </div>

                        <!-- Filtre par catégorie -->
                        <div class="md:w-52">
                            <select
                                v-model="selectedCategory"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent transition-all bg-white"
                            >
                                <option value="">Toutes les catégories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.title }}
                                </option>
                            </select>
                        </div>

                        <!-- Filtre par agence -->
                        <div class="md:w-52">
                            <select
                                v-model="selectedAgency"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent transition-all bg-white"
                            >
                                <option value="">Toutes les agences</option>
                                <option v-for="agency in agencies" :key="agency.id" :value="agency.id">
                                    {{ agency.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Bouton réinitialiser -->
                        <button
                            v-if="hasActiveFilters"
                            @click="resetFilters"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Résultats trouvés -->
                    <div v-if="services.total !== undefined" class="mt-4 text-sm text-gray-600">
                        {{ services.total }} service(s) trouvé(s)
                    </div>
                </div>
            </div>

            <!-- Grille des services -->
            <div v-if="services.data && services.data.length > 0" class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                <div v-for="service in services.data" :key="service.id"
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

                            <!-- Overlay -->
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
                            <!-- Agence -->
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

                            <!-- Prix -->
                            <div class="space-y-2 mb-4">
                                <div v-if="service.price" class="flex items-center gap-2 text-bold text-gray-500">
                                    <Banknote class="text-yellow-500" />
                                    À partir de <span class="text-[#c10e46] font-bold">{{ formatAmount(service.price) }}</span>
                                </div>
                            </div>

                            <!-- Bouton de devis -->
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

            <!-- Message si aucun service -->
            <div v-else class="text-center py-12">
                <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                    <p class="text-gray-600 text-lg">Aucun service trouvé</p>
                    <p class="text-gray-500 text-sm mt-2">Essayez de modifier vos critères de recherche</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="services.last_page && services.last_page > 1" class="mt-12">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                    <!-- Bouton Précédent -->
                    <button
                        @click="changePage(services.current_page - 1)"
                        :disabled="services.current_page === 1"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Précédent
                    </button>

                    <!-- Pages -->
                    <div class="flex gap-2">
                        <button
                            v-for="page in services.last_page"
                            :key="page"
                            @click="changePage(page)"
                            :class="[
                                'w-10 h-10 rounded-lg transition-all',
                                services.current_page === page
                                    ? 'bg-gradient-to-r from-[#c10e46] to-[#ff4d7e] text-white'
                                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <!-- Bouton Suivant -->
                    <button
                        @click="changePage(services.current_page + 1)"
                        :disabled="services.current_page === services.last_page"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Suivant
                    </button>
                </div>

                <!-- Information de pagination -->
                <div class="text-center mt-4 text-sm text-gray-500">
                    Affichage de {{ services.from || 0 }} à {{ services.to || 0 }} sur {{ services.total || 0 }} services
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
