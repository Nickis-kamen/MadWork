<script setup>
import Nav from '@/Components/Nav.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Phone, Mail, MapPin } from 'lucide-vue-next';

const props = defineProps({
    service: {
        type: Object,
        required: true
    },
    similarServices: {
        type: Array,
        default: () => []
    }
});

</script>

<template>
    <Head>
        <title>{{ service.title }} - {{ service.agency?.name }}</title>
    </Head>
    <Nav />
    <div class="bg-[url('@/assets/images/bg-header.jpg')] bg-cover bg-fixed bg-center pt-16">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Fil d'Ariane -->
            <div class="flex items-center gap-2 text-sm text-white mb-6">
                <Link href="/" class="hover:text-purple-600">Accueil</Link>
                <span>/</span>
                <span class="text-white">{{ service.title }}</span>
            </div>

            <div class="space-y-6">
                <!-- Section principale avec image, description et agence -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Image + Description -->
                    <div class="flex-1">
                        <div class="bg-gray-100  w-full h-full rounded-lg overflow-hidden border" :style="{ borderColor: '#c10e4620' }">
                            <div class="flex flex-col md:flex-row">
                                <!-- Image -->
                                <div class="md:w-1/3">
                                    <img
                                        :src="`/storage/${service.image}`"
                                        :alt="service.title"
                                        class="w-full h-64 md:h-full object-cover"
                                    />
                                </div>

                                <!-- Description -->
                                <div class="md:w-2/3 p-6">
                                    <h1 class="text-2xl font-bold mb-3" :style="{ color: '#1e002f' }">{{ service.title }}</h1>
                                    <p class="text-[#1e002f] mb-4">A partir de <span class="text-2xl font-bold" :style="{ color: '#c10e46' }">{{ service.price }} Ar</span></p>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ service.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Infos agence -->
                    <div class="lg:w-80 h-full">
                        <div class="bg-gray-100 rounded-lg p-6 border" :style="{ borderColor: '#c10e4620' }">
                            <h2 class="text-xl font-bold mb-4" :style="{ color: '#1e002f' }">A propos de l'agence</h2>
                            <h2 class="text-xl font-bold mb-4" :style="{ color: '#1e002f' }">{{ service.agency?.name }}</h2>

                            <!-- Coordonnées -->
                            <div class="space-y-3 mb-6">
                                <div v-if="service.agency?.address" class="flex items-start gap-3 text-gray-600">
                                    <MapPin class="w-5 h-5 mt-0.5 flex-shrink-0" :style="{ color: '#c10e46' }" />
                                    <span class="text-sm">{{ service.agency.address }}</span>
                                </div>
                                <div v-if="service.agency?.phone" class="flex items-center gap-3 text-gray-600">
                                    <Phone class="w-5 h-5 flex-shrink-0" :style="{ color: '#c10e46' }" />
                                    <span class="text-sm">{{ service.agency.phone }}</span>
                                </div>
                                <div v-if="service.agency?.email" class="flex items-center gap-3 text-gray-600">
                                    <Mail class="w-5 h-5 flex-shrink-0" :style="{ color: '#c10e46' }" />
                                    <span class="text-sm">{{ service.agency.email }}</span>
                                </div>
                            </div>

                            <!-- Boutons -->
                            <div class="space-y-3">
                                <Link
                                    :href="`/agences/${service.agency.slug}`"
                                    class="block w-full text-center px-4 py-3 rounded-lg border-2 font-medium transition-all hover:bg-gray-50"
                                    :style="{ borderColor: '#c10e4620', color: '#1e002f' }"
                                >
                                    Voir
                                </Link>
                                <Link
                                    :href="`/devis?service_id=${service.id}`"
                                    class="block w-full text-center px-4 py-3 rounded-lg font-medium transition-colors"
                                    :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)', color: 'white' }"
                                >
                                    Demander un devis
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projets réalisés -->
                <div v-if="service.service_details && service.service_details.length > 0" class="bg-gray-100 rounded-lg p-6 border" :style="{ borderColor: '#c10e4620' }">
                    <h2 class="text-xl font-bold mb-4" :style="{ color: '#1e002f' }">Projets réalisés</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div v-for="detail in service.service_details" :key="detail.id" class="bg-white border rounded-lg p-3" :style="{ borderColor: '#c10e4620' }">
                            <img
                                :src="`/storage/${detail.image}`"
                                :alt="detail.title"
                                class="w-full h-32 object-cover rounded-lg mb-2"
                            />
                            <h3 class="font-semibold text-sm" :style="{ color: '#1e002f' }">{{ detail.title }}</h3>
                            <p class="text-xs text-gray-600 line-clamp-2">{{ detail.description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services similaires -->
            <div v-if="similarServices.length > 0" class="mt-12">
                <h2 class="text-2xl text-center uppercase font-bold mb-4 text-white">Services similaires</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link
                        v-for="similar in similarServices"
                        :key="similar.id"
                        :href="`/services/${similar.slug}`"
                        class="bg-white rounded-lg overflow-hidden border hover:shadow-lg transition-all group"
                        :style="{ borderColor: '#c10e4620' }"
                    >
                        <img
                            :src="`/storage/${similar.image}`"
                            :alt="similar.title"
                            class="w-full h-32 object-cover"
                        />
                        <div class="p-4">
                            <h3 class="font-semibold mb-1 text-sm" :style="{ color: '#1e002f' }">{{ similar.title }}</h3>
                            <p class="text-base font-bold" :style="{ color: '#c10e46' }">{{ similar.price }} Ar</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
