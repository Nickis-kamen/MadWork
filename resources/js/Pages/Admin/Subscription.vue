<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
  CreditCard,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  Check,
  X,
  Calendar,
  Users,
  Building2,
  DollarSign,
  TrendingUp,
  Filter,
  Search,
  RefreshCw,
  Mail,
  Phone,
  MapPin,
  Wallet
} from 'lucide-vue-next';

// Props reçues de Laravel
const props = defineProps({
    subscriptions: {
        type: Array,
        default: () => []
    },
    pendingCount: {
        type: Number,
        default: 0
    },
    totalAmount: {
        type: Number,
        default: 0
    }
});

const subscriptions = ref([...props.subscriptions]);
const showModal = ref(false);
const selectedSubscription = ref(null);
const searchTerm = ref('');
const filterPlan = ref('all');
const filterStatus = ref('all');

// Filtrer les abonnements
const filteredSubscriptions = computed(() => {
    let filtered = subscriptions.value;

    // Filtre par recherche
    if (searchTerm.value) {
        filtered = filtered.filter(sub =>
            sub.reference.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            sub.agency?.name?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            sub.agency?.email?.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }

    // Filtre par plan
    if (filterPlan.value !== 'all') {
        filtered = filtered.filter(sub => sub.plan === filterPlan.value);
    }

    // Filtre par statut
    if (filterStatus.value !== 'all') {
        filtered = filtered.filter(sub => sub.status === filterStatus.value);
    }

    return filtered;
});

// Statistiques
const stats = computed(() => {
    const pending = subscriptions.value.filter(s => s.status === 'pending').length;

    return { pending };
});

// Ouvrir les détails
const openDetails = (subscription) => {
    selectedSubscription.value = subscription;
    showModal.value = true;
};

// Fermer le modal
const closeModal = () => {
    showModal.value = false;
    selectedSubscription.value = null;
};

const validateSubscription = () => {
    if (confirm('Valider cet abonnement ?')) {
        router.put(`/admin/subscriptions/${selectedSubscription.value.reference}/active`, {}, {
            onSuccess: () => {
                selectedSubscription.value.status = 'active';
                const index = subscriptions.value.findIndex(s => s.reference === selectedSubscription.value.reference);
                if (index !== -1) {
                    subscriptions.value[index] = { ...selectedSubscription.value };
                }
                closeModal();
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Erreur:', errors);
            }
        });
    }
};

// Refuser un abonnement
const rejectSubscription = () => {
    const reason = prompt('Motif du refus :');
    if (reason) {
        router.put(`/admin/subscriptions/${selectedSubscription.value.reference}/reject`, { reason }, {
            onSuccess: () => {
                selectedSubscription.value.status = 'cancelled';
                const index = subscriptions.value.findIndex(s => s.reference === selectedSubscription.value.reference);
                if (index !== -1) {
                    subscriptions.value[index] = { ...selectedSubscription.value };
                }
                closeModal();
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Erreur:', errors);
            }
        });
    }
};

// Obtenir les infos du plan
const getPlanInfo = (plan) => {
    const planMap = {
        'basic': {
            label: 'Basic',
            color: 'text-blue-600',
            bg: 'bg-blue-100',
            price: '50.000 Ar'
        },
        'premium': {
            label: 'Premium',
            color: 'text-purple-600',
            bg: 'bg-purple-100',
            price: '150.000 Ar'
        },
        'pro': {
            label: 'Pro',
            color: 'text-yellow-600',
            bg: 'bg-yellow-100',
            price: '300.000 Ar'
        }
    };
    return planMap[plan] || { label: plan, color: 'text-gray-600', bg: 'bg-gray-100', features: [] };
};

// Obtenir les infos du statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100', action: 'validation' },
        'active': { label: 'Actif', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100', action: 'cancel' },
        'cancelled': { label: 'Annulé', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100', action: 'none' },
        'finished': { label: 'Terminé', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100', action: 'none' };
};

// Obtenir les infos du paiement (affichage seulement)
const getPaymentInfo = (paymentStatus) => {
    const paymentMap = {
        'unpaid': { label: 'Non payé', icon: AlertCircle, color: 'text-red-600', bg: 'bg-red-100' },
        'paid': { label: 'Payé', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' }
    };
    return paymentMap[paymentStatus] || { label: paymentStatus, icon: Wallet, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'Non défini';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return 'Non défini';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const isPending = (status,payment_status)  => status === 'pending' && payment_status === 'paid';
</script>

<template>
    <Head title="Gestion des Abonnements - Admin" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Gestion des Abonnements
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Gérez et validez les demandes d'abonnement des agences
                    </p>
                </div>
                <div class="flex gap-2">
                    <div class="px-4 py-2 rounded-xl font-semibold bg-yellow-100 text-yellow-700">
                        En attente: {{ stats.pending }}
                    </div>
                    <button @click="() => window.location.reload()" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 transition-colors">
                        <RefreshCw class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            type="text"
                            v-model="searchTerm"
                            placeholder="Rechercher par référence, agence ou email..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <select v-model="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="active">Actif</option>
                            <option value="cancelled">Annulé</option>
                        </select>
                        <select v-model="filterPlan" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">Tous les plans</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="pro">Pro</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des abonnements -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="subscription in filteredSubscriptions" :key="subscription.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ subscription.agency?.name || 'N/A' }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ subscription.reference }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(subscription.status).bg">
                                <component :is="getStatusInfo(subscription.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(subscription.status).color" />
                                <span :class="getStatusInfo(subscription.status).color">{{ getStatusInfo(subscription.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Plan</p>
                                <p class="font-medium">{{ getPlanInfo(subscription.plan).label }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Montant</p>
                                <p class="font-bold text-green-600">{{ subscription.amount.toLocaleString() }} Ar</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Paiement</p>
                                <span class="px-2 py-0.5 rounded-full text-xs inline-flex items-center" :class="getPaymentInfo(subscription.payment_status).bg">
                                    <component :is="getPaymentInfo(subscription.payment_status).icon" class="w-3 h-3 mr-1" />
                                    <span>{{ getPaymentInfo(subscription.payment_status).label }}</span>
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Demandé le</p>
                                <p class="text-xs">{{ formatDate(subscription.created_at) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button @click="openDetails(subscription)" class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
                                <Eye class="w-4 h-4 inline mr-1" />
                                Détails
                            </button>
                        </div>
                    </div>

                    <div v-if="filteredSubscriptions.length === 0" class="p-12 text-center">
                        <CreditCard class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500">Aucun abonnement trouvé</p>
                    </div>
                </div>

                <!-- Version desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="subscription in filteredSubscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ subscription.agency?.name || 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ subscription.agency?.email || '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ subscription.reference }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getPlanInfo(subscription.plan).bg">
                                        {{ getPlanInfo(subscription.plan).label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">{{ subscription.amount.toLocaleString() }} Ar</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(subscription.status).bg">
                                        <component :is="getStatusInfo(subscription.status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getStatusInfo(subscription.status).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getPaymentInfo(subscription.payment_status).bg">
                                        <component :is="getPaymentInfo(subscription.payment_status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getPaymentInfo(subscription.payment_status).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openDetails(subscription)" class="text-blue-600 hover:text-blue-800">
                                        <Eye class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredSubscriptions.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <CreditCard class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500">Aucun abonnement trouvé</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Détails et Validation -->
        <div v-if="showModal && selectedSubscription" class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b z-10">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <Building2 class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Détails de l'abonnement
                                </h3>
                                <p class="text-sm text-gray-500 font-mono">{{ selectedSubscription.reference }}</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <X class="w-5 h-5 text-gray-500" />
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Infos agence -->
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3 flex items-center gap-2">
                                <Users class="w-4 h-4" />
                                Informations de l'agence
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Nom de l'agence</p>
                                    <p class="font-medium text-gray-900">{{ selectedSubscription.agency?.name || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="font-medium text-gray-900 flex items-center gap-1">
                                        <Mail class="w-3 h-3" />
                                        {{ selectedSubscription.agency?.email || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Téléphone</p>
                                    <p class="font-medium text-gray-900 flex items-center gap-1">
                                        <Phone class="w-3 h-3" />
                                        {{ selectedSubscription.agency?.phone || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Adresse</p>
                                    <p class="font-medium text-gray-900 flex items-center gap-1">
                                        <MapPin class="w-3 h-3" />
                                        {{ selectedSubscription.agency?.address || '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Infos abonnement -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="border rounded-lg p-4">
                                <p class="text-xs text-gray-500 uppercase">Plan souscrit</p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium" :class="getPlanInfo(selectedSubscription.plan).bg">
                                        {{ getPlanInfo(selectedSubscription.plan).label }}
                                    </span>
                                </div>
                            </div>
                            <div class="border rounded-lg p-4">
                                <p class="text-xs text-gray-500 uppercase">Montant</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ selectedSubscription.amount.toLocaleString() }} Ar</p>
                            </div>
                            <div class="border rounded-lg p-4">
                                <p class="text-xs text-gray-500 uppercase">Statut paiement</p>
                                <div class="mt-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium inline-flex items-center gap-2" :class="getPaymentInfo(selectedSubscription.payment_status).bg">
                                        <component :is="getPaymentInfo(selectedSubscription.payment_status).icon" class="w-4 h-4" />
                                        {{ getPaymentInfo(selectedSubscription.payment_status).label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border rounded-lg p-4">
                                <p class="text-xs text-gray-500 uppercase">Statut abonnement</p>
                                <div class="mt-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium inline-flex items-center gap-2" :class="getStatusInfo(selectedSubscription.status).bg">
                                        <component :is="getStatusInfo(selectedSubscription.status).icon" class="w-4 h-4" />
                                        {{ getStatusInfo(selectedSubscription.status).label }}
                                    </span>
                                </div>
                            </div>
                            <div class="border rounded-lg p-4">
                                <p class="text-xs text-gray-500 uppercase">Date de demande</p>
                                <p class="font-medium mt-1">{{ formatDateTime(selectedSubscription.created_at) }}</p>
                            </div>
                        </div>

                        <!-- Période d'abonnement -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Période d'abonnement</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Date de début</p>
                                    <p class="font-medium">{{ formatDate(selectedSubscription.starts_at) || 'Non définie' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Date de fin</p>
                                    <p class="font-medium">{{ formatDate(selectedSubscription.ends_at) || 'Non définie' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions admin -->
                        <div class="border-t pt-4 flex flex-wrap gap-3">
                            <button
                                v-if="isPending(selectedSubscription.status,selectedSubscription.payment_status)"
                                @click="validateSubscription"
                                class="flex-1 px-4 py-2 rounded-lg text-white font-medium flex items-center justify-center gap-2 transition-all hover:opacity-90 bg-gradient-to-r from-green-500 to-green-600"
                            >
                                <CheckCircle class="w-4 h-4" />
                                Valider l'abonnement
                            </button>
                            <button
                                v-if="isPending(selectedSubscription.status,selectedSubscription.payment_status)"
                                @click="rejectSubscription"
                                class="flex-1 px-4 py-2 rounded-lg border border-red-500 text-red-600 font-medium flex items-center justify-center gap-2 transition-all hover:bg-red-50"
                            >
                                <XCircle class="w-4 h-4" />
                                Refuser
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
