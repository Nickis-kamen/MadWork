<script setup>

</script>

<template>
    <!-- About Section -->
    <section id="about" class="relative bg-gradient-to-br from-[#1e002f] to-[#2c0047] py-24 overflow-hidden">

        <!-- Éléments décoratifs -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#c10e46] opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-[#c10e46] opacity-10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                <!-- Colonne gauche - Contenu texte -->
                <div class="text-white space-y-8">
                    <!-- Badge -->
                    <div class="inline-flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                        <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2"></span>
                        <span class="text-sm font-semibold tracking-wider">À PROPOS DE MADWORK</span>
                    </div>

                    <!-- Titre principal -->
                    <p class="text-2xl md:text-4xl font-bold leading-tight">
                        Nous Connectons des <span class="text-[#c10e46]">Clients</span> ayant des besoins digitaux avec des <span class="text-[#c10e46]">agences</span> spécialisées
                    </p>

                    <!-- Statistiques -->
                    <div class="grid grid-cols-3 gap-4 pt-6">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-[#c10e46]">50+</div>
                            <div class="text-sm text-gray-400 mt-1">Nombre de clients</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-[#c10e46]">25+</div>
                            <div class="text-sm text-gray-400 mt-1">Nombre d'agences</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-[#c10e46]">4.9</div>
                            <div class="text-sm text-gray-400 mt-1">Note moyenne</div>
                        </div>
                    </div>

                    <!-- Bouton CTA -->
                    <div class="pt-4">
                        <a href="#contact" class="inline-flex items-center group bg-pink-600 hover:bg-pink-700 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                            Contactez-nous
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Colonne droite - Espace image -->
                <div class="relative">
                    <!-- Cercle décoratif -->
                    <div class="absolute -top-10 -left-10 w-64 h-64 bg-[#c10e46]/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10 rounded-2xl overflow-hidden border border-white/10">
                        <img
                            src="@/assets/images/cloud-technology-with-futuristic-hologram-smartwatch.jpg"
                            alt="MadAgency - Innovation digitale"
                            class="w-full h-auto rounded-2xl transition-transform duration-700 hover:scale-105"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>

</style>
