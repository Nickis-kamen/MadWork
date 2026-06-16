<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import {
  CreditCard,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  X,
  Search,
  Calendar,
  DollarSign,
  User,
  ShoppingBag,
  Package,
  FileText,
  TrendingUp,
  TrendingDown,
  Wallet,
  Banknote,
  Smartphone,
  ChevronLeft,
  ChevronRight,
  ChevronsLeft,
  ChevronsRight,
  Store
} from 'lucide-vue-next';

// Props
const props = defineProps({
    transactions: {
        type: Array,
        default: () => []
    }
});

const transactions = ref([...props.transactions]);
const showModal = ref(false);
const selectedTransaction = ref(null);

// État pour la recherche et les filtres
const searchQuery = ref('');
const typeFilter = ref('all');
const methodFilter = ref('all');
const statusFilter = ref('all');

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Obtenir infos statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100' },
        'processing': { label: 'En cours', icon: TrendingUp, color: 'text-blue-600', bg: 'bg-blue-100' },
        'completed': { label: 'Complété', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
        'failed': { label: 'Échoué', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' },
        'cancelled': { label: 'Annulé', icon: XCircle, color: 'text-gray-600', bg: 'bg-gray-100' },
        'refunded': { label: 'Remboursé', icon: TrendingDown, color: 'text-red-600', bg: 'bg-red-100' }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Obtenir le type de transaction (avec tous les types)
const getTypeInfo = (type) => {
    const typeMap = {
        'subscription': { label: 'Abonnement', icon: Package, color: 'text-blue-600' },
        'deposit': { label: 'Dépôt', icon: Banknote, color: 'text-indigo-600' },
        'withdrawal': { label: 'Retrait', icon: CreditCard, color: 'text-orange-600' },
        'purchase': { label: 'Achat', icon: ShoppingBag, color: 'text-green-600' },
        'sale': { label: 'Vente', icon: Store, color: 'text-blue-600' }
    };
    return typeMap[type] || { label: type || 'Non défini', icon: CreditCard, color: 'text-gray-600' };
};

// Obtenir le mode de paiement
const getMethodInfo = (method) => {
    const methodMap = {
        'mobile_money': { label: 'Mobile Money', icon: Smartphone },
        'visa': { label: 'Carte bancaire', icon: CreditCard },
        'wallet': { label: 'Portefeuille', icon: Wallet }
    };
    return methodMap[method] || { label: method || 'N/A', icon: CreditCard };
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

// Filtrer les transactions
const filteredTransactions = computed(() => {
    let filtered = transactions.value;

    // Filtre par recherche
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(transaction =>
            transaction.reference?.toLowerCase().includes(query) ||
            transaction.user?.name?.toLowerCase().includes(query) ||
            transaction.user?.email?.toLowerCase().includes(query) ||
            transaction.description?.toLowerCase().includes(query) ||
            transaction.order?.reference?.toLowerCase().includes(query) ||
            transaction.subscription?.title?.toLowerCase().includes(query)
        );
    }

    // Filtre par type
    if (typeFilter.value !== 'all') {
        filtered = filtered.filter(transaction => transaction.type === typeFilter.value);
    }

    // Filtre par statut
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(transaction => transaction.status === statusFilter.value);
    }

    return filtered;
});

// Pagination - transactions paginées
const paginatedTransactions = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredTransactions.value.slice(start, end);
});

// Nombre total de pages
const totalPages = computed(() => {
    return Math.ceil(filteredTransactions.value.length / itemsPerPage.value);
});

// Réinitialiser la page quand les filtres changent
watch([searchQuery, typeFilter, methodFilter, statusFilter], () => {
    currentPage.value = 1;
});

// Changer de page
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// Statistiques
const stats = computed(() => {
    const total = transactions.value.length;
    const pending = transactions.value.filter(t => t.status === 'pending').length;
    const completed = transactions.value.filter(t => t.status === 'completed').length;
    const failed = transactions.value.filter(t => t.status === 'failed').length;
    const payments = transactions.value.filter(t => t.type === 'payment').length;
    const subscriptions = transactions.value.filter(t => t.type === 'subscription').length;
    const commissions = transactions.value.filter(t => t.type === 'commission').length;
    const deposits = transactions.value.filter(t => t.type === 'deposit').length;
    const withdrawals = transactions.value.filter(t => t.type === 'withdrawal').length;

    const totalAmount = transactions.value
        .filter(t => t.status === 'completed')
        .reduce((sum, t) => sum + (t.amount || 0), 0);

    return {
        total,
        pending,
        completed,
        failed,
        payments,
        subscriptions,
        commissions,
        deposits,
        withdrawals,
        totalAmount
    };
});

// Ouvrir modal de détails
const openDetails = (transaction) => {
    selectedTransaction.value = transaction;
    showModal.value = true;
};

// Fermer le modal
const closeModal = () => {
    showModal.value = false;
    selectedTransaction.value = null;
};

</script>

<template>
    <Head title="Gestion des Transactions - Admin" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des transactions
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez toutes les transactions financières
                    </p>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="px-4 py-2 rounded-xl font-semibold" :style="{ background: '#c10e4610', color: '#c10e46' }">
                        Total: {{ stats.total }} transactions
                    </span>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Total</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ stats.total }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">En attente</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">
                            {{ stats.pending }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Complétées</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-600">
                            {{ stats.completed }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Échouées</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">
                            {{ stats.failed }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Abonnements</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-600">
                            {{ stats.subscriptions }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Dépôts</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-600">
                            {{ stats.deposits }}
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-2 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Retraits</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-600">
                            {{ stats.withdrawals }}
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
                            placeholder="Rechercher par référence, client, description, commande, abonnement..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <select
                            v-model="typeFilter"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 bg-white"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        >
                            <option value="all">Tous les types</option>
                            <option value="subscription">Abonnement</option>
                            <option value="deposit">Dépôt</option>
                            <option value="withdrawal">Retrait</option>
                            <option value="purchase">Achat</option>
                            <option value="sale">Vente</option>
                        </select>
                        <select
                            v-model="statusFilter"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 bg-white"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        >
                            <option value="all">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="processing">En cours</option>
                            <option value="completed">Complété</option>
                            <option value="failed">Échoué</option>
                            <option value="cancelled">Annulé</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des transactions -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="transaction in paginatedTransactions" :key="transaction.id" class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                    <component :is="getTypeInfo(transaction.type).icon" class="w-5 h-5" :class="getTypeInfo(transaction.type).color" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ transaction.reference }}</p>
                                    <p class="text-xs text-gray-500">{{ getTypeInfo(transaction.type).label }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(transaction.status).bg">
                                <component :is="getStatusInfo(transaction.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(transaction.status).color" />
                                <span :class="getStatusInfo(transaction.status).color">{{ getStatusInfo(transaction.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Client</p>
                                <p class="font-medium text-xs">{{ transaction.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Montant</p>
                                <p class="font-medium text-xs" :style="{ color: '#c10e46' }">{{ formatAmount(transaction.amount) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Mode</p>
                                <p class="font-medium text-xs flex items-center gap-1">
                                    <component :is="getMethodInfo(transaction.method).icon" class="w-3 h-3" />
                                    {{ getMethodInfo(transaction.method).label }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Date</p>
                                <p class="font-medium text-xs">{{ formatDate(transaction.created_at) }}</p>
                            </div>
                        </div>

                        <div v-if="transaction.description" class="mb-3 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                            {{ transaction.description }}
                        </div>

                        <div class="flex justify-end">
                            <button
                                @click="openDetails(transaction)"
                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm">Détails</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="paginatedTransactions.length === 0" class="p-12 text-center">
                        <CreditCard class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500 text-lg">Aucune transaction trouvée</p>
                        <p class="text-gray-400 text-sm">
                            {{ searchQuery || typeFilter !== 'all' || methodFilter !== 'all' || statusFilter !== 'all' ? 'Aucun résultat pour votre recherche' : 'La liste des transactions est vide' }}
                        </p>
                    </div>
                </div>

                <!-- Version desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence liée</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            <tr @click="openDetails(transaction)" v-for="transaction in paginatedTransactions" :key="transaction.id" class="hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono font-medium" :style="{ color: '#1e002f' }">
                                        {{ transaction.reference }}
                                    </span>
                                    <p class="text-xs text-gray-400">{{ formatDate(transaction.created_at) }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 text-sm">
                                        <component :is="getTypeInfo(transaction.type).icon" class="w-4 h-4" :class="getTypeInfo(transaction.type).color" />
                                        <span :class="getTypeInfo(transaction.type).color">{{ getTypeInfo(transaction.type).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <User class="w-4 h-4 text-gray-400 mr-2" />
                                        <div>
                                            <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ transaction.user?.name || 'N/A' }}</p>
                                            <p class="text-xs text-gray-400">{{ transaction.user?.email || '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold" :style="{ color: transaction.type === 'refund' || transaction.type === 'withdrawal' ? '#dc2626' : '#c10e46' }">
                                        {{ transaction.type === 'refund' || transaction.type === 'withdrawal' ? '- ' : '' }}{{ formatAmount(transaction.amount) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div v-if="transaction.order" class="flex items-center gap-1">
                                            <ShoppingBag class="w-3 h-3 text-gray-400" />
                                            <span class="font-mono text-xs">{{ transaction.order.reference }}</span>
                                        </div>
                                        <div v-else-if="transaction.subscription" class="flex items-center gap-1">
                                            <Package class="w-3 h-3 text-gray-400" />
                                            <span class="text-xs">{{ transaction.subscription.reference }}</span>
                                        </div>
                                        <span v-else class="text-gray-400 text-xs">-</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(transaction.status).bg">
                                        <component :is="getStatusInfo(transaction.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(transaction.status).color" />
                                        <span :class="getStatusInfo(transaction.status).color">{{ getStatusInfo(transaction.status).label }}</span>
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="paginatedTransactions.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <CreditCard class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg">Aucune transaction trouvée</p>
                                    <p class="text-gray-400 text-sm">
                                        {{ searchQuery || typeFilter !== 'all' || methodFilter !== 'all' || statusFilter !== 'all' ? 'Aucun résultat pour votre recherche' : 'La liste des transactions est vide' }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="filteredTransactions.length > 0" class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span>Affichage de 10</span>
                        <span>sur {{ filteredTransactions.length }} transactions</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            @click="goToPage(1)"
                            :disabled="currentPage === 1"
                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <ChevronsLeft class="w-4 h-4" :style="{ color: currentPage === 1 ? '#9ca3af' : '#1e002f' }" />
                        </button>
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <ChevronLeft class="w-4 h-4" :style="{ color: currentPage === 1 ? '#9ca3af' : '#1e002f' }" />
                        </button>

                        <div class="flex items-center gap-1">
                            <span class="px-3 py-1 rounded-lg text-sm font-medium" :style="{ background: '#c10e4610', color: '#c10e46' }">
                                {{ currentPage }}
                            </span>
                            <span class="text-sm text-gray-500">/ {{ totalPages }}</span>
                        </div>

                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <ChevronRight class="w-4 h-4" :style="{ color: currentPage === totalPages ? '#9ca3af' : '#1e002f' }" />
                        </button>
                        <button
                            @click="goToPage(totalPages)"
                            :disabled="currentPage === totalPages"
                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <ChevronsRight class="w-4 h-4" :style="{ color: currentPage === totalPages ? '#9ca3af' : '#1e002f' }" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Détails - Consultation seule -->
        <div v-if="showModal && selectedTransaction" class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b z-10" :style="{ borderColor: '#c10e4620' }">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                <CreditCard class="w-6 h-6" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedTransaction.reference }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ getTypeInfo(selectedTransaction.type).label }} •
                                    {{ getMethodInfo(selectedTransaction.method).label }}
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
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getStatusInfo(selectedTransaction.status).bg">
                                <component :is="getStatusInfo(selectedTransaction.status).icon" class="w-4 h-4" :class="getStatusInfo(selectedTransaction.status).color" />
                                <span class="text-sm font-medium" :class="getStatusInfo(selectedTransaction.status).color">
                                    {{ getStatusInfo(selectedTransaction.status).label }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Calendar class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Date: {{ formatDate(selectedTransaction.created_at) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <DollarSign class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Montant: {{ formatAmount(selectedTransaction.amount) }}</span>
                            </div>
                        </div>

                        <!-- Informations générales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <div class="space-y-3">
                                <h4 class="font-semibold text-sm uppercase text-gray-500">Informations transaction</h4>

                                <div class="flex items-start space-x-3">
                                    <component :is="getTypeInfo(selectedTransaction.type).icon" class="w-5 h-5 text-gray-400 mt-0.5" :class="getTypeInfo(selectedTransaction.type).color" />
                                    <div>
                                        <p class="text-xs text-gray-500">Type</p>
                                        <p class="text-sm font-medium" :class="getTypeInfo(selectedTransaction.type).color">
                                            {{ getTypeInfo(selectedTransaction.type).label }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <component :is="getMethodInfo(selectedTransaction.method).icon" class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Mode de paiement</p>
                                        <p class="text-sm font-medium" :style="{ color: '#1e002f' }">
                                            {{ getMethodInfo(selectedTransaction.method).label }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="selectedTransaction.number" class="flex items-start space-x-3">
                                    <FileText class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Numéro de transaction</p>
                                        <p class="text-sm font-mono" :style="{ color: '#1e002f' }">{{ selectedTransaction.number }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedTransaction.payment_stage" class="flex items-start space-x-3">
                                    <TrendingUp class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Étape de paiement</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedTransaction.payment_stage }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <h4 class="font-semibold text-sm uppercase text-gray-500">Client</h4>

                                <div class="flex items-start space-x-3">
                                    <User class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Nom</p>
                                        <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ selectedTransaction.user?.name || 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <Mail class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedTransaction.user?.email || 'Non disponible' }}</p>
                                    </div>
                                </div>

                                <div v-if="selectedTransaction.user?.phone" class="flex items-start space-x-3">
                                    <Phone class="w-5 h-5 text-gray-400 mt-0.5" />
                                    <div>
                                        <p class="text-xs text-gray-500">Téléphone</p>
                                        <p class="text-sm" :style="{ color: '#1e002f' }">{{ selectedTransaction.user.phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Éléments liés -->
                        <div v-if="selectedTransaction.order || selectedTransaction.subscription || selectedTransaction.devis_id" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Éléments liés</h4>
                            <div class="space-y-2">
                                <div v-if="selectedTransaction.order" class="p-3 rounded-lg bg-gray-50 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <ShoppingBag class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm font-medium" :style="{ color: '#1e002f' }">Commande: {{ selectedTransaction.order.reference }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ formatAmount(selectedTransaction.order.amount) }}</span>
                                </div>

                                <div v-if="selectedTransaction.subscription" class="p-3 rounded-lg bg-gray-50 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <Package class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm font-medium" :style="{ color: '#1e002f' }">Abonnement: {{ selectedTransaction.subscription.title }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">Plan: {{ selectedTransaction.plan || 'Standard' }}</span>
                                </div>

                                <div v-if="selectedTransaction.devis_id" class="p-3 rounded-lg bg-gray-50">
                                    <div class="flex items-center gap-2">
                                        <FileText class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm font-medium" :style="{ color: '#1e002f' }">Devis #{{ selectedTransaction.devis_id }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="selectedTransaction.description" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Description</h4>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-sm">{{ selectedTransaction.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
