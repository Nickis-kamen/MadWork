<script setup>
import Nav from '@/Components/Nav.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Phone, Mail, MapPin, Briefcase, FolderCheck } from 'lucide-vue-next';

const props = defineProps({
    agency: {
        type: Object,
        required: true
    },
    count_order: {
        type: Number,
        default: 0
    }
});

const services = props.agency.services || [];

const formatAmount = (amount) => {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>

<template>
    <Head>
        <title>{{ agency.name }} - Notre Agence</title>
    </Head>

    <Nav />

    <div class="bg-[url('@/assets/images/bg-header.jpg')] bg-cover bg-fixed bg-center pt-16">
        <div class="max-w-7xl mx-auto px-4 py-8">

            <!-- Fil d'Ariane -->
            <div class="flex items-center gap-2 text-sm text-white mb-6">
                <Link href="/" class="hover:text-purple-600">Accueil</Link>
                <span>/</span>
                <span class="text-white">{{ agency.name }}</span>
            </div>

            <div class="space-y-6">

                <div class="bg-gray-100 w-full rounded-lg overflow-hidden border" :style="{ borderColor: '#c10e4620' }">
                    <div class="flex flex-col md:flex-row">

                        <!-- Image agence grande -->
                        <div class="md:w-1/3">
                            <img
                                v-if="agency.image"
                                :src="`/storage/${agency.image}`"
                                :alt="agency.name"
                                class="w-full h-64 md:h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-64 md:h-full flex items-center justify-center"
                                :style="{ background: '#1e002f10', minHeight: '280px' }"
                            >
                                <Briefcase class="w-16 h-16" :style="{ color: '#1e002f30' }" />
                            </div>
                        </div>

                        <!-- Description + coordonnées -->
                        <div class="md:w-2/3 p-6 flex flex-col justify-between">
                            <div>
                                <h1 class="text-2xl font-bold mb-3" :style="{ color: '#1e002f' }"><span class="font-medium text-gray-500">Agence : </span>{{ agency.name }}</h1>

                                <!-- Badge projets réalisés -->
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white mb-4"
                                    :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }">
                                    <FolderCheck class="w-4 h-4" />
                                    {{ count_order }} projet{{ count_order !== 1 ? 's' : '' }} réalisé{{ count_order !== 1 ? 's' : '' }}
                                </div>

                                <p class="text-gray-700 leading-relaxed whitespace-pre-line mb-6">
                                    {{ agency.description || "Aucune description disponible." }}
                                </p>
                            </div>

                            <!-- Coordonnées -->
                            <div class="space-y-3">
                                <div v-if="agency.address" class="flex items-start gap-3 text-gray-600">
                                    <MapPin class="w-5 h-5 mt-0.5 flex-shrink-0" :style="{ color: '#c10e46' }" />
                                    <span class="text-sm">{{ agency.address }}</span>
                                </div>
                                <div v-if="agency.phone" class="flex items-center gap-3 text-gray-600">
                                    <Phone class="w-5 h-5 flex-shrink-0" :style="{ color: '#c10e46' }" />
                                    <span class="text-sm">{{ agency.phone }}</span>
                                </div>
                                <div v-if="agency.email" class="flex items-center gap-3 text-gray-600">
                                    <Mail class="w-5 h-5 flex-shrink-0" :style="{ color: '#c10e46' }" />
                                    <span class="text-sm">{{ agency.email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services de l'agence -->
                <div v-if="services.length > 0" class="bg-gray-100 rounded-lg p-6 border" :style="{ borderColor: '#c10e4620' }">
                    <h2 class="text-xl font-bold mb-4" :style="{ color: '#1e002f' }">Services proposés</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Link
                            v-for="service in services"
                            :key="service.id"
                            :href="`/services/${service.slug}`"
                            class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-all group"
                            :style="{ borderColor: '#c10e4620' }"
                        >
                            <img
                                v-if="service.image"
                                :src="`/storage/${service.image}`"
                                :alt="service.title"
                                class="w-full h-32 object-cover"
                            />
                            <div v-else class="w-full h-32 flex items-center justify-center bg-gray-100">
                                <Briefcase class="w-10 h-10" :style="{ color: '#1e002f30' }" />
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-sm mb-1" :style="{ color: '#1e002f' }">{{ service.title }}</h3>
                                <p class="text-xs text-gray-600 line-clamp-2 mb-2">{{ service.description }}</p>
                                <p class="text-base font-bold" :style="{ color: '#c10e46' }">{{ formatAmount(service.price) }}</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Aucun service -->
                <div v-else class="bg-gray-100 rounded-lg p-12 text-center border" :style="{ borderColor: '#c10e4620' }">
                    <Briefcase class="w-12 h-12 mx-auto mb-3" :style="{ color: '#1e002f30' }" />
                    <p class="text-gray-500">Aucun service disponible pour le moment</p>
                </div>

            </div>

            <div class="pb-12"></div>
        </div>
    </div>
</template>
