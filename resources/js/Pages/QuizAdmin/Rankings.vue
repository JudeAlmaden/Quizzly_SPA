<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    quiz: Object,
    rankings: Array,
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="quiz.name + ' - Rankings'" />

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

                <h1 class="text-5xl font-black text-white text-center mb-2 drop-shadow-lg">Leaderboard</h1>
                <p class="text-center text-white/80 text-lg">{{ quiz.name }}</p>
            </div>

            <!-- Leaderboard Table -->
            <div class="bg-white/5 backdrop-blur-md rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                <!-- Empty State -->
                <div v-if="rankings.length === 0" class="text-center py-16 px-4">
                    <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">No Rankings Yet</h2>
                    <p class="text-white/70">Play the quiz and answer questions to see rankings here.</p>
                </div>

                <!-- Table -->
                <div v-else>
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-4 px-8 py-4 bg-black border-b border-white/10">
                        <div class="col-span-2 text-white font-bold text-sm uppercase tracking-wide">Rank</div>
                        <div class="col-span-7 text-white font-bold text-sm uppercase tracking-wide">Team name</div>
                        <div class="col-span-3 text-white font-bold text-sm uppercase tracking-wide text-right">Points</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="divide-y divide-black/10">
                        <div
                            v-for="(ranking, index) in rankings"
                            :key="ranking.user.id"
                            class="grid grid-cols-12 gap-4 px-8 py-4 transition-all hover:brightness-105"
                            :class="[
                                index === 0 ? 'bg-amber-200' : 
                                index === 1 ? 'bg-slate-300' : 
                                index === 2 ? 'bg-orange-200' : 
                                'bg-gray-100'
                            ]"
                        >
                            <!-- Rank -->
                            <div class="col-span-2 flex items-center">
                                <span class="text-xl font-bold text-gray-900">
                                    {{ index + 1 }}
                                </span>
                            </div>

                            <!-- Team Name -->
                            <div class="col-span-7 flex items-center">
                                <span class="text-lg font-medium text-gray-900">
                                    {{ ranking.user.name }}
                                </span>
                            </div>

                            <!-- Points -->
                            <div class="col-span-3 flex items-center justify-end">
                                <span class="text-lg font-bold text-gray-900">
                                    {{ Number(ranking.total_points).toFixed(2) }}
                                </span>
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
