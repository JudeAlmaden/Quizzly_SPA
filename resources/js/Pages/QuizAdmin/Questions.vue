<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    quiz: Object,
    categories: Array,
});

const page = usePage();
page.props.breadcrumbs = ref([
    { label: 'Dashboard', url: route('dashboard') },
    { label: props.quiz.name, url: route('quiz.play', props.quiz.id) },
    { label: 'Questions', url: null },
]).value;

// Category Management
const showingCategoryModal = ref(false);
const categoryForm = useForm({
    name: '',
});

const openCategoryModal = () => {
    showingCategoryModal.value = true;
};

const closeCategoryModal = () => {
    showingCategoryModal.value = false;
    categoryForm.reset();
};

const createCategory = () => {
    categoryForm.post(route('categories.store', props.quiz.id), {
        onSuccess: () => closeCategoryModal(),
    });
};

const deleteCategory = (categoryId) => {
    if (confirm('Are you sure you want to delete this category? All questions within will be deleted.')) {
        router.delete(route('categories.destroy', categoryId));
    }
};

// Question Management
const showingQuestionModal = ref(false);
const selectedCategory = ref(null);
const editingQuestion = ref(null);

const questionForm = useForm({
    type: 'MCQ',
    question_text: '',
    choices: [],
    correct_answer: '',
    points: 10,
    bonus_points: 0,
});

const openQuestionModal = (category) => {
    selectedCategory.value = category;
    editingQuestion.value = null;
    questionForm.reset();
    questionForm.type = 'MCQ';
    questionForm.points = 10;
    questionForm.bonus_points = 0;
    questionForm.choices = [
        { id: 1, text: '', is_correct: true },
        { id: 2, text: '', is_correct: false },
        { id: 3, text: '', is_correct: false },
        { id: 4, text: '', is_correct: false },
    ];
    showingQuestionModal.value = true;
};

const closeQuestionModal = () => {
    showingQuestionModal.value = false;
    questionForm.reset();
    selectedCategory.value = null;
    editingQuestion.value = null;
};

const createQuestion = () => {
    questionForm.post(route('questions.store', selectedCategory.value.id), {
        onSuccess: () => closeQuestionModal(),
    });
};

const deleteQuestion = (questionId) => {
    if (confirm('Are you sure you want to delete this question?')) {
        router.delete(route('questions.destroy', questionId));
    }
};

// Dynamic choices for MCQ
const handleTypeChange = () => {
    if (questionForm.type === 'MCQ') {
        questionForm.choices = [
            { id: 1, text: '', is_correct: false },
            { id: 2, text: '', is_correct: false },
            { id: 3, text: '', is_correct: false },
            { id: 4, text: '', is_correct: false },
        ];
    } else if (questionForm.type === 'TrueOrFalse') {
        questionForm.choices = [
            { id: 1, text: 'True', is_correct: true },
            { id: 2, text: 'False', is_correct: false },
        ];
    } else {
        questionForm.choices = [];
    }
};
</script>

<template>
    <Head :title="quiz.name + ' - Questions'" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- White Container -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    
                    <!-- Page Header -->
                    <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-200">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Questions</h1>
                            <p class="text-gray-600 mt-1">{{ quiz.name }}</p>
                        </div>
                        <PrimaryButton @click="openCategoryModal" class="bg-indigo-600 hover:bg-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Category
                        </PrimaryButton>
                    </div>

                    <!-- No Categories State -->
                    <div v-if="categories.length === 0" class="text-center py-16 bg-gray-50 rounded-xl">
                        <div class="bg-gray-200 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No categories yet</h3>
                        <p class="text-gray-500 mb-6">Create your first category to start adding questions!</p>
                        <PrimaryButton @click="openCategoryModal" class="bg-indigo-600 hover:bg-indigo-700">
                            Create First Category
                        </PrimaryButton>
                    </div>

                    <!-- Categories List -->
                    <div v-else class="space-y-6">
                        <div v-for="category in categories" :key="category.id" class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ category.name }}</h3>
                                    <div class="flex gap-2">
                                        <PrimaryButton @click="openQuestionModal(category)" class="bg-green-600 hover:bg-green-700">
                                            Add Question
                                        </PrimaryButton>
                                        <DangerButton @click="deleteCategory(category.id)">
                                            Delete Category
                                        </DangerButton>
                                    </div>
                                </div>

                                <!-- Questions List -->
                                <div v-if="category.questions && category.questions.length > 0" class="space-y-3">
                                    <div v-for="question in category.questions" :key="question.id" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-grow">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                                        {{ question.question_data.type }}
                                                    </span>
                                                    <span class="text-sm text-gray-600">
                                                        Points: {{ question.points }}
                                                    </span>
                                                    <span v-if="question.bonus_points > 0" class="text-sm text-gray-600">
                                                        Bonus: {{ question.bonus_points }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-900 mb-2 font-medium">{{ question.question_data.question_text }}</p>
                                                
                                                <!-- Display Choices -->
                                                <div v-if="question.question_data.choices && question.question_data.choices.length > 0" class="ml-4 text-sm">
                                                    <div v-for="choice in question.question_data.choices" :key="choice.id" class="flex items-center gap-2">
                                                        <span :class="choice.is_correct ? 'text-green-600 font-semibold' : 'text-gray-600'">
                                                            {{ String.fromCharCode(64 + choice.id) }}. {{ choice.text }}
                                                            <span v-if="choice.is_correct" class="text-xs">(Correct)</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Display Correct Answer for Identification -->
                                                <div v-if="question.question_data.type === 'Identification' && question.question_data.correct_answer" class="ml-4 text-sm">
                                                    <span class="text-green-600 font-semibold">
                                                        Answer: {{ question.question_data.correct_answer }}
                                                    </span>
                                                </div>
                                            </div>
                                            <DangerButton @click="deleteQuestion(question.id)" class="ml-4">
                                                Delete
                                            </DangerButton>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500 bg-white rounded-lg">
                                    No questions yet. Click "Add Question" to create one.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Create Category Modal -->
        <Modal :show="showingCategoryModal" @close="closeCategoryModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create New Category</h2>
                <div class="mt-6">
                    <InputLabel for="category_name" value="Category Name" />
                    <TextInput
                        id="category_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="categoryForm.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="categoryForm.errors.name" />
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeCategoryModal">Cancel</SecondaryButton>
                    <PrimaryButton @click="createCategory" :class="{ 'opacity-25': categoryForm.processing }" :disabled="categoryForm.processing">
                        Create
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Create/Edit Question Modal -->
        <Modal :show="showingQuestionModal" @close="closeQuestionModal" max-width="2xl">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingQuestion ? 'Edit' : 'Create' }} Question
                </h2>

                <div class="mt-6 space-y-4">
                    <!-- Question Type -->
                    <div>
                        <InputLabel for="type" value="Question Type" />
                        <select
                            id="type"
                            v-model="questionForm.type"
                            @change="handleTypeChange"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="MCQ">Multiple Choice (A-D)</option>
                            <option value="TrueOrFalse">True or False</option>
                            <option value="Identification">Identification</option>
                        </select>
                    </div>

                    <!-- Question Text -->
                    <div>
                        <InputLabel for="question_text" value="Question" />
                        <textarea
                            id="question_text"
                            v-model="questionForm.question_text"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        ></textarea>
                        <InputError class="mt-2" :message="questionForm.errors.question_text" />
                    </div>

                    <!-- MCQ Choices -->
                    <div v-if="questionForm.type === 'MCQ'">
                        <InputLabel value="Choices" />
                        <div class="space-y-2 mt-2">
                            <div v-for="(choice, index) in questionForm.choices" :key="choice.id" class="flex items-center gap-2">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ String.fromCharCode(65 + index) }}.</span>
                                <TextInput
                                    type="text"
                                    v-model="choice.text"
                                    class="flex-grow"
                                    placeholder="Enter choice text"
                                />
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        :name="'correct_choice'"
                                        v-model="choice.is_correct"
                                        :value="true"
                                        @change="questionForm.choices.forEach((c, i) => c.is_correct = i === index)"
                                        class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Correct</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- True/False Choices -->
                    <div v-if="questionForm.type === 'TrueOrFalse'">
                        <InputLabel value="Correct Answer" />
                        <div class="mt-2 space-y-2">
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    v-model="questionForm.choices[0].is_correct"
                                    :value="true"
                                    @change="questionForm.choices[1].is_correct = false"
                                    class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-gray-700 dark:text-gray-300">True</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    v-model="questionForm.choices[1].is_correct"
                                    :value="true"
                                    @change="questionForm.choices[0].is_correct = false"
                                    class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-gray-700 dark:text-gray-300">False</span>
                            </label>
                        </div>
                    </div>

                    <!-- Identification Answer -->
                    <div v-if="questionForm.type === 'Identification'">
                        <InputLabel for="correct_answer" value="Correct Answer" />
                        <TextInput
                            id="correct_answer"
                            type="text"
                            v-model="questionForm.correct_answer"
                            class="mt-1 block w-full"
                            placeholder="Enter the correct answer"
                        />
                    </div>

                    <!-- Points -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="points" value="Points" />
                            <TextInput
                                id="points"
                                type="number"
                                v-model.number="questionForm.points"
                                class="mt-1 block w-full"
                                min="0"
                            />
                        </div>
                        <div>
                            <InputLabel for="bonus_points" value="Bonus Points (First to Answer)" />
                            <TextInput
                                id="bonus_points"
                                type="number"
                                v-model.number="questionForm.bonus_points"
                                class="mt-1 block w-full"
                                min="0"
                            />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeQuestionModal">Cancel</SecondaryButton>
                    <PrimaryButton @click="createQuestion" :class="{ 'opacity-25': questionForm.processing }" :disabled="questionForm.processing">
                        {{ editingQuestion ? 'Update' : 'Create' }} Question
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
