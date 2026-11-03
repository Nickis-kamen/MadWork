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
  Plus,
  X,
  Calendar,
  Ban,
  Activity,
  Zap,
  Star,
  Crown,
  RefreshCw
} from 'lucide-vue-next';

// Props reçues de Laravel
const props = defineProps({
    subscriptions: {
        type: Array,
        default: () => []
    },
    agency: {
        type: Object,
        default: () => ({})
    }
});

const subscriptions = ref([...props.subscriptions]);
const showModal = ref(false);
const selectedSubscription = ref(null);

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

// Obtenir les infos du plan
const getPlanInfo = (plan) => {
    const planMap = {
        'basic': { label: 'Basic', icon: Zap, color: 'text-blue-600', bg: 'bg-blue-100' },
        'premium': { label: 'Premium', icon: Star, color: 'text-purple-600', bg: 'bg-purple-100' },
        'pro': { label: 'Pro', icon: Crown, color: 'text-yellow-600', bg: 'bg-yellow-100' }
    };
    return planMap[plan] || { label: plan, icon: Activity, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Obtenir les infos du statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100' },
        'active': { label: 'Actif', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
        'cancelled': { label: 'Annulé', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' },
        'finished': { label: 'Terminé', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'En attente';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Obtenir les infos du paiement
const getPaymentInfo = (paymentStatus) => {
    const paymentMap = {
        'unpaid': { label: 'Non payé', icon: AlertCircle, color: 'text-red-600', bg: 'bg-red-100' },
        'paid': { label: 'Payé', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' }
    };
    return paymentMap[paymentStatus] || { label: paymentStatus, icon: Wallet, color: 'text-gray-600', bg: 'bg-gray-100' };
};

</script>

<template>
    <Head title="Mes Abonnements - Agence" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Mes Abonnements
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez et gérez vos abonnements
                    </p>
                </div>
                <Link href="/agency/subscription" class="px-4 py-2 rounded-xl font-semibold text-white flex items-center gap-2 transition-all hover:opacity-90" :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }">
                    <Plus class="w-4 h-4" />
                    Nouvel abonnement
                </Link>
            </div>

            <!-- Liste des abonnements simplifiée -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="subscription in subscriptions" :key="subscription.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                    <component :is="getPlanInfo(subscription.plan).icon" class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ getPlanInfo(subscription.plan).label }}</p>
                                    <p class="text-xs text-gray-500">{{ subscription.reference }}</p>
                                    <span class="px-2 py-0.5 rounded-full text-xs inline-flex items-center" :class="getPaymentInfo(subscription.payment_status).bg">
                                        <component :is="getPaymentInfo(subscription.payment_status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getPaymentInfo(subscription.payment_status).label }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-sm" :style="{ color: '#c10e46' }">{{ subscription.amount.toLocaleString() }} Ar</p>
                                <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(subscription.status).bg">
                                    <component :is="getStatusInfo(subscription.status).icon" class="w-3 h-3 mr-1" />
                                    <span>{{ getStatusInfo(subscription.status).label }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Début</p>
                                <p class="font-medium text-xs">{{ formatDate(subscription.starts_at) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Fin</p>
                                <p class="font-medium text-xs">{{ formatDate(subscription.ends_at) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button @click="openDetails(subscription)" class="p-2 rounded-lg border hover:bg-gray-100" :style="{ borderColor: '#c10e4620' }">
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm">Détails</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="subscriptions.length === 0" class="p-12 text-center">
                        <CreditCard class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500">Aucun abonnement</p>
                    </div>
                </div>

                <!-- Version desktop simplifiée -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Paiement</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Date début</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Date fin</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            <tr v-for="subscription in subscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <component :is="getPlanInfo(subscription.plan).icon" class="w-4 h-4" :class="getPlanInfo(subscription.plan).color" />
                                        <span class="text-sm font-medium">{{ getPlanInfo(subscription.plan).label }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono">{{ subscription.reference }}</td>
                                <td class="px-6 py-4 text-sm font-semibold" :style="{ color: '#c10e46' }">{{ subscription.amount.toLocaleString() }} Ar</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getPaymentInfo(subscription.payment_status).bg">
                                        <component :is="getPaymentInfo(subscription.payment_status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getPaymentInfo(subscription.payment_status).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(subscription.status).bg">
                                        <component :is="getStatusInfo(subscription.status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getStatusInfo(subscription.status).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ formatDate(subscription.starts_at) }}</td>
                                <td class="px-6 py-4 text-sm">{{ formatDate(subscription.ends_at) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openDetails(subscription)" class="p-2 rounded-lg border hover:bg-gray-100" :style="{ borderColor: '#c10e4620' }">
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="subscriptions.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <CreditCard class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500">Aucun abonnement</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Détails Abonnement -->
        <div v-if="showModal && selectedSubscription" class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b z-10" :style="{ borderColor: '#c10e4620' }">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                <CreditCard class="w-6 h-6" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">
                                    Abonnement {{ getPlanInfo(selectedSubscription.plan).label }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ selectedSubscription.reference }}</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <X class="w-5 h-5 text-gray-500" />
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Infos principales -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 rounded-lg bg-gray-50">
                                <p class="text-xs text-gray-500">Montant</p>
                                <p class="text-xl font-bold" :style="{ color: '#c10e46' }">{{ selectedSubscription.amount.toLocaleString() }} Ar</p>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50">
                                <p class="text-xs text-gray-500">Statut</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <component :is="getStatusInfo(selectedSubscription.status).icon" class="w-4 h-4" :class="getStatusInfo(selectedSubscription.status).color" />
                                    <span class="font-medium" :class="getStatusInfo(selectedSubscription.status).color">
                                        {{ getStatusInfo(selectedSubscription.status).label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 rounded-lg bg-gray-50">
                                <p class="text-xs text-gray-500">Date de début</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    <span class="font-medium">{{ formatDate(selectedSubscription.starts_at) }}</span>
                                </div>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50">
                                <p class="text-xs text-gray-500">Date de fin</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    <span class="font-medium">{{ formatDate(selectedSubscription.ends_at) }}</span>
                                </div>
                            </div>
                        </div>



                        <!-- Message simple -->
                        <div v-if="selectedSubscription.status === 'pending'" class="bg-blue-50 p-3 rounded-lg text-center">
                            <p class="text-sm text-blue-700">En attente de validation</p>
                        </div>
                        <div v-else-if="selectedSubscription.status === 'cancelled'" class="bg-red-50 p-3 rounded-lg text-center">
                            <p class="text-sm text-red-700">Abonnement annulé</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
