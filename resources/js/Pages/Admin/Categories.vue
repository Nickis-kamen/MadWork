<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Trash2, Edit2 } from 'lucide-vue-next';
import Toast from '@/Components/Toastify/Toast.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props reçues de Laravel via Inertia
const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
});

// État pour le formulaire d'ajout avec Inertia
const form = useForm({
    title: ''
});

// État pour l'édition
const editingCategory = ref(null);
const editForm = useForm({
    id: null,
    title: ''
});

// Fonction pour ajouter une catégorie
const addCategory = () => {
    if (!form.title.trim()) {
        form.setError('title', 'Le titre de la catégorie est requis');
        return;
    }

    form.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

// Fonction pour démarrer l'édition
const startEdit = (category) => {
    editingCategory.value = category.id;
    editForm.id = category.id;
    editForm.slug = category.slug;
    editForm.title = category.title;
};

// Fonction pour annuler l'édition
const cancelEdit = () => {
    editingCategory.value = null;
    editForm.reset();
    editForm.clearErrors();
};

// Fonction pour sauvegarder l'édition
const saveEdit = () => {
    if (!editForm.title.trim()) {
        editForm.setError('title', 'Le titre de la catégorie est requis');
        return;
    }

    editForm.put(`/admin/categories/${editForm.slug}`, {
        preserveScroll: true,
        onSuccess: () => {
            cancelEdit();
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

// Fonction pour supprimer une catégorie
const deleteCategory = (categorySlug, categoryTitle) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${categoryTitle}" ?`)) {
        const deleteForm = useForm({});
        deleteForm.delete(`/admin/categories/${categorySlug}`, {
            preserveScroll: true
        });
    }
};

// Vérifier si c'est la page en cours d'édition
const isEditing = (categoryId) => {
    return editingCategory.value === categoryId;
};
</script>

<template>
    <Head title="Gestion des catégories" />
    <AuthenticatedLayout>
        <!-- Container -->
        <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                        Gestion des catégories
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">
                        Ajoutez et gérez vos catégories
                    </p>
                </div>
            </div>

            <!-- Formulaire d'ajout -->
            <div class="bg-white rounded-xl p-6 border" :style="{ borderColor: '#c10e4620' }">
                <h2 class="text-lg font-semibold mb-4" :style="{ color: '#1e002f' }">
                    Ajouter une nouvelle catégorie
                </h2>
                <form @submit.prevent="addCategory">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Titre de la catégorie"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                :class="{ 'border-red-500': form.errors.title }"
                                :style="{ borderColor: form.errors.title ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
                                @keyup.enter="addCategory"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">
                                {{ form.errors.title }}
                            </p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
                            :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                        >
                            <Plus class="w-4 h-4" />
                            <span>{{ form.processing ? 'Ajout...' : 'Ajouter' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des catégories -->
            <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                <div class="p-4 border-b" :style="{ borderColor: '#c10e4620' }">
                    <h2 class="text-lg font-semibold" :style="{ color: '#1e002f' }">
                        Liste des catégories
                        <span class="text-sm font-normal text-gray-500 ml-2">
                            ({{ categories.length }} catégorie{{ categories.length > 1 ? 's' : '' }})
                        </span>
                    </h2>
                </div>

                <!-- Version mobile (cartes) -->
                <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="p-4 hover:bg-gray-50 transition-colors"
                    >
                        <!-- Mode édition -->
                        <div v-if="isEditing(category.id)" class="space-y-3">
                            <form @submit.prevent="saveEdit">
                                <input
                                    v-model="editForm.title"
                                    type="text"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2"
                                    :class="{ 'border-red-500': editForm.errors.title }"
                                    :style="{ borderColor: editForm.errors.title ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
                                    @keyup.enter="saveEdit"
                                />
                                <p v-if="editForm.errors.title" class="mt-1 text-sm text-red-500">
                                    {{ editForm.errors.title }}
                                </p>
                                <div class="flex gap-2 mt-3">
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
                        <div v-else class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-medium" :style="{ color: '#1e002f' }">{{ category.title }}</p>
                                <p class="text-xs text-gray-500 mt-1">Slug: {{ category.slug }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Créée le: {{ new Date(category.created_at).toLocaleDateString('fr-FR') }}
                                </p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button
                                    @click="startEdit(category)"
                                    class="p-2 rounded-lg border hover:bg-gray-100 transition-colors"
                                    :style="{ borderColor: '#c10e4620' }"
                                    title="Modifier"
                                >
                                    <Edit2 class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                </button>
                                <button
                                    @click="deleteCategory(category.slug, category.title)"
                                    class="p-2 rounded-lg border hover:bg-red-50 transition-colors"
                                    :style="{ borderColor: '#c10e4620' }"
                                    title="Supprimer"
                                >
                                    <Trash2 class="w-4 h-4 text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Message si aucune catégorie -->
                    <div v-if="categories.length === 0" class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" :style="{ background: '#c10e4610' }">
                            <Plus class="w-8 h-8" :style="{ color: '#c10e46' }" />
                        </div>
                        <p class="text-gray-500 text-lg mb-2">Aucune catégorie</p>
                        <p class="text-gray-400 text-sm">
                            Commencez par ajouter votre première catégorie
                        </p>
                    </div>
                </div>

                <!-- Tableau desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Slug
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date de création
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50 transition-colors">
                                <!-- Mode édition -->
                                <template v-if="isEditing(category.id)">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" :style="{ color: '#1e002f' }">
                                        #{{ category.id }}
                                    </td>
                                    <td class="px-6 py-4" colspan="2">
                                        <form @submit.prevent="saveEdit" class="flex flex-col gap-1">
                                            <input
                                                v-model="editForm.title"
                                                type="text"
                                                class="w-full px-3 py-1 border rounded-lg focus:outline-none focus:ring-2"
                                                :class="{ 'border-red-500': editForm.errors.title }"
                                                :style="{ borderColor: editForm.errors.title ? '#ef4444' : '#c10e4620', '--tw-ring-color': '#c10e46' }"
                                            />
                                            <p v-if="editForm.errors.title" class="text-xs text-red-500">
                                                {{ editForm.errors.title }}
                                            </p>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ new Date(category.created_at).toLocaleDateString('fr-FR') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                @click="saveEdit"
                                                :disabled="editForm.processing"
                                                class="px-3 py-1 rounded-lg text-sm transition-colors"
                                                :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                            >
                                                {{ editForm.processing ? 'Sauvegarde...' : 'Sauvegarder' }}
                                            </button>
                                            <button
                                                @click="cancelEdit"
                                                class="px-3 py-1 rounded-lg border text-sm transition-colors hover:bg-gray-50"
                                                :style="{ borderColor: '#c10e4620', color: '#1e002f' }"
                                            >
                                                Annuler
                                            </button>
                                        </div>
                                    </td>
                                </template>

                                <!-- Mode affichage -->
                                <template v-else>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" :style="{ color: '#1e002f' }">
                                        #{{ category.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium" :style="{ color: '#1e002f' }">
                                            {{ category.title }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ category.slug }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ new Date(category.created_at).toLocaleDateString('fr-FR') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                @click="startEdit(category)"
                                                class="p-2 rounded-lg border hover:bg-gray-100 transition-colors"
                                                :style="{ borderColor: '#c10e4620' }"
                                                title="Modifier"
                                            >
                                                <Edit2 class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                            </button>
                                            <button
                                                @click="deleteCategory(category.slug, category.title)"
                                                class="p-2 rounded-lg border hover:bg-red-50 transition-colors"
                                                :style="{ borderColor: '#c10e4620' }"
                                                title="Supprimer"
                                            >
                                                <Trash2 class="w-4 h-4 text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

                            <!-- Message si aucune catégorie -->
                            <tr v-if="categories.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" :style="{ background: '#c10e4610' }">
                                        <Plus class="w-8 h-8" :style="{ color: '#c10e46' }" />
                                    </div>
                                    <p class="text-gray-500 text-lg mb-2">Aucune catégorie</p>
                                    <p class="text-gray-400 text-sm">
                                        Commencez par ajouter votre première catégorie
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
