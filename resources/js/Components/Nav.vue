<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from './ApplicationLogo.vue';
import { computed, ref } from 'vue';
import { PiggyBank, Menu, X } from 'lucide-vue-next';

const user = computed(() => usePage().props.auth.user)
const isCustomer = computed(() => user.value?.role === 'customer')
const isAgency = computed(() => user.value?.role === 'agency')
const isAdmin = computed(() => user.value?.role === 'admin')

const balance = computed(() => user.value?.balance?.amount || 0)
const mobileMenuOpen = ref(false)

// Formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

// Fermer le menu mobile après navigation
const closeMobileMenu = () => {
    mobileMenuOpen.value = false
}
</script>

<template>
    <nav class="bg-gray-900/45 shadow-md fixed top-0 z-50 w-full backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <Link href="/" class="flex items-center shrink-0" @click="closeMobileMenu">
                    <ApplicationLogo />
                </Link>

                <!-- Liens de navigation desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <Link
                        href="/"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Accueil
                    </Link>
                    <Link
                        href="/#about"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        A propos
                    </Link>
                    <Link
                        href="/services"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Services
                    </Link>
                    <Link v-if="!user"
                        href="/#contact"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Contact
                    </Link>
                    <Link v-if="isCustomer"
                        href="/my-devis"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Vos devis
                    </Link>
                    <Link v-if="isCustomer"
                        href="/orders"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Vos commandes
                    </Link>
                    <Link v-if="isAgency"
                        href="/agency/dashboard"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Votre espace
                    </Link>
                    <Link v-if="isAdmin"
                        href="/admin/dashboard"
                        class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                    >
                        Espace admin
                    </Link>
                </div>

                <!-- Actions utilisateur -->
                <div class="flex items-center space-x-3">
                    <Link v-if="user" href="/balance">
                        <p class="bg-amber-500 p-2 rounded-xl flex gap-2 items-center font-bold text-white hover:bg-amber-600 transition-colors">
                            <PiggyBank :size="18" />
                            {{ formatAmount(balance) }}
                        </p>
                    </Link>
                    <Link v-if="!user"
                        href="/login"
                        class="hidden md:block bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Connexion
                    </Link>
                    <Link v-else
                        href="/logout"
                        method="post"
                        class="hidden md:block bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Déconnexion
                    </Link>

                    <!-- Bouton menu mobile -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:text-pink-600 hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        :aria-label="mobileMenuOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
                    >
                        <Menu v-if="!mobileMenuOpen" :size="24" />
                        <X v-else :size="24" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu mobile (dépliant avec animation) -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div v-if="mobileMenuOpen" class="md:hidden bg-gray-900/95 backdrop-blur-md border-t border-white/10 shadow-lg">
                <div class="px-4 pt-2 pb-4 space-y-1">
                    <!-- Liens de navigation mobile -->
                    <Link
                        href="/"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Accueil
                    </Link>
                    <Link
                        href="/#about"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        A propos
                    </Link>
                    <Link
                        href="/services"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Services
                    </Link>
                    <Link v-if="!user"
                        href="/#contact"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Contact
                    </Link>
                    <Link v-if="isCustomer"
                        href="/my-devis"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Vos devis
                    </Link>
                    <Link v-if="isCustomer"
                        href="/orders"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Vos commandes
                    </Link>
                    <Link v-if="isAgency"
                        href="/agency/dashboard"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Votre espace
                    </Link>
                    <Link v-if="isAdmin"
                        href="/admin/dashboard"
                        @click="closeMobileMenu"
                        class="flex items-center text-white hover:text-pink-600 hover:bg-pink-50/10 px-3 py-3 rounded-lg text-base font-medium transition-colors"
                    >
                        Espace admin
                    </Link>

                    <!-- Séparateur -->
                    <div class="border-t border-white/20 my-2"></div>

                    <!-- Actions mobile (connexion/déconnexion) -->
                    <Link v-if="!user"
                        href="/login"
                        @click="closeMobileMenu"
                        class="flex items-center justify-center bg-pink-600 hover:bg-pink-700 text-white px-4 py-3 rounded-xl text-base font-semibold transition-all duration-200 mt-2"
                    >
                        Connexion
                    </Link>
                    <Link v-else
                        href="/logout"
                        method="post"
                        @click="closeMobileMenu"
                        class="flex items-center justify-center bg-pink-600 hover:bg-pink-700 text-white px-4 py-3 rounded-xl text-base font-semibold transition-all duration-200 mt-2"
                    >
                        Déconnexion
                    </Link>
                </div>
            </div>
        </Transition>
    </nav>
</template>
