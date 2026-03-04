<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
  Users,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  Mail,
  Phone,
  MapPin,
  Calendar,
  User,
  FileText,
  Search,
  X
} from 'lucide-vue-next';
import Toast from '@/Components/Toastify/Toast.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props reçues de Laravel
const props = defineProps({
    customers: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// État pour le popup
const showModal = ref(false);
const selectedCustomer = ref(null);

// État pour la recherche
const searchQuery = ref('');

// Fonction pour ouvrir le popup
const openDetails = (customer) => {
    selectedCustomer.value = customer;
    showModal.value = true;
};

// Fonction pour fermer le popup
const closeModal = () => {
    showModal.value = false;
    selectedCustomer.value = null;
};

// Fonction pour obtenir le statut de vérification
const getVerificationStatus = (customer) => {
    if (customer.is_verified) {
        return { label: 'Vérifié', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' };
    } else {
        return { label: 'Non vérifié', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100' };
    }
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

// Filtrer les clients par recherche
const filteredCustomers = computed(() => {
    let filtered = props.customers;

    // Filtre par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(customer =>
            customer.name?.toLowerCase().includes(query) ||
            customer.email?.toLowerCase().includes(query) ||
            customer.phone?.includes(query)
        );
    }

    return filtered;
});
</script>

<template>
    <Head title="Gestion des clients" />
    <Toast />
    <AuthenticatedLayout>
        <!-- Container -->
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Liste des clients
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez tous vos clients inscrits sur la plateforme
                    </p>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Rechercher par nom, email, téléphone ou entreprise..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                        :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                    />
                </div>
            </div>

            <!-- Liste des clients -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile (cartes) -->
                <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                    <div
                        v-for="customer in filteredCustomers"
                        :key="customer.id"
                        class="p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium"
                                    :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                >
                                    <img v-if="customer.image" :src="`/storage/${customer.image}`" alt="Avatar" class="w-full h-full rounded-full object-cover">
                                    <span v-else class="text-white">{{ customer.name?.charAt(0) || '?' }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ customer.name }}</p>
                                    <p v-if="customer.company" class="text-xs text-gray-500">{{ customer.company }}</p>
                                </div>
                            </div>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                :class="getVerificationStatus(customer).bg"
                            >
                                <component :is="getVerificationStatus(customer).icon" class="w-3 h-3 mr-1" :class="getVerificationStatus(customer).color" />
                                <span :class="getVerificationStatus(customer).color">{{ getVerificationStatus(customer).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Email</p>
                                <p class="font-medium text-xs truncate" :style="{ color: '#1e002f' }">{{ customer.email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Téléphone</p>
                                <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ customer.phone || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Inscription</p>
                                <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ formatDate(customer.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">ID</p>
                                <p class="font-medium text-xs font-mono" :style="{ color: '#1e002f' }">#{{ customer.id }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                @click="openDetails(customer)"
                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620' }"
                                title="Voir détails"
                            >
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm" :style="{ color: '#1e002f' }">Voir détails</span>
                            </button>
                        </div>
                    </div>

                    <!-- Message si aucun client (mobile) -->
                    <div v-if="filteredCustomers.length === 0" class="p-12 text-center">
                        <Users class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500 text-lg mb-2">Aucun client trouvé</p>
                        <p class="text-gray-400 text-sm">
                            {{ searchQuery ? 'Aucun résultat pour votre recherche' : 'La liste des clients est vide' }}
                        </p>
                    </div>
                </div>

                <!-- Tableau desktop-->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                        <!-- En-tête -->
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscription</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <!-- Corps du tableau -->
                        <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                            <tr v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" :style="{ color: '#1e002f' }">
                                    #{{ customer.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div
                                                class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-medium"
                                                :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                            >
                                                <img v-if="customer.image" :src="`/storage/${customer.image}`" alt="Avatar" class="w-full h-full rounded-full object-cover">
                                                <span v-else>{{ customer.name?.charAt(0) || '?' }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ customer.name }}</p>
                                            <p v-if="customer.company" class="text-xs text-gray-500">{{ customer.company }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ customer.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ customer.phone || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ formatDate(customer.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                        :class="getVerificationStatus(customer).bg"
                                    >
                                        <component :is="getVerificationStatus(customer).icon" class="w-3 h-3 mr-1" :class="getVerificationStatus(customer).color" />
                                        <span :class="getVerificationStatus(customer).color">{{ getVerificationStatus(customer).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        @click="openDetails(customer)"
                                        class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                        :style="{ borderColor: '#c10e4620' }"
                                        title="Voir détails"
                                    >
                                        <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                    </button>
                                </td>
                            </tr>
                            <!-- Message si aucun client -->
                            <tr v-if="filteredCustomers.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <Users class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg mb-2">Aucun client trouvé</p>
                                    <p class="text-gray-400 text-sm">
                                        {{ searchQuery ? 'Aucun résultat pour votre recherche' : 'La liste des clients est vide' }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal / Popup pour les détails du client -->
        <div v-if="showModal && selectedCustomer" class="flex items-center justify-center fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                            <img v-if="selectedCustomer.image" :src="`/storage/${selectedCustomer.image}`" alt="Avatar" class="w-full h-full rounded-xl object-cover">
                            <User v-else class="w-6 h-6" :style="{ color: '#c10e46' }" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedCustomer.name }}</h3>
                            <p class="text-sm text-gray-500">Client #{{ selectedCustomer.id }}</p>
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
                        <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getVerificationStatus(selectedCustomer).bg">
                            <component :is="getVerificationStatus(selectedCustomer).icon" class="w-4 h-4" :class="getVerificationStatus(selectedCustomer).color" />
                            <span class="text-sm font-medium" :class="getVerificationStatus(selectedCustomer).color">
                                {{ getVerificationStatus(selectedCustomer).label }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                            <Calendar class="w-4 h-4 text-gray-600" />
                            <span class="text-sm text-gray-600">Inscrit le: {{ formatDate(selectedCustomer.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Grille d'information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Colonne gauche : Informations personnelles -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Informations personnelles</h4>

                            <div class="space-y-3">
                                <div class="flex items-start space-x-3">
                                    <User class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Nom complet</p>
                                        <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ selectedCustomer.name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <Mail class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedCustomer.email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <Phone class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Téléphone</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedCustomer.phone || 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite : Informations professionnelles -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Informations professionnelles</h4>

                            <div class="space-y-3">
                                <div v-if="selectedCustomer.company" class="flex items-start space-x-3">
                                    <FileText class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Entreprise</p>
                                        <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ selectedCustomer.company }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedCustomer.siret" class="flex items-start space-x-3">
                                    <FileText class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">SIRET</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedCustomer.siret }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedCustomer.address" class="flex items-start space-x-3">
                                    <MapPin class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Adresse</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedCustomer.address }}</p>
                                    </div>
                                </div>

                                <div v-if="!selectedCustomer.company && !selectedCustomer.siret && !selectedCustomer.address" class="text-sm text-gray-400 italic">
                                    Aucune information professionnelle renseignée
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
