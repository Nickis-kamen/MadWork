<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
  Users,
  DollarSign,
  ShoppingBag,
  FileText,
  Settings,
  Bell,
  Calendar,
  Clock,
  Award,
  TrendingUp,
  HelpCircle,
  MessageCircle,
  CreditCard,
  Download,
  Server,
  Globe,
  Mail
} from 'lucide-vue-next';

// Statistiques client
const stats = [
  {
    title: 'Solde du compte',
    value: '2 450 €',
    change: 150,
    icon: DollarSign,
    color: '#c10e46',
    subtext: '+150 € ce mois'
  },
  {
    title: 'Services actifs',
    value: '3',
    change: null,
    icon: ShoppingBag,
    color: '#1e002f',
    subtext: 'Hébergement, Domaine, Mail'
  },
  {
    title: 'Prochain paiement',
    value: '15 Mai',
    change: 'Hébergement Pro',
    icon: Calendar,
    color: '#c10e46',
    subtext: '49 €/mois'
  },
  {
    title: 'Tickets ouverts',
    value: '1',
    change: 'En attente',
    icon: MessageCircle,
    color: '#1e002f',
    subtext: 'Réponse sous 24h'
  }
];

// Services du client
const myServices = [
  {
    id: 1,
    name: 'Hébergement Pro',
    price: '49 €/mois',
    nextBilling: '15/05/2024',
    status: 'Actif',
    statusColor: 'text-green-600',
    icon: Server
  },
  {
    id: 2,
    name: 'Nom de domaine .fr',
    price: '15 €/an',
    nextBilling: '20/06/2024',
    status: 'Actif',
    statusColor: 'text-green-600',
    icon: Globe
  },
  {
    id: 3,
    name: 'Pack Mail Pro',
    price: '29 €/mois',
    nextBilling: '15/05/2024',
    status: 'Actif',
    statusColor: 'text-green-600',
    icon: Mail
  }
];

// Dernières factures
const recentInvoices = [
  {
    id: 'INV-2024-001',
    date: '15/04/2024',
    amount: '49 €',
    status: 'Payée',
    statusColor: 'text-green-600',
    service: 'Hébergement Pro'
  },
  {
    id: 'INV-2024-002',
    date: '15/03/2024',
    amount: '49 €',
    status: 'Payée',
    statusColor: 'text-green-600',
    service: 'Hébergement Pro'
  },
  {
    id: 'INV-2024-003',
    date: '20/02/2024',
    amount: '15 €',
    status: 'Payée',
    statusColor: 'text-green-600',
    service: 'Domaine .fr'
  }
];

// Notifications client
const notifications = [
  {
    id: 1,
    title: 'Facture disponible',
    message: 'Votre facture d\'avril est disponible',
    time: '2 jours',
    icon: FileText,
    type: 'info'
  },
  {
    id: 2,
    title: 'Maintenance programmée',
    message: 'Maintenance serveur le 20/05',
    time: '5 jours',
    icon: Server,
    type: 'warning'
  },
  {
    id: 3,
    title: 'Nouveau service',
    message: 'Découvrez nos offres Cloud',
    time: '1 semaine',
    icon: Award,
    type: 'promo'
  }
];

// Actions rapides client
const quickActions = [
  { label: 'Payer une facture', icon: CreditCard, action: 'pay', route: '/billing' },
  { label: 'Ouvrir un ticket', icon: MessageCircle, action: 'ticket', route: '/support' },
  { label: 'Mes services', icon: ShoppingBag, action: 'services', route: '/services' },
  { label: 'Aide', icon: HelpCircle, action: 'help', route: '/help' }
];

// Prochains événements
const upcomingEvents = [
  {
    id: 1,
    title: 'Paiement Hébergement',
    date: '15/05/2024',
    amount: '49 €',
    type: 'payment'
  },
  {
    id: 2,
    title: 'Fin de domaine .fr',
    date: '20/06/2024',
    amount: '15 €',
    type: 'renewal'
  }
];

const handleQuickAction = (action) => {
  console.log('Action:', action.label);
  // Redirection ou modal selon l'action
};
</script>

<template>
    <Head title="Mon Espace Client" />
    <AuthenticatedLayout>
        <!-- Container client -->
        <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen">
            <!-- Header personnalisé client -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Bonjour, Jean 👋
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Bienvenue dans votre espace client
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        href="/support"
                        class="px-4 py-2 border rounded-xl hover:shadow-md transition-all duration-200 font-medium"
                        :style="{
                            borderColor: '#c10e4620',
                            color: '#c10e46'
                        }"
                    >
                        Support
                    </Link>
                    <Link
                        href="/billing"
                        class="px-4 py-2 text-white rounded-xl hover:shadow-lg transition-all duration-200 hover:scale-105 font-semibold shadow-md"
                        :style="{
                            background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)'
                        }"
                    >
                        Payer une facture
                    </Link>
                </div>
            </div>

            <!-- Stats Cards Client -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div
                    v-for="stat in stats"
                    :key="stat.title"
                    class="bg-white p-4 sm:p-6 rounded-xl border hover:shadow-lg transition-all duration-300 group"
                    :style="{ borderColor: stat.color + '20' }"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">{{ stat.title }}</p>
                            <p class="text-2xl font-bold mt-1" :style="{ color: '#1e002f' }">
                                {{ stat.value }}
                            </p>
                            <p v-if="stat.change" class="text-sm mt-1 font-medium" :style="{ color: stat.color }">
                                {{ stat.change }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ stat.subtext }}</p>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
                            :style="{
                                background: `linear-gradient(135deg, ${stat.color}15 0%, ${stat.color}30 100%)`
                            }"
                        >
                            <component :is="stat.icon" class="w-6 h-6" :style="{ color: stat.color }" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section principale client -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Mes services (plus large) -->
                <div class="lg:col-span-2 bg-white rounded-xl border" :style="{ borderColor: '#c10e4620' }">
                    <div class="p-4 sm:p-6 border-b flex justify-between items-center" :style="{ borderColor: '#c10e4620' }">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Mes services</h3>
                        <Link
                            href="/services"
                            class="text-sm font-medium hover:underline"
                            :style="{ color: '#c10e46' }"
                        >
                            Voir tout →
                        </Link>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-4">
                            <div
                                v-for="service in myServices"
                                :key="service.id"
                                class="flex items-center justify-between p-4 rounded-lg border hover:shadow-md transition-all"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 rounded-xl flex items-center justify-center"
                                        :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                                    >
                                        <component :is="service.icon" class="w-6 h-6" :style="{ color: '#c10e46' }" />
                                    </div>
                                    <div>
                                        <p class="font-semibold" :style="{ color: '#1e002f' }">{{ service.name }}</p>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="text-sm text-gray-600">{{ service.price }}</span>
                                            <span class="text-sm text-gray-600">Prochain: {{ service.nextBilling }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium" :class="service.statusColor">{{ service.status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications client -->
                <div class="lg:col-span-1 bg-white rounded-xl border" :style="{ borderColor: '#1e002f20' }">
                    <div class="p-4 sm:p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Notifications</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-4">
                            <div
                                v-for="notif in notifications"
                                :key="notif.id"
                                class="p-3 rounded-lg border cursor-pointer hover:shadow-sm transition-all"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                        :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                                    >
                                        <component :is="notif.icon" class="w-4 h-4" :style="{ color: '#c10e46' }" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-sm" :style="{ color: '#1e002f' }">{{ notif.title }}</p>
                                        <p class="text-gray-500 text-xs mt-1">{{ notif.message }}</p>
                                        <p class="text-gray-400 text-xs mt-2">Il y a {{ notif.time }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Factures et paiements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Dernières factures -->
                <div class="bg-white rounded-xl border" :style="{ borderColor: '#c10e4620' }">
                    <div class="p-4 sm:p-6 border-b flex justify-between items-center" :style="{ borderColor: '#c10e4620' }">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Dernières factures</h3>
                        <Link
                            href="/invoices"
                            class="text-sm font-medium hover:underline"
                            :style="{ color: '#c10e46' }"
                        >
                            Toutes mes factures →
                        </Link>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-3">
                            <div
                                v-for="invoice in recentInvoices"
                                :key="invoice.id"
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50"
                            >
                                <div class="flex items-center space-x-3">
                                    <FileText class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                    <div>
                                        <p class="font-medium text-sm" :style="{ color: '#1e002f' }">{{ invoice.id }}</p>
                                        <p class="text-xs text-gray-500">{{ invoice.service }} • {{ invoice.date }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="font-medium">{{ invoice.amount }}</span>
                                    <span class="text-sm font-medium" :class="invoice.statusColor">{{ invoice.status }}</span>
                                    <button
                                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                        :style="{ color: '#c10e46' }"
                                    >
                                        <Download class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prochains paiements -->
                <div class="bg-white rounded-xl border" :style="{ borderColor: '#1e002f20' }">
                    <div class="p-4 sm:p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Prochains paiements</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-4">
                            <div
                                v-for="event in upcomingEvents"
                                :key="event.id"
                                class="flex items-center justify-between p-4 rounded-lg border"
                                :style="{ borderColor: '#c10e4620', background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)' }"
                            >
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center"
                                        :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                                    >
                                        <Calendar class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                    </div>
                                    <div>
                                        <p class="font-medium" :style="{ color: '#1e002f' }">{{ event.title }}</p>
                                        <p class="text-sm text-gray-500">{{ event.date }} • {{ event.amount }}</p>
                                    </div>
                                </div>
                                <Link
                                    href="/billing"
                                    class="px-3 py-1 text-sm rounded-lg border hover:bg-white transition-colors"
                                    :style="{ borderColor: '#c10e4620', color: '#c10e46' }"
                                >
                                    Payer
                                </Link>
                            </div>
                        </div>

                        <!-- Moyens de paiement -->
                        <div class="mt-6 p-4 rounded-lg" :style="{ background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)' }">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <CreditCard class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                    <div>
                                        <p class="font-medium text-sm" :style="{ color: '#1e002f' }">Carte enregistrée</p>
                                        <p class="text-xs text-gray-500">Visa •••• 4242</p>
                                    </div>
                                </div>
                                <Link
                                    href="/payment-methods"
                                    class="text-sm hover:underline"
                                    :style="{ color: '#c10e46' }"
                                >
                                    Modifier
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides client -->
            <div class="bg-white rounded-xl border p-4 sm:p-6" :style="{ borderColor: '#c10e4620' }">
                <h3 class="text-lg font-semibold mb-4" :style="{ color: '#1e002f' }">Actions rapides</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <Link
                        v-for="action in quickActions"
                        :key="action.label"
                        :href="action.route"
                        class="flex flex-col items-center p-4 rounded-lg border hover:shadow-md transition-all group text-center"
                        :style="{ borderColor: '#c10e4620' }"
                    >
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"
                            :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                        >
                            <component :is="action.icon" class="w-6 h-6" :style="{ color: '#c10e46' }" />
                        </div>
                        <span class="font-medium text-sm" :style="{ color: '#1e002f' }">{{ action.label }}</span>
                    </Link>
                </div>
            </div>

            <!-- Support section -->
            <div class="bg-white rounded-xl border p-6 text-center" :style="{ borderColor: '#c10e4620' }">
                <h3 class="text-lg font-semibold mb-2" :style="{ color: '#1e002f' }">Besoin d'aide ?</h3>
                <p class="text-gray-600 text-sm mb-4">
                    Notre équipe support est disponible 24h/24 pour vous aider
                </p>
                <div class="flex justify-center space-x-4">
                    <Link
                        href="/support/ticket"
                        class="px-6 py-2 rounded-lg border hover:shadow-md transition-all font-medium"
                        :style="{ borderColor: '#c10e4620', color: '#c10e46' }"
                    >
                        Ouvrir un ticket
                    </Link>
                    <Link
                        href="/faq"
                        class="px-6 py-2 rounded-lg border hover:shadow-md transition-all font-medium"
                        :style="{ borderColor: '#1e002f20', color: '#1e002f' }"
                    >
                        FAQ
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
