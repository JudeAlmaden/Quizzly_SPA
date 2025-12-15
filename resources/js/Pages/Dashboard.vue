<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

defineProps({
    quizzes: Array,
});

const creatingQuiz = ref(false);

const form = useForm({
    name: '',
    description: '',
});

const joinForm = useForm({
    code: '',
});

const openCreateModal = () => {
    creatingQuiz.value = true;
};

const closeCreateModal = () => {
    creatingQuiz.value = false;
    form.reset();
};

const createQuiz = () => {
    form.post(route('quiz.store'), {
        onSuccess: () => closeCreateModal(),
    });
};

const joinQuiz = () => {
    joinForm.post(route('quiz.join'), { // We need to create this route
        onSuccess: () => joinForm.reset(),
    });
};

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 bg-white rounded-lg p-6">
                <!-- Welcome Section -->
                <div class="mb-8 pl-1">
                    <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                        Welcome back, {{ $page.props.auth.user.name }}
                    </h2>
                    <p class="text-gray-600 mt-2">Ready to challenge yourself today?</p>
                </div>

                <!-- Action Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <!-- Create Quiz Card -->
                    <div 
                        class="relative group bg-gradient-to-br from-indigo-500 to-purple-500 overflow-hidden rounded-2xl shadow-lg border border-indigo-200 hover:shadow-xl transition-all duration-300 cursor-pointer"
                        @click="openCreateModal"
                    >
                        <div class="p-8 relative z-10 flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-white">Create Quiz</h3>
                                <p class="text-white/90 mt-2">Design a new challenge for your audience.</p>
                            </div>
                            <div class="bg-white/20 p-4 rounded-full group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Join Quiz Card -->
                    <div class="relative bg-gray-50 overflow-hidden rounded-2xl shadow-lg border border-gray-200">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Join a Quiz</h3>
                            <p class="text-gray-600 mb-6">Enter a code to jump into an existing session.</p>
                            
                            <form @submit.prevent="joinQuiz" class="flex gap-3">
                                <div class="relative flex-grow">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="quiz_code"
                                        type="text"
                                        class="block w-full pl-10 h-12 rounded-xl bg-white border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 transition-all font-mono tracking-wider placeholder:font-sans uppercase"
                                        v-model="joinForm.code"
                                        placeholder="ENTER-CODE"
                                        required
                                        maxlength="5"
                                    />
                                </div>
                                <PrimaryButton 
                                    class="h-12 px-8 rounded-xl bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 transition-all shadow-md hover:shadow-lg text-white font-semibold"
                                    :class="{ 'opacity-75 cursor-not-allowed': joinForm.processing }" 
                                    :disabled="joinForm.processing"
                                >
                                    Join
                                </PrimaryButton>
                            </form>
                             <div v-if="joinForm.errors.code" class="mt-2 text-red-500 text-sm font-medium animate-pulse">
                                {{ joinForm.errors.code }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Quizzes Section -->
                <div class="flex items-center justify-between mb-6 pl-1">
                    <h3 class="text-2xl font-bold text-gray-900">Your Quizzes</h3>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">{{ quizzes.length }} Active</span>
                </div>
                
                <div v-if="quizzes.length === 0" class="text-center py-12 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-300">
                    <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-medium text-gray-900 mb-2">No quizzes yet</h4>
                    <p class="text-gray-600 mb-6">Create your first quiz or join one to see it here.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="quiz in quizzes" 
                        :key="quiz.id" 
                        class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 flex flex-col overflow-hidden"
                    >
                        <div class="p-6 flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span v-if="quiz.creator_id === $page.props.auth.user.id" class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700 mb-2">
                                        CREATOR
                                    </span>
                                    <span v-else-if="quiz.participants && quiz.participants.length > 0 && quiz.participants[0].status === 'pending'" class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700 mb-2">
                                        PENDING APPROVAL
                                    </span>
                                    <span v-else-if="quiz.participants && quiz.participants.length > 0 && quiz.participants[0].status === 'joined'" class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 mb-2">
                                        PARTICIPANT
                                    </span>
                                    <span v-else class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 mb-2">
                                        UNKNOWN
                                    </span>
                                    <h4 class="text-xl font-bold text-gray-900 line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ quiz.name }}</h4>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Code</span>
                                    <span class="font-mono text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded-md select-all">
                                        {{ quiz.quiz_code }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed">
                                {{ quiz.description || 'No description provided for this quiz.' }}
                            </p>
                        </div>
                        
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                            <div class="text-xs text-gray-500 font-medium">
                                {{ new Date(quiz.created_at).toLocaleDateString() }}
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('quiz.play', quiz.id)"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Open
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Quiz Modal -->
        <Modal :show="creatingQuiz" @close="closeCreateModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Create New Quiz
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Give your quiz a name and description to get started.
                </p>

                <div class="mt-6">
                    <InputLabel for="name" value="Quiz Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-6">
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        v-model="form.description"
                        rows="3"
                    ></textarea>
                     <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeCreateModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="createQuiz"
                    >
                        Create Quiz
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
