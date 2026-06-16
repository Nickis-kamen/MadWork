<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
  FileText,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  Download,
  X,
  Calendar,
  HandCoins,
  Building2,
  User,
  Search,
  Filter,
  FileCheck
} from 'lucide-vue-next';

// Props reçues de Laravel
const props = defineProps({
    devis: {
        type: Array,
        default: () => []
    }
});

const devis = ref([...props.devis]);

// État pour le popup
const showModal = ref(false);
const selectedDevis = ref(null);

// État pour la recherche et les filtres
const searchQuery = ref('');
const statusFilter = ref('all');

// Fonction pour ouvrir le popup
const openDetails = (devisItem) => {
    selectedDevis.value = devisItem;
    showModal.value = true;
};

// Fonction pour fermer le popup
const closeModal = () => {
    showModal.value = false;
    selectedDevis.value = null;
};

// Fonction pour obtenir le libellé du statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100' },
        'proposed': { label: 'Proposition faite', icon: FileCheck, color: 'text-blue-600', bg: 'bg-blue-100' },
        'accepted': { label: 'Accepté', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
        'refused': { label: 'Refusé', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Fonction pour formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'Non défini';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Formater le montant
const formatAmount = (amount) => {
    if (!amount) return '-';
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

// Filtrer les devis
const filteredDevis = computed(() => {
    let filtered = devis.value;

    // Filtre par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(devisItem =>
            devisItem.reference?.toLowerCase().includes(query) ||
            devisItem.title?.toLowerCase().includes(query) ||
            devisItem.user?.name?.toLowerCase().includes(query) ||
            devisItem.agency?.name?.toLowerCase().includes(query) ||
            devisItem.service?.title?.toLowerCase().includes(query)
        );
    }

    // Filtre par statut
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(devisItem => devisItem.status === statusFilter.value);
    }

    return filtered;
});

// Statistiques
const stats = computed(() => {
    const total = devis.value.length;
    const pending = devis.value.filter(d => d.status === 'pending').length;
    const proposed = devis.value.filter(d => d.status === 'proposed').length;
    const accepted = devis.value.filter(d => d.status === 'accepted').length;
    const refused = devis.value.filter(d => d.status === 'refused').length;
    return { total, pending, proposed, accepted, refused };
});

// Savoir si le devis a une proposition
const hasProposal = (devisItem) => {
    return devisItem.price || devisItem.delai_agency || devisItem.description_agency;
};
</script>

<template>
    <Head title="Gestion des Devis - Admin" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des devis
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez toutes les demandes de devis et leurs propositions
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-4 py-2 rounded-xl font-semibold" :style="{ background: '#c10e4610', color: '#c10e46' }">
                        Total: {{ stats.total }} devis
                    </span>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">En attente</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">
                            {{ stats.pending }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Propositions</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600">
                            {{ stats.proposed }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Acceptés</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                            {{ stats.accepted }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Refusés</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                            {{ stats.refused }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Barre de recherche et filtres -->
            <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher par référence, titre, client, agence..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        />
                    </div>
                    <div class="flex gap-2">
                        <select
                            v-model="statusFilter"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 bg-white"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        >
                            <option value="all">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="proposed">Proposition faite</option>
                            <option value="accepted">Accepté</option>
                            <option value="refused">Refusé</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des devis -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="devisItem in filteredDevis" :key="devisItem.id" class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                    <FileText class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ devisItem.title }}</p>
                                    <p class="text-xs text-gray-500">#{{ devisItem.reference }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(devisItem.status).bg">
                                <component :is="getStatusInfo(devisItem.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(devisItem.status).color" />
                                <span :class="getStatusInfo(devisItem.status).color">{{ getStatusInfo(devisItem.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Client</p>
                                <p class="font-medium text-xs">{{ devisItem.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Agence</p>
                                <p class="font-medium text-xs">{{ devisItem.agency?.name || 'Non assignée' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Service</p>
                                <p class="font-medium text-xs">{{ devisItem.service?.title || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Budget</p>
                                <p class="font-medium text-xs">{{ formatAmount(devisItem.budget) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Délai souhaité</p>
                                <p class="font-medium text-xs">{{ devisItem.delai }} jours</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Date</p>
                                <p class="font-medium text-xs">{{ formatDate(devisItem.created_at) }}</p>
                            </div>
                        </div>

                        <div v-if="hasProposal(devisItem)" class="mb-3 p-2 rounded-lg" :style="{ background: '#c10e4610' }">
                            <p class="text-xs" :style="{ color: '#c10e46' }">
                                💰 {{ formatAmount(devisItem.price) }} - ⏱️ {{ devisItem.delai_agency || '-' }} jours
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button @click="openDetails(devisItem)" class="p-2 rounded-lg border hover:bg-gray-100 transition-colors" :style="{ borderColor: '#c10e4620' }">
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm">Détails</span>
                            </button>
                            <a v-if="devisItem.cdc_file" :href="`/storage/${devisItem.cdc_file}`" download class="p-2 rounded-lg border hover:bg-gray-100 transition-colors" :style="{ borderColor: '#c10e4620' }">
                                <Download class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                <span class="ml-2 text-sm">PDF</span>
                            </a>
                        </div>
                    </div>

                    <div v-if="filteredDevis.length === 0" class="p-12 text-center">
                        <FileText class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500 text-lg">Aucun devis trouvé</p>
                        <p class="text-gray-400 text-sm">
                            {{ searchQuery || statusFilter !== 'all' ? 'Aucun résultat pour votre recherche' : 'La liste des devis est vide' }}
                        </p>
                    </div>
                </div>

                <!-- Version desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre / Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            <tr @click="openDetails(devisItem)" v-for="devisItem in filteredDevis" :key="devisItem.id" class="hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono font-medium" :style="{ color: '#1e002f' }">
                                        #{{ devisItem.reference }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ devisItem.title }}</p>
                                    <p class="text-xs text-gray-500">{{ devisItem.service?.title || '-' }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm">{{ devisItem.user?.name || 'N/A' }}</span>
                                    <p class="text-xs text-gray-500">{{ devisItem.user?.email || '-' }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="">
                                            <span class="text-sm">{{ devisItem.agency?.name || 'Non assignée' }}</span>
                                            <p class="text-xs text-gray-500">{{ devisItem.agency?.email || '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ formatAmount(devisItem.budget) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span v-if="devisItem.price" :style="{ color: '#c10e46' }" class="font-semibold">
                                        {{ formatAmount(devisItem.price) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(devisItem.status).bg">
                                        <component :is="getStatusInfo(devisItem.status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getStatusInfo(devisItem.status).label }}</span>
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="filteredDevis.length === 0">
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <FileText class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg">Aucun devis trouvé</p>
                                    <p class="text-gray-400 text-sm">
                                        {{ searchQuery || statusFilter !== 'all' ? 'Aucun résultat pour votre recherche' : 'La liste des devis est vide' }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Détails - Consultation seule -->
        <div v-if="showModal && selectedDevis" class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b z-10" :style="{ borderColor: '#c10e4620' }">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                <FileText class="w-6 h-6" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedDevis.title }}</h3>
                                <p class="text-sm text-gray-500">
                                    #{{ selectedDevis.reference }} •
                                    Service: {{ selectedDevis.service?.title || '-' }} •
                                    Client: {{ selectedDevis.user?.name || 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <X class="w-5 h-5 text-gray-500" />
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Infos statut -->
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getStatusInfo(selectedDevis.status).bg">
                                <component :is="getStatusInfo(selectedDevis.status).icon" class="w-4 h-4" :class="getStatusInfo(selectedDevis.status).color" />
                                <span class="text-sm font-medium" :class="getStatusInfo(selectedDevis.status).color">
                                    {{ getStatusInfo(selectedDevis.status).label }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Calendar class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Demande: {{ formatDate(selectedDevis.created_at) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Clock class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Délai souhaité: {{ selectedDevis.delai }} jours</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <HandCoins class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Budget: {{ formatAmount(selectedDevis.budget) }}</span>
                            </div>
                        </div>

                        <!-- Informations client et agence -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg bg-gray-50">
                                <h4 class="font-semibold text-sm text-gray-500 mb-2">Client</h4>
                                <p class="font-medium" :style="{ color: '#1e002f' }">{{ selectedDevis.user?.name || 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ selectedDevis.user?.email || 'Email non disponible' }}</p>
                                <p class="text-sm text-gray-500">{{ selectedDevis.user?.phone || 'Téléphone non disponible' }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <h4 class="font-semibold text-sm text-gray-500 mb-2">Agence</h4>
                                <p class="font-medium" :style="{ color: '#1e002f' }">{{ selectedDevis.agency?.name || 'Non assignée' }}</p>
                                <p class="text-sm text-gray-500">{{ selectedDevis.agency?.email || 'Email non disponible' }}</p>
                                <p v-if="selectedDevis.agency" class="text-sm text-gray-500">{{ selectedDevis.agency.address || 'Adresse non disponible' }}</p>
                            </div>
                        </div>

                        <!-- Demande client -->
                        <div class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Demande du client</h4>
                            <p class="text-sm bg-gray-50 p-3 rounded-lg">{{ selectedDevis.description }}</p>
                            <a v-if="selectedDevis.cdc_file" :href="`/storage/${selectedDevis.cdc_file}`" download class="inline-flex items-center space-x-2 mt-3 px-3 py-2 rounded-lg border hover:bg-gray-50 transition-colors" :style="{ borderColor: '#c10e4620', color: '#c10e46' }">
                                <FileText class="w-4 h-4" />
                                <span>Télécharger le cahier des charges</span>
                            </a>
                        </div>

                        <!-- Proposition de l'agence -->
                        <div class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Proposition de l'agence</h4>

                            <div v-if="hasProposal(selectedDevis)" class="space-y-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 rounded-lg bg-green-50">
                                        <p class="text-xs text-gray-500">Montant proposé</p>
                                        <p class="text-xl font-bold" :style="{ color: '#c10e46' }">{{ formatAmount(selectedDevis.price) }}</p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-blue-50">
                                        <p class="text-xs text-gray-500">Délai proposé</p>
                                        <p class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedDevis.delai_agency || '-' }} jours</p>
                                    </div>
                                </div>
                                <div class="p-3 rounded-lg bg-gray-50">
                                    <p class="text-xs text-gray-500">Détails de l'offre</p>
                                    <p class="text-sm mt-1">{{ selectedDevis.description_agency || 'Aucune description' }}</p>
                                </div>
                            </div>
                            <div v-else class="p-4 rounded-lg bg-gray-50 text-center text-gray-500">
                                <p>Aucune proposition soumise par l'agence</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
