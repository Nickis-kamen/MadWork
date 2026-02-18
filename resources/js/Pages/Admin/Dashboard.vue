<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
  Users,
  DollarSign,
  ShoppingBag,
  FileText,
  Settings,
  Bell,
  Server,
  Database,
  Shield,
  Activity
} from 'lucide-vue-next';

// Statistiques avec vos couleurs
const stats = [
  {
    title: 'Chiffre d\'affaires',
    value: '45 678 €',
    change: 12.5,
    icon: DollarSign,
    color: '#c10e46'
  },
  {
    title: 'Utilisateurs',
    value: '1 234',
    change: 8.2,
    icon: Users,
    color: '#1e002f'
  },
  {
    title: 'Commandes',
    value: '456',
    change: -2.4,
    icon: ShoppingBag,
    color: '#c10e46'
  },
  {
    title: 'Tickets',
    value: '23',
    change: 5.7,
    icon: FileText,
    color: '#1e002f'
  }
];

// Actions rapides
const quickActions = [
  { label: 'Nouvel utilisateur', icon: Users, action: 'addUser' },
  { label: 'Créer un devis', icon: FileText, action: 'createQuote' },
  { label: 'Paramètres', icon: Settings, action: 'settings' },
  { label: 'Notifications', icon: Bell, action: 'notifications' }
];

// Activité récente
const recentActivity = [
  {
    id: 1,
    title: 'Nouvel utilisateur inscrit',
    description: 'Marie Dupont a créé un compte',
    time: '5 min',
    icon: Users
  },
  {
    id: 2,
    title: 'Devis validé',
    description: 'Projet site vitrine - 2 500 €',
    time: '1 heure',
    icon: FileText
  },
  {
    id: 3,
    title: 'Nouvelle commande',
    description: 'Pack marketing digital - 890 €',
    time: '3 heures',
    icon: ShoppingBag
  },
  {
    id: 4,
    title: 'Mise à jour système',
    description: 'Version 2.5.0 installée',
    time: '5 heures',
    icon: Activity
  }
];

// État du système
const systemStatus = [
  { name: 'Base de données', status: '99.9% uptime', icon: Database },
  { name: 'Serveur', status: 'Charge: 23%', icon: Server },
  { name: 'Sécurité', status: 'Firewall actif', icon: Shield },
  { name: 'API', status: 'Latence: 45ms', icon: Activity }
];

// Graphiques simulés
const handleQuickAction = (action) => {
  console.log('Action:', action.label);
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <!-- Container avec fond blanc pur -->
        <div class="p-4 sm:p-6 space-y-6 bg-white min-h-screen">
            <!-- Header avec gradient subtil -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Tableau de Bord Admin
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Vue d'ensemble de votre activité
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button
                        class="px-4 py-2 text-white rounded-xl hover:shadow-lg transition-all duration-200 hover:scale-105 font-semibold shadow-md"
                        :style="{
                            background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)'
                        }"
                    >
                        Générer un rapport
                    </button>
                </div>
            </div>

            <!-- Stats Cards - Design épuré sur fond blanc -->
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
                            <p :class="['text-sm mt-1 font-medium', stat.change > 0 ? 'text-green-600' : 'text-red-600']">
                                {{ stat.change > 0 ? '↗' : '↘' }} {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}%
                            </p>
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

            <!-- Charts Section -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
                <!-- Revenue Chart -->
                <div class="bg-white p-4 sm:p-6 rounded-xl border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Revenus mensuels</h3>
                        <select
                            class="text-sm border rounded-lg px-3 py-1 bg-white focus:ring-2 focus:outline-none"
                            :style="{ borderColor: '#c10e4620', ringColor: '#c10e46' }"
                        >
                            <option>30 jours</option>
                            <option>90 jours</option>
                            <option>1 an</option>
                        </select>
                    </div>
                    <div
                        class="h-64 rounded-xl flex items-center justify-center border"
                        :style="{
                            background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)',
                            borderColor: '#c10e4620'
                        }"
                    >
                        <!-- Simulateur de graphique -->
                        <div class="w-full h-full p-4 flex items-end justify-around">
                            <div v-for="i in 7" :key="i" class="flex flex-col items-center">
                                <div
                                    class="w-8 rounded-t-lg transition-all duration-500 hover:scale-110"
                                    :style="{
                                        height: (Math.random() * 100 + 50) + 'px',
                                        background: `linear-gradient(to top, #c10e46, #1e002f)`
                                    }"
                                ></div>
                                <span class="text-xs text-gray-500 mt-2">J{{ i }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Chart -->
                <div class="bg-white p-4 sm:p-6 rounded-xl border" :style="{ borderColor: '#1e002f20' }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Activité des utilisateurs</h3>
                        <select
                            class="text-sm border rounded-lg px-3 py-1 bg-white focus:ring-2 focus:outline-none"
                            :style="{ borderColor: '#1e002f20', ringColor: '#1e002f' }"
                        >
                            <option>7 jours</option>
                            <option>30 jours</option>
                            <option>90 jours</option>
                        </select>
                    </div>
                    <div
                        class="h-64 rounded-xl flex items-center justify-center border"
                        :style="{
                            background: 'linear-gradient(135deg, #1e002f05 0%, #c10e4605 100%)',
                            borderColor: '#1e002f20'
                        }"
                    >
                        <!-- Graphique circulaire simulé -->
                        <div class="relative w-40 h-40">
                            <svg viewBox="0 0 100 100" class="transform -rotate-90 w-full h-full">
                                <circle
                                    cx="50"
                                    cy="50"
                                    r="40"
                                    fill="none"
                                    stroke="#c10e4620"
                                    stroke-width="10"
                                />
                                <circle
                                    cx="50"
                                    cy="50"
                                    r="40"
                                    fill="none"
                                    :stroke="'#c10e46'"
                                    stroke-width="10"
                                    stroke-dasharray="251.2"
                                    :stroke-dashoffset="251.2 * 0.3"
                                    class="transition-all duration-1000"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center flex-col">
                                <span class="text-2xl font-bold" :style="{ color: '#1e002f' }">1.2k</span>
                                <span class="text-xs text-gray-500">actifs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Quick Actions -->
                <div class="lg:col-span-1 bg-white rounded-xl border p-4 sm:p-6" :style="{ borderColor: '#c10e4620' }">
                    <h3 class="text-lg font-semibold mb-4" :style="{ color: '#1e002f' }">Actions rapides</h3>
                    <div class="space-y-3">
                        <button
                            v-for="action in quickActions"
                            :key="action.label"
                            @click="handleQuickAction(action)"
                            class="w-full flex items-center space-x-3 p-3 rounded-lg border hover:shadow-md transition-all duration-200 group cursor-pointer"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                            >
                                <component :is="action.icon" class="w-5 h-5" :style="{ color: '#c10e46' }" />
                            </div>
                            <span class="font-medium text-sm" :style="{ color: '#1e002f' }">{{ action.label }}</span>
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="lg:col-span-2 bg-white rounded-xl border" :style="{ borderColor: '#1e002f20' }">
                    <div class="p-4 sm:p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                        <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">Activité récente</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-4">
                            <div
                                v-for="activity in recentActivity"
                                :key="activity.id"
                                class="flex items-center space-x-4 p-3 rounded-lg border hover:shadow-sm transition-all duration-200 group cursor-pointer"
                                :style="{ borderColor: '#c10e4620' }"
                            >
                                <div
                                    class="w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                    :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                                >
                                    <component :is="activity.icon" class="w-5 h-5" :style="{ color: '#c10e46' }" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm" :style="{ color: '#1e002f' }">{{ activity.title }}</p>
                                    <p class="text-gray-500 text-xs mt-1">{{ activity.description }}</p>
                                </div>
                                <span class="text-gray-400 text-xs font-medium whitespace-nowrap">{{ activity.time }}</span>
                            </div>
                        </div>

                        <!-- Voir plus -->
                        <button
                            class="w-full mt-4 py-2 rounded-lg transition-colors text-sm font-medium border"
                            :style="{
                                color: '#c10e46',
                                borderColor: '#c10e4620',
                                backgroundColor: '#c10e4605'
                            }"
                        >
                            Voir toute l'activité
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl border p-4 sm:p-6" :style="{ borderColor: '#c10e4620' }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" :style="{ color: '#1e002f' }">État du système</h3>
                    <div class="flex items-center space-x-2 text-sm text-green-600 font-medium">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span>Tous les systèmes fonctionnent</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div
                        v-for="system in systemStatus"
                        :key="system.name"
                        class="text-center p-4 rounded-lg"
                        :style="{
                            background: 'linear-gradient(135deg, #c10e4605 0%, #1e002f05 100%)',
                            borderColor: '#c10e4620'
                        }"
                    >
                        <component :is="system.icon" class="w-8 h-8 mx-auto mb-2" :style="{ color: '#c10e46' }" />
                        <p class="font-medium text-sm" :style="{ color: '#1e002f' }">{{ system.name }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ system.status }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
</style>
