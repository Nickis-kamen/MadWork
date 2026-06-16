<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
  ShoppingBag,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  TrendingUp,
  X,
  Truck,
  Search,
  DollarSign
} from 'lucide-vue-next';

// Props
const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
});

const orders = ref([...props.orders]);
const showModal = ref(false);
const selectedOrder = ref(null);

// État pour la recherche et les filtres
const searchQuery = ref('');
const statusFilter = ref('all');
const paymentFilter = ref('all');

// Obtenir infos statut
const getStatusInfo = (status) => {
    const allStatuses = [
        { value: 'pending', label: 'En attente', icon: Clock, bg: 'bg-yellow-100', text: 'text-yellow-600' },
        { value: 'in_progress', label: 'En cours', icon: TrendingUp, bg: 'bg-blue-100', text: 'text-blue-600' },
        { value: 'delivered', label: 'Livré', icon: Truck, bg: 'bg-purple-100', text: 'text-purple-600' },
        { value: 'completed', label: 'Terminé', icon: CheckCircle, bg: 'bg-green-100', text: 'text-green-600' },
        { value: 'cancelled', label: 'Annulé', icon: XCircle, bg: 'bg-red-100', text: 'text-red-600' }
    ];
    return allStatuses.find(s => s.value === status) || { label: status, icon: AlertCircle, bg: 'bg-gray-100', text: 'text-gray-600' };
};

// Formater montant
const formatAmount = (amount) => {
    if (!amount) return '-';
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};

// Formater date
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

// Filtrer les commandes
const filteredOrders = computed(() => {
    let filtered = orders.value;

    // Filtre par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(order =>
            order.reference?.toLowerCase().includes(query) ||
            order.user?.name?.toLowerCase().includes(query) ||
            order.agency?.name?.toLowerCase().includes(query) ||
            order.service?.title?.toLowerCase().includes(query)
        );
    }

    // Filtre par statut
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(order => order.status === statusFilter.value);
    }

    return filtered;
});

// Statistiques
const stats = computed(() => {
    const total = orders.value.length;
    const pending = orders.value.filter(o => o.status === 'pending').length;
    const inProgress = orders.value.filter(o => o.status === 'in_progress').length;
    const delivered = orders.value.filter(o => o.status === 'delivered').length;
    const completed = orders.value.filter(o => o.status === 'completed').length;
    const cancelled = orders.value.filter(o => o.status === 'cancelled').length;

    return { total, pending, inProgress, delivered, completed, cancelled };
});

// Ouvrir modal de détails
const openDetails = (order) => {
    selectedOrder.value = order;
    showModal.value = true;
};

// Fermer le modal
const closeModal = () => {
    showModal.value = false;
    selectedOrder.value = null;
};
</script>

<template>
    <Head title="Gestion des Commandes - Admin" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Suivi des projets
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez tous les projets et leur progression
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-4 py-2 rounded-xl font-semibold" :style="{ background: '#c10e4610', color: '#c10e46' }">
                        Total: {{ stats.total }} projets
                    </span>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
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
                        <span class="text-sm text-gray-500">En cours</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600">
                            {{ stats.inProgress }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Livrées</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-600">
                            {{ stats.delivered }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Terminées</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                            {{ stats.completed }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Annulées</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                            {{ stats.cancelled }}
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
                            placeholder="Rechercher par référence, client, agence, service..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <select
                            v-model="statusFilter"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 bg-white"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        >
                            <option value="all">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="in_progress">En cours</option>
                            <option value="delivered">Livré</option>
                            <option value="completed">Terminé</option>
                            <option value="cancelled">Annulé</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des commandes -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="order in filteredOrders" :key="order.id" class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ order.reference }}</p>
                                <p class="text-xs text-gray-500">{{ order.service?.title || '-' }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(order.status).bg">
                                <component :is="getStatusInfo(order.status).icon" class="w-3 h-3 mr-1" />
                                <span>{{ getStatusInfo(order.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Client</p>
                                <p class="font-medium text-xs">{{ order.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Agence</p>
                                <p class="font-medium text-xs">{{ order.agency?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Montant</p>
                                <p class="font-medium text-xs" :style="{ color: '#c10e46' }">{{ formatAmount(order.amount) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Paiement</p>
                                <p class="text-xs font-medium" :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-orange-600'">
                                    {{ order.payment_status === 'paid' ? 'Payé' : 'Paiement final requis' }}
                                </p>
                            </div>
                        </div>

                        <!-- Barre progression -->
                        <div class="mb-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span>Progression</span>
                                <span :style="{ color: '#c10e46' }">{{ order.progress || 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all"
                                    :class="order.status === 'delivered' ? 'bg-purple-500' : 'bg-blue-500'"
                                    :style="{ width: `${order.progress || 0}%` }"></div>
                            </div>
                        </div>

                        <!-- Note si présente -->
                        <div v-if="order.progress_note" class="mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                            {{ order.progress_note }}
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end">
                            <button
                                @click="openDetails(order)"
                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm">Détails</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="filteredOrders.length === 0" class="p-12 text-center">
                        <ShoppingBag class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500 text-lg">Aucune commande trouvée</p>
                        <p class="text-gray-400 text-sm">
                            {{ searchQuery || statusFilter !== 'all' || paymentFilter !== 'all' ? 'Aucun résultat pour votre recherche' : 'La liste des commandes est vide' }}
                        </p>
                    </div>
                </div>

                <!-- Version desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client/ Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            <tr @click="openDetails(order)" v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono font-medium" :style="{ color: '#1e002f' }">
                                        {{ order.reference }}
                                    </span>
                                    <p class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ order.client?.name || 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ order.service?.title || '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm">{{ order.agency?.name || 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(order.amount) }}</p>
                                    <p v-if="order.commission" class="text-xs text-gray-400">Commission: {{ formatAmount(order.commission) }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap w-48">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs">{{ order.progress || 0 }}%</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all"
                                                :class="order.status === 'delivered' ? 'bg-purple-500' : 'bg-blue-500'"
                                                :style="{ width: `${order.progress || 0}%` }"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(order.status).bg">
                                        <component :is="getStatusInfo(order.status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getStatusInfo(order.status).label }}</span>
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="filteredOrders.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <ShoppingBag class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg">Aucune commande trouvée</p>
                                    <p class="text-gray-400 text-sm">
                                        {{ searchQuery || statusFilter !== 'all' || paymentFilter !== 'all' ? 'Aucun résultat pour votre recherche' : 'La liste des commandes est vide' }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Détails - Consultation seule -->
        <div v-if="showModal && selectedOrder" class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b z-10" :style="{ borderColor: '#c10e4620' }">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                <ShoppingBag class="w-6 h-6" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedOrder.reference }}</h3>
                                <p class="text-sm text-gray-500">
                                    Client: {{ selectedOrder.client?.name || 'N/A' }} •
                                    Service: {{ selectedOrder.service?.title || '-' }}
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
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getStatusInfo(selectedOrder.status).bg">
                                <component :is="getStatusInfo(selectedOrder.status).icon" class="w-4 h-4" :class="getStatusInfo(selectedOrder.status).text" />
                                <span class="text-sm font-medium" :class="getStatusInfo(selectedOrder.status).text">
                                    {{ getStatusInfo(selectedOrder.status).label }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Clock class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Créée le: {{ formatDate(selectedOrder.created_at) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <DollarSign class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Montant: {{ formatAmount(selectedOrder.amount) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <span class="text-sm text-gray-600">Paiement:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="selectedOrder.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'">
                                    {{ selectedOrder.payment_status === 'paid' ? 'Payé' : 'Paiement final requis' }}
                                </span>
                            </div>
                        </div>

                        <!-- Progression -->
                        <div class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Progression</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Avancement</span>
                                    <span class="font-semibold" :style="{ color: '#c10e46' }">{{ selectedOrder.progress || 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all"
                                        :class="selectedOrder.status === 'delivered' ? 'bg-purple-500' : 'bg-blue-500'"
                                        :style="{ width: `${selectedOrder.progress || 0}%` }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Note de progression -->
                        <div v-if="selectedOrder.progress_note" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Note de progression</h4>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-sm">{{ selectedOrder.progress_note }}</p>
                            </div>
                        </div>

                        <!-- Informations client et agence -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <div class="p-4 rounded-lg bg-gray-50">
                                <h4 class="font-semibold text-sm text-gray-500 mb-2">Client</h4>
                                <p class="font-medium" :style="{ color: '#1e002f' }">{{ selectedOrder.client?.name || 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ selectedOrder.client?.email || 'Email non disponible' }}</p>
                                <p class="text-sm text-gray-500">{{ selectedOrder.client?.phone || 'Téléphone non disponible' }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <h4 class="font-semibold text-sm text-gray-500 mb-2">Agence</h4>
                                <p class="font-medium" :style="{ color: '#1e002f' }">{{ selectedOrder.agency?.name || 'Non assignée' }}</p>
                                <p class="text-sm text-gray-500">{{ selectedOrder.agency?.email || 'Email non disponible' }}</p>
                                <p v-if="selectedOrder.agency" class="text-sm text-gray-500">{{ selectedOrder.agency.address || 'Adresse non disponible' }}</p>
                            </div>
                        </div>

                        <!-- Commission -->
                        <div v-if="selectedOrder.commission" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Commission</h4>
                            <div class="p-4 rounded-lg bg-green-50">
                                <p class="text-lg font-bold" :style="{ color: '#c10e46' }">{{ formatAmount(selectedOrder.commission) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
