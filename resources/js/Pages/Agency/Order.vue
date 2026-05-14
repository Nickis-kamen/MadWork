<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
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
  Save,
  Truck,
  PackageCheck
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
const showDeliveryModal = ref(false);
const isProcessing = ref(false);

// Formulaire de progression
const progressForm = ref({
    status: '',
    progress: 0,
    progress_note: ''
});

// Liste des statuts (sans delivered)
const statusOptions = [
    { value: 'pending', label: 'En attente', icon: Clock, bg: 'bg-yellow-100', text: 'text-yellow-600' },
    { value: 'in_progress', label: 'En cours', icon: TrendingUp, bg: 'bg-blue-100', text: 'text-blue-600' },
    { value: 'completed', label: 'Terminé', icon: CheckCircle, bg: 'bg-green-100', text: 'text-green-600' },
    { value: 'cancelled', label: 'Annulé', icon: XCircle, bg: 'bg-red-100', text: 'text-red-600' }
];

// Vérifier si la commande est livrable (payment_status = 'paid' et status != 'delivered')
const canDeliver = (order) => {
    return order.payment_status === 'paid' && order.status !== 'delivered' && order.status !== 'cancelled';
};

// Vérifier si la commande est verrouillée (déjà livrée)
const isLocked = (order) => {
    return order.status === 'delivered' || order.status === 'completed' || order.status === 'cancelled';
};

// Vérifier si la mise à jour est autorisée
const canUpdate = (order) => {
    return !isLocked(order);
};

// Ouvrir modal de suivi
const openDetails = (order) => {
    if (!canUpdate(order)) {
        alert('Cette commande est verrouillée et ne peut plus être modifiée');
        return;
    }

    selectedOrder.value = order;
    progressForm.value = {
        status: order.status,
        progress: order.progress || 0,
        progress_note: order.progress_note || ''
    };
    showModal.value = true;
};

// Ouvrir modal de livraison
const openDeliveryModal = (order) => {
    selectedOrder.value = order;
    showDeliveryModal.value = true;
};

// Fermer les modals
const closeModal = () => {
    showModal.value = false;
    selectedOrder.value = null;
};

const closeDeliveryModal = () => {
    showDeliveryModal.value = false;
    selectedOrder.value = null;
    isProcessing.value = false;
};

// Mettre à jour la progression
const updateProgress = () => {
    router.put(`/agency/orders/${selectedOrder.value.id}/progress`, {
        status: progressForm.value.status,
        progress: progressForm.value.progress,
        progress_note: progressForm.value.progress_note
    }, {
        onSuccess: () => {
            const index = orders.value.findIndex(o => o.id === selectedOrder.value.id);
            if (index !== -1) {
                orders.value[index] = {
                    ...orders.value[index],
                    status: progressForm.value.status,
                    progress: progressForm.value.progress,
                    progress_note: progressForm.value.progress_note,
                    updated_at: new Date().toISOString()
                };
                selectedOrder.value = { ...orders.value[index] };
            }
            closeModal();
        },
        onError: (errors) => {
            console.error('Erreur:', errors);
            alert('Erreur lors de la mise à jour');
        }
    });
};

// Marquer comme livré
const markAsDelivered = () => {
    isProcessing.value = true;

    router.put(`/agency/orders/${selectedOrder.value.id}/delivered`, {}, {
        onSuccess: () => {
            const index = orders.value.findIndex(o => o.id === selectedOrder.value.id);
            if (index !== -1) {
                orders.value[index] = {
                    ...orders.value[index],
                    status: 'delivered',
                    progress: 100,
                    updated_at: new Date().toISOString()
                };
            }
            closeDeliveryModal();
        },
        onError: (errors) => {
            console.error('Erreur:', errors);
            alert('Erreur lors de la livraison');
            isProcessing.value = false;
        }
    });
};

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
</script>

<template>
    <Head title="Gestion des Commandes" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des commandes
                    </h1>
                    <p class="text-gray-600 mt-1">Suivez et mettez à jour l'avancement de vos projets</p>
                </div>
            </div>

            <!-- Liste des commandes -->
            <div class="bg-white rounded-xl border overflow-hidden">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="order in orders" :key="order.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="font-semibold" :style="{ color: '#1e002f' }">{{ order.reference }}</p>
                                <p class="text-xs text-gray-500">{{ order.service?.title }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(order.status).bg">
                                <component :is="getStatusInfo(order.status).icon" class="w-3 h-3 mr-1" />
                                <span>{{ getStatusInfo(order.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Client</p>
                                <p class="font-medium">{{ order.client?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Montant</p>
                                <p class="font-medium" :style="{ color: '#c10e46' }">{{ formatAmount(order.amount) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Paiement</p>
                                <p class="text-xs font-medium" :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-orange-600'">
                                    {{ order.payment_status === 'paid' ? 'Payé' : 'En Incomplet' }}
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
                        <div class="flex gap-2">
                            <button
                                v-if="canDeliver(order)"
                                @click="openDeliveryModal(order)"
                                class="flex-1 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors inline-flex items-center justify-center space-x-2"
                            >
                                <Truck class="w-4 h-4" />
                                <span>Livrer</span>
                            </button>
                            <button
                                @click="openDetails(order)"
                                :disabled="!canUpdate(order)"
                                class="flex-1 py-2 rounded-lg border hover:bg-gray-100 inline-flex items-center justify-center space-x-2 transition-colors"
                                :class="!canUpdate(order) ? 'opacity-50 cursor-not-allowed border-gray-300' : ''"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                <TrendingUp class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                <span>{{ canUpdate(order) ? 'Mettre à jour' : 'Verrouillé' }}</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="orders.length === 0" class="p-12 text-center">
                        <ShoppingBag class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500">Aucune commande</p>
                    </div>
                </div>

                <!-- Version desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Commande</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Client / Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Paiement</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Progression</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Dernière note</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">
                                    {{ order.reference }}
                                    <p class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium">{{ order.client?.name || 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ order.service?.title }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(order.amount) }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'">
                                        {{ order.payment_status === 'paid' ? 'Payé' : 'Paiement final requis' }}
                                    </span>
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
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-600 truncate max-w-[200px]" :title="order.progress_note">
                                        {{ order.progress_note || '-' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            v-if="canDeliver(order)"
                                            @click="openDeliveryModal(order)"
                                            class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors inline-flex items-center space-x-2"
                                        >
                                            <PackageCheck class="w-4 h-4" />
                                            <span>Livrer</span>
                                        </button>
                                        <button v-else
                                            @click="openDetails(order)"
                                            class="px-3 py-2 rounded-lg border hover:bg-gray-100 inline-flex items-center space-x-2 transition-colors"
                                            :style="{ borderColor: '#c10e4620' }"
                                        >
                                            <Eye class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                            <span>Suivi</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="orders.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <ShoppingBag class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500">Aucune commande</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal mise à jour progression -->
        <div v-if="showModal && selectedOrder" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full">
                    <div class="flex items-center justify-between p-6 border-b">
                        <div>
                            <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedOrder.reference }}</h3>
                            <p class="text-sm text-gray-500">Client: {{ selectedOrder.client?.name }}</p>
                            <p class="text-xs text-gray-400">Service: {{ selectedOrder.service?.title }}</p>
                        </div>
                        <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Statut -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Statut</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    v-for="status in statusOptions"
                                    :key="status.value"
                                    @click="progressForm.status = status.value"
                                    class="px-3 py-2 rounded-lg text-sm font-medium transition-all border-2 flex items-center justify-center space-x-2"
                                    :class="progressForm.status === status.value ? status.bg + ' border-' + status.text : 'border-gray-200 bg-white hover:bg-gray-50'"
                                >
                                    <component :is="status.icon" class="w-4 h-4" />
                                    <span>{{ status.label }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Progression -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Progression (%)</label>
                            <div class="flex items-center space-x-3">
                                <input type="number" v-model="progressForm.progress" min="0" max="100" class="w-24 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]">
                                <input type="range" min="0" max="100" v-model="progressForm.progress" class="flex-1">
                            </div>
                        </div>

                        <!-- Note -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Note (visible par le client)</label>
                            <textarea v-model="progressForm.progress_note" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]" placeholder="Description de l'avancement..."></textarea>
                            <p class="text-xs text-gray-400 mt-1">Le client verra cette note dans le suivi de sa commande</p>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button @click="updateProgress" class="flex-1 px-4 py-2 rounded-lg text-white font-medium flex items-center justify-center space-x-2" :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }">
                                <Save class="w-4 h-4" />
                                <span>Enregistrer</span>
                            </button>
                            <button @click="closeModal" class="px-4 py-2 rounded-lg border hover:bg-gray-50">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de livraison -->
        <div v-if="showDeliveryModal && selectedOrder" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeDeliveryModal"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full">
                    <div class="flex items-center justify-between p-6 border-b">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <Truck class="w-6 h-6 text-green-600" />
                            </div>
                            <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">Confirmation de livraison</h3>
                        </div>
                        <button @click="closeDeliveryModal" class="p-2 rounded-lg hover:bg-gray-100">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm text-green-800">
                                Vous vous apprêtez à marquer cette commande comme <strong>livrée</strong>.
                            </p>
                        </div>

                        <div class="border rounded-lg p-4">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Commande</span>
                                    <span class="font-medium">{{ selectedOrder.reference }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Client</span>
                                    <span class="font-medium">{{ selectedOrder.client?.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Montant total</span>
                                    <span class="font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(selectedOrder.amount) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Statut paiement</span>
                                    <span class="text-green-600 font-medium">Payé</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3 pt-4">
                            <button
                                @click="markAsDelivered"
                                :disabled="isProcessing"
                                class="flex-1 px-4 py-2 rounded-lg text-white font-medium flex items-center justify-center space-x-2 transition-all"
                                :style="{ background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)' }"
                                :class="{ 'opacity-50 cursor-not-allowed': isProcessing }"
                            >
                                <PackageCheck class="w-4 h-4" />
                                <span>{{ isProcessing ? 'Traitement...' : 'Confirmer la livraison' }}</span>
                            </button>
                            <button @click="closeDeliveryModal" class="px-4 py-2 rounded-lg border hover:bg-gray-50">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
