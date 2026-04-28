<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lessons: Array
});

const form = useForm({
    title: '',
    content: '',
    video_url: '',
});

const submit = () => {
    form.post(route('courses.lessons.store', props.course.id), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Add Lessons to: {{ course.title }}</h2>
                <Link :href="route('courses.index')" class="text-indigo-600 hover:underline">
                    ← Finish & Back to Catalog
                </Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <form @submit.prevent="submit" class="bg-white p-6 rounded-xl shadow-sm space-y-4">
                    <h3 class="font-bold border-b pb-2">New Lesson</h3>
                    <div>
                        <label class="block text-sm font-medium">Lesson Title</label>
                        <input v-model="form.title" type="text" class="w-full rounded-md border-gray-300" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Video URL (Embed)</label>
                        <input v-model="form.video_url" type="text" class="w-full rounded-md border-gray-300" placeholder="https://youtube.com/..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Text Content</label>
                        <textarea v-model="form.content" rows="4" class="w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full bg-black text-white py-2 rounded-md font-bold">
                        Save Lesson
                    </button>
                </form>

                <div class="bg-gray-100 p-6 rounded-xl">
                    <h3 class="font-bold mb-4">Curriculum Preview</h3>
                    <div v-if="lessons.length === 0" class="text-gray-500 italic">No lessons added yet.</div>
                    <ul class="space-y-2">
                        <li v-for="(lesson, index) in lessons" :key="lesson.id" class="p-3 bg-white rounded border shadow-sm flex items-center">
                            <span class="mr-3 font-bold text-gray-400">{{ index + 1 }}.</span>
                            {{ lesson.title }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>