<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    course: Object,
    lessons: Array,
});

const form = useForm({
    title: props.course.title,
    description: props.course.description,
    price: props.course.price ?? '',
    thumbnail: null,
});

const thumbnailPreview = ref(props.course.thumbnail ? `/storage/${props.course.thumbnail}` : null);

const onThumbnailChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.thumbnail = file;
    thumbnailPreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route('courses.update', props.course.id), {
        forceFormData: true,
        onSuccess: () => alert('Course updated successfully!'),
    });
};

// Lesson editing
const editingLesson = ref(null);

const lessonForm = useForm({
    title: '',
    content: '',
    video_url: '',
    position: '',
});

const startEditLesson = (lesson) => {
    editingLesson.value = lesson.id;
    lessonForm.title = lesson.title;
    lessonForm.content = lesson.content ?? '';
    lessonForm.video_url = lesson.video_url ?? '';
    lessonForm.position = lesson.position;
};

const cancelEditLesson = () => {
    editingLesson.value = null;
    lessonForm.reset();
};

const saveLesson = (lesson) => {
    lessonForm.put(route('lessons.update', lesson.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingLesson.value = null;
            lessonForm.reset();
        },
    });
};

const deleteLesson = (lesson) => {
    if (!confirm(`Delete "${lesson.title}"?`)) return;
    router.delete(route('lessons.destroy', lesson.id), { preserveScroll: true });
};
</script>

<template>
    <Head :title="'Edit ' + course.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Course: {{ course.title }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- Course Details -->
                <form @submit.prevent="submit" class="bg-white p-8 rounded-xl shadow-sm space-y-6 border">
                    <h3 class="text-lg font-bold text-gray-800">Course Details</h3>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md"
                            required
                        />
                        <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md"
                        />
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Price ($)</label>
                        <input
                            v-model="form.price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full border-gray-300 rounded-md"
                            placeholder="0.00"
                        />
                        <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Thumbnail</label>
                        <div v-if="thumbnailPreview" class="mb-3">
                            <img :src="thumbnailPreview" class="h-32 rounded-lg object-cover" />
                        </div>
                        <input
                            type="file"
                            accept="image/*"
                            @change="onThumbnailChange"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <p v-if="form.errors.thumbnail" class="text-red-500 text-sm mt-1">{{ form.errors.thumbnail }}</p>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold hover:bg-indigo-700 disabled:opacity-50 transition"
                        >
                            Save Course
                        </button>
                    </div>
                </form>

                <!-- Lessons -->
                <div class="bg-white p-8 rounded-xl shadow-sm border">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Lessons</h3>
                        <Link
                            :href="route('courses.lessons.create', course.id)"
                            class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
                        >
                            + Add Lesson
                        </Link>
                    </div>

                    <div v-if="lessons.length === 0" class="text-gray-400 text-sm text-center py-8">
                        No lessons yet. Add your first lesson above.
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="lesson in lessons"
                            :key="lesson.id"
                            class="border border-gray-200 rounded-lg overflow-hidden"
                        >
                            <!-- Lesson row -->
                            <div v-if="editingLesson !== lesson.id" class="flex items-center justify-between px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs font-bold text-gray-400 w-6">#{{ lesson.position }}</span>
                                    <span class="text-sm font-medium text-gray-800">{{ lesson.title }}</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        @click="startEditLesson(lesson)"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 px-3 py-1 rounded border border-indigo-200 hover:bg-indigo-50 transition"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteLesson(lesson)"
                                        class="text-xs text-red-500 hover:text-red-700 px-3 py-1 rounded border border-red-200 hover:bg-red-50 transition"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Inline lesson edit form -->
                            <div v-else class="p-4 bg-gray-50 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                        <input
                                            v-model="lessonForm.title"
                                            type="text"
                                            class="w-full border-gray-300 rounded-md text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Position</label>
                                        <input
                                            v-model="lessonForm.position"
                                            type="number"
                                            min="1"
                                            class="w-full border-gray-300 rounded-md text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Video URL</label>
                                    <input
                                        v-model="lessonForm.video_url"
                                        type="url"
                                        class="w-full border-gray-300 rounded-md text-sm"
                                        placeholder="https://youtube.com/watch?v=..."
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Content</label>
                                    <textarea
                                        v-model="lessonForm.content"
                                        rows="4"
                                        class="w-full border-gray-300 rounded-md text-sm"
                                    />
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button
                                        @click="cancelEditLesson"
                                        class="text-sm px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 transition"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="saveLesson(lesson)"
                                        :disabled="lessonForm.processing"
                                        class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50 transition"
                                    >
                                        Save Lesson
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>