<script setup>
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    quiz: Object,
    question: Object,
    history: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};

const toggleStatus = (answerId) => {
    router.post(route('answer.toggle', answerId), {}, {
        preserveScroll: true,
    });
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="quiz.name + ' - Question History'" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-purple-800 relative overflow-hidden">
        <!-- Background Animation -->
        <div class="background-animation">
            <div class="question-marks layer-1"></div>
        </div>

        <div class="relative z-10">
            <!-- Header Bar (Black) -->
            <div class="bg-black/90 backdrop-blur-md shadow-lg border-b border-white/10 sticky top-0 z-20">
                <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                    <button
                        @click="goBack"
                        class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-white">Answers to this question</h1>
                    </div>

                    <div class="w-10"></div> <!-- Spacer for centering -->
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 py-8">
                <!-- Question Text -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 mb-8 border border-white/10 text-center">
                    <h2 class="text-xl md:text-2xl font-bold text-white">{{ question.question_data.question_text }}</h2>
                    <div class="mt-2 flex justify-center gap-4 text-sm">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white">{{ question.question_data.type }}</span>
                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full border border-green-500/30">
                            {{ question.question_data.correct_answer }}
                        </span>
                    </div>
                </div>

                <!-- Answers List -->
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                    <!-- Empty State -->
                    <div v-if="history.length === 0" class="p-12 text-center text-gray-500">
                        No answers submitted yet.
                    </div>

                    <!-- List container -->
                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="(entry, index) in history"
                            :key="entry.id"
                            class="flex items-center justify-between px-6 py-4 transition-colors relative"
                            :class="[
                                entry.points > question.points && entry.is_correct
                                    ? 'bg-amber-400' 
                                    : 'bg-gray-100 hover:bg-gray-200'
                            ]"
                        >
                            <!-- Rank/Team Info -->
                            <div class="flex flex-col flex-1">
                                <span class="text-xl font-medium text-gray-800">
                                    {{ entry.user.name }}
                                </span>
                                <span 
                                    v-if="entry.points > question.points && entry.is_correct" 
                                    class="text-xs font-semibold text-white mt-1"
                                >
                                    1st To answer correctly <br>
                                    <span class="text-sm">{{ Number(entry.points).toFixed(2) }}</span>
                                </span>
                            </div>

                            <!-- Answer & Status -->
                            <div class="flex-1 text-center">
                                <div class="text-lg font-bold uppercase text-gray-800">
                                    {{ entry.answer }}
                                </div>
                                <div 
                                    class="text-sm font-semibold italic mt-0.5"
                                    :class="entry.is_correct ? 'text-green-600' : 'text-red-500'"
                                >
                                    {{ entry.is_correct ? 'Correct' : 'Incorrect' }}
                                </div>
                            </div>

                            <!-- Toggle Action -->
                            <div class="flex-1 flex justify-end">
                                <button
                                    @click="toggleStatus(entry.id)"
                                    class="p-2 rounded-full hover:bg-black/10 transition-colors"
                                    title="Toggle Correctness"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animated Gradient Background matches Login.vue */
.bg-gradient-to-br {
    background: linear-gradient(135deg, #1a0b2e 0%, #2d1b4e 50%, #1a0b2e 100%);
}

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
    opacity: 0.15;
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
