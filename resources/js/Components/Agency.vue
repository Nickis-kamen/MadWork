<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'

const agencies = ref([
  {
    id: 1,
    nom: "Digital Boost Agency",
    description: "Agence spécialisée dans la création de sites vitrines et e-commerce avec une approche design-first.",
    specialite: "E-commerce & Design",
    logo: "/images/agences/digital-boost.jpg",
    a_propos: "Fondée en 2018, Digital Boost accompagne les PME dans leur transformation digitale avec des solutions sur-mesure."
  },
  {
    id: 2,
    nom: "WebSolutions Pro",
    description: "Experts en applications web complexes et solutions d'entreprise innovantes.",
    specialite: "Applications Web",
    logo: "/images/agences/websolutions.jpg",
    a_propos: "Une équipe de développeurs passionnés spécialisés dans les architectures modernes et scalables."
  },
  {
    id: 3,
    nom: "CréaTech Studio",
    description: "L'alliance parfaite entre créativité et technologie pour des sites uniques et mémorables.",
    specialite: "Création & Design",
    logo: "/images/agences/createch.jpg",
    a_propos: "Des designers et développeurs unis pour créer des expériences digitales exceptionnelles."
  },
  {
    id: 4,
    nom: "E-Comm Experts",
    description: "Spécialistes de la vente en ligne et de l'optimisation du taux de conversion.",
    specialite: "E-commerce",
    logo: "/images/agences/ecomm.jpg",
    a_propos: "Plus de 100 boutiques en ligne créées avec un focus sur la performance et l'UX."
  },
  {
    id: 5,
    nom: "Mobile First Agency",
    description: "Expert en applications mobiles et sites responsifs pour une expérience utilisateur optimale.",
    specialite: "Mobile & Responsive",
    logo: "/images/agences/mobile-first.jpg",
    a_propos: "La philosophie mobile-first appliquée à tous vos projets digitaux."
  },
  {
    id: 6,
    nom: "SEO Performers",
    description: "Agence spécialisée en référencement naturel et stratégies de contenu.",
    specialite: "SEO & Marketing",
    logo: "/images/agences/seo-performers.jpg",
    a_propos: "Des experts SEO qui boostent votre visibilité et votre trafic organique."
  }
])

// Fonction pour obtenir les initiales (fallback si image non disponible)
const getInitials = (name) => {
  return name.split(' ').map(word => word[0]).join('').slice(0, 2).toUpperCase()
}
</script>

<template>
    <!-- Agencies Section -->
    <section id="agences" class="relative py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Éléments décoratifs -->
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-[#1e002f]/5 to-transparent"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#c10e46]/5 rounded-full blur-3xl"></div>
        <div class="absolute top-20 left-20 w-72 h-72 bg-[#1e002f]/5 rounded-full blur-3xl"></div>

        <div class="relative container mx-auto px-4">
            <!-- En-tête de section -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-[#1e002f]/10 px-4 py-2 rounded-full mb-4 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-semibold tracking-wider text-[#1e002f]">AGENCES PARTENAIRES</span>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-[#1e002f] mb-4">
                    Découvrez nos <span class="text-[#c10e46]">agences expertes</span>
                </h2>

                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Des partenaires de confiance sélectionnés pour leur expertise et leur savoir-faire unique
                </p>
            </div>

            <!-- Grille des agences -->
            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                <div v-for="agency in agencies" :key="agency.id"
                    class="group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">

                    <!-- Badge spécialité -->
                    <div class="absolute top-4 right-4 z-20">
                        <span class="bg-gradient-to-r from-[#c10e46] to-[#ff4d7e] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                            {{ agency.specialite }}
                        </span>
                    </div>

                    <!-- En-tête avec logo -->
                    <div class="relative h-48 bg-gradient-to-br from-[#1e002f] to-[#2a0040] overflow-hidden">

                        <!-- Logo ou initiales -->
                        <div class="absolute -bottom-12 left-6">
                            <div class="w-24 h-24 rounded-2xl bg-white shadow-xl border-4 border-white overflow-hidden transform group-hover:scale-105 transition-transform duration-500">
                                <img v-if="agency.logo" :src="agency.logo" :alt="agency.nom"
                                     class="w-full h-full object-cover"
                                     @error="agency.logoError = true">
                                <div v-else-if="agency.logoError"
                                     class="w-full h-full bg-gradient-to-br from-[#c10e46] to-[#ff4d7e] flex items-center justify-center text-white font-bold text-2xl">
                                    {{ getInitials(agency.nom) }}
                                </div>
                                <div v-else
                                     class="w-full h-full bg-gradient-to-br from-[#c10e46] to-[#ff4d7e] flex items-center justify-center text-white font-bold text-2xl">
                                    {{ getInitials(agency.nom) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="pt-16 p-6">
                        <h3 class="text-xl font-bold text-[#1e002f] mb-2 group-hover:text-[#c10e46] transition-colors">
                            {{ agency.nom }}
                        </h3>

                        <!-- "À propos" -->
                        <p class="text-sm text-gray-500 mb-3 italic border-l-3 border-[#c10e46] pl-3">
                            {{ agency.a_propos }}
                        </p>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ agency.description }}
                        </p>

                        <!-- Tags spécialités additionnelles -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                {{ agency.specialite }}
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                Antananarivo
                            </span>
                        </div>

                        <!-- Bouton et stats -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span>4.9 (128 avis)</span>
                            </div>

                            <Link :href="`/agences/${agency.id}`"
                                  class="inline-flex items-center text-[#c10e46] hover:text-[#ff4d7e] font-semibold text-sm group/link">
                                Voir profil
                                <svg class="w-4 h-4 ml-1 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mt-16">
                <div class="inline-flex items-center gap-4 bg-white p-1 rounded-full shadow-lg">
                    <Link href="/agences"
                          class="inline-flex items-center bg-gradient-to-r from-[#1e002f] to-[#2c0047] hover:from-[#2c0047] hover:to-[#3a0057] text-white px-8 py-4 rounded-full font-semibold transition-all duration-300 transform">
                        Toutes les agences
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
</style>
