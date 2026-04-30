<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const fillDemoCredentials = (role) => {
    if (role === 'instructor') {
        form.email = 'admin@example.com';
        form.password = 'password123';
    } else {
        form.email = 'student@example.com';
        form.password = 'password123';
    }
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput 
                    id="email" 
                    type="email" 
                    class="mt-1 block w-full shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md" 
                    v-model="form.email" 
                    required 
                    autofocus
                    autocomplete="username" 
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" />
                    <Link v-if="canResetPassword" :href="route('password.request')"
                        class="text-xs text-indigo-600 hover:text-indigo-500 underline underline-offset-2">
                        Forgot password?
                    </Link>
                </div>
                <TextInput 
                    id="password" 
                    type="password" 
                    class="mt-1 block w-full shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md" 
                    v-model="form.password" 
                    required
                    autocomplete="current-password" 
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="ms-2 text-sm text-gray-600">Keep me logged in</span>
            </div>

            <div>
                <PrimaryButton 
                    class="w-full justify-center py-3 text-sm font-bold uppercase tracking-widest transition duration-150 ease-in-out" 
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                >
                    Sign In
                </PrimaryButton>
            </div>

            <div class="relative py-4">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-2 text-gray-400 font-medium">Demo Access</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button 
                    type="button" 
                    @click="fillDemoCredentials('instructor')"
                    class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group"
                >
                    <span class="text-xs font-bold text-gray-700 group-hover:text-indigo-600">Instructor</span>
                    <span class="text-[10px] text-gray-500">Full Dashboard</span>
                </button>
                
                <button 
                    type="button" 
                    @click="fillDemoCredentials('student')"
                    class="flex flex-col items-center justify-center p-3 border border-gray-300 rounded-lg bg-gray-50 hover:bg-emerald-50 hover:border-emerald-200 transition group"
                >
                    <span class="text-xs font-bold text-gray-700 group-hover:text-emerald-600">Student</span>
                    <span class="text-[10px] text-gray-500">Learning View</span>
                </button>
            </div>

            <p class="mt-8 text-center text-sm text-gray-600">
                New to the platform? 
                <Link :href="route('register')" class="font-bold text-indigo-600 hover:text-indigo-500 underline underline-offset-4">
                    Create an account
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>