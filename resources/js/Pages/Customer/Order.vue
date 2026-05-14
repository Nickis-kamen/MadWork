<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  ShoppingBag,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  Download,
  TrendingUp,
  X,
  Calendar,
  Package,
  Truck,
  Home,
  CreditCard,
  Wallet,
  AlertTriangle,
} from 'lucide-vue-next';
import Nav from '@/Components/Nav.vue';

// Props reçues de Laravel
const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
});

// État pour le popup
const showModal = ref(false);
const selectedOrder = ref(null);
const showPaymentModal = ref(false);
const isProcessing = ref(false);

// Fonction pour ouvrir le popup
const openDetails = (order) => {
    selectedOrder.value = order;
    showModal.value = true;
};

// Fonction pour fermer le popup
const closeModal = () => {
    showModal.value = false;
    selectedOrder.value = null;
};

// Calculer le montant restant (50%)
const getRemainingAmount = (order) => {
    return order.amount / 2;
};

// Vérifier si le paiement final est possible
const canMakeFinalPayment = (order) => {
    return order.status === 'completed' && order.payment_status !== 'paid';
};

// Fonction pour obtenir le libellé du statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100', progress: 0 },
        'in_progress': { label: 'En cours', icon: TrendingUp, color: 'text-blue-600', bg: 'bg-blue-100', progress: 50 },
        'delivered': { label: 'Livré', icon: Truck, color: 'text-purple-600', bg: 'bg-purple-100', progress: 90 },
        'completed': { label: 'Terminé', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100', progress: 100 },
        'cancelled': { label: 'Annulé', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100', progress: 0 }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100', progress: 0 };
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

const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

// Calculer le pourcentage de progression
const getProgressPercentage = (order) => {
    if (order.progress !== undefined && order.progress !== null) {
        return order.progress;
    }
    return getStatusInfo(order.status).progress;
};

// Obtenir la classe de couleur pour la barre de progression
const getProgressColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-500',
        'in_progress': 'bg-blue-500',
        'delivered': 'bg-purple-500',
        'completed': 'bg-green-500',
        'cancelled': 'bg-red-500'
    };
    return colors[status] || 'bg-gray-500';
};
</script>

<template>
    <Head title="Mes Commandes" />
    <Nav />

    <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen mt-20">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                    Mes commandes
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Suivez l'avancement de vos projets en cours
                </p>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
            <!-- Version mobile -->
            <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="p-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                            >
                                <ShoppingBag class="w-5 h-5" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ order.reference }}</p>
                                <p class="text-xs text-gray-500">{{ order.service?.title || 'Service' }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                            :class="getStatusInfo(order.status).bg"
                        >
                            <component :is="getStatusInfo(order.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(order.status).color" />
                            <span :class="getStatusInfo(order.status).color">{{ getStatusInfo(order.status).label }}</span>
                        </span>
                    </div>

                    <!-- Barre de progression -->
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">Progression</span>
                            <span class="font-semibold" :style="{ color: '#c10e46' }">{{ getProgressPercentage(order) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div
                                class="h-2 rounded-full transition-all duration-500"
                                :class="getProgressColor(order.status)"
                                :style="{ width: `${getProgressPercentage(order)}%` }"
                            ></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                        <Link :href="`/agencies/${order.agency?.slug}`">
                            <p class="text-gray-500 text-xs">Agence</p>
                            <p class="font-medium text-xs font-mono" :style="{ color: '#1e002f' }">{{ order.agency?.name || 'N/A' }}</p>
                        </Link>
                        <div>
                            <p class="text-gray-500 text-xs">Date commande</p>
                            <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ formatDate(order.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Montant</p>
                            <p class="font-medium text-xs" :style="{ color: '#c10e46' }">{{ formatAmount(order.amount) }}</p>
                        </div>
                    </div>

                    <!-- Bouton paiement final si commande terminée -->
                    <div v-if="canMakeFinalPayment(order)" class="mb-3">
                        <button
                            class="w-full py-2 px-3 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition-colors flex items-center justify-center space-x-2"
                        >
                            <CreditCard class="w-4 h-4" />
                            <span>Payer le solde (50%)</span>
                        </button>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button
                            @click="openDetails(order)"
                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                            :style="{ borderColor: '#c10e4620' }"
                            title="Voir progression"
                        >
                            <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                            <span class="ml-2 text-sm" :style="{ color: '#1e002f' }">Suivi</span>
                        </button>
                    </div>
                </div>

                <!-- Message si aucune commande -->
                <div v-if="orders.length === 0" class="p-12 text-center">
                    <ShoppingBag class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                    <p class="text-gray-500 text-lg mb-2">Aucune commande pour le moment</p>
                    <p class="text-gray-400 text-sm mb-6">Vos commandes apparaîtront ici après acceptation des devis</p>
                </div>
            </div>

            <!-- Version desktop -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service / Agence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-mono font-semibold" :style="{ color: '#1e002f' }">{{ order.reference }}</p>
                                <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ order.service?.title || 'Service' }}</p>
                                <Link :href="`/agencies/${order.agency?.slug}`" class="text-xs text-blue-600">
                                    {{ order.agency?.name || 'N/A' }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(order.amount) }}</p>
                                <p v-if="order.payment_status != 'paid'" class="text-xs text-gray-500">Reste à payer: {{ formatAmount(getRemainingAmount(order)) }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap min-w-[200px]">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-medium">{{ getProgressPercentage(order) }}%</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div
                                            class="h-2 rounded-full transition-all duration-500"
                                            :class="getProgressColor(order.status)"
                                            :style="{ width: `${getProgressPercentage(order)}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(order.status).bg">
                                    <component :is="getStatusInfo(order.status).icon" class="w-3 h-3 mr-1" />
                                    <span>{{ getStatusInfo(order.status).label }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="order.payment_status === 'paid'" class="text-green-600 text-sm font-medium">
                                    Payé
                                </span>
                                <p
                                    v-else-if="canMakeFinalPayment(order)"
                                    class="px-3 py-1 bg-yellow-600 text-white text-xs rounded-lg flex items-center space-x-1"
                                >
                                    <CreditCard class="w-3 h-3" />
                                    <span>Incomplet</span>
                                </p>
                                <span v-else class="text-gray-400 text-sm">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button
                                    @click="openDetails(order)"
                                    class="p-2 rounded-lg border hover:bg-gray-100 transition-colors"
                                    :style="{ borderColor: '#c10e4620' }"
                                    title="Voir progression"
                                >
                                    <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="orders.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <ShoppingBag class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                <p class="text-gray-500 text-lg mb-2">Aucune commande pour le moment</p>
                                <p class="text-gray-400 text-sm">Vos commandes apparaîtront ici après acceptation des devis</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal détails commande -->
    <div v-if="showModal && selectedOrder" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

        <div class="bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full mx-auto">
            <div class="flex items-center justify-between p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                        <ShoppingBag class="w-6 h-6" :style="{ color: '#c10e46' }" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedOrder.reference }}</h3>
                        <p class="text-sm text-gray-500">{{ selectedOrder.service?.title }}</p>
                    </div>
                </div>
                <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100">
                    <X class="w-5 h-5 text-gray-500" />
                </button>
            </div>

            <div class="p-6 space-y-6">
                <!-- Statut -->
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getStatusInfo(selectedOrder.status).bg">
                        <component :is="getStatusInfo(selectedOrder.status).icon" class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ getStatusInfo(selectedOrder.status).label }}</span>
                    </div>
                    <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                        <Calendar class="w-4 h-4 text-gray-600" />
                        <span class="text-sm text-gray-600">Commande: {{ formatDate(selectedOrder.created_at) }}</span>
                    </div>
                </div>

                <!-- Barre progression -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold" :style="{ color: '#1e002f' }">Progression du projet</span>
                        <span class="text-2xl font-bold" :style="{ color: '#c10e46' }">{{ getProgressPercentage(selectedOrder) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div
                            class="h-3 rounded-full transition-all duration-500"
                            :class="getProgressColor(selectedOrder.status)"
                            :style="{ width: `${getProgressPercentage(selectedOrder)}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Infos paiement -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Montant total</p>
                        <p class="text-xl font-bold" :style="{ color: '#c10e46' }">{{ formatAmount(selectedOrder.amount) }}</p>
                    </div>
                    <div v-if="selectedOrder.payment_status != 'paid'"  class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Solde restant (50%)</p>
                        <p class="text-xl font-bold text-orange-600">{{ formatAmount(getRemainingAmount(selectedOrder)) }}</p>
                    </div>
                </div>

                <!-- Note agence -->
                <div v-if="selectedOrder.progress_note" class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Note de l'agence</p>
                    <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedOrder.progress_note }}</p>
                </div>

                <!-- Bouton paiement si commande terminée -->
                <div v-if="canMakeFinalPayment(selectedOrder)" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                    <Link
                        :href="`/orders/${selectedOrder.id}/final-payment`"
                        method="post"
                        class="w-full py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors flex items-center justify-center space-x-2"
                    >
                        <CreditCard class="w-5 h-5" />
                        <span>Payer le solde de {{ formatAmount(getRemainingAmount(selectedOrder)) }}</span>
                    </Link>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        Après paiement, l'agence procédera à la livraison finale du projet
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
