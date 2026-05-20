<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  FolderOpen,
  ArrowLeft,
  Save,
  X,
  Upload,
  FileText,
  AlertCircle
} from 'lucide-vue-next';
import Nav from '@/Components/Nav.vue';

// Props
const props = defineProps({
    project: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    }
});

// État pour le fichier
const cdcFile = ref(null);

// Formulaire simplifié
const form = useForm({
    title: props.project.title || '',
    category_id: props.project.category_id || '',
    delai: props.project.delai || '',
    description: props.project.description || '',
    budget: props.project.budget || '',
    cdc_file: null,
    _method: 'PUT'
});

// Gestion du fichier
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.type !== 'application/pdf') {
            alert('Seuls les fichiers PDF sont acceptés');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Le fichier ne doit pas dépasser 5 Mo');
            return;
        }
        cdcFile.value = file;
        form.cdc_file = file;
    }
};

// Supprimer le fichier
const removeFile = () => {
    cdcFile.value = null;
    form.cdc_file = null;
    const fileInput = document.getElementById('cdc_file');
    if (fileInput) fileInput.value = '';
};

// Soumission
const submitForm = () => {
    form.post(`/customer/project/${props.project.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Modifier - ${project.title}`" />
    <Nav />

    <div class="min-h-screen bg-gray-50 mt-20">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <!-- Header -->
            <div class="mb-6">
                <Link
                    :href="route('customer.projects.index')"
                    class="inline-flex items-center space-x-1 text-sm mb-4 hover:opacity-70"
                    :style="{ color: '#c10e46' }"
                >
                    <ArrowLeft class="w-4 h-4" />
                    <span>Retour</span>
                </Link>

                <h1 class="text-2xl font-bold" :style="{ color: '#1e002f' }">
                    Modifier le projet
                </h1>
                <p class="text-gray-500 text-sm mt-1">Modifiez les informations de votre projet</p>
            </div>

            <!-- Formulaire simplifié -->
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Titre -->
                <div class="bg-white rounded-lg border p-4" :style="{ borderColor: '#c10e4620' }">
                    <label class="block text-sm font-medium mb-1" :style="{ color: '#1e002f' }">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.title"
                        required
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent"
                        placeholder="Ex: Création site e-commerce"
                    />
                    <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                </div>

                <!-- Catégorie et Délai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Catégorie -->
                    <div class="bg-white rounded-lg border p-4" :style="{ borderColor: '#c10e4620' }">
                        <label class="block text-sm font-medium mb-1" :style="{ color: '#1e002f' }">
                            Catégorie
                        </label>
                        <select
                            v-model="form.category_id"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent text-gray-700"
                        >
                            <option value="">Sélectionnez une catégorie</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id" class="text-gray-900">
                                {{ cat.title }}
                            </option>
                        </select>
                    </div>

                    <!-- Délai -->
                    <div class="bg-white rounded-lg border p-4" :style="{ borderColor: '#c10e4620' }">
                        <label class="block text-sm font-medium mb-1" :style="{ color: '#1e002f' }">
                            Délai <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                v-model="form.delai"
                                required
                                min="1"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent"
                                placeholder="30"
                            />
                            <span class="text-gray-500">jours</span>
                        </div>
                        <p v-if="form.errors.delai" class="text-red-500 text-xs mt-1">{{ form.errors.delai }}</p>
                    </div>
                </div>

                <!-- Budget -->
                <div class="bg-white rounded-lg border p-4" :style="{ borderColor: '#c10e4620' }">
                    <label class="block text-sm font-medium mb-1" :style="{ color: '#1e002f' }">
                        Budget <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Ar</span>
                        <input
                            type="number"
                            v-model="form.budget"
                            required
                            min="1"
                            class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent"
                            placeholder="1 000 000"
                        />
                    </div>
                    <p v-if="form.errors.budget" class="text-red-500 text-xs mt-1">{{ form.errors.budget }}</p>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-lg border p-4" :style="{ borderColor: '#c10e4620' }">
                    <label class="block text-sm font-medium mb-1" :style="{ color: '#1e002f' }">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.description"
                        required
                        rows="4"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#c10e46] focus:border-transparent resize-y"
                        placeholder="Décrivez votre projet..."
                    ></textarea>
                    <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
                </div>

                <!-- Fichier PDF -->
                <div class="bg-white rounded-lg border p-4" :style="{ borderColor: '#c10e4620' }">
                    <label class="block text-sm font-medium mb-1" :style="{ color: '#1e002f' }">
                        Cahier des charges (PDF)
                    </label>

                    <!-- Fichier actuel -->
                    <div v-if="project.cdc_file && !cdcFile" class="mb-3 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <FileText class="w-4 h-4" :style="{ color: '#c10e46' }" />
                            <span class="text-sm text-gray-600">Fichier actuel</span>
                        </div>
                        <a :href="`/storage/${project.cdc_file}`" download class="text-sm hover:underline" :style="{ color: '#c10e46' }">
                            Télécharger
                        </a>
                    </div>

                    <!-- Upload -->
                    <div>
                        <input
                            id="cdc_file"
                            type="file"
                            accept=".pdf"
                            @change="handleFileChange"
                            class="hidden"
                        />
                        <label
                            for="cdc_file"
                            class="flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                            :class="cdcFile ? 'border-[#c10e46] bg-[#c10e46]/5' : 'border-gray-300'"
                        >
                            <Upload class="w-4 h-4" :class="cdcFile ? 'text-[#c10e46]' : 'text-gray-400'" />
                            <span class="text-sm" :class="cdcFile ? 'text-[#c10e46]' : 'text-gray-500'">
                                {{ cdcFile ? cdcFile.name : 'Remplacer le PDF (optionnel)' }}
                            </span>
                        </label>
                        <button
                            v-if="cdcFile"
                            type="button"
                            @click="removeFile"
                            class="mt-2 text-xs text-red-500 hover:text-red-600"
                        >
                            Annuler
                        </button>
                    </div>
                    <p v-if="form.errors.cdc_file" class="text-red-500 text-xs mt-1">{{ form.errors.cdc_file }}</p>
                </div>

                <!-- Boutons -->
                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 px-4 py-2 text-white rounded-lg font-medium hover:opacity-90 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }"
                    >
                        <Save v-if="!form.processing" class="w-4 h-4" />
                        <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span>{{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}</span>
                    </button>

                    <Link
                        :href="route('customer.projects.index')"
                        class="flex-1 px-4 py-2 border rounded-lg text-center hover:bg-gray-50 transition-colors"
                        :style="{ borderColor: '#c10e4620', color: '#1e002f' }"
                    >
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
</style>
