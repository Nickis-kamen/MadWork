<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import {
  Wallet,
  Plus,
  Minus,
  History,
  CheckCircle,
  XCircle,
  AlertCircle,
  ArrowUp,
  ArrowDown
} from 'lucide-vue-next';
import Nav from '@/Components/Nav.vue';
import mvola from '@/assets/payment/mvola.png';
import orangemoney from '@/assets/payment/orangemoney.png';
import airtelmoney from '@/assets/payment/airtelmoney.png';
import visa from '@/assets/payment/visa.png';
import Toast from '@/Components/Toastify/Toast.vue';

// Props reçues de Laravel
const props = defineProps({
    balance: {
        type: Number,
        default: 0
    },
    transactions: {
        type: Array,
        default: () => []
    }
});

// État pour les modals
const showDepositModal = ref(false);
const showWithdrawModal = ref(false);
const processing = ref(false);
const selectedTransaction = ref(null);
const showDetailsModal = ref(false);

// États des formulaires
const depositForm = ref({
    amount: '',
    payment_method: 'mvola',
    phone_number: ''
});

const withdrawForm = ref({
    amount: '',
    payment_method: 'mvola',
    phone_number: ''
});

// Messages d'erreur pour les numéros
const phoneError = ref('');

// Règles de validation des numéros par méthode
const phonePatterns = {
    mvola: {
        pattern: /^(034|038)[0-9]{7}$/,
        example: '034XXXXXXX ou 038XXXXXXX',
        prefix: ['034', '038']
    },
    orangemoney: {
        pattern: /^(032|037)[0-9]{7}$/,
        example: '032XXXXXXX ou 037XXXXXXX',
        prefix: ['032', '037']
    },
    airtelmoney: {
        pattern: /^(033)[0-9]{7}$/,
        example: '033XXXXXXX',
        prefix: ['033']
    },
    visa: {
        pattern: /^[0-9]{16}$/,
        example: '1234567890123456 (16 chiffres)',
        prefix: []
    }
};

// Valider le numéro selon la méthode sélectionnée
const validatePhoneNumber = (method, number) => {
    if (!number) {
        return 'Le numéro est requis';
    }

    // Supprimer les espaces et les tirets
    const cleanNumber = number.replace(/[\s-]/g, '');

    if (method === 'visa') {
        if (!phonePatterns.visa.pattern.test(cleanNumber)) {
            return 'Numéro de carte Visa invalide (16 chiffres)';
        }
        return null;
    }

    // Pour les méthodes mobile money
    const pattern = phonePatterns[method];
    if (pattern && !pattern.pattern.test(cleanNumber)) {
        return `Numéro invalide. Format: ${pattern.example}`;
    }

    return null;
};

// Watchers pour valider en temps réel
watch(() => depositForm.value.payment_method, () => {
    depositForm.value.phone_number = '';
    phoneError.value = '';
});

watch(() => withdrawForm.value.payment_method, () => {
    withdrawForm.value.phone_number = '';
    phoneError.value = '';
});

// Validation du numéro pour dépôt
const depositPhoneError = computed(() => {
    if (!depositForm.value.phone_number) return null;
    return validatePhoneNumber(depositForm.value.payment_method, depositForm.value.phone_number);
});

// Validation du numéro pour retrait
const withdrawPhoneError = computed(() => {
    if (!withdrawForm.value.phone_number) return null;
    return validatePhoneNumber(withdrawForm.value.payment_method, withdrawForm.value.phone_number);
});

// Méthodes de paiement avec icônes (vous mettrez vos images plus tard)
const paymentMethods = [
    { id: 'mvola', name: 'Mvola', image: mvola, color: '#03b311', bg: '#E8F5E9' },
    { id: 'orangemoney', name: 'Orange Money', image: orangemoney, color: '#FF7900', bg: '#FFF3E0' },
    { id: 'airtelmoney', name: 'Airtel Money', image: airtelmoney, color: '#E11D1D', bg: '#FFEBEE' },
    { id: 'visa', name: 'Visa', image: visa, color: '#1A1F71', bg: '#E8EAF6' }
];

// Validation des montants
const minAmount = 5000;
const maxDeposit = 1000000;
const maxWithdraw = 1000000;

const depositAmountError = computed(() => {
    const amount = parseFloat(depositForm.value.amount);
    if (isNaN(amount)) return null;
    if (amount < minAmount) return `Le montant minimum est de ${minAmount.toLocaleString()} Ar`;
    if (amount > maxDeposit) return `Le montant maximum est de ${maxDeposit.toLocaleString()} Ar`;
    return null;
});

const withdrawAmountError = computed(() => {
    const amount = parseFloat(withdrawForm.value.amount);
    if (isNaN(amount)) return null;
    if (amount < minAmount) return `Le montant minimum est de ${minAmount.toLocaleString()} Ar`;
    if (amount > maxWithdraw) return `Le montant maximum est de ${maxWithdraw.toLocaleString()} Ar`;
    if (amount > props.balance) return `Solde insuffisant (${props.balance.toLocaleString()} Ar disponible)`;
    return null;
});

// Validation globale des formulaires
const isDepositValid = computed(() => {
    return !depositAmountError.value &&
           depositForm.value.amount &&
           depositForm.value.phone_number &&
           !depositPhoneError.value;
});

const isWithdrawValid = computed(() => {
    return !withdrawAmountError.value &&
           withdrawForm.value.amount &&
           withdrawForm.value.phone_number &&
           !withdrawPhoneError.value;
});

// Formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

// Formater la date
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

// Formater le numéro pour affichage
const formatPhoneNumber = (phone, method) => {
    if (!phone) return '';
    if (method === 'visa') {
        return phone.replace(/(\d{4})(\d{4})(\d{4})(\d{4})/, '$1 $2 $3 $4');
    }
    return phone;
};

// Obtenir les infos du statut
const getStatusInfo = (status) => {
    const statusMap = {
        'pending': { label: 'En attente', icon: AlertCircle, color: 'text-yellow-600', bg: 'bg-yellow-100' },
        'completed': { label: 'Complété', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
        'failed': { label: 'Échoué', icon: XCircle, color: 'text-red-600', bg: 'bg-red-100' },
        'cancelled': { label: 'Annulé', icon: XCircle, color: 'text-gray-600', bg: 'bg-gray-100' }
    };
    return statusMap[status] || { label: status, icon: AlertCircle, color: 'text-gray-600', bg: 'bg-gray-100' };
};

// Soumettre dépôt
const submitDeposit = () => {
    if (!isDepositValid.value) return;

    processing.value = true;
    router.post('/balance/deposit', {
        amount: depositForm.value.amount,
        method: depositForm.value.payment_method,
        number: depositForm.value.phone_number.replace(/[\s-]/g, '')
    }, {
        onSuccess: () => {
            depositForm.value = { amount: '', payment_method: 'mvola', phone_number: '' };
            processing.value = false;
        },
        onError: (errors) => {
            if (errors.phone_number) {
                phoneError.value = errors.phone_number;
            }
            processing.value = false;
        }
    });
};

// Soumettre retrait
const submitWithdraw = () => {
    if (!isWithdrawValid.value) return;

    processing.value = true;
    router.post('/balance/withdraw', {
        amount: withdrawForm.value.amount,
        method: withdrawForm.value.payment_method,
        number: withdrawForm.value.phone_number.replace(/[\s-]/g, '')
    }, {
        onSuccess: () => {
            withdrawForm.value = { amount: '', payment_method: 'mvola', phone_number: '' };
            processing.value = false;
        },
        onError: (errors) => {
            if (errors.phone_number) {
                phoneError.value = errors.phone_number;
            }
            processing.value = false;
        }
    });
};

// Ouvrir détails transaction
const openTransactionDetails = (transaction) => {
    selectedTransaction.value = transaction;
    showDetailsModal.value = true;
};

// Fermer modals
const closeModals = () => {
    showDepositModal.value = false;
    showWithdrawModal.value = false;
    showDetailsModal.value = false;
    selectedTransaction.value = null;
    phoneError.value = '';
};
</script>

<template>
    <Head title="Mon Solde" />
    <Nav />
    <Toast />
    <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen mt-20">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                    Mon solde
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Gérez votre porte-monnaie électronique
                </p>
            </div>
        </div>

        <div class="flex justify-between gap-6 items-center">
            <!-- Solde actuel -->
            <div class="flex-1 bg-gradient-to-r from-[#c10e46] to-[#1e002f] rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Solde disponible</p>
                        <p class="text-4xl font-bold mt-2">{{ formatAmount(balance) }}</p>
                        <p class="text-purple-100 text-xs mt-2">Minimum pour transaction: {{ formatAmount(minAmount) }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <Wallet class="w-10 h-10" />
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-4">
                <button
                    @click="showDepositModal = true"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-green-500 to-green-600 p-0.5 transition-all duration-300 hover:shadow-lg"
                >
                    <div class="relative flex items-center justify-center space-x-3 rounded-xl bg-green-600 px-6 py-4 transition-all duration-300 group-hover:bg-green-500">
                        <Plus class="h-6 w-6 text-white" />
                        <span class="font-semibold text-white">Effectuer un dépôt</span>
                    </div>
                </button>

                <button
                    @click="showWithdrawModal = true"
                    :disabled="balance < minAmount"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-red-500 to-red-600 p-0.5 transition-all duration-300 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <div class="relative flex items-center justify-center space-x-3 rounded-xl bg-red-600 px-6 py-4 transition-all duration-300 group-hover:bg-red-500">
                        <Minus class="h-6 w-6 text-white" />
                        <span class="font-semibold text-white">Effectuer un retrait</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Historique des transactions -->
        <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
            <div class="px-6 py-4 border-b" :style="{ borderColor: '#c10e4620' }">
                <div class="flex items-center space-x-2">
                    <History class="w-5 h-5" :style="{ color: '#c10e46' }" />
                    <h2 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Historique des transactions</h2>
                </div>
                <p class="text-sm text-gray-500 mt-1">Retrouvez toutes vos opérations</p>
            </div>

            <div class="divide-y" :style="{ borderColor: '#c10e4620' }">
                <div
                    v-for="transaction in transactions"
                    :key="transaction.id"
                    class="p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="openTransactionDetails(transaction)"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center"
                                :class="transaction.type === 'deposit' ? 'bg-green-100' : 'bg-red-100'"
                            >
                                <ArrowUp v-if="transaction.type === 'deposit'" class="w-5 h-5 text-green-600" />
                                <ArrowDown v-else class="w-5 h-5 text-red-600" />
                            </div>
                            <div>
                                <p class="font-medium" :style="{ color: '#1e002f' }">
                                    {{ transaction.type === 'deposit' ? 'Dépôt' : 'Retrait' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ formatDate(transaction.created_at) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold" :class="transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'">
                                {{ transaction.type === 'deposit' ? '+' : '-' }}{{ formatAmount(Math.abs(transaction.amount)) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="transactions.length === 0" class="p-12 text-center">
                    <History class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                    <p class="text-gray-500 text-lg mb-2">Aucune transaction</p>
                    <p class="text-gray-400 text-sm">Commencez par effectuer un dépôt</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dépôt -->
    <div v-if="showDepositModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModals"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">Effectuer un dépôt</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <XCircle class="w-6 h-6" />
                        </button>
                    </div>

                    <form @submit.prevent="submitDeposit">
                        <!-- Montant -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant (Ar)</label>
                            <input
                                type="number"
                                v-model="depositForm.amount"
                                :min="minAmount"
                                :max="maxDeposit"
                                step="1000"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="5000"
                            />
                            <p v-if="depositAmountError" class="text-red-500 text-xs mt-1">{{ depositAmountError }}</p>
                            <p class="text-gray-500 text-xs mt-1">Minimum: {{ formatAmount(minAmount) }}</p>
                        </div>

                        <!-- Méthode de paiement -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Mode de paiement</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div
                                    v-for="method in paymentMethods"
                                    :key="method.id"
                                    @click="depositForm.payment_method = method.id"
                                    class="cursor-pointer rounded-lg border-2 p-3 transition-all"
                                    :class="depositForm.payment_method === method.id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
                                >
                                    <div class="flex items-center justify-center">
                                        <img :src="`${method.image}`" :alt="method.name" class="h-8" />
                                        <span class="ml-2 text-sm font-medium" :style="{ color: method.color }">{{ method.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Numéro de téléphone / Carte -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ depositForm.payment_method === 'visa' ? 'Numéro de carte Visa' : 'Numéro de téléphone' }}
                            </label>
                            <input
                                type="text"
                                v-model="depositForm.phone_number"
                                :placeholder="depositForm.payment_method === 'visa' ? '1234 5678 9012 3456' : '034 12 345 67'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            />
                            <p v-if="depositPhoneError" class="text-red-500 text-xs mt-1">{{ depositPhoneError }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                {{ depositForm.payment_method === 'visa' ? 'Entrez les 16 chiffres de votre carte' : 'Exemple: ' + phonePatterns[depositForm.payment_method]?.example }}
                            </p>
                        </div>

                        <div class="flex space-x-3">
                            <button
                                type="button"
                                @click="closeModals"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                :disabled="processing || !isDepositValid"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ processing ? 'Traitement...' : 'Déposer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Retrait -->
    <div v-if="showWithdrawModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModals"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">Effectuer un retrait</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <XCircle class="w-6 h-6" />
                        </button>
                    </div>

                    <form @submit.prevent="submitWithdraw">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant (Ar)</label>
                            <input
                                type="number"
                                v-model="withdrawForm.amount"
                                :min="minAmount"
                                :max="Math.min(maxWithdraw, balance)"
                                step="1000"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                placeholder="5000"
                            />
                            <p v-if="withdrawAmountError" class="text-red-500 text-xs mt-1">{{ withdrawAmountError }}</p>
                            <p class="text-gray-500 text-xs mt-1">Solde disponible: {{ formatAmount(balance) }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Mode de retrait</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div
                                    v-for="method in paymentMethods"
                                    :key="method.id"
                                    @click="withdrawForm.payment_method = method.id"
                                    class="cursor-pointer rounded-lg border-2 p-3 transition-all"
                                    :class="withdrawForm.payment_method === method.id ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'"
                                >
                                    <div class="flex items-center justify-center">
                                        <img :src="`${method.image}`" :alt="method.name" class="h-8" />
                                        <span class="ml-2 text-sm font-medium" :style="{ color: method.color }">{{ method.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ withdrawForm.payment_method === 'visa' ? 'Numéro de carte Visa' : 'Numéro de téléphone' }}
                            </label>
                            <input
                                type="text"
                                v-model="withdrawForm.phone_number"
                                :placeholder="withdrawForm.payment_method === 'visa' ? '1234 5678 9012 3456' : '034 12 345 67'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            />
                            <p v-if="withdrawPhoneError" class="text-red-500 text-xs mt-1">{{ withdrawPhoneError }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                {{ withdrawForm.payment_method === 'visa' ? 'Entrez les 16 chiffres de votre carte' : 'Exemple: ' + phonePatterns[withdrawForm.payment_method]?.example }}
                            </p>
                        </div>

                        <div class="flex space-x-3">
                            <button
                                type="button"
                                @click="closeModals"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                :disabled="processing || !isWithdrawValid"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ processing ? 'Traitement...' : 'Retirer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Détails Transaction -->
    <div v-if="showDetailsModal && selectedTransaction" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModals"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">Détails de la transaction</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <XCircle class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b" :style="{ borderColor: '#c10e4620' }">
                            <span class="text-gray-600">Type</span>
                            <span class="font-semibold" :class="selectedTransaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'">
                                {{ selectedTransaction.type === 'deposit' ? 'Dépôt' : 'Retrait' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b" :style="{ borderColor: '#c10e4620' }">
                            <span class="text-gray-600">Montant</span>
                            <span class="font-bold" :class="selectedTransaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'">
                                {{ formatAmount(selectedTransaction.amount) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b" :style="{ borderColor: '#c10e4620' }">
                            <span class="text-gray-600">Méthode</span>
                            <span class="font-medium" :style="{ color: '#1e002f' }">{{ selectedTransaction.method }}</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b" :style="{ borderColor: '#c10e4620' }">
                            <span class="text-gray-600">Numéro</span>
                            <span class="font-medium" :style="{ color: '#1e002f' }">{{ formatPhoneNumber(selectedTransaction.number, selectedTransaction.method) }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Statut</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusInfo(selectedTransaction.status).bg">
                                {{ getStatusInfo(selectedTransaction.status).label }}
                            </span>
                        </div>

                        <div class="pt-4">
                            <p class="text-xs text-gray-500 text-center">Réf: {{ selectedTransaction.reference }}</p>
                            <p class="text-xs text-gray-500 text-center mt-1">{{ formatDate(selectedTransaction.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
