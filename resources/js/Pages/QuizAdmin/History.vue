<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    quiz: Object,
    questions: Array,
});

const page = usePage();
page.props.breadcrumbs = ref([
    { label: 'Dashboard', url: route('dashboard') },
    { label: props.quiz.name, url: route('quiz.play', props.quiz.id) },
    { label: 'History', url: null },
]).value;

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};
</script>

<template>
    <Head :title="quiz.name + ' - History'" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                        <div v-if="questions.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <p class="text-lg">No questions have been asked yet.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <Link
                                v-for="question in questions"
                                :key="question.id"
                                :href="route('quiz.history.question', { quiz: quiz.id, question: question.id })"
                                class="block bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg p-6 transition-colors border-l-4"
                                :class="question.answer_histories_count > 0 ? 'border-blue-500' : 'border-gray-300'"
                            >
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ question.question_data.type }}
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Points: {{ question.points }}
                                                <span v-if="question.bonus_points > 0" class="text-yellow-600 dark:text-yellow-400">
                                                    (+{{ question.bonus_points }} bonus)
                                                </span>
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                            {{ question.question_data.question_text }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Asked: {{ formatDate(question.updated_at) }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <div class="flex items-center gap-2 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                                {{ question.answer_histories_count }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">answers</span>
                                    </div>
                                </div>
                            </Link>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
