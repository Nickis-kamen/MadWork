<script setup>
import { Link } from '@inertiajs/vue3';
import logo from '@/assets/logo/logo.png';

</script>

<template>
    <nav class="bg-gray-900/45 shadow-md fixed top-0 z-50 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <Link href="/" class="flex items-center">
                    <img :src="logo" alt="Logo" class="h-20 w-auto">
                </Link>
                <div class="flex items-center space-x-8">

                    <!-- Liens de navigation -->
                    <div class="hidden md:flex space-x-6">
                        <Link
                            href="/"
                            class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                        >
                            Accueil
                        </Link>
                        <Link
                            href="/dashboard"
                            class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                        >
                            Services
                        </Link>
                        <Link
                            href="/profile"
                            class="text-white hover:text-pink-600 hover:bg-pink-50 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                        >
                            Profile
                        </Link>
                    </div>
                </div>

                <!-- Bouton Connexion -->
                <div class="flex items-center space-x-4">
                    <Link
                        href="/login"
                        class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Connexion
                    </Link>

                    <!-- Menu mobile -->
                    <button class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:text-pink-600 hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu mobile (dépliant) -->
        <div class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <Link
                    href="/"
                    class="text-white hover:text-pink-600 hover:bg-pink-50 block px-3 py-2 rounded-md text-base font-medium"
                >
                    Accueil
                </Link>
                <Link
                    href="/dashboard"
                    class="text-white hover:text-pink-600 hover:bg-pink-50 block px-3 py-2 rounded-md text-base font-medium"
                >
                    Dashboard
                </Link>
                <Link
                    href="/profile"
                    class="text-white hover:text-pink-600 hover:bg-pink-50 block px-3 py-2 rounded-md text-base font-medium"
                >
                    Profile
                </Link>
                <Link
                    href="/login"
                    class="bg-pink-600 hover:bg-pink-700 text-white block px-3 py-2 rounded-md text-base font-medium mt-4 text-center"
                >
                    Connexion
                </Link>
            </div>
        </div>
    </nav>
</template>
