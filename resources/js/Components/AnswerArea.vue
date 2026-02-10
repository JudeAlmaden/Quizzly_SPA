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
    selectedAnswer.value = newVal;
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

watch(() => props.question?.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        if (props.question.question_data?.choices) {
            // Only shuffle if it's MCQ
            if (props.question.question_data.type === 'MCQ' || !props.question.question_data.type) {
                shuffledChoices.value = shuffleArray(props.question.question_data.choices);
            } else {
                shuffledChoices.value = [];
            }
        }
    } else if (!newId) {
        shuffledChoices.value = [];
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
            @click="!isAdmin && !disabled ? selectedAnswer = choice.id : null"
            :disabled="disabled || isAdmin"
            :class="[
                'w-full p-6 rounded-xl border-2 text-left transition-all duration-200',
                isRevealed
                ? choice.is_correct
                    ? 'border-green-500 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 ring-2 ring-green-500'
                    : (selectedAnswer == choice.id ? 'border-red-500 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 ring-2 ring-red-500 font-bold' : 'border-white/10 bg-white/5 text-white/50')
                : selectedAnswer == choice.id
                    ? 'border-purple-500 bg-purple-500/30 ring-4 ring-purple-500/50 text-white shadow-purple-500/20 shadow-2xl'
                    : 'border-white/20 bg-white/5 hover:bg-white/10 hover:border-purple-400 text-white',
                (disabled || isAdmin) ? 'cursor-default' : 'cursor-pointer hover:shadow-md'
            ]"
            >
            <div class="flex items-center gap-4">
                <div
                :class="[
                    'w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg transition-colors',
                    isRevealed
                    ? choice.is_correct
                        ? 'bg-green-500 text-white'
                        : (selectedAnswer == choice.id ? 'bg-red-500 text-white' : 'bg-white/10 text-white/50')
                    : selectedAnswer == choice.id
                        ? 'bg-purple-500 text-white'
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
                    : (selectedAnswer == choice.id ? 'text-red-900 dark:text-red-100' : 'text-white/50')
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
                v-for="tfChoice in question.question_data.choices"
                :key="tfChoice.id"
                @click="!isAdmin && !disabled ? selectedAnswer = tfChoice.id : null"
                :disabled="disabled || isAdmin"
                :class="[
                    'w-full p-8 rounded-xl border-2 transition-all duration-200',
                     isRevealed && tfChoice.is_correct
                        ? 'border-green-500 bg-green-100 dark:bg-green-900/40 ring-2 ring-green-500'
                        : isRevealed && selectedAnswer == tfChoice.id && !tfChoice.is_correct
                            ? 'border-red-500 bg-red-100 dark:bg-red-900/40 ring-2 ring-red-500'
                            : selectedAnswer == tfChoice.id
                                ? (tfChoice.text === 'True' 
                                    ? 'border-green-500 bg-green-500/30 ring-4 ring-green-500/50 text-white shadow-green-500/20 shadow-2xl'
                                    : 'border-red-500 bg-red-500/30 ring-4 ring-red-500/50 text-white shadow-red-500/20 shadow-2xl')
                                : 'border-white/20 bg-white/5 hover:bg-white/10 text-white',
                    (disabled || isAdmin) ? 'cursor-default' : 'cursor-pointer hover:shadow-md'
                ]"
            >
                <div class="flex items-center justify-center gap-3">
                    <svg v-if="tfChoice.text === 'True'" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="(isRevealed && tfChoice.is_correct) || selectedAnswer == tfChoice.id ? (isRevealed && !tfChoice.is_correct ? 'text-red-600' : 'text-green-500') : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="(isRevealed && tfChoice.is_correct) || selectedAnswer == tfChoice.id ? (isRevealed && !tfChoice.is_correct ? 'text-red-600' : 'text-red-500') : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-2xl font-bold uppercase">{{ tfChoice.text }}</span>
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
                    'w-full p-6 text-lg text-center border-2 rounded-xl focus:ring-2 transition-all disabled:opacity-75',
                    isRevealed
                        ? (String(selectedAnswer || '').trim().toLowerCase() === String(question.question_data.correct_answer || '').trim().toLowerCase()
                            ? 'border-green-500 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 font-bold'
                            : 'border-red-500 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 font-bold')
                        : 'border-white/20 bg-white/5 text-white focus:border-purple-500 focus:ring-purple-500'
                ]"
                :placeholder="isAdmin ? 'Participants will type answer here...' : 'Enter your answer here...'"
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
