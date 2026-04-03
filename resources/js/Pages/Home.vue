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

const props = defineProps({
    agencies: Array,
    services: Array,
})

const user = computed(() => usePage().props.auth.user)

const isCustomer = computed(() => user.value?.role === 'customer')

</script>
<template>
    <Head title="Accueil" />
    <Nav />
    <Header />
    <div v-if="isCustomer">
        <Post />
    </div>
    <div v-if="!user">
        <AgencyRegisterLink />
    </div>
    <About />
    <Services :services="services" />
    <Agency :agencies="agencies" />
</template>

