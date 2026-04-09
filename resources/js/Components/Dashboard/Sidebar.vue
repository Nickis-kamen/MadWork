<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
  LayoutDashboard,
  FileText,
  Folder,
  Users,
  UserCircle,
  LogOut,
  Menu,
  Building2,
  ChartColumnStacked
} from 'lucide-vue-next';
import ApplicationLogoMini from '../ApplicationLogoMini.vue';

// État
const isCollapsed = ref(false);
const isMobile = ref(false);

// Détection mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024;
  if (isMobile.value) {
    isCollapsed.value = true;
  }
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile);
});

// Récupérer l'URL actuelle
const page = usePage();
const currentRoute = computed(() => page.url);

// Authentification
const user = computed(() => usePage().props.auth.user)

const isAdmin = computed(() => user.value?.role === 'admin')
const isCustomer = computed(() => user.value?.role === 'customer')
const isAgency = computed(() => user.value?.role === 'agency')

// Définition des liens du menu
let menuItems = [];

if(isAdmin.value){
    menuItems = [
        {
            id: 'dashboard',
            name: 'Tableau de bord',
            href: '/admin/dashboard',
            icon: LayoutDashboard,
            badge: 0
        },
        {
            id: 'categories',
            name: 'Catégories',
            href: '/admin/categories',
            icon: ChartColumnStacked,
            badge: 0
        },
        {
            id: 'requests',
            name: 'Demandes Devis',
            href: '/admin/requests',
            icon: FileText,
            badge: 3
        },
        {
            id: 'projects',
            name: 'Projets Actifs',
            href: '/admin/projects',
            icon: Folder,
            badge: 0
        },
        {
            id: 'staff',
            name: 'Équipe',
            href: '/admin/staff',
            icon: Users,
            badge: 0
        },
        {
            id: 'clients',
            name: 'Clients',
            href: '/admin/customers',
            icon: UserCircle,
            badge: 0
        },
    ];

}

if(isCustomer.value){
    menuItems = [
        {
            id: 'dashboard',
            name: 'Tableau de bord',
            href: '/customer/dashboard',
            icon: LayoutDashboard,
            badge: 0
        },
        {
            id: 'devis',
            name: 'Mes Devis',
            href: '/customer/devis',
            icon: FileText,
            badge: 3
        },
        {
            id: 'projects',
            name: 'Projets Actifs',
            href: '/customer/projects',
            icon: Folder,
            badge: 0
        },
    ];
}

if(isAgency.value){
    menuItems = [
        {
            id: 'dashboard',
            name: 'Tableau de bord',
            href: '/agency/dashboard',
            icon: LayoutDashboard,
            badge: 0
        },
        {
            id: 'agence',
            name: 'Mon Agence',
            href: '/agency/my-agency',
            icon: Building2,
            badge: 0
        },
        {
            id: 'services',
            name: 'Mes Services',
            href: '/agency/my-services',
            icon: ChartColumnStacked,
            badge: 0
        },
        {
            id: 'devis',
            name: 'Mes Devis',
            href: '/agency/my-devis',
            icon: FileText,
            badge: 3
        },
        {
            id: 'projects',
            name: 'Projets Actifs',
            href: '/agency/projects',
            icon: Folder,
            badge: 0
        },
    ];
}

const role = computed(() => user.value?.role)

const isActive = (href) => {
    if (href === '/admin/dashboard') {
        return currentRoute.value === href;
    }
    return currentRoute.value === href || currentRoute.value.startsWith(href + '/');
};

const handleNavigation = () => {
  if (isMobile.value) {
    isCollapsed.value = true;
  }
};

// Toggle sidebar
const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <div class="flex h-screen" :style="{ background: 'linear-gradient(135deg, rgba(30,0,47,0.05) 0%, rgba(193,14,70,0.05) 100%)' }">
        <!-- Sidebar Admin -->
        <div :class="[
        'flex flex-col justify-between shadow-2xl transition-all duration-300 ease-in-out border-r relative overflow-hidden z-50',
        isCollapsed ? 'w-16' : 'w-64',
        isMobile ? 'fixed left-0 top-0 h-full transform transition-transform duration-300' : 'relative',
        isMobile && !isCollapsed ? 'translate-x-0' : isMobile && isCollapsed ? '-translate-x-full' : ''
        ]" :style="{ backgroundColor: '#1e002f', borderColor: 'rgba(193,14,70,0.2)' }">
        <!-- Effet de fond décoratif -->
        <div class="absolute inset-0 pointer-events-none z-40" :style="{
            background: 'linear-gradient(135deg, rgba(193,14,70,0.1) 0%, rgba(30,0,47,0.1) 50%, rgba(193,14,70,0.1) 100%)'
        }"></div>

        <div class="relative z-10 flex flex-col h-full">
            <!-- Header avec infos -->
            <div class="p-4 border-b flex-shrink-0" :style="{ borderColor: 'rgba(193,14,70,0.2)', background: 'linear-gradient(90deg, rgba(193,14,70,0.1) 0%, rgba(30,0,47,0.1) 100%)' }">
            <div class="flex items-center justify-between">
                <div v-if="!isCollapsed" class="flex items-center space-x-3 min-w-0">
                <!-- Avatar -->
                <div class="relative flex-shrink-0">
                    <Link href="/">
                        <ApplicationLogoMini />
                    </Link>
                </div>

                <!-- Info -->
                <div class="flex flex-col min-w-0 flex-1">
                    <span class="font-bold text-sm truncate" style="color: #c10e46">Espace {{ role === 'admin' ? 'Administrateur' : role === 'agency' ? 'Agence' : 'Client' }}</span>
                    <span class="text-xs font-medium truncate" style="color: rgba(255,255,255,0.7)">{{ user?.name || "Not found" }}</span>
                </div>
                </div>

                <!-- Bouton toggle -->
                <button
                @click="toggleSidebar"
                class="p-2 rounded-xl transition-all duration-200 shadow-sm border flex-shrink-0"
                :class="isCollapsed && !isMobile ? 'mx-auto' : ''"
                :style="{
                    backgroundColor: 'rgba(255,255,255,0.1)',
                    borderColor: 'rgba(193,14,70,0.3)',
                    color: '#c10e46'
                }"
                @mouseenter="$event.target.style.backgroundColor = 'rgba(193,14,70,0.2)'"
                @mouseleave="$event.target.style.backgroundColor = 'rgba(255,255,255,0.1)'"
                >
                <Menu class="w-4 h-4" :style="{ color: '#c10e46' }" />
                </button>
            </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            <Link
                v-for="item in menuItems"
                :key="item.id"
                :href="item.href"
                @click="handleNavigation"
                class="flex items-center rounded-xl transition-all duration-200 group relative overflow-hidden"
                :class="[
                isCollapsed && !isMobile ? 'justify-center px-3 py-3' : 'space-x-3 px-3 py-3 w-full'
                ]"
                :style="isActive(item.href) ? {
                background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)',
                color: 'white',
                boxShadow: '0 10px 15px -3px rgba(193,14,70,0.3)'
                } : {
                color: 'rgba(255,255,255,0.6)',
                border: '1px solid transparent'
                }"
                @mouseenter="!isActive(item.href) && ($event.currentTarget.style.backgroundColor = 'rgba(193,14,70,0.1)', $event.currentTarget.style.color = 'white', $event.currentTarget.style.borderColor = 'rgba(193,14,70,0.3)')"
                @mouseleave="!isActive(item.href) && ($event.currentTarget.style.backgroundColor = 'transparent', $event.currentTarget.style.color = 'rgba(255,255,255,0.6)', $event.currentTarget.style.borderColor = 'transparent')"
            >
                <!-- Icône avec effet de survol -->
                <div class="relative flex-shrink-0 flex items-center justify-center"
                    :class="isCollapsed && !isMobile ? 'w-5 h-5' : ''">
                <component
                    :is="item.icon"
                    class="transition-transform duration-200 group-hover:scale-110"
                    :class="isCollapsed && !isMobile ? 'w-5 h-5' : 'w-5 h-5'"
                    :style="isActive(item.href) ? { color: 'white' } : { color: 'rgba(255,255,255,0.6)' }"
                />
                </div>

                <span
                v-if="!isCollapsed || isMobile"
                class="font-medium text-sm transition-all duration-200 flex-1 truncate"
                :class="isActive(item.href) ? 'font-semibold' : ''"
                :style="{ color: isActive(item.href) ? 'white' : 'inherit' }"
                >
                {{ item.name }}
                </span>

                <span
                v-if="item.badge > 0 && (!isCollapsed || isMobile)"
                class="ml-auto text-white text-xs px-2 py-1 rounded-full flex-shrink-0"
                :style="{ backgroundColor: '#c10e46' }"
                >
                {{ item.badge }}
                </span>

                <div
                v-if="isCollapsed && !isMobile && isActive(item.href)"
                class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full animate-ping"
                :style="{ backgroundColor: '#c10e46' }"
                ></div>
            </Link>
            </nav>

            <!-- Section déconnexion -->
            <div class="relative z-10 flex-shrink-0">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="w-full flex items-center cursor-pointer transition-all duration-200 border-t group"
                    :class="isCollapsed && !isMobile ? 'justify-center px-4 py-4' : 'space-x-3 px-4 py-3'"
                    :style="{
                    borderColor: 'rgba(193,14,70,0.2)',
                    background: 'linear-gradient(90deg, rgba(193,14,70,0.1) 0%, rgba(30,0,47,0.1) 100%)'
                    }"
                >
                    <!-- Icône déconnexion -->
                    <div class="relative flex-shrink-0 flex items-center justify-center"
                        :class="isCollapsed && !isMobile ? 'w-5 h-5' : ''">
                        <LogOut
                            class="group-hover:scale-110 transition-transform duration-200"
                            :class="isCollapsed && !isMobile ? 'w-5 h-5' : 'w-5 h-5'"
                            :style="{ color: '#c10e46' }"
                        />
                    </div>

                    <!-- Texte déconnexion -->
                    <span v-if="!isCollapsed || isMobile" class="font-semibold transition-colors duration-200 truncate" :style="{ color: 'rgba(255,255,255,0.7)' }">
                        Déconnexion
                    </span>
                </Link>
            </div>
        </div>
        </div>

        <!-- Overlay mobile -->
        <div
        v-if="isMobile && !isCollapsed"
        class="fixed inset-0 z-10 lg:hidden"
        :style="{ backgroundColor: 'rgba(30,0,47,0.5)' }"
        @click="toggleSidebar"
        ></div>

        <!-- Bouton menu flottant pour mobile (quand sidebar caché) -->
        <button
        v-if="isMobile && isCollapsed"
        @click="toggleSidebar"
        class="fixed top-4 left-4 z-30 p-3 text-white rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 hover:scale-110"
        :style="{ background: 'linear-gradient(135deg, #c10e46 0%, #1e002f 100%)' }"
        >
            <Menu class="w-5 h-5" />
        </button>
    </div>
</template>

<style scoped>
</style>
