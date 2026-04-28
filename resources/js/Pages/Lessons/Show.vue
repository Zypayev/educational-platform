<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    allLessons: Array,
    isCompleted: Boolean,
    comments: Array,
});

const getEmbedUrl = (url) => {
    if (!url) return '';
    // This converts 'watch?v=ID' to 'embed/ID'
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length == 11) ? `https://www.youtube.com/embed/${match[2]}` : url;
};

const commentForm = useForm({
    body: '',
});

const submitComment = () => {
    commentForm.post(route('comments.store', props.lesson.id), {
        preserveScroll: true, // Crucial: stops the page from jumping to the top
        onSuccess: () => commentForm.reset(),
    });
};

</script>

<template>

    <Head :title="lesson.title" />

    <AuthenticatedLayout>
        <div class="flex h-[calc(100vh-65px)] overflow-hidden">
            <div class="w-80 bg-white border-r border-gray-200 overflow-y-auto">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-700">{{ course.title }}</h3>
                </div>
                <nav>
                    <Link v-for="l in allLessons" :key="l.id" :href="route('lessons.show', [course.slug, l.slug])"
                        class="flex justify-between items-center px-4 py-3 text-sm border-b ...">
                        <span>{{ l.title }}</span>
                        <span v-if="l.is_done" class="text-green-500 text-lg">●</span>
                    </Link>
                </nav>
            </div>

            <div class="flex-1 overflow-y-auto bg-gray-50 p-8">
                <div class="max-w-4xl mx-auto">
                    <div
                        class="aspect-video bg-black rounded-xl mb-8 flex items-center justify-center text-white overflow-hidden shadow-lg">
                        <iframe :src="getEmbedUrl(lesson.video_url)" class="w-full h-96" allowfullscreen></iframe>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm">
                        <h1 class="text-3xl font-extrabold mb-6">{{ lesson.title }}</h1>
                        <div class="prose prose-indigo max-w-none text-gray-700" v-html="lesson.content"></div>
                        <button @click="router.post(route('lessons.complete', lesson.slug))"
                            :class="isCompleted ? 'bg-green-500' : 'bg-gray-200'"
                            class="mt-6 px-6 py-2 rounded-lg text-white font-bold transition">
                            {{ isCompleted ? '✓ Completed' : 'Mark as Complete' }}
                        </button>
                    </div>
                    <div class="mt-12 border-t pt-8">
                        <h3 class="text-xl font-bold mb-4">Discussion</h3>

                        <form @submit.prevent="submitComment" class="mb-8">
                            <textarea v-model="commentForm.body" class="w-full rounded-lg border-gray-300"
                                placeholder="Ask a question..."></textarea>
                            <button type="submit" class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded-md"
                                :disabled="commentForm.processing">
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