<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
Building2,
Clock,
CheckCircle,
Eye,
Mail,
Phone,
MapPin,
Calendar,
User,
FileText,
Search,
X,
Users,
Globe,
Info
} from 'lucide-vue-next';
import Toast from '@/Components/Toastify/Toast.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props reçues de Laravel
const props = defineProps({
    agencies: {
        type: Array,
        default: () => []
    }
});

// État pour le popup
const showModal = ref(false);
const selectedAgency = ref(null);

// État pour la recherche
const searchQuery = ref('');

// Fonction pour ouvrir le popup
const openDetails = (agency) => {
    selectedAgency.value = agency;
    showModal.value = true;
};

// Fonction pour fermer le popup
const closeModal = () => {
    showModal.value = false;
    selectedAgency.value = null;
};

// Fonction pour obtenir le statut de l'agence
const getAgencyStatus = (agency) => {
    // Vous pouvez adapter cette logique selon vos besoins
    // Par exemple, si vous avez un champ is_active ou verified
    if (agency.is_active) {
        return { label: 'Active', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' };
    } else {
        return { label: 'Inactive', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100' };
    }
};

// Fonction pour compter les utilisateurs de l'agence
const getUserCount = (agency) => {
    return agency.users?.length || 0;
};

// Fonction pour formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'Non défini';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Filtrer les agences par recherche
const filteredAgencies = computed(() => {
    let filtered = props.agencies;

    // Filtre par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(agency =>
            agency.name?.toLowerCase().includes(query) ||
            agency.email?.toLowerCase().includes(query) ||
            agency.address?.toLowerCase().includes(query) ||
            agency.description?.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Récupérer le nom du propriétaire (user)
const getOwnerName = (agency) => {
    return agency.user?.name || 'Non assigné';
};
</script>

<template>
    <Head title="Gestion des agences" />
    <AuthenticatedLayout>
        <!-- Container -->
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Liste des agences
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez toutes les agences inscrites sur la plateforme
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">
                        Total: {{ filteredAgencies.length }} agence(s)
                    </span>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Rechercher par nom, email, adresse ou description..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                        :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                    />
                </div>
            </div>

            <!-- Liste des agences -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile (cartes) -->
                <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                    <div
                        v-for="agency in filteredAgencies"
                        :key="agency.id"
                        class="p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium"
                                    :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                >
                                    <img v-if="agency.image" :src="`/storage/${agency.image}`" alt="Logo" class="w-full h-full rounded-full object-cover">
                                    <span v-else class="text-white">{{ agency.name?.charAt(0) || '?' }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ agency.name }}</p>
                                    <p class="text-xs text-gray-500">Propriétaire: {{ getOwnerName(agency) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Email</p>
                                <p class="font-medium text-xs truncate" :style="{ color: '#1e002f' }">{{ agency.email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Créée le</p>
                                <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ formatDate(agency.created_at) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                @click="openDetails(agency)"
                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620' }"
                                title="Voir détails"
                            >
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm" :style="{ color: '#1e002f' }">Voir détails</span>
                            </button>
                        </div>
                    </div>

                    <!-- Message si aucune agence (mobile) -->
                    <div v-if="filteredAgencies.length === 0" class="p-12 text-center">
                        <Building2 class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500 text-lg mb-2">Aucune agence trouvée</p>
                        <p class="text-gray-400 text-sm">
                            {{ searchQuery ? 'Aucun résultat pour votre recherche' : 'La liste des agences est vide' }}
                        </p>
                    </div>
                </div>

                <!-- Tableau desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                        <!-- En-tête -->
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propriétaire</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créée le</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <!-- Corps du tableau -->
                        <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                            <tr v-for="agency in filteredAgencies" :key="agency.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div
                                                class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-medium"
                                                :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                            >
                                                <img v-if="agency.image" :src="`/storage/${agency.image}`" alt="Logo" class="w-full h-full rounded-full object-cover">
                                                <span v-else>{{ agency.name?.charAt(0) || '?' }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ agency.name }}</p>
                                            <p v-if="agency.slug" class="text-xs text-gray-500">{{ agency.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ getOwnerName(agency) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ agency.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="truncate max-w-[150px] inline-block">
                                        {{ agency.address || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ formatDate(agency.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        @click="openDetails(agency)"
                                        class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                        :style="{ borderColor: '#c10e4620' }"
                                        title="Voir détails"
                                    >
                                        <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                    </button>
                                </td>
                            </tr>
                            <!-- Message si aucune agence -->
                            <tr v-if="filteredAgencies.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <Building2 class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg mb-2">Aucune agence trouvée</p>
                                    <p class="text-gray-400 text-sm">
                                        {{ searchQuery ? 'Aucun résultat pour votre recherche' : 'La liste des agences est vide' }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal / Popup pour les détails de l'agence -->
        <div v-if="showModal && selectedAgency" class="flex items-center justify-center fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

            <!-- Modal content -->
            <div class="bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full">
                <!-- Header modal -->
                <div class="flex items-center justify-between p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center"
                            :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                        >
                            <img v-if="selectedAgency.image" :src="`/storage/${selectedAgency.image}`" alt="Logo" class="w-full h-full rounded-xl object-cover">
                            <Building2 v-else class="w-6 h-6" :style="{ color: '#c10e46' }" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedAgency.name }}</h3>
                            <p class="text-sm text-gray-500">ID: #{{ selectedAgency.id }}</p>
                        </div>
                    </div>
                    <button
                        @click="closeModal"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        :style="{ borderColor: '#c10e4620' }"
                    >
                        <X class="w-5 h-5 text-gray-500" />
                    </button>
                </div>

                <!-- Corps modal -->
                <div class="p-6 space-y-6">
                    <!-- Statut et informations rapides -->
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                            <Calendar class="w-4 h-4 text-gray-600" />
                            <span class="text-sm text-gray-600">Créée le: {{ formatDate(selectedAgency.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Grille d'information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Colonne gauche : Informations générales -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Informations générales</h4>

                            <div class="space-y-3">
                                <div class="flex items-start space-x-3">
                                    <Building2 class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Nom de l'agence</p>
                                        <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ selectedAgency.name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <Mail class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedAgency.email }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedAgency.slug" class="flex items-start space-x-3">
                                    <Globe class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Slug</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedAgency.slug }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite : Informations de contact -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Contact & Localisation</h4>

                            <div class="space-y-3">
                                <div v-if="selectedAgency.address" class="flex items-start space-x-3">
                                    <MapPin class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Adresse</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedAgency.address }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedAgency.user" class="flex items-start space-x-3">
                                    <User class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Propriétaire</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedAgency.user.name }}</p>
                                        <p class="text-xs text-gray-400">{{ selectedAgency.user.email }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedAgency.description" class="flex items-start space-x-3">
                                    <Info class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Description</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedAgency.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
