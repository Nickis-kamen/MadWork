<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

// Configuration par défaut des toasts
const defaultOptions = {
    position: toast.POSITION.TOP_RIGHT,
    autoClose: 3000,
    hideProgressBar: false,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
};

const page = usePage();

// Écouter les événements Inertia
let finish = router.on('finish', () => {
    const flash = page.props.flash;

    if (flash?.message) {
        const type = flash.type || 'success';

        // Afficher le toast selon le type
        switch(type) {
            case 'success':
                toast.success(flash.message, defaultOptions);
                break;
            case 'error':
                toast.error(flash.message, defaultOptions);
                break;
            case 'warning':
                toast.warning(flash.message, defaultOptions);
                break;
            case 'info':
                toast.info(flash.message, defaultOptions);
                break;
            default:
                toast(flash.message, defaultOptions);
        }
    }
});

// Nettoyer l'écouteur
onUnmounted(() => finish());
</script>

<template>
    <!-- Ce composant ne rend rien visuellement -->
</template>
