<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  FolderOpen,
  Clock,
  Eye,
  Plus,
  FileText,
  X,
  Edit,
  CheckCircle,
  XCircle,
  Banknote
} from 'lucide-vue-next';
import Nav from '@/Components/Nav.vue';
import Toast from '@/Components/Toastify/Toast.vue';

// Props reçues de Laravel
const props = defineProps({
    projects: {
        type: Array,
        default: () => []
    }
});

// État pour le popup de fermeture de projet
const showCloseModal = ref(false);
const projectToClose = ref(null);

// État pour les détails du projet
const showDetailsModal = ref(false);
const selectedProject = ref(null);

// Fonction pour ouvrir les détails
const openDetails = (project) => {
    selectedProject.value = project;
    showDetailsModal.value = true;
};

// Fonction pour fermer les modals
const closeModal = () => {
    showDetailsModal.value = false;
    selectedProject.value = null;
    projectToClose.value = null;
};

// Fonction pour confirmer la fermeture du projet
const confirmClose = (project) => {
    projectToClose.value = project;
    showCloseModal.value = true;
};

const closeProject = () => {
    router.patch(`/customer/project/${projectToClose.value.id}/close`, {},
        {
            onSuccess: () => {
                showCloseModal.value = false;
                projectToClose.value = null;
            }
        }
    );
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

// Fonction pour formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

// Tronquer le texte
const truncateText = (text, length = 100) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

// Statut du projet
const getStatusBadge = (status) => {
    if (status === 'closed') {
        return { label: 'Fermé', class: 'bg-gray-100 text-gray-600', icon: XCircle };
    }else if (status === 'assigned') {
        return { label: 'Attribué', class: 'bg-blue-100 text-blue-600', icon: CheckCircle };
    }
    return { label: 'Ouvert', class: 'bg-green-100 text-green-600', icon: CheckCircle };
};
</script>

<template>
    <Head title="Mes Projets" />
    <Nav />
    <Toast />
    <!-- Container -->
    <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen mt-20">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                    Mes publications
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Gérez vos projets publiés
                </p>
            </div>
        </div>

        <!-- Grille des projets -->
        <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="project in projects"
                :key="project.id"
                class="bg-white rounded-xl border hover:shadow-lg transition-all duration-300 overflow-hidden group"
                :style="{ borderColor: '#c10e4620' }"
            >
                <!-- En-tête -->
                <div class="p-4 border-b" :style="{ borderColor: '#c10e4620', background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)' }">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2 flex-1">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                            >
                                <FolderOpen class="w-5 h-5" :style="{ color: '#c10e46' }" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-base truncate" :style="{ color: '#1e002f' }">
                                    {{ project.title }}
                                </h3>
                                <p class="text-xs text-gray-500 truncate">
                                    Publié le {{ formatDate(project.created_at) }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center flex-shrink-0 ml-2"
                            :class="getStatusBadge(project.status).class"
                        >
                            <component :is="getStatusBadge(project.status).icon" class="w-3 h-3 mr-1" />
                            <span>{{ getStatusBadge(project.status).label }}</span>
                        </span>
                    </div>
                </div>

                <!-- Corps -->
                <div class="p-4 space-y-3">
                    <!-- Description -->
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Description</p>
                        <p class="text-sm text-gray-700 line-clamp-3">
                            {{ truncateText(project.description, 80) }}
                        </p>
                    </div>

                    <!-- Infos -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="flex items-center space-x-2">
                            <Clock class="w-4 h-4 text-gray-400" />
                            <div>
                                <p class="text-xs text-gray-500">Délai souhaité</p>
                                <p class="text-xs font-medium" :style="{ color: '#1e002f' }">{{ project.delai }} jours</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Banknote class="w-4 h-4 text-gray-400" />
                            <div>
                                <p class="text-xs text-gray-500">Budget</p>
                                <p class="text-xs font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(project.budget) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Catégorie -->
                    <div v-if="project.category" class="pt-1">
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                            {{ project.category.title }}
                        </span>
                    </div>

                    <!-- Cahier des charges -->
                    <div v-if="project.cdc_file" class="pt-2">
                        <a
                            :href="`/storage/${project.cdc_file}`"
                            download
                            class="inline-flex items-center space-x-2 text-xs px-2 py-1 rounded-lg border hover:bg-gray-50 transition-colors"
                            :style="{ borderColor: '#c10e4620', color: '#c10e46' }"
                        >
                            <FileText class="w-3 h-3" />
                            <span>Télécharger le PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-4 border-t bg-gray-50" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex justify-between items-center gap-2">
                        <button
                            @click="openDetails(project)"
                            class="px-3 py-2 rounded-lg border hover:bg-white transition-colors inline-flex items-center space-x-2 flex-1 justify-center"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                            <span class="text-sm" :style="{ color: '#1e002f' }">Voir</span>
                        </button>

                        <Link
                            v-if="project.status !== 'closed'"
                            :href="`/customer/project/${project.id}/edit`"
                            class="px-3 py-2 rounded-lg border hover:bg-white transition-colors inline-flex items-center space-x-2 flex-1 justify-center"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <Edit class="w-4 h-4" :style="{ color: '#c10e46' }" />
                            <span class="text-sm" :style="{ color: '#c10e46' }">Modifier</span>
                        </Link>

                        <button
                            v-if="project.status !== 'closed'"
                            @click="confirmClose(project)"
                            class="px-3 py-2 rounded-lg border hover:bg-white transition-colors inline-flex items-center space-x-2 flex-1 justify-center"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <XCircle class="w-4 h-4 text-gray-500" />
                            <span class="text-sm text-gray-600">Fermer</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucun projet -->
        <div v-else class="text-center py-16">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 mb-6">
                <FolderOpen class="w-12 h-12 text-gray-400" />
            </div>
            <h3 class="text-xl font-semibold mb-2" :style="{ color: '#1e002f' }">Aucun projet publié</h3>
            <p class="text-gray-500 mb-6">Publiez votre premier projet pour recevoir des devis</p>
            <Link
                href="/"
                class="inline-flex items-center space-x-2 px-6 py-3 text-white rounded-xl hover:shadow-lg transition-all duration-200 hover:scale-105 font-semibold shadow-md"
                :style="{
                    background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)'
                }"
            >
                <Plus class="w-4 h-4" />
                <span>Publier un projet</span>
            </Link>
        </div>
    </div>

    <!-- Modal Détails du projet -->
    <div v-if="showDetailsModal && selectedProject" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-2xl sm:w-full">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center"
                            :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                        >
                            <FolderOpen class="w-6 h-6" :style="{ color: '#c10e46' }" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selectedProject.title }}</h3>
                            <p class="text-sm text-gray-500">Publié le {{ formatDate(selectedProject.created_at) }}</p>
                        </div>
                    </div>
                    <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100">
                        <X class="w-5 h-5 text-gray-500" />
                    </button>
                </div>

                <!-- Corps -->
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Statut</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs" :class="getStatusBadge(selectedProject.status).class">
                                <component :is="getStatusBadge(selectedProject.status).icon" class="w-3 h-3 mr-1" />
                                {{ getStatusBadge(selectedProject.status).label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Délai souhaité</p>
                            <p class="font-medium" :style="{ color: '#1e002f' }">{{ selectedProject.delai }} jours</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Budget</p>
                            <p class="font-semibold text-lg" :style="{ color: '#c10e46' }">{{ formatAmount(selectedProject.budget) }}</p>
                        </div>
                        <div v-if="selectedProject.category">
                            <p class="text-xs text-gray-500 mb-1">Catégorie</p>
                            <p class="font-medium" :style="{ color: '#1e002f' }">{{ selectedProject.category.name }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1">Description complète</p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm whitespace-pre-wrap">{{ selectedProject.description }}</p>
                        </div>
                    </div>

                    <div v-if="selectedProject.cdc_file">
                        <p class="text-xs text-gray-500 mb-1">Cahier des charges</p>
                        <a
                            :href="`/storage/${selectedProject.cdc_file}`"
                            download
                            class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg border hover:bg-gray-50 transition-colors"
                            :style="{ borderColor: '#c10e4620', color: '#c10e46' }"
                        >
                            <FileText class="w-4 h-4" />
                            <span>Télécharger le PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end space-x-3 p-6 border-t" :style="{ borderColor: '#c10e4620' }">
                    <button @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation fermeture -->
    <div v-if="showCloseModal && projectToClose" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
        <div class="relative flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                        <XCircle class="h-6 w-6 text-yellow-600" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2" :style="{ color: '#1e002f' }">Fermer ce projet ?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Êtes-vous sûr de vouloir fermer le projet "{{ projectToClose.title }}" ?<br>
                        Une fois fermé, vous ne pourrez plus le modifier et il ne sera plus visible pour les agences.
                    </p>
                    <div class="flex space-x-3">
                        <button @click="closeModal" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button
                            @click="closeProject"
                            class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors"
                        >
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
