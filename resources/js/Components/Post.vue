<script setup>
import { CirclePlus, SquarePen } from 'lucide-vue-next'
import { ref } from 'vue'

const showProjectForm = ref(false)

const newProject = ref({
  title: '',
  description: '',
  category: '',
  budget: '',
  deadline: '',
  location: '',
  cdc: null,
  isAnonyme: false
})

const categories = [
  'Site vitrine',
  'E-commerce',
  'Application mobile',
  'Application web',
  'Refonte de site',
  'Maintenance',
  'SEO / Marketing',
  'Design UI/UX'
]

const submitProject = () => {
  console.log('Projet:', newProject.value)
  showProjectForm.value = false
  // Ici vous pouvez envoyer les données à votre API
  alert('✅ Votre projet a été publié ! Les agences vous contacteront dans les 48h.')

  // Réinitialiser
  newProject.value = {
    title: '', description: '', category: '', budget: '',
    deadline: '', location: '', cdc: null, isAnonyme: false
  }
}

// Simulation upload fichier
const handleFileUpload = (e) => {
  newProject.value.cdc = e.target.files[0]
}
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
              Vous avez un projet digital ?
            </p>
            <p class="text-sm text-gray-500 hidden sm:block">
              Publiez-le et recevez jusqu'à 5 devis d'agences spécialisées
            </p>
          </div>
        </div>

        <button @click="showProjectForm = true"
            class="relative bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:shadow-lg transition-all duration-300 flex items-center gap-2">
            <CirclePlus />
            Publier mon projet
        </button>
      </div>
    </div>
  </div>

  <!-- Modal du formulaire - Design amélioré -->
  <div v-if="showProjectForm"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
     @click.self="showProjectForm = false">

    <div class="bg-white rounded-2xl max-w-2xl w-full flex flex-col shadow-2xl max-h-[90vh]">

        <!-- En-tête avec dégradé - STICKY en haut -->
        <div class="bg-gradient-to-r from-[#1e002f] to-[#2c0047] p-6 rounded-t-2xl flex-shrink-0">
        <div class="flex justify-between items-start">
            <div>
            <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                Publier votre projet
            </h2>
            <p class="text-white/70 text-sm mt-1">
                Remplissez ce formulaire pour recevoir des devis personnalisés
            </p>
            </div>
            <button @click="showProjectForm = false"
                    class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white/60 hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            </button>
        </div>
        </div>

        <!-- Formulaire - UNIQUEMENT cette partie défile -->
        <div class="p-6 space-y-5 overflow-y-auto">
        <!-- Titre du projet -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
            Titre du projet <span class="text-[#c10e46]">*</span>
            </label>
            <input
            v-model="newProject.title"
            type="text"
            placeholder="ex: Création d'un site e-commerce pour boutique de mode"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#c10e46] focus:ring-2 focus:ring-[#c10e46]/20 transition-all"
            >
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
            Description détaillée <span class="text-[#c10e46]">*</span>
            </label>
            <textarea
            v-model="newProject.description"
            rows="4"
            placeholder="Décrivez votre projet : objectifs, fonctionnalités souhaitées, design souhaité, contraintes techniques..."
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#c10e46] focus:ring-2 focus:ring-[#c10e46]/20 transition-all resize-none"
            ></textarea>
        </div>

        <!-- Grille catégorie et budget -->
        <div class="grid md:grid-cols-2 gap-4">
            <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie</label>
            <select
                v-model="newProject.category"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#c10e46] focus:ring-2 focus:ring-[#c10e46]/20 bg-white"
            >
                <option value="" disabled selected>Sélectionnez une catégorie</option>
                <option v-for="cat in categories" :key="cat">{{ cat }}</option>
            </select>
            </div>
            <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Budget estimé (Ar)</label>
            <input
                v-model="newProject.budget"
                type="number"
                placeholder=""
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#c10e46] focus:ring-2 focus:ring-[#c10e46]/20 transition-all"
            >
            </div>
        </div>

        <!-- Grille délai et localisation -->
        <div class="grid md:grid-cols-2 gap-4">
            <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Délai souhaité (en jours)</label>
            <input
                v-model="newProject.deadline"
                type="number"
                placeholder="ex: 30"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#c10e46] focus:ring-2 focus:ring-[#c10e46]/20 transition-all"
            >
        </div>
        <!-- Cahier des charges (upload) -->
        <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Cahier des charges
                    <span class="text-xs font-normal text-gray-400 ml-2">(PDF, DOC - max 10Mo)</span>
                </label>
                <div class="relative">
                <input
                    type="file"
                    @change="handleFileUpload"
                    accept=".pdf,.doc,.docx,.txt"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                >
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-[#c10e46] hover:bg-[#c10e46]/5 transition-all">
                    <p class="text-sm text-gray-600">
                    <span class="font-semibold text-[#c10e46]">Cliquez pour uploader</span>
                    </p>
                    <p v-if="newProject.cdc" class="text-xs text-green-600 mt-1">
                    ✓ {{ newProject.cdc.name }}
                    </p>
                </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Pied du formulaire - STICKY en bas -->
        <div class="bg-gray-50 p-6 border-t rounded-b-2xl flex-shrink-0">
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
            <button @click="showProjectForm = false"
                    class="px-6 py-3 border border-gray-200 rounded-xl hover:bg-white transition-all font-medium text-gray-600 order-2 sm:order-1">
            Annuler
            </button>
            <button @click="submitProject"
                    class="px-8 py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-xl  transition-all font-semibold flex items-center justify-center gap-2 order-1 sm:order-2">
            Publier mon projet
            </button>
        </div>
        </div>
    </div>
    </div>
</template>

<style scoped>
</style>
