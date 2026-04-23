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
  FileText,
  Percent,
  TrendingUp,
  ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    devis: Object,
    price: Number,
    commission: Number,
    amountToPay: Number,
    remainingAmount: Number,
    balance: Number
});

const processing = ref(false);

// Vérifier si le solde est suffisant
const isBalanceSufficient = computed(() => {
    return props.balance >= props.amountToPay;
});

const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const submitPayment = () => {
    if (!isBalanceSufficient.value) return;

    processing.value = true;
    router.post(`/payment/${props.devis.id}/process`, {}, {
        onError: (errors) => {
            console.error(errors);
            processing.value = false;
        }
    });
};
</script>

<template>
    <Head title="Paiement - {{ devis.title }}" />
    <Nav />

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <h1 class="text-2xl font-bold text-gray-900">Paiement du service</h1>
                        <p class="text-gray-600">Finalisez votre commande en toute sécurité</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulaire de paiement -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Détails du paiement -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Wallet class="w-5 h-5 mr-2 text-[#c10e46]" />
                            Paiement par solde
                        </h2>

                        <div class="space-y-4">
                            <!-- Solde actuel -->
                            <div class="bg-gradient-to-r from-[#c10e46] to-[#1e002f] rounded-lg p-4 text-white">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm opacity-90">Votre solde actuel</p>
                                        <p class="text-2xl font-bold">{{ formatAmount(balance) }}</p>
                                    </div>
                                    <Wallet class="w-10 h-10 opacity-80" />
                                </div>
                            </div>

                            <!-- Montant à payer -->
                            <div class="border rounded-lg p-4" :class="isBalanceSufficient ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700">Montant total du devis</span>
                                    <span class="font-semibold text-gray-900">{{ formatAmount(price) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700">Acompte (50%)</span>
                                    <span class="font-semibold text-gray-900">{{ formatAmount(price * 0.5) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700 flex items-center">
                                        Commission (8%)
                                        <Percent class="w-4 h-4 ml-1 text-gray-400" />
                                    </span>
                                    <span class="font-semibold text-gray-900">{{ formatAmount(commission) }}</span>
                                </div>
                                <div class="border-t pt-2 mt-2" :class="isBalanceSufficient ? 'border-green-200' : 'border-red-200'">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-900">Total à payer aujourd'hui</span>
                                        <span class="text-2xl font-bold text-[#c10e46]">{{ formatAmount(amountToPay) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Message d'erreur si solde insuffisant -->
                            <div v-if="!isBalanceSufficient" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <AlertCircle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p class="text-red-800 font-medium">Solde insuffisant</p>
                                        <p class="text-red-700 text-sm mt-1">
                                            Votre solde actuel est de {{ formatAmount(userBalance) }}.
                                            Il vous manque {{ formatAmount(amountToPay - userBalance) }} pour effectuer ce paiement.
                                        </p>
                                        <button
                                            @click="router.get('/balance')"
                                            class="mt-3 text-sm text-red-700 font-medium hover:text-red-800 flex items-center"
                                        >
                                            Approvisionner mon compte
                                            <ArrowRight class="w-4 h-4 ml-1" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton de paiement -->
                            <button
                                @click="submitPayment"
                                :disabled="processing || !isBalanceSufficient"
                                class="w-full py-3 rounded-lg font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="isBalanceSufficient
                                    ? 'bg-gradient-to-r from-[#c10e46] to-[#1e002f] text-white hover:shadow-lg'
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                            >
                                <div class="flex items-center justify-center">
                                    <Lock v-if="isBalanceSufficient" class="w-5 h-5 mr-2" />
                                    <TrendingUp v-else class="w-5 h-5 mr-2" />
                                    {{ processing ? 'Traitement en cours...' : (isBalanceSufficient ? `Payer ${formatAmount(amountToPay)}` : 'Solde insuffisant') }}
                                </div>
                            </button>

                            <!-- Information -->
                            <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-800">
                                <p class="flex items-center">
                                    <AlertCircle class="w-4 h-4 mr-2" />
                                    Le paiement sera débité directement de votre solde
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de livraison -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <CheckCircle class="w-5 h-5 mr-2 text-green-600" />
                            Reste à payer
                        </h2>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-gray-700 mb-2">
                                Après validation de ce paiement, vous ne payerez que
                                <span class="font-bold text-green-600">{{ formatAmount(remainingAmount) }}</span>
                                à la livraison du projet.
                            </p>
                            <p class="text-sm text-gray-600 mt-2">
                                Le projet sera démarré dès réception de l'acompte.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Résumé de la commande -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé de la commande</h2>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-3 pb-4 border-b">
                                <div class="bg-gradient-to-r from-[#c10e46] to-[#1e002f] p-2 rounded-lg">
                                    <FileText class="w-5 h-5 text-white" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ devis.title }}</p>
                                    <p class="text-sm text-gray-600">Devis #{{ devis.id }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service</span>
                                    <span class="font-medium text-gray-900">{{ devis.service?.title || 'Non spécifié' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Agence</span>
                                    <span class="font-medium text-gray-900">{{ devis.agency?.name || 'Non spécifiée' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Délai</span>
                                    <span class="font-medium text-gray-900">{{ devis.delai_agency || devis.delai }} jours</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date</span>
                                    <span class="font-medium text-gray-900">{{ formatDate(devis.created_at) }}</span>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Prix total</span>
                                        <span class="font-medium text-gray-900">{{ formatAmount(price) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Acompte (50%)</span>
                                        <span class="font-medium text-gray-900">{{ formatAmount(price * 0.5) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Commission (8%)</span>
                                        <span class="text-gray-500">{{ formatAmount(commission) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t">
                                        <span class="font-bold text-gray-900">Total à payer</span>
                                        <span class="text-xl font-bold text-[#c10e46]">{{ formatAmount(amountToPay) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Note sur le paiement -->
                            <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-600">
                                <p class="flex items-center">
                                    <Wallet class="w-4 h-4 mr-2 text-[#c10e46]" />
                                    Débit direct de votre portefeuille
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
