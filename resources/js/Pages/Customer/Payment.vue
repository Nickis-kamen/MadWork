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
  ArrowRight,
  Package,
  Truck
} from 'lucide-vue-next';

const props = defineProps({
    service: Object,
    agency: Object,
    devis: Object,
    transaction: Object,
    price: Number,
    commission: Number,
    amountToPay: Number,
    remainingAmount: Number,
    balance: Number,
    paymentStage: {
        type: String,
        default: 'first'
    }
});

const processing = ref(false);

// Vérifier si le solde est suffisant
const isBalanceSufficient = computed(() => {
    return props.balance >= props.amountToPay;
});

// Informations sur le stage de paiement
const paymentStageInfo = computed(() => {
    if (props.paymentStage === 'first') {
        return {
            title: 'Premier paiement (Acompte 50%)',
            description: 'Vous payez 50% du montant total aujourd\'hui pour démarrer le projet.',
            nextStep: 'Le projet sera démarré immédiatement après validation.',
            finalAmount: props.remainingAmount,
            icon: TrendingUp,
            color: 'blue'
        };
    } else {
        return {
            title: 'Paiement final (Solde 50%)',
            description: 'Vous payez le solde restant de 50% pour finaliser la livraison.',
            nextStep: 'Après validation, l\'agence procédera à la livraison finale du projet.',
            finalAmount: 0,
            icon: Truck,
            color: 'green'
        };
    }
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
    if (props.paymentStage === 'first')
    {
        router.post(`/payment/${props.transaction?.reference}/first`, {}, {
            onError: (errors) => {
                console.error(errors);
                processing.value = false;
            }
        });
    }else
    {
        router.post(`/payment/${props.transaction?.reference}/final`, {}, {
            onError: (errors) => {
                console.error(errors);
                processing.value = false;
            }
        });
    }
};

// Badge de stage
const getStageBadge = () => {
    if (props.paymentStage === 'first') {
        return {
            label: 'Acompte (50%)',
            color: 'bg-blue-100 text-blue-800'
        };
    } else {
        return {
            label: 'Paiement final (Solde)',
            color: 'bg-green-100 text-green-800'
        };
    }
};
</script>

<template>
    <Head :title="'Paiement - ' + (paymentStage === 'first' ? 'Acompte' : 'Solde final')" />
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
                        <component :is="paymentStageInfo.icon" class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ paymentStageInfo.title }}</h1>
                        <p class="text-gray-600">{{ paymentStageInfo.description }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulaire de paiement -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Badge stage -->
                    <div class="flex justify-end">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold" :class="getStageBadge().color">
                            {{ getStageBadge().label }}
                        </span>
                    </div>

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
                                <!-- Stage actuel -->
                                <div class="mb-4 pb-4 border-b" :class="isBalanceSufficient ? 'border-green-200' : 'border-red-200'">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-700">Stage de paiement</span>
                                        <span class="font-semibold px-3 py-1 rounded-full text-sm" :class="paymentStage === 'first' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'">
                                            {{ paymentStage === 'first' ? 'Premier paiement' : 'Paiement final' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700">Montant total du devis</span>
                                    <span class="font-semibold text-gray-900">{{ formatAmount(price) }}</span>
                                </div>

                                <!-- Afficher acompte déjà payé si paiement final -->
                                <div v-if="paymentStage === 'final'" class="flex justify-between items-center mb-2 text-green-600">
                                    <span class="flex items-center">
                                        <CheckCircle class="w-4 h-4 mr-1" />
                                        Acompte déjà payé (50%)
                                    </span>
                                    <span class="font-semibold">{{ formatAmount(price * 0.5) }}</span>
                                </div>

                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700 flex items-center">
                                        {{ paymentStage === 'first' ? 'Acompte (50%)' : 'Solde restant (50%)' }}
                                    </span>
                                    <span class="font-semibold text-gray-900">{{ formatAmount(price * 0.5) }}</span>
                                </div>

                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700 flex items-center">
                                        Commission (8%)
                                    </span>
                                    <span class="font-semibold text-gray-900">{{ formatAmount(commission) }}</span>
                                </div>

                                <div class="border-t pt-2 mt-2" :class="isBalanceSufficient ? 'border-green-200' : 'border-red-200'">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-900">
                                            {{ paymentStage === 'first' ? 'Total à payer aujourd\'hui' : 'Solde à payer' }}
                                        </span>
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
                                            Votre solde actuel est de {{ formatAmount(balance) }}.
                                            Il vous manque {{ formatAmount(amountToPay - balance) }} pour effectuer ce paiement.
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
                                    {{ processing ? 'Traitement en cours...' : (isBalanceSufficient ? `Payer ${formatAmount(amountToPay)}` : 'Solde insuffisant') }}
                                </div>
                            </button>

                            <!-- Information stage -->
                            <div class="rounded-lg p-3 text-sm" :class="paymentStage === 'first' ? 'bg-blue-50 text-blue-800' : 'bg-green-50 text-green-800'">
                                <p class="flex items-center">
                                    <component :is="paymentStageInfo.icon" class="w-4 h-4 mr-2" />
                                    {{ paymentStageInfo.nextStep }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Récapitulatif du paiement -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <CheckCircle class="w-5 h-5 mr-2 text-green-600" />
                            {{ paymentStage === 'first' ? 'Prochain paiement' : 'Finalisation' }}
                        </h2>
                        <div class="rounded-lg p-4" :class="paymentStage === 'first' ? 'bg-blue-50' : 'bg-green-50'">
                            <p class="text-gray-700 mb-2">
                                <span class="font-semibold">
                                    {{ paymentStage === 'first' ? 'Reste à payer à la livraison :' : 'Statut final :' }}
                                </span>
                            </p>
                            <p v-if="paymentStage === 'first'" class="text-2xl font-bold text-blue-600">
                                {{ formatAmount(remainingAmount) }}
                            </p>
                            <p v-else class="text-green-600 font-medium flex items-center">
                                <CheckCircle class="w-5 h-5 mr-2" />
                                Plus aucun paiement à effectuer après celui-ci
                            </p>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ paymentStage === 'first'
                                    ? 'Le deuxième paiement (50%) sera demandé à la livraison du projet.'
                                    : 'Une fois le paiement validé, l\'agence procédera à la livraison finale.'
                                }}
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
                                    <p class="text-sm text-gray-600">Devis #{{ devis.reference }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service</span>
                                    <span class="font-medium text-gray-900">{{ service?.title || 'Non spécifié' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Agence</span>
                                    <span class="font-medium text-gray-900">{{ agency?.name || 'Non spécifiée' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Délai</span>
                                    <span class="font-medium text-gray-900">{{ devis.delai_agency }} jours</span>
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
                                        <span class="text-gray-600">{{ paymentStage === 'first' ? 'Acompte (50%)' : 'Déjà payé' }}</span>
                                        <span class="font-medium" :class="paymentStage === 'first' ? 'text-blue-600' : 'text-green-600'">
                                            {{ formatAmount(paymentStage === 'first' ? price * 0.5 : price * 0.5) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Commission (8%)</span>
                                        <span class="text-gray-500">{{ formatAmount(commission) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t">
                                        <span class="font-bold text-gray-900">
                                            {{ paymentStage === 'first' ? 'À payer maintenant' : 'Solde restant' }}
                                        </span>
                                        <span class="text-xl font-bold text-[#c10e46]">{{ formatAmount(amountToPay) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Note sur le paiement -->
                            <div class="rounded-lg p-3 text-sm" :class="paymentStage === 'first' ? 'bg-blue-50 text-blue-800' : 'bg-green-50 text-green-800'">
                                <p class="flex items-center">
                                    <Wallet class="w-4 h-4 mr-2" />
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
