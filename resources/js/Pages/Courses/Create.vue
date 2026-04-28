<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    thumbnail: null,
});

const submit = () => {
    // Inertia automatically converts this to a multipart/form-data request
    // because it detects the File object in the thumbnail field.
    form.post(route('courses.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Course" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Course</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white p-8 rounded-xl shadow-sm space-y-6">
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Course Title</label>
                        <input 
                            v-model="form.title" 
                            type="text" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                            required
                        />
                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Description</label>
                        <textarea 
                            v-model="form.description" 
                            rows="4" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Course Thumbnail</label>
                        <input 
                            @input="form.thumbnail = $event.target.files[0]" 
                            type="file" 
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <div v-if="form.errors.thumbnail" class="text-red-500 text-xs mt-1">{{ form.errors.thumbnail }}</div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Uploading...' : 'Create Course' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>