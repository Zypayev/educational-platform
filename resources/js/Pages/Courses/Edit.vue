<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
});

const form = useForm({
    title: props.course.title,
    description: props.course.description,
    // We leave thumbnail null because we only update it if a new one is selected
    thumbnail: null,
});

const submit = () => {
    // Note: Some browsers have trouble with 'PUT' and file uploads. 
    // If you add a file upload to Edit, it's safer to use POST and add _method: 'PUT'
    form.put(route('courses.update', props.course.id), {
        forceFormData: true,
        onSuccess: () => alert('Course Updated Successfully!'),
    });
};
</script>

<template>
    <Head :title="'Edit ' + course.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Course: {{ course.title }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white p-8 rounded-xl shadow-sm space-y-6 border">
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Course Title</label>
                        <input v-model="form.title" type="text" class="mt-1 block w-full border-gray-300 rounded-md" required />
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Description</label>
                        <textarea v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <Link :href="route('courses.lessons.create', course.id)" class="text-indigo-600 hover:underline text-sm">
                            Manage Lessons
                        </Link>
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>