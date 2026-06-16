<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  FileText,
  Clock,
  CheckCircle,
  XCircle,
  AlertCircle,
  Eye,
  Download,
  Plus,
  FileCheck,
  X,
  Calendar,
  Send,
  ThumbsDown,
  Currency,
  HandCoins,
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
const selectedDevi = ref(null);
const formData = ref({
    price_agency: '',
    delai_agency: '',
    description_agency: ''
});

// Fonction pour ouvrir le popup
const openDetails = (devi) => {
    selectedDevi.value = devi;
    formData.value = {
        price_agency: devi.price_agency || '',
        delai_agency: devi.delai_agency || '',
        description_agency: devi.description_agency || ''
    };
    showModal.value = true;
};

// Fonction pour fermer le popup
const closeModal = () => {
    showModal.value = false;
    selectedDevi.value = null;
};

// Fonction pour soumettre la proposition (passe le statut à "proposed")
const submitProposal = () => {
    router.put(`/agency/my-devis/${selectedDevi.value.reference}/propose`, {
        price_agency: formData.value.price_agency,
        delai_agency: formData.value.delai_agency,
        description_agency: formData.value.description_agency,
        status: 'proposed'
    }, {
        onSuccess: () => {
            selectedDevi.value.price_agency = formData.value.price_agency;
            selectedDevi.value.delai_agency = formData.value.delai_agency;
            selectedDevi.value.description_agency = formData.value.description_agency;
            selectedDevi.value.status = 'proposed';

            const index = devis.value.findIndex(d => d.reference === selectedDevi.value.reference);
            if (index !== -1) {
                devis.value[index] = { ...selectedDevi.value };
            }
            closeModal();
            window.location.reload();
        },
        onError: (errors) => {
            console.error('Erreur:', errors);
        }
    });
};

// Fonction pour refuser le devis
const refuseDevis = () => {
    if (confirm('Êtes-vous sûr de vouloir refuser ce devis ?')) {
        router.put(`/agency/devis/${selectedDevi.value.reference}`, {
            status: 'refused'
        }, {
            onSuccess: () => {
                selectedDevi.value.status = 'refused';
                const index = devis.value.findIndex(d => d.reference === selectedDevi.value.reference);
                if (index !== -1) {
                    devis.value[index] = { ...selectedDevi.value };
                }
                closeModal();
            }
        });
    }
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
        year: 'numeric'
    });
};

// Formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

const isPending = (status) => status === 'pending';
const hasProposal = (devi) => devi.price_agency || devi.delai_agency || devi.description_agency;
</script>

<template>
    <Head title="Gestion des Devis - Agence" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des devis
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez les demandes et proposez vos offres
                    </p>
                </div>
                <div class="px-4 py-2 rounded-xl font-semibold" :style="{ background: '#c10e4610', color: '#c10e46' }">
                    Total: {{ devis.length }} devis
                </div>
            </div>

            <!-- Liste des devis -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile -->
                <div class="block lg:hidden divide-y">
                    <div v-for="devi in devis" :key="devi.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }">
                                    <FileText class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ devi.title }}</p>
                                    <p class="text-xs text-gray-500">{{ devi.service?.title || '-' }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(devi.status).bg">
                                <component :is="getStatusInfo(devi.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(devi.status).color" />
                                <span :class="getStatusInfo(devi.status).color">{{ getStatusInfo(devi.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">REF</p>
                                <p class="font-medium text-xs font-mono">#{{ devi.reference }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Client</p>
                                <p class="font-medium text-xs">{{ devi.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Date</p>
                                <p class="font-medium text-xs">{{ formatDate(devi.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Délai souhaité</p>
                                <p class="font-medium text-xs">{{ devi.delai }} jours</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Budget</p>
                                <p class="font-medium text-xs">{{formatAmount(devi.budget) }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ devi.description }}</p>

                        <div v-if="hasProposal(devi)" class="mb-3 p-2 rounded-lg" :style="{ background: '#c10e4610' }">
                            <p class="text-xs" :style="{ color: '#c10e46' }">💰 {{ formatAmount(devi.price) }} - ⏱️ {{ devi.delai_agency }} jours</p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button @click="openDetails(devi)" class="p-2 rounded-lg border hover:bg-gray-100" :style="{ borderColor: '#c10e4620' }">
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm">Détails</span>
                            </button>
                            <a v-if="devi.cdc_file" :href="`/storage/${devi.cdc_file}`" download class="p-2 rounded-lg border hover:bg-gray-100" :style="{ borderColor: '#c10e4620' }">
                                <Download class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                <span class="ml-2 text-sm">PDF</span>
                            </a>
                        </div>
                    </div>

                    <div v-if="devis.length === 0" class="p-12 text-center">
                        <FileText class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500">Aucune demande de devis</p>
                    </div>
                </div>

                <!-- Version desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">REF</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Client / Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Date demande</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Délai souhaité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Budget souhaité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Proposition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            <tr v-for="devi in devis" :key="devi.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-mono">{{ devi.reference }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium">{{ devi.user.name || 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ devi.service?.title || '-' }} - {{ devi.title }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ formatDate(devi.created_at) }}</td>
                                <td class="px-6 py-4 text-sm">{{ devi.delai }} jours</td>
                                <td class="px-6 py-4 text-sm">{{ formatAmount(devi.budget) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span v-if="devi.price" :style="{ color: '#c10e46' }" class="font-semibold">{{ formatAmount(devi.price) }}</span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center" :class="getStatusInfo(devi.status).bg">
                                        <component :is="getStatusInfo(devi.status).icon" class="w-3 h-3 mr-1" />
                                        <span>{{ getStatusInfo(devi.status).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click="openDetails(devi)" class="p-2 rounded-lg border hover:bg-gray-100" :style="{ borderColor: '#c10e4620' }">
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <a v-if="devi.cdc_file" :href="`/storage/${devi.cdc_file}`" download class="p-2 rounded-lg border hover:bg-gray-100" :style="{ borderColor: '#c10e4620' }">
                                            <Download class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="devis.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <FileText class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500">Aucune demande de devis</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Détails + Formulaire -->
        <div v-if="showModal && selectedDevi" class="fixed inset-0 z-50">
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
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedDevi.title }}</h3>
                                <p class="text-sm text-gray-500">#{{ selectedDevi.reference }} • Service: {{ selectedDevi.service?.title || '-' }} • Client: {{ selectedDevi.user?.name || 'N/A' }}</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <X class="w-5 h-5 text-gray-500" />
                        </button>
                    </div>

                    <div class="p-6 space-y-6 overflow-y-auto">
                        <!-- Infos statut -->
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getStatusInfo(selectedDevi.status).bg">
                                <component :is="getStatusInfo(selectedDevi.status).icon" class="w-4 h-4" :class="getStatusInfo(selectedDevi.status).color" />
                                <span class="text-sm font-medium" :class="getStatusInfo(selectedDevi.status).color">
                                    {{ getStatusInfo(selectedDevi.status).label }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Calendar class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Demande: {{ formatDate(selectedDevi.created_at) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Clock class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Délai souhaité: {{ selectedDevi.delai }} jours</span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <HandCoins class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Budget: {{ formatAmount(selectedDevi.budget) }}</span>
                            </div>
                        </div>

                        <!-- Demande client -->
                        <div>
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Demande du client</h4>
                            <p class="text-sm bg-gray-50 p-3 rounded-lg">{{ selectedDevi.description }}</p>
                            <a v-if="selectedDevi.cdc_file" :href="`/storage/${selectedDevi.cdc_file}`" download class="inline-flex items-center space-x-2 mt-3 px-3 py-2 rounded-lg border hover:bg-gray-50 transition-colors" :style="{ borderColor: '#c10e4620', color: '#c10e46' }">
                                <FileText class="w-4 h-4" />
                                <span>Télécharger le cahier des charges</span>
                            </a>
                        </div>

                        <!-- Proposition de l'agence -->
                        <div class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <h4 class="font-semibold text-sm uppercase text-gray-500 mb-3">Proposition de l'agence</h4>

                            <!-- Formulaire pour les devis en attente -->
                            <div v-if="isPending(selectedDevi.status)" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Montant (Ar) *</label>
                                        <input type="number" v-model="formData.price_agency" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]" :style="{ borderColor: '#c10e4620' }" placeholder="Ex: 1500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Délai (jours) *</label>
                                        <input type="number" v-model="formData.delai_agency" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]" :style="{ borderColor: '#c10e4620' }" placeholder="Ex: 14">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Description de l'offre</label>
                                    <textarea v-model="formData.description_agency" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]" :style="{ borderColor: '#c10e4620' }" placeholder="Détails de votre proposition..."></textarea>
                                </div>
                                <div class="flex space-x-3 pt-2">
                                    <button @click="submitProposal" class="flex-1 px-4 py-2 rounded-lg text-white font-medium flex items-center justify-center space-x-2 transition-all hover:opacity-90" :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }">
                                        <Send class="w-4 h-4" />
                                        <span>Envoyer la proposition</span>
                                    </button>
                                    <button @click="refuseDevis" class="px-4 py-2 rounded-lg border flex items-center space-x-2 transition-all hover:bg-red-50" :style="{ borderColor: '#dc2626', color: '#dc2626' }">
                                        <ThumbsDown class="w-4 h-4" />
                                        <span>Refuser</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Affichage pour les devis avec proposition -->
                            <div v-else class="space-y-3">
                                <div class="p-3 rounded-lg" :class="selectedDevi.price_agency ? 'bg-green-50' : 'bg-gray-100'">
                                    <p class="text-xs text-gray-500">Montant proposé</p>
                                    <p class="text-xl font-bold" :style="{ color: '#c10e46' }">{{ formatAmount(selectedDevi.price) || '-' }}</p>
                                </div>
                                <div class="p-3 rounded-lg" :class="selectedDevi.delai_agency ? 'bg-blue-50' : 'bg-gray-100'">
                                    <p class="text-xs text-gray-500">Délai proposé</p>
                                    <p class="font-medium">{{ selectedDevi.delai_agency || '-' }} jours</p>
                                </div>
                                <div class="p-3 rounded-lg bg-gray-50">
                                    <p class="text-xs text-gray-500">Détails de l'offre</p>
                                    <p class="text-sm mt-1">{{ selectedDevi.description_agency || 'Aucune description' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages finaux -->
                        <div v-if="selectedDevi.status === 'accepted'" class="bg-green-50 p-4 rounded-lg flex items-center space-x-3">
                            <CheckCircle class="w-5 h-5 text-green-600" />
                            <p class="text-sm text-green-700">Devis accepté par le client !</p>
                        </div>
                        <div v-else-if="selectedDevi.status === 'refused'" class="bg-red-50 p-4 rounded-lg flex items-center space-x-3">
                            <XCircle class="w-5 h-5 text-red-600" />
                            <p class="text-sm text-red-700">Devis refusé</p>
                        </div>
                        <div v-else-if="selectedDevi.status === 'proposed'" class="bg-blue-50 p-4 rounded-lg flex items-center space-x-3">
                            <FileCheck class="w-5 h-5 text-blue-600" />
                            <p class="text-sm text-blue-700">Proposition envoyée, en attente réponse client</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
