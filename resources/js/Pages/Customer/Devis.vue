<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
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
} from 'lucide-vue-next';

// Props reçues de Laravel
const props = defineProps({
    devis: {
        type: Array,
        default: () => []
    }
});

// État pour le popup
const showModal = ref(false);
const selectedDevi = ref(null);

// Fonction pour ouvrir le popup
const openDetails = (devi) => {
    selectedDevi.value = devi;
    showModal.value = true;
};

// Fonction pour fermer le popup
const closeModal = () => {
    showModal.value = false;
    selectedDevi.value = null;
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

// Vérifier si le devis est en attente
const isPending = (status) => status === 'pending';
</script>

<template>
    <Head title="Mes Devis" />
    <AuthenticatedLayout>
        <!-- Container -->
        <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen">
            <!-- Header avec bouton nouveau devis -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Mes devis
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Consultez l'historique de vos demandes de devis
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        href="/devis"
                        class="px-4 py-2 text-white rounded-xl hover:shadow-lg transition-all duration-200 hover:scale-105 font-semibold shadow-md flex items-center space-x-2"
                        :style="{
                            background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)'
                        }"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Nouveau devis</span>
                    </Link>
                </div>
            </div>

            <!-- Liste des devis -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <!-- Version mobile (cartes) -->
                <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                    <div
                        v-for="devi in devis"
                        :key="devi.id"
                        class="p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 rounded-lg flex items-center justify-center"
                                    :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                                >
                                    <FileText class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ devi.title }}</p>
                                    <p class="text-xs text-gray-500">{{ devi.service_type }}</p>
                                </div>
                            </div>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                :class="getStatusInfo(devi.status).bg"
                            >
                                <component :is="getStatusInfo(devi.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(devi.status).color" />
                                <span :class="getStatusInfo(devi.status).color">{{ getStatusInfo(devi.status).label }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <p class="text-gray-500 text-xs">Date demande</p>
                                <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ formatDate(devi.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Délai souhaité</p>
                                <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ devi.delai }} jours</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">ID</p>
                                <p class="font-medium text-xs font-mono" :style="{ color: '#1e002f' }">#{{ devi.id }}</p>
                            </div>
                            <div v-if="devi.price && !isPending(devi.status)">
                                <p class="text-gray-500 text-xs">Montant</p>
                                <p class="font-medium text-xs" :style="{ color: '#c10e46' }">{{ devi.price }} €</p>
                            </div>
                        </div>

                        <!-- Description courte -->
                        <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ devi.description }}</p>

                        <div class="flex justify-end space-x-2">
                            <button
                                @click="openDetails(devi)"
                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620' }"
                                title="Voir détails"
                            >
                                <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                <span class="ml-2 text-sm" :style="{ color: '#1e002f' }">Détails</span>
                            </button>
                            <a
                                v-if="devi.cdc_file"
                                :href="`/storage/${devi.cdc_file}`"
                                download
                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620' }"
                                title="Télécharger le cahier des charges"
                            >
                                <Download class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                <span class="ml-2 text-sm" :style="{ color: '#c10e46' }">PDF</span>
                            </a>
                            <Link
                                v-if="devi.status === 'proposed'"
                                :href="`/devis/${devi.id}/reponse`"
                                class="px-3 py-2 text-xs rounded-lg border hover:bg-gray-50 transition-colors inline-flex items-center"
                                :style="{ borderColor: '#c10e4620', color: '#c10e46' }"
                            >
                                Répondre
                            </Link>
                        </div>
                    </div>

                    <!-- Message si aucun devis (mobile) -->
                    <div v-if="devis.length === 0" class="p-12 text-center">
                        <FileText class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                        <p class="text-gray-500 text-lg mb-2">Aucun devis pour le moment</p>
                        <p class="text-gray-400 text-sm mb-6">Vous n'avez pas encore fait de demande de devis</p>
                        <Link
                            href="/devis/creer"
                            class="inline-flex items-center space-x-2 px-6 py-3 text-white rounded-xl hover:shadow-lg transition-all duration-200 hover:scale-105 font-semibold shadow-md"
                            :style="{
                                background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)'
                            }"
                        >
                            <Plus class="w-4 h-4" />
                            <span>Créer votre premier devis</span>
                        </Link>
                    </div>
                </div>

                <!-- Version desktop (tableau) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                        <!-- En-tête -->
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre / Service</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date demande</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Délai souhaité</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget souhaité</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <!-- Corps du tableau -->
                        <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                            <tr v-for="devi in devis" :key="devi.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" :style="{ color: '#1e002f' }">
                                    #{{ devi.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ devi.title }}</p>
                                    <p class="text-xs text-gray-500">{{ devi.service.title }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ formatDate(devi.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ devi.delai }} jours
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ devi.budget }} Ar
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span v-if="devi.price && !isPending(devi.status)" :style="{ color: '#c10e46' }" class="font-semibold">
                                        {{ devi.price }} Ar
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                        :class="getStatusInfo(devi.status).bg"
                                    >
                                        <component :is="getStatusInfo(devi.status).icon" class="w-3 h-3 mr-1" :class="getStatusInfo(devi.status).color" />
                                        <span :class="getStatusInfo(devi.status).color">{{ getStatusInfo(devi.status).label }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button
                                            @click="openDetails(devi)"
                                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Voir détails"
                                        >
                                            <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                        </button>
                                        <a
                                            v-if="devi.cdc_file"
                                            :href="`/storage/${devi.cdc_file}`"
                                            download
                                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Télécharger le cahier des charges"
                                        >
                                            <Download class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <!-- Message si aucun devis (desktop) -->
                            <tr v-if="devis.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <FileText class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg mb-2">Aucun devis pour le moment</p>
                                    <p class="text-gray-400 text-sm mb-6">Vous n'avez pas encore fait de demande de devis</p>
                                    <Link
                                        href="/devis"
                                        class="inline-flex items-center space-x-2 px-6 py-3 text-white rounded-xl hover:shadow-lg transition-all duration-200 hover:scale-105 font-semibold shadow-md"
                                        :style="{
                                            background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)'
                                        }"
                                    >
                                        <Plus class="w-4 h-4" />
                                        <span>Demander un devis</span>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal / Popup pour les détails du devis -->
        <div v-if="showModal && selectedDevi" class="flex items-center justify-center fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

                <!-- Modal content -->
                <div class="bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                    <!-- Header modal -->
                    <div class="flex items-center justify-between p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center"
                                :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                            >
                                <FileText class="w-6 h-6" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedDevi.title }}</h3>
                                <p class="text-sm text-gray-500">Devis #{{ selectedDevi.id }} • {{ selectedDevi.service.title }}</p>
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
                        <!-- Statut et date -->
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
                        </div>

                        <!-- Grille d'information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Colonne gauche : Infos client/demande -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Votre demande</h4>

                                <!-- Description -->
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Description</p>
                                    <p class="text-sm bg-gray-50 p-3 rounded-lg" :style="{ color: '#1e002f' }">{{ selectedDevi.description }}</p>
                                </div>

                                <!-- Cahier des charges -->
                                <div v-if="selectedDevi.cdc_file">
                                    <p class="text-xs text-gray-500 mb-1">Cahier des charges</p>
                                    <a
                                        :href="`/storage/${selectedDevi.cdc_file}`"
                                        download
                                        class="inline-flex items-center space-x-2 px-3 py-2 rounded-lg border hover:bg-gray-50 transition-colors"
                                        :style="{ borderColor: '#c10e4620', color: '#c10e46' }"
                                    >
                                        <FileText class="w-4 h-4" />
                                        <span class="text-sm">Télécharger le PDF</span>
                                        <Download class="w-4 h-4" />
                                    </a>
                                </div>
                            </div>

                            <!-- Colonne droite : Réponse agence -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Réponse de l'équipe</h4>

                                <!-- Délai agence -->
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Délai proposé</p>
                                    <div
                                        class="p-3 rounded-lg"
                                        :class="{
                                            'bg-gray-100 text-gray-400': isPending(selectedDevi.status),
                                            'bg-blue-50': !isPending(selectedDevi.status) && selectedDevi.delai_agency
                                        }"
                                    >
                                        <p v-if="selectedDevi.delai_agency && !isPending(selectedDevi.status)" class="text-sm" :style="{ color: '#1e002f' }">
                                            {{ selectedDevi.delai_agency }} jours
                                        </p>
                                        <p v-else class="text-sm text-gray-400 italic">
                                            En attente de réponse
                                        </p>
                                    </div>
                                </div>

                                <!-- Prix -->
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Montant proposé</p>
                                    <div
                                        class="p-3 rounded-lg"
                                        :class="{
                                            'bg-gray-100 text-gray-400': isPending(selectedDevi.status),
                                            'bg-green-50': !isPending(selectedDevi.status) && selectedDevi.price_agency
                                        }"
                                    >
                                        <p v-if="selectedDevi.price && !isPending(selectedDevi.status)" class="text-sm font-semibold" :style="{ color: '#c10e46' }">
                                            {{ selectedDevi.price }} Ar
                                        </p>
                                        <p v-else class="text-sm text-gray-400 italic">
                                            Non défini
                                        </p>
                                    </div>
                                </div>

                                <!-- Description agence -->
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Commentaire de l'agence</p>
                                    <div
                                        class="p-3 rounded-lg min-h-[80px]"
                                        :class="{
                                            'bg-gray-100 text-gray-400': isPending(selectedDevi.status),
                                            'bg-gray-50': !isPending(selectedDevi.status) && selectedDevi.description_agency
                                        }"
                                    >
                                        <p v-if="selectedDevi.description_agency && !isPending(selectedDevi.status)" class="text-sm" :style="{ color: '#1e002f' }">
                                            {{ selectedDevi.description_agency }}
                                        </p>
                                        <p v-else class="text-sm text-gray-400 italic">
                                            Aucun commentaire pour le moment
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions du devis -->
                        <div v-if="selectedDevi.status === 'proposed'" class="border-t pt-4 flex justify-end space-x-3" :style="{ borderColor: '#c10e4620' }">
                            <Link
                                :href="`/customer/devis/${selectedDevi.id}/accept`"
                                method="post"
                                as="button"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                            >
                                Accepter le devis
                            </Link>
                            <Link
                                :href="`/customer/devis/${selectedDevi.id}/refuse`"
                                method="post"
                                as="button"
                                class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors text-sm font-medium"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                Refuser
                            </Link>
                        </div>

                        <div v-else-if="selectedDevi.status === 'accepted'" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <div class="bg-green-50 p-4 rounded-lg flex items-center space-x-3">
                                <CheckCircle class="w-5 h-5 text-green-600" />
                                <p class="text-sm text-green-700">
                                    Devis accepté. Vous serez contacté rapidement pour démarrer le projet.
                                </p>
                            </div>
                        </div>

                        <div v-else-if="selectedDevi.status === 'refused'" class="border-t pt-4" :style="{ borderColor: '#c10e4620' }">
                            <div class="bg-red-50 p-4 rounded-lg flex items-center space-x-3">
                                <XCircle class="w-5 h-5 text-red-600" />
                                <p class="text-sm text-red-700">
                                    Devis refusé. N'hésitez pas à nous contacter pour discuter d'une alternative.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </AuthenticatedLayout>
</template>
