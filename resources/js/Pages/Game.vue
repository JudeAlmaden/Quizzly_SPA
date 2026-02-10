<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import QuestionScreen from '@/Components/QuestionScreen.vue';
import AnswerArea from '@/Components/AnswerArea.vue';
import GameAdmin from '@/Components/GameAdmin.vue';
import GameParticipant from '@/Components/GameParticipant.vue';
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    quiz: Object,
    selectedQuestion: Object,
    categories: Array,
    remainingTime: Number,
    isAdmin: Boolean,
    hasAnswered: Boolean,
    userAnswer: [String, Number],
    userScore: Number,
});

const localUserScore = ref(props.userScore || 0);
const isAnswerSubmitted = ref(props.hasAnswered);
const currentQuestion = ref(props.selectedQuestion);
const isAcceptingAnswers = ref(props.selectedQuestion?.accepting_answers || false);
const localUserAnswer = ref(props.userAnswer || null);

watch(() => props.hasAnswered, (newVal) => {
    isAnswerSubmitted.value = newVal;
});

watch(() => props.userScore, (newVal) => {
    localUserScore.value = newVal || 0;
});

watch(() => props.userAnswer, (newVal) => {
    localUserAnswer.value = newVal;
});

watch(() => props.selectedQuestion, (newVal, oldVal) => {
    // If it's a completely new question, reset state
    if (newVal?.id !== currentQuestion.value?.id) {
        currentQuestion.value = newVal;
        isAcceptingAnswers.value = newVal?.accepting_answers || false;
        localUserAnswer.value = null; // Reset selection on new question
        // Don't reset isAnswerSubmitted here if it's just a data sync, 
        // but it's handled in the .selected listener anyway
    } else if (newVal) {
        // If it's the same question (sync update), only enable if server says so
        // but DON'T disable if we already set it to true locally via WebSocket
        if (newVal.accepting_answers) {
            isAcceptingAnswers.value = true;
        }
        currentQuestion.value = newVal;
    }
});

onMounted(() => {
     if (props.quiz?.id && window.Echo) {
        const channelName = `quiz.${props.quiz.id}`;
        
        // Clean up any existing listeners on this channel to prevent duplicates
        window.Echo.leave(channelName);

        window.Echo.channel(channelName)
            .listen('.question.selected', (e) => {
                console.log('Game: Question selected', e);
                // Update state immediately from event data
                currentQuestion.value = e.question;
                isAnswerSubmitted.value = false;
                isAcceptingAnswers.value = e.question.accepting_answers;
                localUserAnswer.value = null; // Clear answer for new question
                
                // Optional: still reload in background to sync any other server state
                // but we already have the critical question data
                router.reload({ preserveScroll: true });
            })
            .listen('.timer.started', (e) => {
                console.log('Game: Timer started', e);
                isAcceptingAnswers.value = true;
                if (currentQuestion.value) {
                    // Update the existing ref to trigger reactivity
                    currentQuestion.value = {
                        ...currentQuestion.value,
                        accepting_answers: true,
                        timer_started_at: new Date().toISOString(),
                        timer_duration: e.duration
                    };
                }
            })
            .listen('.answer.revealed', (e) => {
                console.log('Game: Answer revealed', e);
                isAcceptingAnswers.value = false;
                if (currentQuestion.value && currentQuestion.value.id === e.question.id) {
                     currentQuestion.value = {
                        ...currentQuestion.value,
                        is_revealed: true,
                        accepting_answers: false
                     };
                }
                // Refresh score when answer is revealed
                router.reload({ only: ['userScore'], preserveScroll: true });
            })
            .listen('.next.question', (e) => {
                console.log('Game: Next question event');
                currentQuestion.value = null;
                isAnswerSubmitted.value = false;
                isAcceptingAnswers.value = false;
                localUserAnswer.value = null;
            });
     }
});

onUnmounted(() => {
    if (props.quiz?.id && window.Echo) {
        window.Echo.leave(`quiz.${props.quiz.id}`);
    }
});

const submitAnswer = (answer) => {
    router.post(route('game.submitAnswer', currentQuestion.value.id), {
        answer: answer,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isAnswerSubmitted.value = true;
        }
    });
};

const onReveal = (questionId) => {
    // Optimistic update
    if (currentQuestion.value) {
        isAcceptingAnswers.value = false;
        currentQuestion.value = { 
            ...currentQuestion.value, 
            is_revealed: true, 
             accepting_answers: false 
        };
    }

    router.post(route('game.revealAnswer', questionId), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
             // Sync state just in case, though listener also handles it
            if (currentQuestion.value) {
                 currentQuestion.value.is_revealed = true;
            }
        }
    });
};

const nextQuestion = () => {
    router.post(route('game.nextQuestion', props.quiz.id), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            currentQuestion.value = null;
            isAnswerSubmitted.value = false;
            localUserAnswer.value = null;
            isAcceptingAnswers.value = false;
        }
    });
};
</script>

<template>
    <Head :title="quiz.name + ' - Game'" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-800 via-purple-800 to-purple-700 relative overflow-hidden">
        <!-- Background Animation -->
        <div class="background-animation">
            <div class="question-marks layer-1"></div>
        </div>

        <!-- Top Navigation -->
        <nav class="p-6 relative z-10">
            <Link
                :href="isAdmin ? route('quiz.play', quiz.id) : route('dashboard')"
                class="inline-flex items-center gap-2 text-white hover:text-indigo-200 transition-colors bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/10"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="font-bold">{{ isAdmin ? 'Dashboard' : 'Exit to Home' }}</span>
            </Link>
        </nav>

        <!-- Main Game Area -->
        <div class="container mx-auto px-4 pb-32 relative z-10">
            <div id="game-content" class="max-w-6xl mx-auto space-y-6">
                <!-- Question Display Area -->
                <QuestionScreen 
                    v-if="currentQuestion" 
                    :question="currentQuestion" 
                />

                <!-- Shared Answer Area -->
                <AnswerArea
                    v-if="currentQuestion"
                    :question="currentQuestion"
                    :isAdmin="isAdmin"
                    :isRevealed="!!currentQuestion.is_revealed"
                    :disabled="isAdmin || isAnswerSubmitted || !isAcceptingAnswers" 
                    :userAnswer="localUserAnswer"
                    @submit="submitAnswer"
                    class="mt-6 w-full max-w-3xl mx-auto"
                />


                
                <!-- Waiting State (When no question is selected) -->
                 <div v-if="!currentQuestion" class="flex flex-col items-center justify-center min-h-[50vh] text-center">
                    <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl shadow-xl max-w-lg w-full border border-white/10">
                        <div class="w-20 h-20 bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-2">Are You Ready?</h2>
                        <p class="text-indigo-200">Waiting for the Game Master to select the next question...</p>
                    </div>
                </div>


            </div>
        </div>

        <!-- Bottom Navbar (Fixed) -->
        <div class="fixed bottom-0 left-0 right-0 z-50">
            <GameAdmin
                v-if="isAdmin"
                :quiz="quiz"
                :selectedQuestion="currentQuestion"
                :categories="categories"
                :remainingTime="remainingTime"
                @reveal="onReveal"
            />
            <GameParticipant
                v-else
                :quiz="quiz"
                :selectedQuestion="currentQuestion"
                :userScore="localUserScore"
                :isAnswerSubmitted="isAnswerSubmitted"
                :remainingTime="remainingTime"
                @timer-ended="currentQuestion.accepting_answers = false"
            />
        </div>
    </div>
</template>

<style scoped>
/* Animated Background */
.background-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
    pointer-events: none;
}

.question-marks {
    position: absolute;
    width: 200%;
    height: 200%;
    background-image: url('/images/question-marks.png');
    background-repeat: repeat;
    background-size: 400px;
    opacity: 0.60;
}

.layer-1 {
    animation: scroll-diagonal-1 60s linear infinite;
}

@keyframes scroll-diagonal-1 {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(-50%, -50%);
    }
}
</style>
