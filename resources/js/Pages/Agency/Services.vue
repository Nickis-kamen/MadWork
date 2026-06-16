<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Trash2, Edit2, FolderOpen, X } from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props
const props = defineProps({
    services: {
        type: Array,
        default: () => []
    },
    categories: {
        type: Array,
        default: () => []
    },
    agency: {
        type: Object,
        default: null
    }
});
const page = usePage();
// État pour savoir si on est en mode édition
const isEditingMode = ref(false);
const editingServiceId = ref(null);
const editingServiceSlug = ref('');

// Formulaire unique (utilisé pour ajout et édition)
const form = useForm({
    id: null,
    title: '',
    description: '',
    price: '',
    category_id: '',
    image: null,
    slug: ''
});

// État pour la gestion des détails (projets)
const selectedServiceForDetails = ref(null);
const showDetailsModal = ref(false);

// Formulaire pour les détails
const detailForm = useForm({
    id: null,
    title: '',
    description: '',
    image: null
});

// Aperçus des images
const imagePreview = ref(null);
const detailImagePreview = ref(null);

// Références pour les inputs file
const fileInput = ref(null);
const detailFileInput = ref(null);

// Gestion de l'image du service
const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

// Gestion des images pour les détails
const handleDetailImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        detailForm.image = file;
        const reader = new FileReader();
        reader.onload = (e) => detailImagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

// CRUD Service avec formulaire unique
const submitService = () => {
    if (!form.title.trim()) {
        form.setError('title', 'Le titre du service est requis');
        return;
    }
    if (!form.description.trim()) {
        form.setError('description', 'La description est requise');
        return;
    }
    if (!form.price || form.price <= 0) {
        form.setError('price', 'Le prix doit être un nombre positif');
        return;
    }
    if (!form.category_id) {
        form.setError('category_id', 'Veuillez sélectionner une catégorie');
        return;
    }
    if (!isEditingMode.value && !form.image) {
        form.setError('image', 'L\'image est requise');
        return;
    }

    if (!isEditingMode.value) {
        // Mode ajout
        form.post('/agency/my-services', {
            preserveScroll: true,
            onSuccess: () => {
                resetForm();
            },
            onError: (errors) => console.error(errors)
        });
    } else {
        // Mode édition
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('agency.services.update', form.slug), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                resetForm();
            },
            onError: (errors) => console.log(errors)
        });

    }
};

// Démarrer l'édition d'un service
const startEdit = (service) => {
    isEditingMode.value = true;
    editingServiceId.value = service.id;
    editingServiceSlug.value = service.slug;

    form.id = service.id;
    form.title = service.title;
    form.description = service.description;
    form.price = service.price;
    form.category_id = service.category_id;
    form.slug = service.slug;
    form.image = null;

    imagePreview.value = null;

    // Scroll vers le formulaire
    document.getElementById('service-form')?.scrollIntoView({ behavior: 'smooth' });
};

// Annuler l'édition
const cancelEdit = () => {
    resetForm();
};

// Réinitialiser le formulaire
const resetForm = () => {
    isEditingMode.value = false;
    editingServiceId.value = null;
    editingServiceSlug.value = null;
    form.reset();
    form.clearErrors();
    imagePreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

// Supprimer un service
const deleteService = (serviceSlug, serviceTitle) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le service "${serviceTitle}" ?`)) {
        const deleteForm = useForm({});
        deleteForm.delete(`/agency/my-services/${serviceSlug}`, {
            preserveScroll: true
        });
    }
};

// Gestion des détails (projets)
const openDetailsModal = (service) => {
    selectedServiceForDetails.value = service;
    showDetailsModal.value = true;
    detailForm.reset();
    detailForm.clearErrors();
    detailImagePreview.value = null;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedServiceForDetails.value = null;
    detailForm.reset();
    detailForm.clearErrors();
    detailImagePreview.value = null;
    if (detailFileInput.value) detailFileInput.value.value = '';
};

const saveDetail = () => {
    if (!detailForm.title.trim()) {
        detailForm.setError('title', 'Le titre est requis');
        return;
    }

    if (!detailForm.image) {
        detailForm.setError('image', 'L\'image est requise');
        return;
    }

    detailForm.post(`/agency/my-services/${selectedServiceForDetails.value.slug}/details`,
    {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            if (detailImagePreview) detailImagePreview.value = null;
            detailForm.reset();

            const updatedService = page.props.services.find(
                s => s.id === selectedServiceForDetails.value.id
            );

            if (updatedService) {
                selectedServiceForDetails.value = updatedService;
            }
        },
        onError: (errors) => console.error(errors)
    });
};

const deleteDetail = (detail) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le projet "${detail.title}" ?`)) {
        const deleteForm = useForm({});
        deleteForm.delete(`/agency/my-services/details/${detail.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                const updatedService = page.props.services.find(
                s => s.id === selectedServiceForDetails.value.id
                );

                if (updatedService) {
                    selectedServiceForDetails.value = updatedService;
                }
            }
        });
    }
};

// Formater le montant
const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

const getCategoryName = (categoryId) => {
    const category = props.categories.find(cat => cat.id === categoryId);
    return category ? category.title : 'Non catégorisé';
};

// Computed pour le titre du formulaire
const formTitle = computed(() => {
    return isEditingMode.value ? 'Modifier le service' : 'Ajouter un nouveau service';
});

const submitButtonText = computed(() => {
    if (form.processing) return isEditingMode.value ? 'Modification...' : 'Ajout...';
    return isEditingMode.value ? 'Modifier le service' : 'Ajouter le service';
});
</script>

<template>
    <Head title="Gestion des services" />
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des services
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Ajoutez et gérez vos prestations et leurs projets associés
                    </p>
                </div>
                <button
                    v-if="isEditingMode"
                    @click="cancelEdit"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors"
                    :style="{ borderColor: '#c10e4620', color: '#1e002f' }"
                >
                    Annuler la modification
                </button>
            </div>

            <!-- Message si pas d'agence -->
            <div v-if="!agency" class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-4">
                <p class="text-yellow-700">Veuillez créer une agence avant de gérer vos services.</p>
            </div>

            <!-- Formulaire unique (ajout/édition) -->
            <div v-else class="bg-white rounded-xl p-6 border" :style="{ borderColor: '#c10e4620' }" id="service-form">
                <h2 class="text-lg font-semibold mb-4" :style="{ color: '#1e002f' }">
                    {{ formTitle }}
                </h2>
                <form @submit.prevent="submitService" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Titre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre du service *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Ex: Site vitrine, E-commerce, etc."
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <!-- Prix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix (Ar) *</label>
                            <input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                :class="{ 'border-red-500': form.errors.price }"
                            />
                            <p v-if="form.errors.price" class="mt-1 text-sm text-red-500">{{ form.errors.price }}</p>
                        </div>

                        <!-- Catégorie -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                            <select
                                v-model="form.category_id"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                :class="{ 'border-red-500': form.errors.category_id }"
                            >
                                <option value="">Sélectionner une catégorie</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.title }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-500">{{ form.errors.category_id }}</p>
                        </div>

                        <!-- Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Image {{ !isEditingMode ? '*' : '(optionnelle)' }}
                            </label>
                            <div class="flex items-center gap-4">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="hidden"
                                    id="imageInput"
                                />
                                <label
                                    for="imageInput"
                                    class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                    :style="{ borderColor: '#c10e4620' }"
                                >
                                    Choisir un fichier
                                </label>
                                <span v-if="form.image" class="text-sm text-green-600">Fichier sélectionné</span>
                            </div>
                            <div v-if="imagePreview" class="mt-2">
                                <img :src="imagePreview" alt="Aperçu" class="h-20 w-20 object-cover rounded-lg border" />
                            </div>
                            <div v-else-if="isEditingMode && editingServiceId" class="mt-2 text-xs text-gray-500">
                                Image actuelle conservée si vous n'en sélectionnez pas une nouvelle
                            </div>
                            <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Description détaillée du service..."
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            v-if="isEditingMode"
                            type="button"
                            @click="cancelEdit"
                            class="px-6 py-2 rounded-lg border font-medium transition-colors hover:bg-gray-50"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
                            :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                        >
                            <Plus v-if="!isEditingMode" class="w-4 h-4" />
                            <Edit2 v-else class="w-4 h-4" />
                            <span>{{ submitButtonText }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des services -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <div class="p-4 border-b" :style="{ borderColor: '#c10e4620' }">
                    <h2 class="text-lg font-semibold" :style="{ color: '#1e002f' }">
                        Liste des services
                        <span class="text-sm font-normal text-gray-500 ml-2">
                            ({{ services.length }} service{{ services.length > 1 ? 's' : '' }})
                        </span>
                    </h2>
                </div>

                <!-- Version mobile (cartes) -->
                <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                    <div
                        v-for="service in services"
                        :key="service.id"
                        class="p-4 hover:bg-gray-50 transition-colors"
                        :class="{ 'bg-blue-50': editingServiceId === service.id }"
                    >
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <img
                                        :src="'/storage/' + service.image"
                                        :alt="service.title"
                                        class="h-16 w-16 object-cover rounded-lg border"
                                        :style="{ borderColor: '#c10e4620' }"
                                    />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium" :style="{ color: '#1e002f' }">{{ service.title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ formatAmount(service.price) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ getCategoryName(service.category_id) }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        @click="openDetailsModal(service)"
                                        class="p-2 rounded-lg border hover:bg-blue-50 transition-colors"
                                        :style="{ borderColor: '#c10e4620' }"
                                        title="Gérer les projets"
                                    >
                                        <FolderOpen class="w-4 h-4 text-blue-600" />
                                    </button>
                                    <button
                                        @click="startEdit(service)"
                                        class="p-2 rounded-lg border hover:bg-gray-100 transition-colors"
                                        :style="{ borderColor: '#c10e4620' }"
                                        title="Modifier"
                                        :class="{ 'bg-gray-100': editingServiceId === service.id }"
                                    >
                                        <Edit2 class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                    </button>
                                    <button
                                        @click="deleteService(service.slug, service.title)"
                                        class="p-2 rounded-lg border hover:bg-red-50 transition-colors"
                                        :style="{ borderColor: '#c10e4620' }"
                                        title="Supprimer"
                                    >
                                        <Trash2 class="w-4 h-4 text-red-500" />
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 line-clamp-2">{{ service.description }}</p>
                        </div>
                    </div>

                    <!-- Message si aucun service -->
                    <div v-if="services.length === 0" class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" :style="{ background: '#c10e4610' }">
                            <Plus class="w-8 h-8" :style="{ color: '#c10e46' }" />
                        </div>
                        <p class="text-gray-500 text-lg mb-2">Aucun service</p>
                        <p class="text-gray-400 text-sm">
                            Commencez par ajouter votre premier service
                        </p>
                    </div>
                </div>

                <!-- Version desktop (tableau) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                            <tr
                                v-for="service in services"
                                :key="service.id"
                                class="hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-blue-50': editingServiceId === service.id }"
                            >
                                <td class="px-6 py-4">
                                    <img :src="'/storage/' + service.image" :alt="service.title" class="h-12 w-12 object-cover rounded-lg border" :style="{ borderColor: '#c10e4620' }" />
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ service.title }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ getCategoryName(service.category_id) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold" :style="{ color: '#c10e46' }">
                                    {{ formatAmount(service.price) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                    {{ service.description }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openDetailsModal(service)"
                                            class="p-2 rounded-lg border hover:bg-blue-50 transition-colors"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Gérer les projets"
                                        >
                                            <FolderOpen class="w-4 h-4 text-blue-600" />
                                        </button>
                                        <button
                                            @click="startEdit(service)"
                                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Modifier"
                                            :class="{ 'bg-gray-100': editingServiceId === service.id }"
                                        >
                                            <Edit2 class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                        </button>
                                        <button
                                            @click="deleteService(service.slug, service.title)"
                                            class="p-2 rounded-lg border hover:bg-red-50 transition-colors"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Supprimer"
                                        >
                                            <Trash2 class="w-4 h-4 text-red-500" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Message si aucun service -->
                            <tr v-if="services.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" :style="{ background: '#c10e4610' }">
                                        <Plus class="w-8 h-8" :style="{ color: '#c10e46' }" />
                                    </div>
                                    <p class="text-gray-500 text-lg mb-2">Aucun service</p>
                                    <p class="text-gray-400 text-sm">Commencez par ajouter votre premier service</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de gestion des projets -->
        <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeDetailsModal">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

                <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header modal -->
                    <div class="sticky top-0 bg-white border-b p-4 flex justify-between items-center" :style="{ borderColor: '#c10e4620' }">
                        <div>
                            <h3 class="text-xl font-semibold" :style="{ color: '#1e002f' }">
                                Gestion des projets - {{ selectedServiceForDetails?.title }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Ajoutez et gérez les projets réalisés pour ce service</p>
                        </div>
                        <button @click="closeDetailsModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6">
                        <!-- Formulaire d'ajout/modification de projet -->
                        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold mb-4" :style="{ color: '#1e002f' }">
                            Ajouter un nouveau projet
                            </h4>
                            <form @submit.prevent="saveDetail" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre du projet *</label>
                                    <input
                                        v-model="detailForm.title"
                                        type="text"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        :class="{ 'border-red-500': detailForm.errors.title }"
                                    />
                                    <p v-if="detailForm.errors.title" class="mt-1 text-sm text-red-500">{{ detailForm.errors.title }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
                                    <textarea
                                        v-model="detailForm.description"
                                        rows="3"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="Description détaillée du projet..."
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Image *
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <input
                                            ref="detailFileInput"
                                            type="file"
                                            accept="image/*"
                                            @change="handleDetailImageChange"
                                            class="hidden"
                                            id="detailImageInput"
                                        />
                                        <label
                                            for="detailImageInput"
                                            class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                            :style="{ borderColor: '#c10e4620' }"
                                        >
                                            Choisir un fichier
                                        </label>
                                        <span v-if="detailForm.image" class="text-sm text-green-600">Image sélectionnée</span>
                                    </div>

                                    <!-- Aperçu nouvelle image -->
                                    <div v-if="detailImagePreview" class="mt-2">
                                        <img :src="detailImagePreview" alt="Aperçu" class="h-20 w-20 object-cover rounded-lg border" />
                                        <p class="text-xs text-gray-500 mt-1">Nouvelle image</p>
                                    </div>
                                    <div v-if="editDetailImagePreview" class="mt-2">
                                        <img :src="editDetailImagePreview" alt="Aperçu" class="h-20 w-20 object-cover rounded-lg border" />
                                        <p class="text-xs text-gray-500 mt-1">Nouvelle image</p>
                                    </div>

                                    <p v-if="detailForm.errors.image" class="mt-1 text-sm text-red-500">{{ detailForm.errors.image }}</p>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button
                                        type="submit"
                                        :disabled="detailForm.processing"
                                        class="px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 disabled:opacity-50"
                                        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                    >
                                        <Plus class="w-4 h-4" />
                                        <span>{{ detailForm.processing ? 'Sauvegarde...' : 'Ajouter' }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Liste des projets existants -->
                        <div>
                            <h4 class="font-semibold mb-4" :style="{ color: '#1e002f' }">
                                Projets existants ({{ selectedServiceForDetails?.service_details?.length || 0 }})
                            </h4>

                            <div v-if="!selectedServiceForDetails?.service_details?.length" class="text-center py-8 bg-gray-50 rounded-lg">
                                <FolderOpen class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                                <p class="text-gray-500">Aucun projet pour ce service</p>
                                <p class="text-sm text-gray-400">Ajoutez votre premier projet ci-dessus</p>
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="detail in selectedServiceForDetails.service_details"
                                    :key="detail.id"
                                    class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                                    :style="{ borderColor: '#c10e4620' }"
                                >
                                    <div class="flex gap-4">
                                        <img
                                            :src="'/storage/' + detail.image"
                                            :alt="detail.title"
                                            class="h-20 w-20 object-cover rounded-lg border"
                                            :style="{ borderColor: '#c10e4620' }"
                                        />
                                        <div class="flex-1">
                                            <h5 class="font-semibold" :style="{ color: '#1e002f' }">{{ detail.title }}</h5>
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ detail.description || 'Aucune description' }}</p>
                                            <p class="text-xs text-gray-400 mt-2">
                                                Créé le {{ new Date(detail.created_at).toLocaleDateString('fr-FR') }}
                                            </p>
                                        </div>
                                        <button
                                            @click="deleteDetail(detail)"
                                            class="p-2 rounded-lg border hover:bg-red-50 transition-colors"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Supprimer"
                                        >
                                            <Trash2 class="w-4 h-4 text-red-500" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
