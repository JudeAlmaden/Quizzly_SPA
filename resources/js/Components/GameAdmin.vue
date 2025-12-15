<script setup>
import { ref, computed, onMounted, onUnmounted, watch} from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AnswerArea from '@/Components/AnswerArea.vue';

const props = defineProps({
    quiz: Object,
    selectedQuestion: Object,
    categories: Array,
    remainingTime: Number,
});

const selectedCategory = ref(null);
const timerDuration = ref(30);
const countdown = ref(props.remainingTime || 0);
const timerInterval = ref(null);

// Computed phase
const gamePhase = computed(() => {
    if (!props.selectedQuestion) return 'selection';
    if (props.selectedQuestion.is_revealed) return 'revealed';
    if (props.selectedQuestion.timer_started_at) return 'timer';
    return 'ready'; // Question selected but timer not started
});

const selectQuestion = () => {
    if (!selectedCategory.value) {
        alert('Please select a category first');
        return;
    }
    
    router.post(route('game.selectQuestion', props.quiz.id), {
        category_id: selectedCategory.value,
    });
};

const startTimer = () => {
    router.post(route('game.startTimer', props.selectedQuestion.id), {
        duration: timerDuration.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            countdown.value = timerDuration.value;
            startCountdown();
        },
    });
};

const emit = defineEmits(['reveal']);

const revealAnswer = () => {
    // Emit optimistic update event logic to parent
    emit('reveal');

    router.post(route('game.revealAnswer', props.selectedQuestion.id), {
        preserveScroll: true,
    });
};

const nextQuestion = () => {
    router.post(route('game.nextQuestion', props.quiz.id));
};

const startCountdown = () => {
    if (timerInterval.value) clearInterval(timerInterval.value);
    
    timerInterval.value = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        } else {
            clearInterval(timerInterval.value);
        }
    }, 1000);
};

const formatTime = (seconds) => {
    const safeSeconds = Math.max(0, Math.floor(seconds));
    const mins = Math.floor(safeSeconds / 60);
    const secs = safeSeconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
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
                console.log('Admin: Question selected', e);
                router.reload();
            })
            .listen('.timer.started', (e) => {
                console.log('Admin: Timer started', e);
                // Immediately start timer for better UX
                countdown.value = e.duration;
                startCountdown();
                // Then reload to sync state
                router.reload();
            })
            .listen('.answer.revealed', (e) => {
                console.log('Admin: Answer revealed', e);
                // Updated via prop, no reload needed
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
    <div class="border-t-4 border-indigo-600 shadow-lg text-white" style="background-color: rgba(33, 0, 70, 0.8)">
        <div class="max-w-7xl mx-auto px-6" :class="gamePhase === 'ready' ? 'py-2' : 'py-4'">
            
            <!-- Selection Phase -->
            <div v-if="gamePhase === 'selection'" class="flex items-center justify-center gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-semibold text-white">Select Category:</label>
                    <select
                        v-model="selectedCategory"
                        class="px-4 py-2.5 h-11 border border-white/20 bg-white/10 text-white rounded-lg focus:ring-2 focus:ring-purple-500 placeholder-white/50"
                    >
                        <option :value="null">Choose a category...</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                            :disabled="category.questions_count === 0"
                        >
                            {{ category.name }} ({{ category.questions_count }} unasked)
                        </option>
                    </select>
                    <button
                        @click="selectQuestion"
                        :disabled="!selectedCategory"
                        :class="[
                            'px-6 py-2.5 h-11 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm',
                            selectedCategory
                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white'
                                : 'bg-gray-700 text-gray-400 cursor-not-allowed border border-white/10'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Select Random Question
                    </button>
                </div>
            </div>

            <!-- Ready Phase (Question selected, timer not started) - Empty state -->
            <div v-else-if="gamePhase === 'ready'" class="flex items-center justify-center gap-4">
                <!-- Timer setup is shown in the bottom bar -->
            </div>

            <!-- Timer Phase -->
            <div v-else-if="gamePhase === 'timer'" class="flex items-center justify-between gap-4">
                <!-- Left: Question Info -->
                <div class="flex items-center gap-3">
                <div class="flex items-center gap-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg px-6 py-2.5 h-11 shadow-lg border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-3xl font-bold text-white font-mono">
                            {{ formatTime(countdown) }}
                        </span>
                    </div>
                   
                    <!-- Restart Timer Button -->
                     <button
                        @click="startTimer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                <!-- Center: Timer Controls -->
                <div class="flex items-center gap-4">
                    <button
                        v-if="countdown === 0"
                        @click="revealAnswer"
                        class="px-6 py-2.5 h-11 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Reveal Answer
                    </button>
                </div>

                <!-- Right: Status & Navigation Buttons -->
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('quiz.history.question', { quiz: quiz.id, question: selectedQuestion.id })"
                        class="inline-flex items-center gap-2 px-4 py-2.5 h-11 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all hover:scale-105 shadow-md text-sm border border-indigo-500/50"
                        title="View answer history for this question"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold">Answers</span>
                    </Link>
                    <Link
                        :href="route('quiz.rankings', quiz.id)"
                        class="inline-flex items-center gap-2 px-4 py-2.5 h-11 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-all hover:scale-105 shadow-md text-sm border border-rose-500/50"
                        title="View quiz rankings"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="font-semibold">Rankings</span>
                    </Link>
                </div>
            </div>

            <!-- Revealed Phase -->
            <div v-else-if="gamePhase === 'revealed'" class="flex items-center justify-between gap-4">
                
                <!-- Left: Status -->
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 dark:bg-green-900/30 px-4 py-2 rounded-lg">
                        <span class="text-sm font-semibold text-green-700 dark:text-green-400">
                            Answer Revealed
                        </span>
                    </div>
                </div>

                <!-- Center: Next Question -->
                <div class="flex justify-center flex-1">
                    <button
                        @click="nextQuestion"
                        class="px-6 py-2.5 h-11 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105 flex items-center gap-2 text-sm"
                    >
                        <span>Next Question</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </div>

                <!-- Right: Navigation -->
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('quiz.history.question', { quiz: quiz.id, question: selectedQuestion.id })"
                        class="inline-flex items-center gap-2 px-4 py-2.5 h-11 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all hover:scale-105 shadow-md text-sm border border-indigo-500/50"
                    >
                        <span class="font-semibold">Answers</span>
                    </Link>

                    <Link
                        :href="route('quiz.rankings', quiz.id)"
                        class="inline-flex items-center gap-2 px-4 py-2.5 h-11 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-all hover:scale-105 shadow-md text-sm border border-rose-500/50"
                    >
                        <span class="font-semibold">Rankings</span>
                    </Link>
                </div>
            </div>


            <!-- Admin Label (Always visible) -->
            <div class="absolute top-2 right-6 flex items-center gap-2">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                <span class="text-xs font-semibold text-white/70">ADMIN MODE</span>
            </div>
        </div>


        <div v-if="selectedQuestion && !selectedQuestion.timer_started_at" class="border-t border-white/10 bg-black/20 px-6 py-3">
            <div class="max-w-7xl mx-auto flex items-center justify-center gap-4">
                <label class="text-sm font-medium text-white">Timer Duration:</label>
                <input
                    type="number"
                    v-model.number="timerDuration"
                    min="5"
                    max="300"
                    class="w-24 px-3 py-2.5 h-11 text-center border border-white/20 bg-white/10 text-white rounded-lg focus:ring-2 focus:ring-purple-500"
                />
                <span class="text-sm text-white/70">seconds</span>
                <button
                    @click="startTimer"
                    class="ml-4 px-6 py-2.5 h-11 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Start Timer & Accept Answers
                </button>
            </div>
        </div>
    </div>
</template>
