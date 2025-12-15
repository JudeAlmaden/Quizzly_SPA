<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    quiz: Object,
});

const page = usePage();
page.props.breadcrumbs = ref([
    { label: 'Dashboard', url: route('dashboard') },
    { label: props.quiz.name, url: null },
]).value;
</script>

<template>
    <Head :title="quiz.name + ' - Dashboard'" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Quiz Banner -->
                <div class="mb-8 bg-gradient-to-r from-purple-700 via-purple-600 to-purple-700 rounded-2xl overflow-hidden shadow-2xl border-2 border-purple-400/30">
                    <div class="p-8 relative">
                        <!-- Decorative elements could go here -->
                        <div class="text-center mb-4">
                            <h1 class="text-5xl font-black text-white mb-2 drop-shadow-lg">{{ quiz.name }}</h1>
                            <p class="text-purple-100 text-lg">{{ quiz.description || 'Ready to start your quiz!' }}</p>
                        </div>
                        <div class="flex justify-center items-center gap-4 text-white/90">
                            <span class="text-sm">Code:</span>
                            <span class="bg-purple-900/50 px-4 py-2 rounded-lg font-mono text-lg font-bold backdrop-blur-sm">
                                {{ quiz.quiz_code }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Rankings - Yellow -->
                    <Link 
                        :href="route('quiz.rankings', quiz.id)" 
                        class="group aspect-square bg-gradient-to-br from-yellow-400 to-orange-400 rounded-3xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden relative"
                    >
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-white">
                            <div class="mb-4 transform group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-center drop-shadow-lg">Rankings</h3>
                        </div>
                    </Link>

                    <!-- Questions - Red -->
                    <Link 
                        :href="route('quiz.questions', quiz.id)" 
                        class="group aspect-square bg-gradient-to-br from-red-500 to-pink-500 rounded-3xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden relative"
                    >
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-white">
                            <div class="mb-4 transform group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-center drop-shadow-lg">Questions</h3>
                        </div>
                    </Link>

                    <!-- History - Green -->
                    <Link 
                        :href="route('quiz.history', quiz.id)" 
                        class="group aspect-square bg-gradient-to-br from-green-500 to-emerald-500 rounded-3xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden relative"
                    >
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-white">
                            <div class="mb-4 transform group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-center drop-shadow-lg">Previous Answers</h3>
                        </div>
                    </Link>

                    <!-- Players/Participants - Cyan -->
                    <Link 
                        :href="route('quiz.participants', quiz.id)" 
                        class="group aspect-square bg-gradient-to-br from-cyan-500 to-blue-500 rounded-3xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden relative"
                    >
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-white">
                            <div class="mb-4 transform group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-center drop-shadow-lg">Teams</h3>
                        </div>
                    </Link>

                </div>

                <!-- Play Game Button -->
                <div class="mt-8 flex justify-center">
                    <Link 
                        :href="route('quiz.game', quiz.id)" 
                        class="group px-12 py-6 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center gap-4"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-2xl font-bold">Play Game</span>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
