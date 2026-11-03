<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Nav from '@/Components/Nav.vue';
import {
  CreditCard,
  Wallet,
  CheckCircle,
  AlertCircle,
  ArrowLeft,
  Lock,
  Calendar,
  Clock,
  Crown,
  Star,
  Sparkles
} from 'lucide-vue-next';

const props = defineProps({
    balance: {
        type: Number,
        default: 0
    },
    transaction: {
        type: Object,
        default: null
    },
    planId: {
        type: String,
        default: null
    }
});

const processing = ref(false);
const showConfirmModal = ref(false);

// Définition des plans directement en JS
const plans = {
    basic: {
        id: 'basic',
        name: 'Basic',
        price: 50000,
        duration: 1,
        duration_label: '1 mois',
        features: [
            'Visibilité sur la plateforme',
            'Accès au tableau de bord',
            'Services illimités',
            'Notifications par email'
        ]
    },
    pro: {
        id: 'pro',
        name: 'Pro',
        price: 200000,
        duration: 5,
        duration_label: '5 mois',
        features: [
            'Visibilité sur la plateforme',
            'Accès au tableau de bord',
            'Services illimités',
            'Notifications par email'
        ]
    },
    premium: {
        id: 'premium',
        name: 'Premium',
        price: 425000,
        duration: 12,
        duration_label: '1 an',
        features: [
            'Visibilité sur la plateforme',
            'Accès au tableau de bord',
            'Services illimités',
            'Notifications par email'
        ]
    }
};

// Récupérer le plan sélectionné
const selectedPlan = computed(() => {
    if (!props.planId) return null;
    return plans[props.planId];
});

// Couleurs et icônes
const planStyle = computed(() => {
    if (!selectedPlan.value) return null;

    const styles = {
        basic: { bg: 'from-blue-500 to-blue-600', border: 'border-blue-200', button: 'bg-blue-600 hover:bg-blue-700', icon: Star },
        premium: { bg: 'from-purple-500 to-purple-600', border: 'border-purple-200', button: 'bg-purple-600 hover:bg-purple-700', icon: Crown },
        pro: { bg: 'from-amber-500 to-amber-600', border: 'border-amber-200', button: 'bg-amber-600 hover:bg-amber-700', icon: Sparkles }
    };

    return styles[selectedPlan.value.id];
});

// Vérifier si le solde est suffisant
const isBalanceSufficient = computed(() => {
    return props.balance >= (selectedPlan.value?.price || 0);
});

// Fermer le modal
const closeConfirmModal = () => {
    showConfirmModal.value = false;
    processing.value = false;
};

// Effectuer le paiement de l'abonnement
const submitPayment = () => {
    processing.value = true;

    router.post(`/agency/payment/${props.transaction?.reference}`, {}, {
        onSuccess: () => {
            closeConfirmModal();
        },
        onError: (errors) => {
            console.error('Erreur:', errors);
            alert('Erreur lors du paiement');
            processing.value = false;
        }
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

// Ouvrir le modal de confirmation
const openConfirmModal = () => {
    if (!isBalanceSufficient.value) {
        alert(`Solde insuffisant. Il vous manque ${formatAmount(selectedPlan.value.price - props.balance)}`);
        return;
    }
    showConfirmModal.value = true;
};
</script>

<template>
    <Head title="Paiement Abonnement - Agence" />
    <Nav />

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <button
                    @click="router.back()"
                    class="flex items-center text-gray-600 hover:text-[#c10e46] transition-colors mb-4"
                >
                    <ArrowLeft class="w-5 h-5 mr-2" />
                    Retour
                </button>

                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-[#c10e46] to-[#1e002f] p-3 rounded-xl">
                        <CreditCard class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Paiement d'abonnement</h1>
                        <p class="text-gray-600">Finalisez votre souscription en toute sécurité</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Plan sélectionné -->
                <div class="lg:col-span-1">
                    <!-- Solde actuel -->
                    <div class="bg-gradient-to-r from-[#c10e46] to-[#1e002f] rounded-xl p-6 mb-8 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm opacity-90">Votre solde actuel</p>
                                <p class="text-3xl font-bold">{{ formatAmount(balance) }}</p>
                            </div>
                            <Wallet class="w-12 h-12 opacity-80" />
                        </div>
                    </div>

                    <!-- Plan à payer -->
                    <div v-if="selectedPlan && planStyle" class="bg-white rounded-xl overflow-hidden shadow-lg border-2" :class="planStyle.border">
                        <div class="p-6">
                            <!-- Icône -->
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center bg-gradient-to-br mb-4"
                                :class="planStyle.bg">
                                <component :is="planStyle.icon" class="w-8 h-8 text-white" />
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedPlan.name }}</h3>
                            <p class="text-gray-500 text-sm mb-4">pour agence professionnelle</p>

                            <!-- Prix -->
                            <div class="mb-6">
                                <span class="text-4xl font-bold text-gray-900">{{ formatAmount(selectedPlan.price) }}</span>
                                <span class="text-gray-500"> / {{ selectedPlan.duration_label }}</span>
                            </div>

                            <!-- Durée -->
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-6 pb-6 border-b">
                                <Calendar class="w-4 h-4" />
                                <span>Validité : {{ selectedPlan.duration_label }}</span>
                                <Clock class="w-4 h-4 ml-2" />
                                <span>Accès immédiat</span>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-3 mb-8">
                                <li v-for="feature in selectedPlan.features" :key="feature" class="flex items-start gap-2">
                                    <CheckCircle class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" />
                                    <span class="text-sm text-gray-600">{{ feature }}</span>
                                </li>
                            </ul>

                            <!-- Message solde insuffisant -->
                            <div v-if="!isBalanceSufficient" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start space-x-3">
                                    <AlertCircle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p class="text-red-800 font-medium">Solde insuffisant</p>
                                        <p class="text-red-700 text-sm mt-1">
                                            Il vous manque {{ formatAmount(selectedPlan.price - balance) }}
                                        </p>
                                        <button
                                            @click="router.get('/balance')"
                                            class="mt-2 text-sm text-red-700 font-medium hover:text-red-800 flex items-center"
                                        >
                                            Approvisionner mon compte
                                            <ArrowLeft class="w-4 h-4 ml-1 rotate-180" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton -->
                            <button
                                @click="openConfirmModal"
                                :disabled="!isBalanceSufficient"
                                class="w-full py-3 rounded-xl text-white font-semibold transition-all duration-300 transform hover:scale-105"
                                :class="isBalanceSufficient ? planStyle.button : 'bg-gray-400 cursor-not-allowed'">
                                {{ isBalanceSufficient ? 'Payer maintenant' : 'Solde insuffisant' }}
                            </button>
                        </div>
                    </div>

                    <div v-else class="bg-white rounded-xl p-6 text-center">
                        <AlertCircle class="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <p class="text-gray-600">Plan non trouvé</p>
                    </div>
                </div>

                <!-- Résumé et informations -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Lock class="w-5 h-5 mr-2 text-[#c10e46]" />
                            Récapitulatif
                        </h2>

                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Plan</span>
                                        <span class="font-semibold text-gray-900">{{ selectedPlan?.name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Durée</span>
                                        <span class="font-semibold text-gray-900">{{ selectedPlan?.duration_label }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-3">
                                        <span class="text-gray-900 font-bold">Total à payer</span>
                                        <span class="text-xl font-bold text-[#c10e46]">{{ formatAmount(selectedPlan?.price) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-800 mb-2 flex items-center">
                                    <CheckCircle class="w-4 h-4 mr-2" />
                                    Ce qui est inclus
                                </h3>
                                <ul class="space-y-2 text-sm text-blue-700">
                                    <li>✅ Accès immédiat aux fonctionnalités</li>
                                    <li>✅ Support prioritaire</li>
                                    <li>✅ Mise à jour automatique</li>
                                    <li>✅ Renouvellement automatique</li>
                                </ul>
                            </div>

                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h3 class="font-semibold text-yellow-800 mb-2">Besoin d'aide ?</h3>
                                <p class="text-sm text-yellow-700 mb-3">
                                    Vous avez des questions sur cet abonnement ?
                                </p>
                                <button
                                    @click="router.get('/contact')"
                                    class="text-sm text-yellow-800 font-semibold hover:text-yellow-900 flex items-center"
                                >
                                    Contactez-nous
                                    <ArrowLeft class="w-4 h-4 ml-1 rotate-180" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation -->
        <div v-if="showConfirmModal && selectedPlan && planStyle" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeConfirmModal"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full">
                    <div class="flex items-center justify-between p-6 border-b">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-r from-[#c10e46] to-[#1e002f] p-2 rounded-lg">
                                <CreditCard class="w-6 h-6 text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Confirmation d'abonnement</h3>
                        </div>
                        <button @click="closeConfirmModal" class="p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="rounded-lg p-4 text-white bg-gradient-to-r" :class="planStyle.bg">
                            <p class="text-sm opacity-90">Plan choisi</p>
                            <p class="text-2xl font-bold">{{ selectedPlan.name }}</p>
                            <p class="text-sm opacity-90">{{ formatAmount(selectedPlan.price) }} / {{ selectedPlan.duration_label }}</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Durée</span>
                                <span class="font-medium">{{ selectedPlan.duration_label }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Montant total</span>
                                <span class="font-bold text-[#c10e46] text-xl">{{ formatAmount(selectedPlan.price) }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Solde après paiement</span>
                                <span class="font-medium">{{ formatAmount(balance - selectedPlan.price) }}</span>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-800">
                            <p>💡 Le paiement sera débité de votre solde. Une fois l'abonnement actif, vous aurez accès à toutes les fonctionnalités.</p>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button
                                @click="submitPayment"
                                :disabled="processing"
                                class="flex-1 px-4 py-2 rounded-lg text-white font-medium transition-all"
                                :class="[planStyle.button, processing ? 'opacity-50 cursor-not-allowed' : '']">
                                {{ processing ? 'Traitement...' : 'Confirmer le paiement' }}
                            </button>
                            <button
                                @click="closeConfirmModal"
                                class="px-4 py-2 rounded-lg border hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
