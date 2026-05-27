<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import Nav from '../Components/Nav.vue';
import Header from '../Components/Header.vue';
import About from '../Components/About.vue';
import Agency from '../Components/Agency.vue';
import Post from '@/Components/Post.vue';
import AgencyRegisterLink from '@/Components/AgencyRegisterLink.vue';
import { computed } from 'vue';
import Services from '@/Components/Services.vue';
import Subscription from '@/Components/Subscription.vue';
import Contact from '@/Components/Contact.vue';
import Footer from '@/Components/Footer.vue';
import Project from '@/Components/Project.vue';
import Toast from '@/Components/Toastify/Toast.vue';

const props = defineProps({
    agencies: Array,
    services: Array,
    categories: Array,
    projects: Array,
})

const user = computed(() => usePage().props.auth.user)

const isCustomer = computed(() => user.value?.role === 'customer')
const isAgency = computed(() => user.value?.role === 'agency')
</script>
<template>
    <Toast />
    <Head title="Accueil" />
    <Nav />
    <Header />
    <About />
    <Subscription />
    <Project :projects="projects" />
    <Services :services="services" />
    <div v-if="!user || isCustomer">
        <Agency :agencies="agencies" />
    </div>
    <div v-if="!user">
        <AgencyRegisterLink />
    </div>
    <div v-if="isCustomer">
        <Post :categories="categories" />
    </div>
    <Contact />
    <Footer />
</template>

