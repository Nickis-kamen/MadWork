<script setup>
import Nav from '@/Components/Nav.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { Phone, Mail, MapPin, Briefcase, Calendar, Clock, DollarSign, FileText, X, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const user = computed(() => usePage().props.auth.user);
const isAgency = computed(() => user.value?.role === 'agency');

const props = defineProps({
    project: {
        type: Object,
        required: true
    },
    existingQuotation: {
        type: Object,
        default: null
    }
});

// État pour le popup
const showModal = ref(false);
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref(null);

// Formulaire de proposition
const form = useForm({
    price: '',
    delai_agency: '',
    description_agency: '',
});

// Ouvrir le popup
const openModal = () => {
    showModal.value = true;
    submitSuccess.value = false;
    submitError.value = null;
    form.reset();
};

// Fermer le popup
const closeModal = () => {
    showModal.value = false;
    form.reset();
};

// Soumettre la proposition
const submitProposal = () => {
    form.post(`/agency/project/${props.project.id}/proposal`, {
        preserveScroll: true,
        onStart: () => {
            isSubmitting.value = true;
            submitError.value = null;
        },
        onSuccess: () => {
            isSubmitting.value = false;
            submitSuccess.value = true;
            setTimeout(() => {
                closeModal();
                router.reload();
            }, 2000);
        },
        onError: (errors) => {
            isSubmitting.value = false;
            submitError.value = errors;
        },
    });
};

const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
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

// Statut du projet
const getStatusBadge = (status) => {
    const statusMap = {
        'open': { label: 'Ouvert', class: 'bg-green-100 text-green-700' },
        'assigned': { label: 'Attribué', class: 'bg-blue-100 text-blue-700' },
        'closed': { label: 'Fermé', class: 'bg-gray-100 text-gray-700' }
    };
    return statusMap[status] || { label: status, class: 'bg-gray-100 text-gray-700' };
};

// Vérifier si une proposition existe déjà
const hasProposal = computed(() => {
    return props.existingQuotation !== null;
});
</script>

<template>
    <Head>
        <title>{{ project.title }}</title>
    </Head>
    <Nav />
    <div class="bg-[url('@/assets/images/bg-header.jpg')] bg-cover bg-fixed bg-center pt-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Fil d'Ariane -->
            <div class="flex items-center gap-2 text-sm text-white mb-6">
                <Link href="/projects" class="hover:text-purple-600">Projets</Link>
                <span>/</span>
                <span class="text-white">{{ project.title }}</span>
            </div>

            <div class="max-w-4xl m-auto bg-white rounded-xl border"
                :style="{ borderColor: '#c10e4620' }">
                <!-- En-tête de la carte -->
                <div class="p-7 border-b" :style="{ borderColor: '#c10e4620', background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)' }">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center space-x-2 flex-1">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                            >
                                <Briefcase class="w-5 h-5" :style="{ color: '#c10e46' }" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-base truncate" :style="{ color: '#1e002f' }">
                                    {{ project.title }}
                                </h3>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ project.category?.title || 'Non catégorisé' }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="px-4 py-1 rounded-full font-medium inline-flex items-center flex-shrink-0 ml-2"
                            :class="getStatusBadge(project.status).class"
                        >
                            {{ getStatusBadge(project.status).label }}
                        </span>
                    </div>
                </div>

                <!-- Corps de la carte -->
                <div class="p-8 space-y-6">
                    <!-- Client -->
                    <div class="flex items-center gap-3">
                        <img v-if="project.user?.image" :src="`/storage/${project.user?.image}`" alt="Photo de profil" class="w-12 h-12 rounded-full object-cover">
                        <div v-else class="w-12 h-12 rounded-full bg-gradient-to-r from-[#c10e46] to-[#1e002f] flex items-center justify-center text-white text-base font-bold">
                            {{ project.user?.name?.charAt(0) || 'C' }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Publié par</p>
                            <p class="text-base font-medium" :style="{ color: '#1e002f' }">{{ project.user?.name || 'Client' }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Description du projet</p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">
                                {{ project.description }}
                            </p>
                        </div>
                    </div>

                    <!-- Informations clés -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <Calendar class="w-4 h-4 text-gray-500" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Date de publication</p>
                                <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ formatDate(project.created_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <Clock class="w-4 h-4 text-gray-500" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Délai souhaité</p>
                                <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ project.delai }} jours</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <DollarSign class="w-4 h-4 text-gray-500" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Budget indicatif</p>
                                <p class="text-base font-semibold" :style="{ color: '#c10e46' }">{{ formatAmount(project.budget) }}</p>
                            </div>
                        </div>
                        <div v-if="project.cdc_file" class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <FileText class="w-4 h-4 text-gray-500" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Cahier des charges</p>
                                <a
                                    :href="`/storage/${project.cdc_file}`"
                                    download
                                    class="inline-flex items-center space-x-2 text-sm hover:underline"
                                    :style="{ color: '#c10e46' }"
                                >
                                    <span>Télécharger le PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Message si déjà proposé -->
                    <div v-if="hasProposal" class="bg-blue-50 rounded-lg p-4 flex items-start gap-3">
                        <CheckCircle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold mb-1">Vous avez déjà proposé un devis pour ce projet</p>
                            <p>Votre proposition est en cours d'examen par le client.</p>
                        </div>
                    </div>

                    <!-- Bouton Proposer -->
                    <div v-if="isAgency && !hasProposal && project.status === 'open'">
                        <button
                            @click="openModal"
                            class="w-full px-6 py-3 rounded-xl font-semibold transition-all duration-200 hover:shadow-lg hover:scale-105"
                            :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                        >
                            Proposer un tarif
                        </button>
                    </div>

                    <!-- Message si projet fermé -->
                    <div v-else-if="project.status !== 'open'" class="bg-gray-100 rounded-lg p-4 text-center">
                        <p class="text-gray-600">Ce projet n'est plus disponible</p>
                    </div>

                    <!-- Message si déjà proposé pour l'agence -->
                    <div v-else-if="hasProposal" class="bg-gray-100 rounded-lg p-4 text-center">
                        <p class="text-gray-600">Vous avez déjà soumis une proposition pour ce projet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de proposition -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                    <div>
                        <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">Proposer un devis</h3>
                        <p class="text-sm text-gray-500 mt-1">Pour le projet : {{ project.title }}</p>
                    </div>
                    <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <X class="w-5 h-5 text-gray-500" />
                    </button>
                </div>

                <!-- Corps du formulaire -->
                <div class="p-6 space-y-4">
                    <!-- Message de succès -->
                    <div v-if="submitSuccess" class="bg-green-50 rounded-lg p-4 flex items-start gap-3">
                        <CheckCircle class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-green-700">
                            <p class="font-semibold">Proposition envoyée !</p>
                            <p>Votre devis a été soumis avec succès au client.</p>
                        </div>
                    </div>

                    <div v-else>
                        <!-- Prix proposé -->
                        <div>
                            <label class="block text-sm font-semibold mb-2" :style="{ color: '#1e002f' }">
                                Montant proposé <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Ar</span>
                                <input
                                    type="number"
                                    v-model="form.price"
                                    required
                                    min="1"
                                    step="1000"
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent"
                                    placeholder="1 000 000"
                                />
                            </div>
                            <p v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</p>
                        </div>

                        <!-- Délai proposé -->
                        <div>
                            <label class="block text-sm font-semibold mb-2" :style="{ color: '#1e002f' }">
                                Délai de réalisation <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    v-model="form.delai_agency"
                                    required
                                    min="1"
                                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent"
                                    placeholder="15"
                                />
                                <span class="text-gray-600">jours</span>
                            </div>
                            <p v-if="form.errors.delai_agency" class="text-red-500 text-xs mt-1">{{ form.errors.delai_agency }}</p>
                        </div>

                        <!-- Commentaire -->
                        <div>
                            <label class="block text-sm font-semibold mb-2" :style="{ color: '#1e002f' }">
                                Commentaire (optionnel)
                            </label>
                            <textarea
                                v-model="form.description_agency"
                                rows="4"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent resize-y"
                                placeholder="Ajoutez des informations complémentaires sur votre proposition..."
                            ></textarea>
                            <p v-if="form.errors.description_agency" class="text-red-500 text-xs mt-1">{{ form.errors.description_agency }}</p>
                        </div>

                        <!-- Message d'erreur général -->
                        <div v-if="submitError" class="bg-red-50 rounded-lg p-3 flex items-start gap-2">
                            <AlertCircle class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" />
                            <p class="text-sm text-red-600">Une erreur est survenue. Veuillez réessayer.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 p-6 border-t" :style="{ borderColor: '#c10e4620' }">
                    <button
                        v-if="!submitSuccess"
                        @click="closeModal"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors"
                        :style="{ borderColor: '#c10e4620', color: '#1e002f' }"
                    >
                        Annuler
                    </button>
                    <button
                        v-if="!submitSuccess"
                        @click="submitProposal"
                        :disabled="isSubmitting"
                        class="px-6 py-2 rounded-lg text-white font-medium transition-all hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }"
                    >
                        <span v-if="!isSubmitting">Envoyer la proposition</span>
                        <span v-else>Envoi en cours...</span>
                    </button>
                    <button
                        v-else
                        @click="closeModal"
                        class="px-6 py-2 rounded-lg text-white font-medium"
                        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }"
                    >
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
