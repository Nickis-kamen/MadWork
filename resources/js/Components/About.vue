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

            <!-- <div class="grid md:grid-cols-3 gap-6 mt-24">
                <div class="bg-white/5 backdrop-blur-sm p-6 rounded-xl border border-white/10 hover:border-[#c10e46]/50 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-[#c10e46]/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-[#c10e46]/30 transition-colors">
                        <svg class="w-6 h-6 text-[#c10e46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold mb-2">Innovation</h3>
                    <p class="text-gray-400">Des solutions créatives et à la pointe de la technologie</p>
                </div>

                <div class="bg-white/5 backdrop-blur-sm p-6 rounded-xl border border-white/10 hover:border-[#c10e46]/50 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-[#c10e46]/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-[#c10e46]/30 transition-colors">
                        <svg class="w-6 h-6 text-[#c10e46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold mb-2">Expertise</h3>
                    <p class="text-gray-400">Une équipe multi-disciplinaire au service de votre succès</p>
                </div>

                <div class="bg-white/5 backdrop-blur-sm p-6 rounded-xl border border-white/10 hover:border-[#c10e46]/50 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-[#c10e46]/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-[#c10e46]/30 transition-colors">
                        <svg class="w-6 h-6 text-[#c10e46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold mb-2">Passion</h3>
                    <p class="text-gray-400">Chaque projet est unique et mérite toute notre attention</p>
                </div>
            </div> -->
        </div>
    </section>
</template>

<style scoped>

</style>
