<script setup>
defineProps({
    question: Object,
});
</script>

<template>
    <div v-if="question" class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-2xl p-12 text-white min-h-[400px] flex flex-col items-center justify-center">
        <div class="w-full max-w-4xl">
            <!-- Question Type Badge -->
            <div class="flex justify-center mb-6">
                <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
                    {{ question.question_data.type }}
                </span>
            </div>

            <!-- Question Text -->
            <h2 class="text-4xl md:text-5xl font-bold text-center mb-8 leading-tight">
                {{ question.question_data.question_text }}
            </h2>

            <!-- Points Display -->
            <div class="flex justify-center gap-6 text-lg">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">{{ question.points }} pts</span>
                </div>
                <div v-if="question.bonus_points > 0" class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span class="font-semibold">+{{ question.bonus_points }} bonus</span>
                </div>
            </div>
        </div>
    </div>

    <!-- No Question Selected -->
    <div v-else class="bg-gray-100 dark:bg-gray-800 rounded-3xl shadow-lg p-12 min-h-[400px] flex flex-col items-center justify-center">
        <div class="text-center">
            <div class="bg-gray-200 dark:bg-gray-700 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">No Question Selected</h3>
            <p class="text-gray-500 dark:text-gray-400">Waiting for the host to select a question...</p>
        </div>
    </div>
</template>
