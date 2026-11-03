<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
  ShoppingBag,
  Clock,
  CheckCircle,
  Package,
  HandCoins,
  ArrowUp,
  ArrowDown,
  Wallet,
  Store,
  AlertCircle,
  XCircle,
  Users,
  TrendingUp,
  Building,
  CreditCard
} from 'lucide-vue-next';

// Props reçues du contrôleur
const props = defineProps({
  // Statistiques principales
  stats: {
    type: Object,
    default: () => ({
      balance: 0,
      activeServices: 0,
      pendingQuotes: 0,
      totalAgencies: 0,
      totalCustomers: 0
    })
  },
  // CA et revenus
  revenue: {
    type: Object,
    default: () => ({
      totalCA: 0,
      monthlyCA: 0,
      totalRevenue: 0,
      monthlyRevenue: 0,
      pendingPayments: 0
    })
  },
  // Statistiques projets
  projectStats: {
    type: Object,
    default: () => ({
      inProgress: 0,
      completed: 0,
      delivered: 0
    })
  },
  // Agences
  agencies: {
    type: Array,
    default: () => []
  },
  // Dernières transactions
  transactions: {
    type: Array,
    default: () => []
  },
  // Informations utilisateur
  user: {
    type: Object,
    default: () => ({
      name: 'Utilisateur'
    })
  }
});

// Configuration des statistiques avec données dynamiques
const statsConfig = [
  {
    title: 'Chiffre d\'affaires total',
    value: formatAmount(props.revenue.totalCA),
    icon: TrendingUp,
    color: '#c10e46'
  },
  {
    title: 'CA du mois',
    value: formatAmount(props.revenue.monthlyCA),
    icon: HandCoins,
    color: '#1e002f',
    subtitle: 'Dernier mois'
  },
  {
    title: 'Agences actives',
    value: props.stats.totalAgencies,
    icon: Building,
    color: '#c10e46',
    subtitle: 'Partenaires'
  },
  {
    title: 'Clients total',
    value: props.stats.totalCustomers,
    icon: Users,
    color: '#1e002f',
    subtitle: 'Utilisateurs'
  }
];

// Configuration des indicateurs secondaires
const secondaryStats = [
  {
    title: 'Revenu total',
    value: formatAmount(props.revenue.totalRevenue),
    icon: HandCoins,
    color: '#c10e46',
    bg: '#c10e4610'
  },
  {
    title: 'Revenu du mois',
    value: formatAmount(props.revenue.monthlyRevenue),
    icon: HandCoins,
    color: '#c10e46',
    bg: '#c10e4610'
  },
  {
    title: 'Services actifs',
    value: props.stats.activeServices,
    icon: ShoppingBag,
    color: '#c10e46',
    bg: '#c10e4610'
  },
  {
    title: 'Paiements en attente',
    value: props.revenue.pendingPayments || 0,
    icon: CreditCard,
    color: '#c10e46',
    bg: '#c10e4610'
  }
];

// Configuration des projets avec données dynamiques
const projectStatsConfig = [
  {
    title: 'Projets en cours',
    value: props.projectStats.inProgress,
    icon: Clock,
    color: '#c10e46',
    bg: '#c10e4610'
  },
  {
    title: 'Projets terminés',
    value: props.projectStats.completed,
    icon: CheckCircle,
    color: '#1e002f',
    bg: '#1e002f10'
  },
  {
    title: 'Projets livrés',
    value: props.projectStats.delivered,
    icon: Package,
    color: '#c10e46',
    bg: '#c10e4610'
  }
];

// Formater la date
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

// Obtenir les infos du type de transaction
const getTransactionTypeInfo = (type) => {
    const typeMap = {
        'deposit': { label: 'Dépôt', icon: ArrowUp, color: 'text-green-600', bg: 'bg-green-100' },
        'withdrawal': { label: 'Retrait', icon: ArrowDown, color: 'text-red-600', bg: 'bg-red-100' },
        'purchase': { label: 'Achat', icon: ShoppingBag, color: 'text-blue-600', bg: 'bg-blue-100' },
        'sale': { label: 'Vente', icon: Store, color: 'text-purple-600', bg: 'bg-purple-100' },
        'subscription': { label: 'Abonnement', icon: Wallet, color: 'text-orange-600', bg: 'bg-orange-100' }
    };
    return typeMap[type] || { label: type, icon: Wallet, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Obtenir les infos du statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: AlertCircle, color: 'text-yellow-600', bg: 'bg-yellow-100' },
        'completed': { label: 'Complété', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
        'failed': { label: 'Échoué', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' },
        'cancelled': { label: 'Annulé', icon: XCircle, color: 'text-gray-600', bg: 'bg-gray-100' }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Formater le montant
function formatAmount(amount) {
    if (!amount && amount !== 0) return '0 Ar';
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}
</script>

<template>
    <Head title="Dashboard Admin" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" style="color: #1e002f">
                        Bonjour, {{ user.name }}
                    </h1>
                    <p class="text-gray-600 mt-1">Dashboard administrateur</p>
                </div>
                <div class="flex gap-3">
                    <Link href="/admin/agencies" class="px-4 py-2 border rounded-lg hover:shadow-md transition" style="border-color: #c10e4620; color: #c10e46">
                        Gérer les agences
                    </Link>
                    <Link href="/admin/transactions" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #c10e46 0%, #1e002f 100%)">
                        Voir toutes les transactions
                    </Link>
                </div>
            </div>

            <!-- Stats principales (CA + indicateurs clés) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="stat in statsConfig"
                    :key="stat.title"
                    class="bg-white p-5 rounded-xl border hover:shadow-lg transition-all"
                    :style="{ borderColor: stat.color + '20' }"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">{{ stat.title }}</p>
                            <p class="text-2xl font-bold mt-1" style="color: #1e002f">
                                {{ stat.value }}
                            </p>
                            <p v-if="stat.subtitle" class="text-xs mt-1" :style="{ color: stat.color }">
                                {{ stat.subtitle }}
                            </p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-xl flex items-center justify-center"
                            :style="{ background: `linear-gradient(135deg, ${stat.color}15 0%, ${stat.color}30 100%)` }"
                        >
                            <component :is="stat.icon" class="w-5 h-5" :style="{ color: stat.color }" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicateurs secondaires -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div
                    v-for="stat in secondaryStats"
                    :key="stat.title"
                    class="bg-white p-5 rounded-xl border hover:shadow-lg transition-all"
                    style="border-color: #c10e4620"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">{{ stat.title }}</p>
                            <p class="text-2xl font-bold mt-1" style="color: #1e002f">
                                {{ stat.value }}
                            </p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-xl flex items-center justify-center"
                            :style="{ background: stat.bg }"
                        >
                            <component :is="stat.icon" class="w-5 h-5" :style="{ color: stat.color }" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projets + Agences -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Statistiques projets -->
                <div class="bg-white rounded-xl border" style="border-color: #c10e4620">
                    <div class="p-5 border-b" style="border-color: #c10e4620">
                        <h3 class="text-lg font-semibold" style="color: #1e002f">Suivi des projets</h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <div
                                v-for="stat in projectStatsConfig"
                                :key="stat.title"
                                class="flex items-center justify-between p-3 rounded-lg border hover:shadow-sm transition"
                                style="border-color: #c10e4620"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-xl flex items-center justify-center"
                                        :style="{ background: stat.bg }"
                                    >
                                        <component :is="stat.icon" class="w-5 h-5" :style="{ color: stat.color }" />
                                    </div>
                                    <p class="font-medium" style="color: #1e002f">{{ stat.title }}</p>
                                </div>
                                <span class="text-xl font-bold" style="color: #1e002f">{{ stat.value }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agences -->
                <div class="lg:col-span-2 bg-white rounded-xl border" style="border-color: #c10e4620">
                    <div class="p-5 border-b flex justify-between items-center" style="border-color: #c10e4620">
                        <h3 class="text-lg font-semibold" style="color: #1e002f">Dernières agences créées</h3>
                        <Link href="/admin/agencies" class="text-sm font-medium" style="color: #c10e46">
                            Voir tout
                        </Link>
                    </div>
                    <div class="p-5">
                        <div v-if="agencies.length > 0" class="space-y-3">
                            <div
                                v-for="agency in agencies.slice(0, 5)"
                                :key="agency.id"
                                class="flex items-center justify-between p-3 rounded-lg border hover:shadow-sm transition"
                                style="border-color: #c10e4620"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden"
                                        style="background: linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)"
                                    >
                                        <img
                                            v-if="agency.image"
                                            :src="`/storage/${agency.image}`"
                                            alt="Agency"
                                            class="w-10 h-10 object-cover rounded"
                                        />
                                        <Building v-else class="w-5 h-5" style="color: #c10e46" />
                                    </div>
                                    <div>
                                        <p class="font-semibold" style="color: #1e002f">{{ agency.name }}</p>
                                        <p class="text-sm text-gray-600">{{ agency.email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500">{{ agency.created_at ? formatDate(agency.created_at) : '' }}</span>
                                    <span class="block text-xs font-medium text-green-600">Actif</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <Building class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                            <p>Aucune agence inscrite</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières transactions -->
            <div class="bg-white rounded-xl border" style="border-color: #c10e4620">
                <div class="p-5 border-b flex justify-between items-center" style="border-color: #c10e4620">
                    <h3 class="text-lg font-semibold" style="color: #1e002f">Dernières transactions</h3>
                    <Link href="/admin/transactions" class="text-sm font-medium" style="color: #c10e46">
                        Voir tout →
                    </Link>
                </div>
                <div class="p-5">
                    <div v-if="transactions.length > 0" class="space-y-3">
                        <div
                            v-for="transaction in transactions.slice(0, 5)"
                            :key="transaction.id"
                            class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 cursor-pointer"
                        >
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 rounded-xl flex items-center justify-center"
                                    :class="getTransactionTypeInfo(transaction.type).bg"
                                >
                                    <component
                                        :is="getTransactionTypeInfo(transaction.type).icon"
                                        class="w-5 h-5"
                                        :class="getTransactionTypeInfo(transaction.type).color"
                                    />
                                </div>
                                <div>
                                    <p class="font-medium text-sm" style="color: #1e002f">
                                        {{ getTransactionTypeInfo(transaction.type).label }}
                                        <span v-if="transaction.user" class="text-xs text-gray-500 ml-1">
                                            - {{ transaction.user.name }} - {{ transaction.user.email }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ transaction.reference }} • {{ formatDate(transaction.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span
                                    class="font-medium"
                                    :class="transaction.type === 'deposit' || transaction.type === 'sale' ? 'text-green-600' :
                                            transaction.type === 'withdrawal' || transaction.type === 'purchase' ? 'text-red-600' :
                                            'text-blue-600'"
                                >
                                    {{ transaction.type === 'deposit' || transaction.type === 'sale' ? '+' :
                                    transaction.type === 'withdrawal' || transaction.type === 'purchase' ? '-' : '' }}
                                    {{ formatAmount(transaction.amount) }}
                                </span>
                                <span
                                    class="text-sm font-medium px-2 py-1 rounded-full"
                                    :class="getStatusInfo(transaction.status).bg"
                                >
                                    {{ getStatusInfo(transaction.status).label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>Aucune transaction récente</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
