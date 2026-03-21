<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Building2, MapPin, Calendar, Upload, X, Pencil, ArrowLeft, Trash2 } from 'lucide-vue-next';
import { ref} from 'vue';

const props = defineProps({
    agency: {
        type: Object,
        default: null
    }
});

// Mode édition ou création
const isEditing = ref(!!props.agency);

// Formulaire pour créer/modifier une agence
const form = useForm({
    name: props.agency?.name || '',
    email: props.agency?.email || '',
    description: props.agency?.description || '',
    image: '',
    address: props.agency?.address || ''
});

const imagePreview = ref(props.agency?.image ? `/storage/${props.agency.image}` : null);
const imageError = ref('');
const showEditForm = ref(false);

// Gestion de l'image
const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
            imageError.value = 'Format non supporté';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            imageError.value = 'Image max 2MB';
            return;
        }

        imageError.value = '';
        form.image = file;

        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    imagePreview.value = null;
    form.image = null;
};

// Soumission du formulaire
const submitForm = () => {
    if (isEditing.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('agency.update', props.agency.slug), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                form.clearErrors();
                showEditForm.value = false;
                window.location.reload();
            },
            onError: (errors) => {
                console.log(errors);
            }
        });
    }
    else {
        form.post(route('agency.store'), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                form.clearErrors();
                window.location.reload();
            },
            onError: (errors) => {
                console.log(errors);
            }
        });
    }
};

// Suppression de l'agence
const deleteAgency = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre agence ? Cette action est irréversible.')) {
        router.delete(route('agency.destroy', props.agency.slug), {
            onSuccess: () => {
                Object.assign(form, {
                    name: '', email: '', description: '', address: '', image: ''
                });
                imagePreview.value = null;
                isEditing.value = false;
                window.location.reload();
            }
        });
    }
};
</script>

<template>
    <Head :title="agency ? 'Mon agence' : 'Créer mon agence'" />
    <AuthenticatedLayout>
        <div class="p-6 bg-white min-h-screen">
            <!-- FORMULAIRE DE CRÉATION OU D'ÉDITION -->
            <div v-if="!agency || showEditForm" class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <button
                        v-if="agency"
                        @click="showEditForm = false"
                        class="flex items-center gap-2 text-sm mb-4 hover:underline"
                        style="color: #c10e46"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Retour
                    </button>
                </div>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2" style="color: #1e002f">
                        {{ agency ? 'Modifier votre agence' : 'Créer votre agence' }}
                    </h1>
                    <p class="text-gray-600">
                        {{ agency ? 'Modifiez les informations de votre agence' : 'Complétez ces informations pour créer votre agence' }}
                    </p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e002f">Logo / Image</label>
                        <div class="flex items-center space-x-4">
                            <div v-if="imagePreview" class="relative">
                                <img :src="imagePreview" class="w-24 h-24 rounded-lg object-cover border-2" style="border-color: #c10e46" />
                                <button type="button" @click="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">
                                    <X class="w-4 h-4" />
                                </button>
                            </div>
                            <label v-else class="flex flex-col items-center justify-center w-24 h-24 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50" style="border-color: #c10e4620">
                                <Upload class="w-8 h-8" style="color: #c10e46" />
                                <span class="text-xs text-gray-500 mt-1">Choisir</span>
                                <input type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP - Max 2MB</p>
                        <div v-if="imageError" class="text-red-500 text-xs mt-1">{{ imageError }}</div>
                        <div v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</div>
                    </div>

                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e002f">Nom de l'agence</label>
                        <input
                            v-model="form.name"
                            @input="generateSlug"
                            type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]"
                            :class="{ 'border-red-500': form.errors.name }"
                            required
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e002f">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]"
                            :class="{ 'border-red-500': form.errors.email }"
                            required
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e002f">Adresse</label>
                        <input
                            v-model="form.address"
                            type="text"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]"
                            :class="{ 'border-red-500': form.errors.address }"
                            required
                        />
                        <div v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e002f">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="5"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#c10e46]"
                            :class="{ 'border-red-500': form.errors.description }"
                            required
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                    </div>

                    <!-- Bouton -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 text-white rounded-lg font-semibold hover:shadow-lg transition-all"
                        style="background: linear-gradient(135deg, #c10e46 0%, #1e002f 100%)"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    >
                        {{ form.processing ? 'Enregistrement...' : (agency ? 'Mettre à jour' : 'Créer mon agence') }}
                    </button>
                </form>
            </div>

            <!-- AFFICHAGE DU DÉTAIL DE L'AGENCE -->
            <div v-else class="max-w-4xl mx-auto">
                <!-- En-tête avec image -->
                <div class="relative h-48 rounded-xl overflow-hidden mb-8">
                    <img :src="`/storage/${agency.image}`" :alt="agency.name" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <h1 class="text-3xl font-bold text-white">{{ agency.name }}</h1>
                        <p class="text-white/80 text-sm">Créée le {{ new Date(agency.created_at).toLocaleDateString('fr-FR') }}</p>
                    </div>
                    <div class="absolute top-4 right-4 flex gap-2">
                        <button
                            @click="showEditForm = true"
                            class="p-2 bg-white rounded-lg hover:shadow-md transition-all"
                            title="Modifier"
                        >
                            <Pencil class="w-4 h-4" style="color: #c10e46" />
                        </button>
                        <button
                            @click="deleteAgency"
                            class="p-2 bg-white rounded-lg hover:shadow-md transition-all"
                            title="Supprimer"
                        >
                            <Trash2 class="w-4 h-4 text-red-500" />
                        </button>
                    </div>
                </div>

                <!-- Informations -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                            <Building2 class="w-5 h-5 mt-0.5" style="color: #c10e46" />
                            <div>
                                <p class="text-sm text-gray-500">Nom</p>
                                <p class="font-medium">{{ agency.name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                            <MapPin class="w-5 h-5 mt-0.5" style="color: #c10e46" />
                            <div>
                                <p class="text-sm text-gray-500">Adresse</p>
                                <p class="font-medium">{{ agency.address }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                            <Calendar class="w-5 h-5 mt-0.5" style="color: #c10e46" />
                            <div>
                                <p class="text-sm text-gray-500">Date de création</p>
                                <p class="font-medium">{{ new Date(agency.created_at).toLocaleDateString('fr-FR') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold mb-3" style="color: #1e002f">Email</h3>
                            <p class="text-gray-700">{{ agency.email }}</p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold mb-3" style="color: #1e002f">Description</h3>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ agency.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
