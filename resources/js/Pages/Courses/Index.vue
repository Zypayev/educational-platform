<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Array
});
</script>

<template>

    <Head title="Course Catalog" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Course Catalog</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="course in courses" :key="course.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 hover:shadow-lg transition">
                        <img :src="course.thumbnail ? '/storage/' + course.thumbnail : 'https://placehold.co/600x400?text=No+Image'"
                            class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="text-xs font-bold uppercase text-indigo-600 mb-2">
                                {{ course.lessons_count }} Lessons
                            </div>
                            <h3 class="text-lg font-bold mb-2">{{ course.title }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ course.description }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-lg">
                                    {{ course.price == 0 ? 'Free' : '$' + course.price }}
                                </span>

                                <template v-if="course.lessons && course.lessons.length > 0">
                                    <Link
                                        :href="route('lessons.show', { course: course.slug, lesson: course.lessons[0].slug })"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                                        Start Learning
                                    </Link>
                                </template>

                                <template v-else>
                                    <span class="text-gray-400 text-sm italic">No lessons available</span>
                                </template>
                                <Link v-if="$page.props.auth.user && $page.props.auth.user.is_instructor"
                                    :href="route('courses.edit', course.id)"
                                    class="text-xs text-gray-500 hover:text-indigo-600 underline">
                                    Edit Course
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>