<script setup>
import { ref } from 'vue';
import {
  Check,
  Crown,
  Star,
  Sparkles,
  Calendar,
  Clock
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

// Plans d'abonnement
const plans = [
    {
        id: 'basic',
        name: 'Basic',
        price: 50000,
        duration: 1,
        duration_label: '1 mois',
        color: 'blue',
        icon: Star,
        features: [
            'Visibilité sur la plateforme',
            'Accès au tableau de bord',
            'Services illimités',
            'Notifications par email'
        ],
        popular: false
    },
    {
        id: 'pro',
        name: 'Pro',
        price: 200000,
        duration: 5,
        duration_label: '5 mois',
        color: 'purple',
        icon: Crown,
        features: [
            'Visibilité sur la plateforme',
            'Accès au tableau de bord',
            'Services illimités',
            'Notifications par email'
        ],
        popular: true
    },
    {
        id: 'premium',
        name: 'Premium',
        price: 425000,
        duration: 12,
        duration_label: '1 an',
        color: 'gold',
        icon: Sparkles,
        features: [
            'Visibilité sur la plateforme',
            'Accès au tableau de bord',
            'Services illimités',
            'Notifications par email'
        ],
        popular: false
    }
];

// Calculer l'économie
const getSavings = (plan) => {
    if (plan.id === 'basic') return 0;
    if (plan.id === 'pro') {
        const monthlyPrice = 50000;
        const totalMonthly = monthlyPrice * 5;
        return totalMonthly - plan.price;
    }
    if (plan.id === 'premium') {
        const monthlyPrice = 50000;
        const totalMonthly = monthlyPrice * 12;
        return totalMonthly - plan.price;
    }
    return 0;
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

// Classe de couleur pour le plan
const getPlanColorClass = (plan) => {
    const colors = {
        blue: 'from-blue-500 to-blue-600',
        purple: 'from-purple-500 to-purple-600',
        gold: 'from-amber-500 to-amber-600'
    };
    return colors[plan.color] || 'from-gray-500 to-gray-600';
};

const getPlanBorderClass = (plan) => {
    const colors = {
        blue: 'border-blue-200',
        purple: 'border-purple-200',
        gold: 'border-amber-200'
    };
    return colors[plan.color] || 'border-gray-200';
};
</script>

<template>
    <section class="relative py-20 bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">

        <div class="relative container mx-auto px-4">
            <!-- En-tête -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-[#1e002f]/10 px-4 py-2 rounded-full mb-4 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-semibold tracking-wider text-[#1e002f]">ABONNEMENT AGENCE</span>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4">
                    Choisissez le <span class="text-[#c10e46]">plan idéal</span>
                </h2>

                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Boostez votre visibilité et accédez à des fonctionnalités exclusives selon vos besoins
                </p>
            </div>

            <!-- Grille des plans -->
            <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div v-for="plan in plans" :key="plan.id"
                    class="relative group"
                    :class="{ 'lg:-mt-4 lg:mb-4': plan.popular }">
                <Link :href="`/agency/plan/${plan.id}`"
                    method="post"
                    as="button">
                    <!-- Badge "Populaire" -->
                    <div v-if="plan.popular" class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-20">
                        <span class="bg-gradient-to-r from-[#c10e46] to-[#ff4d7e] text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                            🌟 Le plus populaire
                        </span>
                    </div>

                    <!-- Carte du plan -->
                    <div class="relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border-2"
                        :class="[getPlanBorderClass(plan), plan.popular ? 'border-[#c10e46]/30' : 'border-transparent']">

                        <!-- Dégradé d'en-tête -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r" :class="getPlanColorClass(plan)"></div>

                        <div class="p-8">
                            <!-- Icône et titre -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-gradient-to-br"
                                    :class="[getPlanColorClass(plan), 'shadow-lg']">
                                    <component :is="plan.icon" class="w-8 h-8 text-white" />
                                </div>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ plan.name }}</h3>
                            <p class="text-gray-500 text-sm mb-2">pour agence professionnelle</p>

                            <!-- Prix -->
                            <div class="mb-4">
                                <span class="text-4xl font-bold text-gray-900">{{ formatAmount(plan.price) }}</span>
                                <span class="text-gray-500"> / {{ plan.duration_label }}</span>
                                <div v-if="getSavings(plan) > 0" class="text-sm text-green-600 mt-1">
                                    Économie de {{ formatAmount(getSavings(plan)) }}
                                </div>
                            </div>

                            <!-- Durée -->
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 pb-6 border-b">
                                <Calendar class="w-4 h-4" />
                                <span>Validité : {{ plan.duration_label }}</span>
                                <Clock class="w-4 h-4 ml-2" />
                                <span>Accès immédiat</span>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-3 mb-8">
                                <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-2">
                                    <Check class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" />
                                    <span class="text-sm text-gray-600">{{ feature }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
</style>
