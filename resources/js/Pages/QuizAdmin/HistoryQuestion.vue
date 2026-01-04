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

    <div class="min-h-screen bg-gradient-to-br from-indigo-800 via-purple-800 to-purple-700 py-8 px-4 relative overflow-hidden">
        <!-- Background Animation (Matches AuthenticatedLayout) -->
        <div class="background-animation">
            <div class="question-marks layer-1"></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            <!-- Header with Back Button -->
            <div class="mb-8">
                <button
                    @click="goBack"
                    class="mb-6 inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-all hover:scale-105 backdrop-blur-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </button>

                <!-- Question Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl md:text-5xl font-black text-white mb-4 drop-shadow-lg">{{ question.question_data.question_text }}</h1>
                     <div class="flex justify-center gap-4 text-sm mb-2">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white backdrop-blur-sm">{{ question.question_data.type }}</span>
                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full border border-green-500/30 backdrop-blur-sm">
                            Correct: {{ question.question_data.correct_answer }}
                        </span>
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full border border-yellow-500/30 backdrop-blur-sm">
                             {{ question.points }} Pts
                        </span>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white/5 backdrop-blur-md rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                <!-- Empty State -->
                <div v-if="history.length === 0" class="text-center py-16 px-4">
                    <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">No Answers Yet</h2>
                    <p class="text-white/70">Answers submitted by participants will appear here.</p>
                </div>

                <!-- Table -->
                <div v-else>
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-4 px-8 py-4 bg-black border-b border-white/10 text-white font-bold text-sm uppercase tracking-wide">
                        <div class="col-span-1">#</div>
                        <div class="col-span-3">Team Name</div>
                        <div class="col-span-4">Answer</div>
                        <div class="col-span-2 text-center">Points</div>
                        <div class="col-span-2 text-right">Action</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="divide-y divide-black/10">
                        <div
                            v-for="(entry, index) in history"
                            :key="entry.id"
                            class="grid grid-cols-12 gap-4 px-8 py-4 items-center transition-all hover:brightness-105"
                            :class="[
                                entry.points > question.points && entry.is_correct ? 'bg-amber-200' :
                                entry.is_correct ? 'bg-green-100' :
                                'bg-gray-100'
                            ]"
                        >
                            <!-- Index -->
                            <div class="col-span-1 font-bold text-gray-900 text-lg">
                                {{ index + 1 }}
                            </div>

                            <!-- Team Name -->
                            <div class="col-span-3 font-medium text-gray-900 text-lg overflow-hidden text-ellipsis whitespace-nowrap">
                                {{ entry.user.name }}
                            </div>

                            <!-- Answer -->
                             <div class="col-span-4">
                                <span class="block font-bold uppercase text-gray-800 text-lg">{{ entry.answer }}</span>
                                <span 
                                    class="text-xs font-bold uppercase"
                                    :class="entry.is_correct ? 'text-green-700' : 'text-red-600'"
                                >
                                    {{ entry.is_correct ? 'Correct' : 'Incorrect' }}
                                </span>
                            </div>

                            <!-- Points -->
                            <div class="col-span-2 text-center">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-bold bg-white/50 text-gray-900 shadow-sm">
                                    {{ Number(entry.points).toFixed(2) }}
                                </span>
                                <div v-if="entry.points > question.points && entry.is_correct" class="text-[10px] font-bold text-amber-700 uppercase mt-1">
                                    + Bonus
                                </div>
                            </div>

                            <!-- Action -->
                             <div class="col-span-2 flex justify-end">
                                <button
                                    @click="toggleStatus(entry.id)"
                                    class="p-2 rounded-lg bg-black/5 hover:bg-black/10 text-gray-700 transition-colors"
                                    title="Toggle Correctness"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
.min-h-screen {
    background: linear-gradient(135deg, #1a0b2e 0%, #2d1b4e 50%, #1a0b2e 100%);
}

/* Animated Background (Exact match from AuthenticatedLayout) */
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
    opacity: 0.15; /* Reduced from 0.60 to 0.15 for better contrast */
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
