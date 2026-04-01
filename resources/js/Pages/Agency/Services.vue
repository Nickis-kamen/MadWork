<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Trash2, Edit2, Image as ImageIcon } from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props reçues de Laravel via Inertia
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

// État pour le formulaire d'ajout avec Inertia
const form = useForm({
    title: '',
    description: '',
    price: '',
    category_id: '',
    image: null
});

// État pour l'édition
const editingService = ref(null);
const editForm = useForm({
    id: null,
    title: '',
    description: '',
    price: '',
    category_id: '',
    image: null,
    slug: ''
});

// Aperçu de l'image
const imagePreview = ref(null);
const editImagePreview = ref(null);

// Fonction pour gérer l'aperçu de l'image (ajout)
const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Fonction pour gérer l'aperçu de l'image (édition)
const handleEditImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        editForm.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            editImagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Fonction pour ajouter un service
const addService = () => {
    // Validation simple côté client
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
    if (!form.image) {
        form.setError('image', 'L\'image est requise');
        return;
    }

    form.post('/agency/my-services', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

// Fonction pour démarrer l'édition
const startEdit = (service) => {
    editingService.value = service.id;
    editForm.id = service.id;
    editForm.title = service.title;
    editForm.description = service.description;
    editForm.price = service.price;
    editForm.category_id = service.category_id;
    editForm.slug = service.slug;
    editForm.image = null;
    editImagePreview.value = null;
};

// Fonction pour annuler l'édition
const cancelEdit = () => {
    editingService.value = null;
    editForm.reset();
    editForm.clearErrors();
    editImagePreview.value = null;
};

// Fonction pour sauvegarder l'édition
const saveEdit = () => {
    if (!editForm.title.trim()) {
        editForm.setError('title', 'Le titre du service est requis');
        return;
    }
    if (!editForm.description.trim()) {
        editForm.setError('description', 'La description est requise');
        return;
    }
    if (!editForm.price || editForm.price <= 0) {
        editForm.setError('price', 'Le prix doit être un nombre positif');
        return;
    }
    if (!editForm.category_id) {
        editForm.setError('category_id', 'Veuillez sélectionner une catégorie');
        return;
    }

    editForm.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('agency.services.update', editForm.slug), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                cancelEdit();
            },
            onError: (errors) => {
                console.log(errors);
            }
        });
};

// Fonction pour supprimer un service
const deleteService = (serviceSlug, serviceTitle) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le service "${serviceTitle}" ?`)) {
        const deleteForm = useForm({});
        deleteForm.delete(`/agency/my-services/${serviceSlug}`, {
            preserveScroll: true
        });
    }
};

// Vérifier si c'est la page en cours d'édition
const isEditing = (serviceId) => {
    return editingService.value === serviceId;
};

// Formater le prix en euros
const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

// Obtenir le nom de la catégorie par son ID
const getCategoryName = (categoryId) => {
    const category = props.categories.find(cat => cat.id === categoryId);
    return category ? category.title : 'Non catégorisé';
};
</script>

<template>
    <Head title="Gestion des services" />
    <AuthenticatedLayout>
        <!-- Container -->
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des services
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Ajoutez et gérez vos prestations
                    </p>
                </div>
            </div>
            <div v-if="!agency" class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-4">
                <p class="text-yellow-700">Veuillez créer une agence avant de gérer vos services.</p>
            </div>
            <!-- Formulaire d'ajout -->
            <div v-else class="bg-white rounded-xl p-6 border" :style="{ borderColor: '#c10e4620' }">
                <h2 class="text-lg font-semibold mb-4" :style="{ color: '#1e002f' }">
                    Ajouter un nouveau service
                </h2>
                <form @submit.prevent="addService" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Titre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre du service *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Ex: Massage relaxant"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                :class="{ 'border-red-500': form.errors.title }"
                                :style="{ borderColor: form.errors.title ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <!-- Prix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix (€) *</label>
                            <input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                :class="{ 'border-red-500': form.errors.price }"
                                :style="{ borderColor: form.errors.price ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
                            />
                            <p v-if="form.errors.price" class="mt-1 text-sm text-red-500">{{ form.errors.price }}</p>
                        </div>

                        <!-- Catégorie -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                            <select
                                v-model="form.category_id"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                :class="{ 'border-red-500': form.errors.category_id }"
                                :style="{ borderColor: form.errors.category_id ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                            <div class="flex items-center gap-4">
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="hidden"
                                    ref="fileInput"
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
                            <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
                        </div>

                        <!-- Description (pleine largeur) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Description détaillée du service..."
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                :class="{ 'border-red-500': form.errors.description }"
                                :style="{ borderColor: form.errors.description ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
                            :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                        >
                            <Plus class="w-4 h-4" />
                            <span>{{ form.processing ? 'Ajout...' : 'Ajouter le service' }}</span>
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
                    >
                        <!-- Mode édition -->
                        <div v-if="isEditing(service.id)" class="space-y-3">
                            <form @submit.prevent="saveEdit" class="space-y-3">
                                <input
                                    v-model="editForm.title"
                                    type="text"
                                    placeholder="Titre"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                    :class="{ 'border-red-500': editForm.errors.title }"
                                    :style="{ borderColor: editForm.errors.title ? '#ef4444' : '#c10e4620' }"
                                />
                                <textarea
                                    v-model="editForm.description"
                                    rows="2"
                                    placeholder="Description"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                    :class="{ 'border-red-500': editForm.errors.description }"
                                    :style="{ borderColor: editForm.errors.description ? '#ef4444' : '#c10e4620' }"
                                ></textarea>
                                <div class="grid grid-cols-2 gap-2">
                                    <input
                                        v-model="editForm.price"
                                        type="number"
                                        step="0.01"
                                        placeholder="Prix"
                                        class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                        :class="{ 'border-red-500': editForm.errors.price }"
                                        :style="{ borderColor: editForm.errors.price ? '#ef4444' : '#c10e4620' }"
                                    />
                                    <select
                                        v-model="editForm.category_id"
                                        class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                        :class="{ 'border-red-500': editForm.errors.category_id }"
                                        :style="{ borderColor: editForm.errors.category_id ? '#ef4444' : '#c10e4620' }"
                                    >
                                        <option value="">Catégorie</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                            {{ cat.title }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <input type="file" @change="handleEditImageChange" accept="image/*" class="text-sm" />
                                    <div v-if="editImagePreview" class="mt-1">
                                        <img :src="editImagePreview" class="h-16 w-16 object-cover rounded" />
                                    </div>
                                    <div v-else-if="service.image" class="mt-1 text-xs text-gray-500">
                                        Image actuelle : {{ service.image.split('/').pop() }}
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        :disabled="editForm.processing"
                                        class="flex-1 px-3 py-2 rounded-lg font-medium text-sm transition-colors"
                                        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                    >
                                        {{ editForm.processing ? 'Sauvegarde...' : 'Sauvegarder' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelEdit"
                                        class="px-3 py-2 rounded-lg border font-medium text-sm transition-colors hover:bg-gray-50"
                                        :style="{ borderColor: '#c10e4620', color: '#1e002f' }"
                                    >
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Mode affichage -->
                        <div v-else class="flex gap-3">
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
                                <p class="text-sm text-gray-600 mt-1">{{ formatPrice(service.price) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ getCategoryName(service.category_id) }}</p>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ service.description }}</p>
                            </div>
                            <div class="flex gap-2 ml-2">
                                <button
                                    @click="startEdit(service)"
                                    class="p-2 rounded-lg border hover:bg-gray-100 transition-colors"
                                    :style="{ borderColor: '#c10e4620' }"
                                    title="Modifier"
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

                <!-- Tableau desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                            <tr v-for="service in services" :key="service.id" class="hover:bg-gray-50 transition-colors">
                                <!-- Mode édition -->
                                <template v-if="isEditing(service.id)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <input type="file" @change="handleEditImageChange" accept="image/*" class="text-xs" />
                                            <div v-if="editImagePreview" class="mt-1">
                                                <img :src="editImagePreview" class="h-12 w-12 object-cover rounded" />
                                            </div>
                                            <div v-else-if="service.image" class="text-xs text-gray-500">
                                                Actuelle
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" :style="{ color: '#1e002f' }">#{{ service.id }}</td>
                                    <td class="px-6 py-4">
                                        <input v-model="editForm.title" type="text" class="w-40 px-2 py-1 border rounded text-sm" :class="{ 'border-red-500': editForm.errors.title }" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <select v-model="editForm.category_id" class="px-2 py-1 border rounded text-sm w-32">
                                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input v-model="editForm.price" type="number" step="0.01" class="w-24 px-2 py-1 border rounded text-sm" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <textarea v-model="editForm.description" rows="2" class="w-48 px-2 py-1 border rounded text-sm"></textarea>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="saveEdit" :disabled="editForm.processing" class="px-3 py-1 rounded-lg text-sm transition-colors" :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }">
                                                {{ editForm.processing ? '...' : 'Sauver' }}
                                            </button>
                                            <button @click="cancelEdit" class="px-3 py-1 rounded-lg border text-sm transition-colors hover:bg-gray-50" :style="{ borderColor: '#c10e4620', color: '#1e002f' }">
                                                Annuler
                                            </button>
                                        </div>
                                    </td>
                                </template>

                                <!-- Mode affichage -->
                                <template v-else>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img :src="'/storage/' + service.image" :alt="service.title" class="h-12 w-12 object-cover rounded-lg border" :style="{ borderColor: '#c10e4620' }" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" :style="{ color: '#1e002f' }">#{{ service.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ service.title }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ getCategoryName(service.category_id) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" :style="{ color: '#c10e46' }">
                                        {{ formatPrice(service.price) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ service.description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="startEdit(service)" class="p-2 rounded-lg border hover:bg-gray-100 transition-colors" :style="{ borderColor: '#c10e4620' }" title="Modifier">
                                                <Edit2 class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                            </button>
                                            <button @click="deleteService(service.slug, service.title)" class="p-2 rounded-lg border hover:bg-red-50 transition-colors" :style="{ borderColor: '#c10e4620' }" title="Supprimer">
                                                <Trash2 class="w-4 h-4 text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

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
    </AuthenticatedLayout>
</template>
