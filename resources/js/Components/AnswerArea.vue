<script setup>
import { ref, computed , watch} from 'vue';

const props = defineProps({
    question: Object,
    disabled: {
        type: Boolean,
        default: false,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
    isRevealed: {
        type: Boolean,
        default: false,
    },
    userAnswer: [String, Number],
});

const emit = defineEmits(['submit']);

const selectedAnswer = ref(props.userAnswer || null);
const shuffledChoices = ref([]);

const questionType = computed(() => props.question?.question_data?.type || 'MCQ');

//Ensure that the selected answer is updated when the userAnswer prop changes
watch(() => props.userAnswer, (newVal) => {
    if (newVal !== undefined && newVal !== null) {
        selectedAnswer.value = newVal;
    }
}, { immediate: true });

//Ensure that the sorting of choices is random
const shuffleArray = (array) => {
    const arr = [...array];
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
};

watch(() => props.question, (newVal) => {
    if (newVal && newVal.question_data?.choices) {
        // Only shuffle if it's MCQ
        if (newVal.question_data.type === 'MCQ' || !newVal.question_data.type) {
             shuffledChoices.value = shuffleArray(newVal.question_data.choices);
        } else {
             shuffledChoices.value = [];
        }
    }
}, { immediate: true });

const submitAnswer = () => {
    if (selectedAnswer.value !== null) {
        emit('submit', selectedAnswer.value);
    }
};

const isChoiceCorrect = (text) => {
    if (!props.question?.question_data?.choices) return false;
    const choice = props.question.question_data.choices.find(c => c.text === text);
    return choice ? choice.is_correct : false;
};

// console.log(isRevealed.value);

</script>

<template>
    <div v-if="question" class="rounded-2xl shadow-lg p-8" style="background-color: rgba(33, 0, 70, 0.8)">
        <!-- Multiple Choice -->
        <div v-if="questionType === 'MCQ'" class="space-y-4">
            <h3 v-if="!isAdmin" class="text-xl font-bold text-white mb-6 col-span-2">Select your answer:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
            v-for="choice in shuffledChoices"
            :key="choice.id"
            :disabled="disabled || isAdmin"
            :class="[
                'w-full p-6 rounded-xl border-2 text-left transition-all duration-200',
                isRevealed
                ? choice.is_correct
                    ? 'border-green-500 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 ring-2 ring-green-500'
                    : 'border-red-500 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 ring-2 ring-red-500'
                : 'border-white/20 bg-white/5 hover:bg-white/10 hover:border-purple-400 text-white',
                (disabled || isAdmin) ? 'cursor-default' : 'cursor-pointer hover:shadow-md'
            ]"
            >
            <div class="flex items-center gap-4">
                <div
                :class="[
                    'w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg',
                    isRevealed
                    ? choice.is_correct
                        ? 'bg-green-500 text-white'
                        : 'bg-red-500 text-white'
                    : 'bg-white/20 text-white'
                ]"
                >
                {{ String.fromCharCode(64 + choice.id) }}
                </div>
                <span
                class="text-lg font-medium"
                :class="isRevealed
                    ? choice.is_correct
                    ? 'text-green-900 dark:text-green-100'
                    : 'text-red-900 dark:text-red-100'
                    : 'text-white'"
                >
                {{ choice.text }}
                </span>
            </div>
            </button>
            </div>
        </div>

        <!-- True or False -->
        <div v-else-if="questionType === 'TrueOrFalse'" class="grid grid-cols-2 gap-4">
             <h3 v-if="!isAdmin" class="text-xl font-bold text-white mb-6 col-span-2">Select your answer:</h3>
            <button
                @click="!isAdmin && !disabled ? selectedAnswer = 'True' : null"
                :disabled="disabled || isAdmin"
                :class="[
                    'w-full p-8 rounded-xl border-2 transition-all duration-200',
                     isRevealed && isChoiceCorrect('True')
                        ? 'border-green-500 bg-green-100 dark:bg-green-900/40 rin-2 ring-green-500'
                        : isRevealed && selectedAnswer === 'True' && !isChoiceCorrect('True')
                            ? 'border-red-500 bg-red-100 dark:bg-red-900/40 ring-2 ring-red-500'
                            : selectedAnswer === 'True'
                                ? 'border-green-600 bg-green-500/20 ring-2 ring-green-500 text-white'
                                : 'border-white/20 bg-white/5 hover:bg-white/10 hover:border-green-400 text-white',
                    (disabled || isAdmin) ? 'cursor-default' : 'cursor-pointer hover:shadow-md'
                ]"
            >
                <div class="flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="(isRevealed && isChoiceCorrect('True')) || selectedAnswer === 'True' ? (isRevealed && !isChoiceCorrect('True') ? 'text-red-600' : 'text-green-600') : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-2xl font-bold">TRUE</span>
                </div>
            </button>
            <button
                @click="!isAdmin && !disabled ? selectedAnswer = 'False' : null"
                 :disabled="disabled || isAdmin"
                :class="[
                    'w-full p-8 rounded-xl border-2 transition-all duration-200',
                    isRevealed && isChoiceCorrect('False')
                        ? 'border-green-500 bg-green-100 dark:bg-green-900/40 ring-2 ring-green-500'
                        : isRevealed && selectedAnswer === 'False' && !isChoiceCorrect('False')
                            ? 'border-red-500 bg-red-100 dark:bg-red-900/40 ring-2 ring-red-500'
                            : selectedAnswer === 'False'
                                ? 'border-red-600 bg-red-500/20 ring-2 ring-red-500 text-white'
                                : 'border-white/20 bg-white/5 hover:bg-white/10 hover:border-red-400 text-white',
                    (disabled || isAdmin) ? 'cursor-default' : 'cursor-pointer hover:shadow-md'
                ]"
            >
                <div class="flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="(isRevealed && isChoiceCorrect('False')) || selectedAnswer === 'False' ? (isRevealed && !isChoiceCorrect('False') ? 'text-red-600' : 'text-red-600') : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-2xl font-bold">FALSE</span>
                </div>
            </button>
        </div>

        <!-- Identification -->
        <div v-else-if="questionType === 'Identification'" class="space-y-4">
             <h3 v-if="!isAdmin" class="text-xl font-bold text-white mb-6">Type your answer:</h3>
             
             <!-- Correct Answer Display for Identification -->
             <div v-if="isRevealed" class="mb-4 p-4 bg-green-100 dark:bg-green-900/40 border border-green-500 rounded-lg text-center"> 
                <p class="text-sm text-green-700 dark:text-green-300 font-semibold uppercase">Correct Answer:</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ question.question_data.correct_answer }}</p>
             </div>

            <input
                type="text"
                v-model="selectedAnswer"
                :disabled="disabled || isAdmin"
                :class="[
                    'w-full p-6 text-lg text-center border-2 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all disabled:opacity-75 disabled:bg-gray-100 dark:disabled:bg-gray-800',
                    isRevealed
                        ? (String(selectedAnswer || '').trim().toLowerCase() === String(question.question_data.correct_answer || '').trim().toLowerCase()
                            ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-bold'
                            : 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 font-bold')
                        : 'border-white/20 bg-white/5 text-white focus:border-purple-500'
                ]"
                :placeholder="isAdmin ? 'Participants will type answer here...' : 'Enter your answer here...'"
                :hidden="isAdmin"
                class="w-full p-6 text-lg text-center border-2 border-white/20 bg-white/5 text-white rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition-all disabled:opacity-75 disabled:bg-white/10"
            />
        </div>

        <!-- Submit Button -->
        <div v-if="!isAdmin" class="mt-8 flex justify-center">
            <button
                @click="submitAnswer"
                :disabled="disabled || selectedAnswer === null"
                :class="[
                    'px-12 py-4 rounded-xl font-bold text-lg transition-all duration-200',
                    disabled || selectedAnswer === null
                        ? 'bg-gray-300 dark:bg-gray-700 text-gray-500 cursor-not-allowed'
                        : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg hover:shadow-xl transform hover:scale-105'
                ]"
            >
                Submit Answer
            </button>
        </div>
    </div>

    <!-- No Question State -->
    <div v-else class="rounded-2xl shadow-lg p-12 text-center" style="background-color: rgba(33, 0, 70, 0.8)">
        <p class="text-white/70 text-lg">Waiting for question...</p>
    </div>
</template>
