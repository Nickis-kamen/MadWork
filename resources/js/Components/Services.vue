<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'

// Données dynamiques des services - SIMPLIFIÉES
const services = ref([
  {
    id: 1,
    title: "Site Vitrine",
    description: "Une présence en ligne élégante pour valoriser votre marque.",
    price: "À partir de 1 500€",
    icon: "presentation",
    badge: "Essentiel"
  },
  {
    id: 2,
    title: "Site E-commerce",
    description: "Vendez vos produits en ligne avec une boutique performante.",
    price: "À partir de 3 500€",
    icon: "ecommerce",
    badge: "Best-seller"
  },
  {
    id: 3,
    title: "Application Web",
    description: "Des solutions sur-mesure pour vos besoins spécifiques.",
    price: "À partir de 5 000€",
    icon: "webapp",
    badge: "Sur-mesure"
  },
  {
    id: 4,
    title: "Site Dynamique",
    description: "Gérez votre contenu en toute autonomie avec un CMS.",
    price: "À partir de 2 500€",
    icon: "dynamic",
    badge: "Évolutif"
  },
  {
    id: 5,
    title: "Site One-Page",
    description: "L'essentiel en une page, idéal pour vos campagnes.",
    price: "À partir de 900€",
    icon: "onepage",
    badge: "Impact"
  },
  {
    id: 6,
    title: "Maintenance",
    description: "Sécurité, mises à jour et évolutions de votre site.",
    price: "À partir de 50€/mois",
    icon: "maintenance",
    badge: "Sérénité"
  }
])

// État pour le service actif (mobile)
const activeService = ref(0)
</script>

<template>
    <!-- Services Section -->
    <section id="services" class="relative py-20 bg-gray-200">
        <!-- Éléments décoratifs légers -->
        <div class="absolute top-0 left-0 w-full h-64 bg-linear-to-b from-[#1e002f]/5 to-transparent"></div>

        <div class="relative container mx-auto px-4">
            <!-- En-tête de section -->
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center bg-[#1e002f]/5 px-4 py-2 rounded-full mb-4">
                    <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2"></span>
                    <span class="text-sm font-semibold tracking-wider text-[#1e002f]">NOS SERVICES</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-bold text-[#1e002f] mb-4">
                    Des solutions web <span class="text-[#c10e46]">claires et accessibles</span>
                </h2>

                <p class="text-gray-600">
                    Du site vitrine à l'application sur-mesure, trouvez la formule adaptée à votre projet.
                </p>
            </div>

            <!-- Grille des services - Version Desktop (3 colonnes) -->
            <div class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 gap-6">
                <div v-for="(service, index) in services" :key="service.id"
                    class="relative group bg-[url('@/assets/images/3265223.jpg')] bg-cover rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-white/10 hover:border-white/30 hover:-translate-y-1">
                    <div class="absolute inset-0 bg-linear-to-br from-[#1e002f]/70 to-[#c10e46]/70 rounded-xl"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <!-- Icône -->
                        <div class="w-16 h-16 bg-linear-to-br from-white/10 to-white/5 rounded-lg flex items-center justify-center border border-white/20 mb-4">
                            <svg v-if="service.icon === 'presentation'" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                            </svg>
                            <svg v-else-if="service.icon === 'ecommerce'" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <svg v-else-if="service.icon === 'webapp'" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <svg v-else-if="service.icon === 'dynamic'" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                            <svg v-else-if="service.icon === 'onepage'" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else-if="service.icon === 'maintenance'" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <div class="border-b border-white/20 pb-2 mb-2">
                            <h3 class="text-xl font-bold text-white">
                                {{ service.title }}
                            </h3>
                        </div>

                        <p class="text-white/80 text-sm mb-4 leading-relaxed">
                            {{ service.description }}
                        </p>

                        <span class="text-lg font-bold text-white mb-5">{{ service.price }}</span>
                        <div class="mt-auto">
                            <Link href="/contact"
                                class="bg-white/10 text-white font-semibold text-lg flex items-center px-3 py-2 rounded-lg hover:bg-white/20 transition-colors duration-300 border border-white/10 hover:border-white/20">
                                Demander un devis
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-16">
                <Link href="/contact"
                   class="inline-flex items-center bg-[#1e002f] hover:bg-[#2c0047] text-white px-8 py-4 rounded-lg font-semibold transition-all duration-300">
                    Discuter de votre projet
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
                <p class="text-sm text-gray-500 mt-4">
                    Tous nos projets incluent design responsive, SEO et formation
                </p>
            </div>
        </div>
    </section>
</template>
