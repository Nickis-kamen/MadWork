<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { FileText, Upload } from 'lucide-vue-next';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const form = useForm({
    service_type: '',
    title: '',
    description: '',
    delai: 7,
    cdc_file: null,
});

const fileName = ref(null);
const fileError = ref(null);

const serviceTypes = [
    { value: 'site-vitrine', label: 'Site Vitrine' },
    { value: 'site-ecommerce', label: 'Site E-commerce' },
    { value: 'application-web', label: 'Application Web' },
    { value: 'site-dynamique', label: 'Site Dynamique' },
    { value: 'site-one-page', label: 'Site One-Page' },
    { value: 'maintenance', label: 'Maintenance' },
    { value: 'autre', label: 'Autre' }
];

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    fileError.value = null;

    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            fileError.value = 'Le fichier ne doit pas dépasser 5 Mo';
            return;
        }

        if (file.type !== 'application/pdf') {
            fileError.value = 'Le fichier doit être au format PDF';
            return;
        }

        form.cdc_file = file;
        fileName.value = file.name;
    }
};

const submit = () => {
    const formData = new FormData();
    formData.append('service_type', form.service_type);
    formData.append('title', form.title);
    formData.append('description', form.description);
    formData.append('delai', form.delai);

    if (form.cdc_file) {
        formData.append('cdc_file', form.cdc_file);
    }

    form.post(route('customer.devis.store'), {
        data: formData,
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        onSuccess: () => {
            form.reset();
            fileName.value = null;
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Demande de devis" />
        <div class="flex justify-center">
            <Link href="/">
                <ApplicationLogo class=""/>
            </Link>
        </div>
        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold text-gray-800">Demande de devis</h2>
            <p class="text-xs text-gray-600 mt-0.5">
                Remplissez ce formulaire pour obtenir un devis
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-3" enctype="multipart/form-data">
            <!-- Grille compacte 2 colonnes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Type de service (col 1) -->
                <div>
                    <InputLabel for="service_type" value="Type de service *" class="text-xs" />
                    <select
                        id="service_type"
                        v-model="form.service_type"
                        class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm text-sm py-1.5"
                        required
                    >
                        <option value="" disabled selected>Sélectionner</option>
                        <option
                            v-for="service in serviceTypes"
                            :key="service.value"
                            :value="service.value"
                        >
                            {{ service.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.service_type" class="text-xs" />
                </div>

                <!-- Titre -->
                <div>
                    <InputLabel for="title" value="Titre du projet *" class="text-xs" />
                    <TextInput
                        id="title"
                        type="text"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.title"
                        required
                        placeholder="Ex: Site e-commerce"
                    />
                    <InputError :message="form.errors.title" class="text-xs" />
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <InputLabel for="description" value="Description du projet *" class="text-xs" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm text-sm"
                        required
                        placeholder="Décrivez votre projet en détail..."
                    ></textarea>
                    <InputError :message="form.errors.description" class="text-xs" />
                </div>

                <!-- Délai -->
                <div>
                    <InputLabel for="delai" value="Délai (jours) *" class="text-xs" />
                    <TextInput
                        id="delai"
                        type="number"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model.number="form.delai"
                        required
                        min="1"
                        max="365"
                        step="1"
                        placeholder="30"
                    />
                    <InputError :message="form.errors.delai" class="text-xs" />
                </div>

                <!-- Upload fichier (col 2) -->
                <div>
                    <InputLabel value="Cahier des charges (PDF)" class="text-xs" />
                    <div class="mt-1 flex items-center space-x-2">
                        <label
                            for="cdc_file"
                            class="flex items-center space-x-1 px-3 py-1.5 bg-white border border-pink-300 rounded-md cursor-pointer hover:bg-pink-50 transition-colors text-sm"
                        >
                            <Upload class="w-3.5 h-3.5 text-pink-600" />
                            <span class="text-xs text-pink-600 font-medium">
                                {{ fileName ? 'Changer' : 'Upload' }}
                            </span>
                        </label>
                        <input
                            type="file"
                            id="cdc_file"
                            class="hidden"
                            accept=".pdf,application/pdf"
                            @change="handleFileUpload"
                        />
                        <span v-if="fileName" class="text-xs text-gray-600 truncate max-w-[120px]">
                            {{ fileName }}
                        </span>
                    </div>
                    <p v-if="fileError" class="mt-1 text-xs text-red-600">{{ fileError }}</p>
                    <p v-else class="mt-0.5 text-xs text-gray-500">PDF, 5 Mo max</p>
                    <InputError :message="form.errors.cdc_file" class="text-xs" />
                </div>
            </div>

            <!-- Bouton d'envoi -->
            <div class="pt-1">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="w-full justify-center text-sm py-2"
                >
                    <span v-if="!form.processing">Envoyer la demande de devis</span>
                    <span v-else class="flex items-center justify-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Envoi...</span>
                    </span>
                </PrimaryButton>
            </div>

            <!-- Note compacte -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    * Champs obligatoires • Réponse sous 24-48h
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
