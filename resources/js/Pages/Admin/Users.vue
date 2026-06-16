    <script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import {
    Users,
    Clock,
    CheckCircle,
    Eye,
    Mail,
    Phone,
    MapPin,
    Calendar,
    User,
    FileText,
    Search,
    X
    } from 'lucide-vue-next';
    import Toast from '@/Components/Toastify/Toast.vue';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

    // Props reçues de Laravel
    const props = defineProps({
        users: {
            type: Array,
            default: () => []
        }
    });

    // État pour le popup
    const showModal = ref(false);
    const selecteduser = ref(null);

    // État pour la recherche
    const searchQuery = ref('');

    // Fonction pour ouvrir le popup
    const openDetails = (user) => {
        selecteduser.value = user;
        showModal.value = true;
    };

    // Fonction pour fermer le popup
    const closeModal = () => {
        showModal.value = false;
        selecteduser.value = null;
    };

    // Fonction pour obtenir le statut de vérification
    const getVerificationStatus = (user) => {
        if (user.is_verified) {
            return { label: 'Vérifié', icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' };
        } else {
            return { label: 'Non vérifié', icon: Clock, color: 'text-yellow-600', bg: 'bg-yellow-100' };
        }
    };

    const getVerificationRole = (user) => {
        if (user.role === 'customer') {
            return { label: 'Client', color: 'text-green-600', bg: 'bg-green-100' };
        } else {
            return { label: 'Agence', color: 'text-blue-600', bg: 'bg-blue-100' };
        }
    };

    // Fonction pour formater la date
    const formatDate = (dateString) => {
        if (!dateString) return 'Non défini';
        return new Date(dateString).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    // Filtrer les utilisateurs par recherche
    const filteredusers = computed(() => {
        let filtered = props.users;

        // Filtre par recherche
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            filtered = filtered.filter(user =>
                user.name?.toLowerCase().includes(query) ||
                user.email?.toLowerCase().includes(query) ||
                user.phone?.includes(query)
            );
        }

        return filtered;
    });
    </script>

    <template>
        <Head title="Gestion des utilisateurs" />
        <AuthenticatedLayout>
            <!-- Container -->
            <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: '#1e002f' }">
                            Liste des utilisateurs
                        </h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">
                            Consultez tous vos utilisateurs inscrits sur la plateforme
                        </p>
                    </div>
                </div>

                <!-- Barre de recherche -->
                <div class="bg-white rounded-xl p-4 border" :style="{ borderColor: '#c10e4620' }">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher par nom, email, téléphone ou entreprise..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                            :style="{ borderColor: '#c10e4620', '--tw-ring-color': '#c10e46' }"
                        />
                    </div>
                </div>

                <!-- Liste des utilisateurs -->
                <div class="bg-white rounded-xl border overflow-hidden" :style="{ borderColor: '#c10e4620' }">
                    <!-- Version mobile (cartes) -->
                    <div class="block lg:hidden divide-y" :style="{ borderColor: '#c10e4620' }">
                        <div
                            v-for="user in filteredusers"
                            :key="user.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium"
                                        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                    >
                                        <img v-if="user.image" :src="`/storage/${user.image}`" alt="Avatar" class="w-full h-full rounded-full object-cover">
                                        <span v-else class="text-white">{{ user.name?.charAt(0) || '?' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ user.name }}</p>
                                        <p v-if="user.company" class="text-xs text-gray-500">{{ user.company }}</p>
                                    </div>
                                </div>
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                    :class="getVerificationStatus(user).bg"
                                >
                                    <component :is="getVerificationStatus(user).icon" class="w-3 h-3 mr-1" :class="getVerificationStatus(user).color" />
                                    <span :class="getVerificationStatus(user).color">{{ getVerificationStatus(user).label }}</span>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                <div>
                                    <p class="text-gray-500 text-xs">Email</p>
                                    <p class="font-medium text-xs truncate" :style="{ color: '#1e002f' }">{{ user.email }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">Téléphone</p>
                                    <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ user.phone || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">Inscription</p>
                                    <p class="font-medium text-xs" :style="{ color: '#1e002f' }">{{ formatDate(user.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">ID</p>
                                    <p class="font-medium text-xs font-mono" :style="{ color: '#1e002f' }">#{{ user.id }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    @click="openDetails(user)"
                                    class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                    :style="{ borderColor: '#c10e4620' }"
                                    title="Voir détails"
                                >
                                    <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                    <span class="ml-2 text-sm" :style="{ color: '#1e002f' }">Voir détails</span>
                                </button>
                            </div>
                        </div>

                        <!-- Message si aucun client (mobile) -->
                        <div v-if="filteredusers.length === 0" class="p-12 text-center">
                            <Users class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                            <p class="text-gray-500 text-lg mb-2">Aucun client trouvé</p>
                            <p class="text-gray-400 text-sm">
                                {{ searchQuery ? 'Aucun résultat pour votre recherche' : 'La liste des clients est vide' }}
                            </p>
                        </div>
                    </div>

                    <!-- Tableau desktop-->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y" :style="{ borderColor: '#c10e4620' }">
                            <!-- En-tête -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscription</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <!-- Corps du tableau -->
                            <tbody class="bg-white divide-y" :style="{ borderColor: '#c10e4620' }">
                                <tr v-for="user in filteredusers" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div
                                                    class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-medium"
                                                    :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                                >
                                                    <img v-if="user.image" :src="`/storage/${user.image}`" alt="Avatar" class="w-full h-full rounded-full object-cover">
                                                    <span v-else>{{ user.name?.charAt(0) || '?' }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ user.name }}</p>
                                                <p v-if="user.company" class="text-xs text-gray-500">{{ user.company }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                            :class="getVerificationRole(user).bg"
                                        >
                                            <span :class="getVerificationRole(user).color">{{ getVerificationRole(user).label }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ user.phone || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                            :class="getVerificationStatus(user).bg"
                                        >
                                            <component :is="getVerificationStatus(user).icon" class="w-3 h-3 mr-1" :class="getVerificationStatus(user).color" />
                                            <span :class="getVerificationStatus(user).color">{{ getVerificationStatus(user).label }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button
                                            @click="openDetails(user)"
                                            class="p-2 rounded-lg border hover:bg-gray-100 transition-colors inline-flex items-center"
                                            :style="{ borderColor: '#c10e4620' }"
                                            title="Voir détails"
                                        >
                                            <Eye class="w-4 h-4" :style="{ color: '#1e002f' }" />
                                        </button>
                                    </td>
                                </tr>
                                <!-- Message si aucun client -->
                                <tr v-if="filteredusers.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <Users class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                                        <p class="text-gray-500 text-lg mb-2">Aucun client trouvé</p>
                                        <p class="text-gray-400 text-sm">
                                            {{ searchQuery ? 'Aucun résultat pour votre recherche' : 'La liste des clients est vide' }}
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal / Popup pour les détails du client -->
            <div v-if="showModal && selecteduser" class="flex items-center justify-center fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

                <!-- Modal content -->
                <div class="bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full">
                    <!-- Header modal -->
                    <div class="flex items-center justify-between p-6 border-b" :style="{ borderColor: '#c10e4620' }">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center"
                                :style="{ background: 'linear-gradient(135deg, #c10e4610 0%, #1e002f10 100%)' }"
                            >
                                <img v-if="selecteduser.image" :src="`/storage/${selecteduser.image}`" alt="Avatar" class="w-full h-full rounded-xl object-cover">
                                <User v-else class="w-6 h-6" :style="{ color: '#c10e46' }" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold" :style="{ color: '#1e002f' }">{{ selecteduser.name }}</h3>
                                <span class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center"
                                        :class="getVerificationRole(selecteduser).bg"
                                    >
                                        <span :class="getVerificationRole(selecteduser).color">{{ getVerificationRole(selecteduser).label }}</span>
                                </span>
                            </div>
                        </div>
                        <button
                            @click="closeModal"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <X class="w-5 h-5 text-gray-500" />
                        </button>
                    </div>

                    <!-- Corps modal -->
                    <div class="p-6 space-y-6">
                        <!-- Statut et informations rapides -->
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg" :class="getVerificationStatus(selecteduser).bg">
                                <component :is="getVerificationStatus(selecteduser).icon" class="w-4 h-4" :class="getVerificationStatus(selecteduser).color" />
                                <span class="text-sm font-medium" :class="getVerificationStatus(selecteduser).color">
                                    {{ getVerificationStatus(selecteduser).label }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100">
                                <Calendar class="w-4 h-4 text-gray-600" />
                                <span class="text-sm text-gray-600">Inscrit le: {{ formatDate(selecteduser.created_at) }}</span>
                            </div>
                        </div>

                        <!-- Grille d'information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Colonne gauche : Informations personnelles -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Informations personnelles</h4>

                                <div class="space-y-3">
                                    <div class="flex items-start space-x-3">
                                        <User class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Nom complet</p>
                                            <p class="text-sm font-medium" :style="{ color: '#1e002f' }">{{ selecteduser.name }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <Mail class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Email</p>
                                            <p class="text-sm" :style="{ color: '#1e002f' }">{{ selecteduser.email }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <Phone class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Téléphone</p>
                                            <p class="text-sm" :style="{ color: '#1e002f' }">{{ selecteduser.phone || 'Non renseigné' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne droite : Informations professionnelles -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-500">Informations professionnelles</h4>

                                <div class="space-y-3">
                                    <div v-if="selecteduser.address" class="flex items-start space-x-3">
                                        <MapPin class="w-5 h-5 text-gray-400 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-gray-500">Adresse</p>
                                            <p class="text-sm" :style="{ color: '#1e002f' }">{{ selecteduser.address }}</p>
                                        </div>
                                    </div>
                                    <div v-if="!selecteduser.address" class="text-sm text-gray-400 italic">
                                        Aucune information professionnelle renseignée
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
