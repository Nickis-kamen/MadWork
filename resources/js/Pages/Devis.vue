<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Upload, Briefcase } from 'lucide-vue-next';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    services: {
        type: Array,
        default: () => []
    },
    selectedServiceId: {
        type: Number,
        default: null
    }
});

// Convertir selectedServiceId en nombre pour être sûr
const selectedServiceIdNumber = computed(() => {
    return props.selectedServiceId ? Number(props.selectedServiceId) : null;
});

// Trouver le service sélectionné
const selectedService = computed(() => {
    if (!selectedServiceIdNumber.value || !props.services.length) return null;
    const service = props.services.find(s => s.id === selectedServiceIdNumber.value);
    return service;
});

const form = useForm({
    title: '',
    service_id: selectedServiceIdNumber.value,
    description: '',
    delai: 7,
    budget: '',
    cdc_file: null,
});

// Watcher pour mettre à jour form.service_id quand selectedServiceId change
watch(selectedServiceIdNumber, (newId) => {
    form.service_id = newId;
});

const fileName = ref(null);
const fileError = ref(null);

// Fonction pour gérer le changement de service
const handleServiceChange = (event) => {
    const serviceId = event.target.value;
    form.service_id = serviceId ? Number(serviceId) : null;

    // Mettre à jour l'URL avec le nouvel ID
    if (serviceId) {
        router.get(route('devis'), { service_id: serviceId }, {
            preserveState: true,
            replace: true,
            only: ['selectedServiceId'] // Ne met à jour que cette prop
        });
    } else {
        router.get(route('devis'), {}, {
            preserveState: true,
            replace: true,
            only: ['selectedServiceId']
        });
    }
};

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
    formData.append('title', form.title);
    formData.append('service_id', form.service_id);
    formData.append('description', form.description);
    formData.append('delai', form.delai);
    formData.append('budget', form.budget);

    if (form.cdc_file) {
        formData.append('cdc_file', form.cdc_file);
    }

    form.post(route('devis.store'), {
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
                <ApplicationLogo />
            </Link>
        </div>

        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold text-gray-800">Demande de devis</h2>
            <p class="text-xs text-gray-600 mt-0.5">
                Remplissez ce formulaire pour obtenir un devis personnalisé
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4" enctype="multipart/form-data">
            <!-- Selecteur de service -->
            <div>
                <InputLabel for="service" value="Choisissez un service *" />
                <select
                    id="service"
                    :value="form.service_id"
                    @change="handleServiceChange"
                    class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm"
                    required
                >
                    <option value="">-- Sélectionnez un service --</option>
                    <option
                        v-for="service in services"
                        :key="service.id"
                        :value="service.id"
                    >
                        {{ service.title }} - {{ service.price }} Ar
                    </option>
                </select>
                <InputError :message="form.errors.service_id" />
            </div>

            <!-- Affichage du service sélectionné -->
            <div v-if="selectedService" class="p-4 bg-gradient-to-r from-pink-50 to-purple-50 border border-pink-200 rounded-xl">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-semibold text-pink-600 bg-pink-100 py-0.5 rounded-full">Service sélectionné</span>
                    <h3 class="text-lg font-bold text-gray-800">{{ selectedService.title }}</h3>
                </div>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-gray-500">Prix de base</span>
                    <span class="text-lg font-bold text-gray-600">
                        {{ selectedService.price }} Ar
                    </span>
                </div>
            </div>

            <!-- Titre -->
            <div>
                <InputLabel for="title" value="Titre du projet *" />
                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.title"
                    required
                    placeholder="Ex: Refonte site vitrine"
                />
                <InputError :message="form.errors.title" />
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="description" value="Description du projet *" />
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm"
                    required
                    placeholder="Décrivez votre projet en détail..."
                ></textarea>
                <InputError :message="form.errors.description" />
            </div>

            <!-- Délai et Budget -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <InputLabel for="delai" value="Délai souhaité (jours)" />
                    <TextInput
                        id="delai"
                        type="number"
                        class="mt-1 block w-full"
                        v-model.number="form.delai"
                        required
                        min="1"
                        max="365"
                    />
                    <InputError :message="form.errors.delai" />
                </div>

                <div>
                    <InputLabel for="budget" value="Budget estimé (Ariary)" />
                    <TextInput
                        id="budget"
                        type="number"
                        class="mt-1 block w-full"
                        v-model.number="form.budget"
                        required
                        min="0"
                        step="100"
                    />
                    <InputError :message="form.errors.budget" />
                </div>
                <!-- Upload fichier -->
                <div>
                    <InputLabel value="Cahier des charges" />
                    <div class="mt-1 flex items-center gap-3">
                        <label
                            for="cdc_file"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-pink-300 rounded-lg cursor-pointer hover:bg-pink-50 transition-colors"
                        >
                            <Upload class="w-4 h-4 text-pink-600" />
                            <span class="text-sm text-pink-600 font-medium">
                                {{ fileName ? 'Changer' : 'Télécharger' }}
                            </span>
                        </label>
                        <input
                            type="file"
                            id="cdc_file"
                            class="hidden"
                            accept=".pdf,application/pdf"
                            @change="handleFileUpload"
                        />
                    </div>
                    <p v-if="fileError" class="mt-1 text-sm text-red-600">{{ fileError }}</p>
                    <p v-else class="mt-1 text-xs text-gray-500">PDF, 5 Mo max (optionnel)</p>
                    <InputError :message="form.errors.cdc_file" />
                </div>
            </div>


            <!-- Bouton d'envoi -->
            <div class="pt-2">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="w-full justify-center py-3"
                >
                    <span v-if="!form.processing">Envoyer la demande de devis</span>
                    <span v-else class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Envoi...</span>
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
