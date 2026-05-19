<script setup>
import { ref, reactive } from 'vue'

const formData = reactive({
  name: '',
  email: '',
  phone: '',
  company: '',
  message: '',
  projectType: ''
})

const isLoading = ref(false)
const isSuccess = ref(false)
const errorMessage = ref('')

const projectTypes = [
  { value: 'web', label: 'Développement Web' },
  { value: 'mobile', label: 'Application Mobile' },
  { value: 'design', label: 'Design UI/UX' },
  { value: 'marketing', label: 'Marketing Digital' },
  { value: 'consulting', label: 'Consulting' },
  { value: 'other', label: 'Autre' }
]

const handleSubmit = async () => {
  isLoading.value = true
  errorMessage.value = ''

  // Validation simple
  if (!formData.name || !formData.email || !formData.message) {
    errorMessage.value = 'Veuillez remplir tous les champs obligatoires'
    isLoading.value = false
    return
  }

  // Simuler un envoi vers une API
  try {
    // Remplacez par votre appel API réel
    await new Promise(resolve => setTimeout(resolve, 1500))

    console.log('Formulaire soumis:', formData)
    isSuccess.value = true

    // Réinitialiser le formulaire après succès
    setTimeout(() => {
      isSuccess.value = false
      Object.keys(formData).forEach(key => {
        formData[key] = ''
      })
    }, 3000)

  } catch (error) {
    errorMessage.value = 'Une erreur est survenue. Veuillez réessayer.'
    console.error(error)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <section id="contact" class="relative bg-gradient-to-br from-[#1e002f] to-[#2c0047] py-24 overflow-hidden">

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- En-tête de section -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
          <span class="w-2 h-2 bg-[#c10e46] rounded-full mr-2"></span>
          <span class="text-sm font-semibold tracking-wider text-white">CONTACTEZ-NOUS</span>
        </div>
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">
          Envoyer votre <span class="text-[#c10e46]">message</span>
        </h2>
        <p class="text-gray-300 max-w-2xl mx-auto text-lg">
          Notre équipe est là pour vous accompagner dans votre réussite digitale.
        </p>
      </div>
        <!-- Colonne droite - Formulaire -->
        <div class="max-w-3xl m-auto bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
          <form @submit.prevent="handleSubmit" class="space-y-6">

            <!-- Nom et Email -->
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-white mb-2 text-sm font-medium">
                  Nom complet <span class="text-[#c10e46]">*</span>
                </label>
                <input
                  v-model="formData.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-[#c10e46] transition-colors"
                  placeholder="Jean Dupont"
                />
              </div>

              <div>
                <label class="block text-white mb-2 text-sm font-medium">
                  Email <span class="text-[#c10e46]">*</span>
                </label>
                <input
                  v-model="formData.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-[#c10e46] transition-colors"
                  placeholder="jean@exemple.com"
                />
              </div>
            </div>

            <!-- Téléphone et Entreprise -->
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-white mb-2 text-sm font-medium">
                  Téléphone
                </label>
                <input
                  v-model="formData.phone"
                  type="tel"
                  class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-[#c10e46] transition-colors"
                  placeholder="+261 38 06 838 79"
                />
              </div>

              <div>
                <label class="block text-white mb-2 text-sm font-medium">
                  Entreprise
                </label>
                <input
                  v-model="formData.company"
                  type="text"
                  class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-[#c10e46] transition-colors"
                  placeholder="Votre entreprise"
                />
              </div>
            </div>

            <!-- Message -->
            <div>
              <label class="block text-white mb-2 text-sm font-medium">
                Message <span class="text-[#c10e46]">*</span>
              </label>
              <textarea
                v-model="formData.message"
                required
                rows="5"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-[#c10e46] transition-colors resize-none"
                placeholder="Ecrivez votre message ici..."
              ></textarea>
            </div>

            <!-- Message d'erreur -->
            <div v-if="errorMessage" class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm">
              {{ errorMessage }}
            </div>

            <!-- Message de succès -->
            <div v-if="isSuccess" class="bg-green-500/20 border border-green-500 text-green-200 px-4 py-3 rounded-lg text-sm">
              ✅ Message envoyé avec succès ! Nous vous contacterons rapidement.
            </div>

            <!-- Bouton d'envoi -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full bg-pink-600 hover:bg-pink-700 text-white px-8 py-4 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!isLoading">Envoyer le message</span>
              <span v-else class="flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Envoi en cours...
              </span>
            </button>
          </form>
        </div>
      </div>
  </section>
</template>

<style scoped>
/* Styles supplémentaires si nécessaire */
</style>
