<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus } from 'lucide-vue-next';

const props = defineProps({
    categories: Array,
})

const form = useForm({
    name: '',
    first_name: '',
    email: '',
    phone: '',
    address: '',
    date_of_birth: '',
    password: '',
    password_confirmation: '',
    role: 'customer',
    image: null,
    is_verified: false,
    category_id: null,
});

const imagePreview = ref(null);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            imagePreview.value = null;
        },
    });
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Inscription" />

        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Inscription</h2>

        <form @submit.prevent="submit" class="space-y-3" enctype="multipart/form-data">
            <!-- Image de profil compacte -->
            <div class="flex justify-center mb-2">
                <div class="relative">
                    <div
                        class="w-20 h-20 rounded-full bg-red-200 flex items-center justify-center overflow-hidden border-3 border-white shadow-md"
                    >
                        <img
                            v-if="imagePreview"
                            :src="imagePreview"
                            alt="Preview"
                            class="w-full h-full object-cover"
                        />
                        <span v-else class="text-2xl text-white font-bold">
                            {{ form.first_name ? form.first_name.charAt(0).toUpperCase() : '?' }}
                        </span>
                    </div>
                    <label
                        for="image"
                        class="absolute bottom-0 right-0 w-6 h-6 bg-[#c10e46] rounded-full flex items-center justify-center cursor-pointer hover:bg-[#a00c38] transition-colors border-2 border-white shadow-sm"
                    >
                        <Plus class="w-3 h-3 text-white" />
                    </label>
                    <input
                        type="file"
                        id="image"
                        class="hidden"
                        accept="image/*"
                        @change="handleImageUpload"
                    />
                </div>
            </div>

            <!-- Grille dense 2 colonnes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Prénom -->
                <div>
                    <InputLabel for="first_name" value="Prénom" class="text-xs" />
                    <TextInput
                        id="first_name"
                        type="text"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.first_name"
                        required
                        autofocus
                        placeholder="Jean"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.first_name" />
                </div>

                <!-- Nom -->
                <div>
                    <InputLabel for="name" value="Nom" class="text-xs" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.name"
                        required
                        placeholder="Dupont"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.name" />
                </div>

                <!-- Email (pleine largeur) -->
                <div>
                    <InputLabel for="email" value="Email" class="text-xs" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.email"
                        required
                        placeholder="jean.dupont@email.com"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.email" />
                </div>

                <!-- Téléphone -->
                <div>
                    <InputLabel for="phone" value="Téléphone" class="text-xs" />
                    <TextInput
                        id="phone"
                        type="tel"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.phone"
                        required
                        placeholder="06 12 34 56 78"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.phone" />
                </div>

                <!-- Date de naissance -->
                <div>
                    <InputLabel for="date_of_birth" value="Date de naissance" class="text-xs" />
                    <TextInput
                        id="date_of_birth"
                        type="date"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.date_of_birth"
                        required
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.date_of_birth" />
                </div>

                <!-- Adresse -->
                <div>
                    <InputLabel for="address" value="Adresse" class="text-xs" />
                    <TextInput
                        id="address"
                        type="text"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.address"
                        required
                        placeholder="123 Rue Example, 75001 Paris"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.address" />
                </div>

                <div class="col-span-2">
                    <InputLabel for="category" value="Centre d'intérêt" class="text-xs" />
                    <select
                        id="categories"
                        class="mt-1 block w-full text-sm py-1.5 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-pink-200 focus:border-pink-500"
                        v-model="form.category_id"
                    >
                        <option value="" disabled>Choisissez un centre d'intérêt</option>
                        <option v-for="category in categories" :value="category.id"  :key="category.id">
                            {{ category.title }}
                        </option>
                    </select>
                    <InputError class="mt-1 text-xs" :message="form.errors.categories" />
                </div>

                <!-- Mot de passe -->
                <div>
                    <InputLabel for="password" value="Mot de passe" class="text-xs" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.password"
                        required
                        placeholder="••••••••"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.password" />
                </div>

                <!-- Confirmation -->
                <div>
                    <InputLabel for="password_confirmation" value="Confirmer" class="text-xs" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full text-sm py-1.5"
                        v-model="form.password_confirmation"
                        required
                        placeholder="••••••••"
                    />
                    <InputError class="mt-1 text-xs" :message="form.errors.password_confirmation" />
                </div>
            </div>
            <!-- Bouton compact -->
            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="!form.processing">S'inscrire</span>
                <span v-else class="flex items-center justify-center">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </PrimaryButton>

            <!-- Lien connexion compact -->
            <div class="text-center">
                <p class="text-xs text-gray-600">
                    Déjà un compte ?
                    <Link
                        href="/login"
                        class="text-pink-600 underline hover:text-pink-900"
                    >
                        Connexion
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
