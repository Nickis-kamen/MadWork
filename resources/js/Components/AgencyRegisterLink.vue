<script setup>
import { Link } from '@inertiajs/vue3';
import { CirclePlus, SquarePen } from 'lucide-vue-next'

</script>

<template>
  <!-- Barre fixe en bas - Design amélioré -->
  <div class="w-full fixed bottom-0 left-0 z-40 bg-gray-200 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
    <div class="max-w-7xl mx-auto px-4 py-3">
      <div class="flex items-center justify-between flex-wrap gap-3">

        <!-- Message d'accroche avec icônes -->
        <div class="flex items-center gap-4">

          <!-- Texte principal -->
          <div>
            <p class="text-gray-800 font-semibold flex items-center gap-2">
              <SquarePen class="text-pink-600"/>
              Vous etes une agence à la recherche de clients ?
            </p>
            <p class="text-sm text-gray-500 hidden sm:block">
              Inscrivez-vous et commencez à partager vos services
            </p>
          </div>
        </div>

        <Link href="/register-agency"
            class="relative bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:shadow-lg transition-all duration-300 flex items-center gap-2">
            <CirclePlus />
            Inscription Agence
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
