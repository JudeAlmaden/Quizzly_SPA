<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AnswerArea from '@/Components/AnswerArea.vue';

const emit = defineEmits(['timer-ended']);

const props = defineProps({
    quiz: Object,
    selectedQuestion: Object,
    userScore: {
        type: Number,
        default: 0,
    },
    isAnswerSubmitted: {
        type: Boolean,
        default: false,
    },
    remainingTime: Number,
});

const countdown = ref(props.remainingTime || 0);
const timerInterval = ref(null);

const gamePhase = computed(() => {
    if (!props.selectedQuestion) return 'waiting';
    if (props.selectedQuestion.is_revealed) return 'revealed';
    if (props.isAnswerSubmitted) return 'submitted';
    if (props.selectedQuestion.accepting_answers) return 'answering';
    return 'ready'; // Question selected but not yet answering
});

const formatTime = (seconds) => {
    const safeSeconds = Math.max(0, Math.floor(seconds));
    const mins = Math.floor(safeSeconds / 60);
    const secs = safeSeconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const startCountdown = () => {
    if (timerInterval.value) clearInterval(timerInterval.value);
    
    timerInterval.value = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        } else {
            clearInterval(timerInterval.value);
            emit('timer-ended');
        }
    }, 1000);
};



// Watch for prop changes from Inertia reloads
watch(() => props.remainingTime, (newVal) => {
    if (newVal !== undefined) {
        countdown.value = Math.floor(newVal);
        if (newVal > 0) startCountdown();
    }
});

onMounted(() => {
    if (props.remainingTime && props.remainingTime > 0) {
        countdown.value = Math.floor(props.remainingTime);
        startCountdown();
    }
    if (window.Echo) {
        window.Echo.channel(`quiz.${props.quiz.id}`)
            .listen('.question.selected', (e) => {
                console.log('Participant: Question selected', e);
                router.reload();
            })
            .listen('.timer.started', (e) => {
                console.log('Participant: Timer started', e);
                // Immediately start user timer for better UX
                countdown.value = e.duration;
                startCountdown();
                // Then reload to sync state
                router.reload();
            })
            .listen('.answer.revealed', (e) => {
                console.log('Participant: Answer revealed', e);
                // Updated via prop from Game.vue, no reload needed
            });
    }
});

onUnmounted(() => {
    if (timerInterval.value) clearInterval(timerInterval.value);
    if (window.Echo) {
        window.Echo.leave(`quiz.${props.quiz.id}`);
    }
});
</script>

<template>
    <!-- choices -->
     
    <div class="border-t-4 border-purple-600 shadow-lg text-white" style="background-color: rgba(33, 0, 70, 0.8)">
        <div class="max-w-7xl mx-auto px-6 py-3">
            
            <!-- Waiting Phase -->
            <div v-if="gamePhase === 'waiting'" class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ quiz.name }}</h3>
                        <p class="text-sm text-indigo-200">Waiting for next question...</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Score -->
                    <div class="flex items-center gap-3 bg-white/10 rounded-lg px-6 py-3 border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <div>
                            <p class="text-xs text-indigo-200">Score</p>
                            <p class="text-2xl font-bold text-white">{{ userScore }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ready Phase -->
            <div v-if="gamePhase === 'ready'" class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                     <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Get Ready!</h3>
                        <p class="text-sm text-indigo-200">Question is loading...</p>
                    </div>
                </div>

                 <div class="flex items-center gap-6">
                    <!-- Score -->
                    <div class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-6 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ userScore }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Answering Phase -->
            <div v-else-if="gamePhase === 'answering'" class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-100 dark:bg-blue-900/30 px-4 py-2 rounded-lg">
                        <span class="text-sm font-semibold text-blue-700 dark:text-blue-400">
                            {{ selectedQuestion.question_data.type }}
                        </span>
                    </div>
                </div>

                <!-- Center: Timer -->
                <div class="flex items-center gap-6">
                    <div v-if="countdown > 0" class="flex items-center gap-3 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg px-6 py-3 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-3xl font-bold text-white font-mono">
                            {{ formatTime(countdown) }}
                        </span>
                    </div>



                    <!-- Score -->
                    <div class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-6 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ userScore }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Status -->
                <div class="flex items-center gap-2 bg-yellow-100 dark:bg-yellow-900/30 px-4 py-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-sm font-semibold text-yellow-700 dark:text-yellow-400">Answer Now!</span>
                </div>
            </div>

            <!-- Submitted Phase -->
            <div v-else-if="gamePhase === 'submitted'" class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-green-400">Answer Submitted!</h3>
                        <p class="text-sm text-indigo-200">Waiting for answer reveal...</p>
                    </div>
                </div>

                <!-- Timer if still running -->
                <div class="flex items-center gap-6">
                    <div v-if="countdown > 0" class="flex items-center gap-3 bg-white/10 rounded-lg px-6 py-3 border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-2xl font-bold text-white font-mono">
                            {{ formatTime(countdown) }}
                        </span>
                    </div>

                    <!-- Score -->
                    <div class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-6 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ userScore }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revealed Phase -->
            <div v-else-if="gamePhase === 'revealed'" class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-indigo-300">Answer Revealed</h3>
                        <p class="text-sm text-indigo-200">Check the correct answer above</p>
                    </div>
                </div>

                <!-- Score -->
                <div class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-6 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <div>
                        <p class="text-xs text-indigo-200">Total Score</p>
                        <p class="text-2xl font-bold text-white">{{ userScore }}</p>
                    </div>
                </div>
            </div>

            <!-- Connection Status (Always visible) -->
            <div class="absolute top-2 right-6 flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-xs font-semibold text-white/50">CONNECTED</span>
            </div>
        </div>
    </div>
</template>
