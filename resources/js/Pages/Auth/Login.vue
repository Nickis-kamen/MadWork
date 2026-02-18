<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
});

const form = useForm({
    email: '',
    password: ''
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Connexion" />
        <div class="flex justify-center">
            <Link href="/">
                <ApplicationLogo class=""/>
            </Link>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Connexion</h2>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-2">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    mot de passe oublié ?
                </Link>
            </div>
            <PrimaryButton
                class=""
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Connexion
            </PrimaryButton>
            <div class="text-center mt-2">
                <p class="text-xs text-gray-600">
                    Pas de compte ?
                    <Link
                        href="/register"
                        class="text-pink-600 underline hover:text-pink-900"
                    >
                        Inscription
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
