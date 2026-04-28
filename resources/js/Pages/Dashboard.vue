<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    enrolledCourses: Array
});
</script>

<template>
    <Head title="My Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Learning Progress</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div v-for="course in enrolledCourses" :key="course.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border">
                        <h3 class="font-bold text-lg mb-2">{{ course.title }}</h3>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div class="bg-indigo-600 h-2.5 rounded-full" :style="{ width: course.progress_percent + '%' }"></div>
                        </div>
                        
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">{{ course.progress_percent }}% Complete</span>
                            <Link :href="route('lessons.show', { course: course.slug, lesson: course.lessons[0].slug })" class="text-indigo-600 font-bold">Continue</Link>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>