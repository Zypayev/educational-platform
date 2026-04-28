<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const isSidebarOpen = ref(false);

const props = defineProps({
    course: Object,
    lesson: Object,
    allLessons: Array,
    isCompleted: Boolean,
    comments: Array,
});

const getEmbedUrl = (url) => {
    if (!url) return '';
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length == 11) ? `https://www.youtube.com/embed/${match[2]}` : url;
};

const commentForm = useForm({ body: '' });

const submitComment = () => {
    commentForm.post(route('comments.store', props.lesson.id), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
};

const markComplete = () => {
    router.post(route('lessons.complete', props.lesson.slug), {}, {
        preserveScroll: true,
        only: ['isCompleted', 'allLessons'],
    });
};
</script>

<template>
    <Head :title="lesson.title" />

    <AuthenticatedLayout>
        <!-- Mobile sidebar toggle -->
        <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="md:hidden p-4 bg-indigo-600 text-white flex items-center justify-between w-full"
        >
            <span>Course Menu</span>
            <svg v-if="!isSidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex min-h-screen">
            <!-- Sidebar: sticky, scrolls independently -->
            <aside :class="[
                isSidebarOpen ? 'block' : 'hidden',
                'md:block w-full md:w-80 bg-white border-r border-gray-200 shrink-0'
            ]">
                <div class="sticky top-0">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-bold text-gray-700">{{ course.title }}</h3>
                    </div>
                    <nav class="overflow-y-auto max-h-[calc(100vh-57px)]">
                        <Link
                            v-for="l in allLessons"
                            :key="l.id"
                            :href="route('lessons.show', [course.slug, l.slug])"
                            class="flex justify-between items-center px-4 py-3 text-sm border-b border-gray-100 hover:bg-indigo-50 transition"
                            :class="l.id === lesson.id ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700'"
                            @click="isSidebarOpen = false"
                        >
                            <span>{{ l.title }}</span>
                            <span v-if="l.is_done" class="text-green-500 text-lg">●</span>
                        </Link>
                    </nav>
                </div>
            </aside>

            <!-- Main content: flows naturally, no height constraint -->
            <div class="flex-1 bg-gray-50 p-8">
                <div class="max-w-4xl mx-auto">
                    <!-- Video -->
                    <div class="aspect-video bg-black rounded-xl mb-8 overflow-hidden shadow-lg">
                        <iframe
                            :src="getEmbedUrl(lesson.video_url)"
                            class="w-full h-full"
                            allowfullscreen
                        />
                    </div>

                    <!-- Lesson content -->
                    <div class="bg-white p-8 rounded-xl shadow-sm">
                        <h1 class="text-3xl font-extrabold mb-6">{{ lesson.title }}</h1>
                        <div class="prose prose-indigo max-w-none text-gray-700" v-html="lesson.content" />
                        <button
                            @click="markComplete"
                            :class="isCompleted ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                            class="mt-6 px-6 py-2 rounded-lg font-bold transition hover:opacity-90"
                        >
                            {{ isCompleted ? '✓ Completed' : 'Mark as Complete' }}
                        </button>
                    </div>

                    <!-- Comments -->
                    <div class="mt-12 border-t pt-8 pb-16">
                        <h3 class="text-xl font-bold mb-4">Discussion</h3>

                        <form @submit.prevent="submitComment" class="mb-8">
                            <textarea
                                v-model="commentForm.body"
                                class="w-full rounded-lg border-gray-300"
                                placeholder="Ask a question..."
                                rows="3"
                            />
                            <button
                                type="submit"
                                class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded-md disabled:opacity-50"
                                :disabled="commentForm.processing"
                            >
                                Post Comment
                            </button>
                        </form>

                        <div class="space-y-6">
                            <div v-for="comment in comments" :key="comment.id" class="flex space-x-3">
                                <div class="flex-1 bg-gray-50 p-4 rounded-lg">
                                    <div class="font-bold text-sm">{{ comment.user.name }}</div>
                                    <p class="text-gray-700 mt-1">{{ comment.body }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>