<script setup>
import Nav from '@/Components/Nav.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, X, Clock, DollarSign, User, Briefcase, FolderOpen, Eye, Calendar } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    projects: Object,
    categories: Array,
    filters: Object,
});

// État local pour les filtres
const searchTerm = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category_id || '');
const currentPage = ref(props.filters?.page || 1);

// Debounce pour la recherche
let searchTimeout = null;

// Fonction pour appliquer les filtres
const applyFilters = () => {
    const query = {};

    if (searchTerm.value) query.search = searchTerm.value;
    if (selectedCategory.value) query.category_id = selectedCategory.value;
    if (currentPage.value > 1) query.page = currentPage.value;

    router.get('/projects', query, {
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

watch(selectedCategory, () => {
    currentPage.value = 1;
    applyFilters();
});

// Réinitialiser les filtres
const resetFilters = () => {
    searchTerm.value = '';
    selectedCategory.value = '';
    currentPage.value = 1;
    applyFilters();
};

// Vérifier si des filtres sont actifs
const hasActiveFilters = computed(() => {
    return searchTerm.value || selectedCategory.value;
});

// Tronquer la description
const truncateDescription = (description, maxLength = 100) => {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
};

// Changement de page
const changePage = (page) => {
    if (page < 1 || page > props.projects.last_page) return;
    currentPage.value = page;
    applyFilters();
};

// Formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};

// Formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'Date inconnue';
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) return 'Hier';
    if (diffDays < 7) return `Il y a ${diffDays} jours`;
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Statut du projet
const getStatusBadge = (status) => {
    const statusMap = {
        'active': { label: 'Ouvert', class: 'bg-green-100 text-green-700' },
        'in_progress': { label: 'En cours', class: 'bg-blue-100 text-blue-700' },
        'closed': { label: 'Fermé', class: 'bg-gray-100 text-gray-700' }
    };
    return statusMap[status] || { label: status, class: 'bg-gray-100 text-gray-700' };
};
</script>

<template>
    <Head title="Projets - MadWork"/>
    <Nav />

    <!-- Projects Section -->
    <div id="projects" class="relative py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="relative container mx-auto px-4">
            <!-- En-tête de section -->
            <h2 class="text-3xl text-center md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4 mt-8">
                Projets <span class="text-[#c10e46]">disponibles</span>
            </h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
                Découvrez les projets publiés par les clients et proposez vos services
            </p>

            <!-- Barre de recherche et filtres (comme la page services) -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Champ de recherche -->
                        <div class="flex-1 relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Rechercher un projet, client..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent transition-all"
                            />
                        </div>

                        <!-- Filtre par catégorie -->
                        <div class="md:w-64">
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

                        <!-- Bouton réinitialiser -->
                        <button
                            v-if="hasActiveFilters"
                            @click="resetFilters"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <X class="w-5 h-5" />
                            <span class="hidden sm:inline">Effacer</span>
                        </button>
                    </div>

                    <!-- Résultats trouvés -->
                    <div v-if="projects.total !== undefined" class="mt-4 text-sm text-gray-600">
                        {{ projects.total }} projet(s) trouvé(s)
                    </div>
                </div>
            </div>

            <!-- Grille des projets (style carte comme avant) -->
            <div v-if="projects.data && projects.data.length > 0" class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                <div v-for="project in projects.data" :key="project.id"
                    class="group bg-white rounded-xl border hover:shadow-xl transition-all duration-300 overflow-hidden"
                    :style="{ borderColor: '#c10e4620' }">
                    <Link :href="`/projects/${project.id}`" class="block">
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

            <!-- Message si aucun projet -->
            <div v-else class="text-center py-12">
                <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                    <FolderOpen class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-600 text-lg">Aucun projet trouvé</p>
                    <p class="text-gray-500 text-sm mt-2">Essayez de modifier vos critères de recherche</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="projects.last_page && projects.last_page > 1" class="mt-12">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                    <!-- Bouton Précédent -->
                    <button
                        @click="changePage(projects.current_page - 1)"
                        :disabled="projects.current_page === 1"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Précédent
                    </button>

                    <!-- Pages -->
                    <div class="flex gap-2">
                        <button
                            v-for="page in projects.last_page"
                            :key="page"
                            @click="changePage(page)"
                            :class="[
                                'w-10 h-10 rounded-lg transition-all',
                                projects.current_page === page
                                    ? 'bg-gradient-to-r from-[#c10e46] to-[#ff4d7e] text-white'
                                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <!-- Bouton Suivant -->
                    <button
                        @click="changePage(projects.current_page + 1)"
                        :disabled="projects.current_page === projects.last_page"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Suivant
                    </button>
                </div>

                <!-- Information de pagination -->
                <div class="text-center mt-4 text-sm text-gray-500">
                    Affichage de {{ projects.from || 0 }} à {{ projects.to || 0 }} sur {{ projects.total || 0 }} projets
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
