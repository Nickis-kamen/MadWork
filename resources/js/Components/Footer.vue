<script setup>
import { Mail, MapPin, Phone } from 'lucide-vue-next'
import ApplicationLogo from './ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear()

const quickLinks = [
  { name: 'Accueil', href: '#home' },
  { name: 'A propos', href: '#about' },
  { name: 'Services', href: '#services' },
  { name: 'Contact', href: '#contact' }
]

const legalLinks = [
  { name: 'Mentions légales', href: '#' },
  { name: 'Politique de confidentialité', href: '#' },
  { name: 'CGU', href: '#' },
  { name: 'Cookies', href: '#' }
]
</script>

<template>
  <footer class="relative bg-gradient-to-br from-[#1e002f] to-[#2c0047] text-white overflow-hidden pb-16">

    <!-- Footer Main Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

        <!-- Colonne 1: Logo et description -->
        <div class="space-y-6">
            <Link href="/" class="flex items-center">
                    <ApplicationLogo />
            </Link>
            <p class="text-gray-400 leading-relaxed">
                MadWork connecte les entreprises ayant des besoins digitaux avec les meilleures agences spécialisées.
            </p>
        </div>

        <!-- Colonne 2: Liens rapides -->
        <div>
          <h4 class="text-lg font-semibold mb-6">Liens rapides</h4>
          <ul class="space-y-3">
            <li v-for="link in quickLinks" :key="link.name">
              <a
                :href="link.href"
                class="text-gray-400 hover:text-[#c10e46] transition-colors duration-300 flex items-center group"
              >
                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Colonne 4: Contact -->
        <div>
          <h4 class="text-lg font-semibold mb-6">Contactez-nous</h4>
          <ul class="space-y-4">
            <li class="flex items-start space-x-3">
              <MapPin class="w-5 h-5 text-[#c10e46]" />
              <span class="text-gray-400 text-sm">Madagascar, Antananarivo</span>
            </li>
            <li class="flex items-center space-x-3">
              <Mail class="w-5 h-5 text-[#c10e46]" />
              <a href="mailto:contact@madwork.com" class="text-gray-400 hover:text-[#c10e46] transition-colors">
                contact@madwork.com
              </a>
            </li>
            <li class="flex items-center space-x-3">
              <Phone class="w-5 h-5 text-[#c10e46]" />
              <a href="tel:+33123456789" class="text-gray-400 hover:text-[#c10e46] transition-colors">
                +261 38 06 838 79
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="relative border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <p class="text-gray-400 text-sm">
            &copy; {{ currentYear }} MadWork. Tous droits réservés.
          </p>
          <div class="flex space-x-6">
            <a
              v-for="link in legalLinks"
              :key="link.name"
              :href="link.href"
              class="text-gray-400 hover:text-[#c10e46] text-sm transition-colors"
            >
              {{ link.name }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
</style>
