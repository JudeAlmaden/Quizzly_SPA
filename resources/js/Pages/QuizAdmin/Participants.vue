<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    quiz: Object,
    participants: Array,
});

const page = usePage();
page.props.breadcrumbs = ref([
    { label: 'Dashboard', url: route('dashboard') },
    { label: props.quiz.name, url: route('quiz.play', props.quiz.id) },
    { label: 'Players', url: null },
]).value;

const pendingParticipants = computed(() => {
    return props.participants.filter(p => p.status === 'pending');
});

const joinedParticipants = computed(() => {
    return props.participants.filter(p => p.status === 'joined');
});

const approveParticipant = (participantId) => {
    router.put(route('participants.updateStatus', participantId), {
        status: 'joined',
    });
};

const rejectParticipant = (participantId) => {
    router.put(route('participants.updateStatus', participantId), {
        status: 'rejected',
    });
};
</script>

<template>
    <Head :title="quiz.name + ' - Participants'" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- White Container -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    
                    <!-- Page Header -->
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h1 class="text-3xl font-bold text-gray-900">Participants</h1>
                        <p class="text-gray-600 mt-1">{{ quiz.name }}</p>
                    </div>

                    <!-- Pending Join Requests -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">
                                Pending Join Requests
                            </h3> <span class="px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                {{ pendingParticipants.length }}
                            </span>
                        </div>

                        <div v-if="pendingParticipants.length === 0" class="text-center py-12 bg-gray-50 rounded-xl">
                            <div class="bg-gray-200 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">No pending requests</p>
                        </div>

                        <div v-else class="space-y-3">
                            <div 
                                v-for="participant in pendingParticipants" 
                                :key="participant.id"
                                class="flex items-center justify-between p-5 border-2 border-yellow-200 rounded-xl bg-yellow-50 hover:border-yellow-300 transition-colors"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="bg-yellow-200 p-3 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ participant.user.name }}</h4>
                                        <p class="text-sm text-gray-600">{{ participant.user.email }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Requested: {{ new Date(participant.created_at).toLocaleString() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <PrimaryButton @click="approveParticipant(participant.id)" class="!bg-green-600 hover:!bg-green-700">
                                        Approve
                                    </PrimaryButton>
                                    <DangerButton @click="rejectParticipant(participant.id)">
                                        Reject
                                    </DangerButton>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Joined Participants -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">
                                Joined Participants
                            </h3>
                            <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                {{ joinedParticipants.length }}
                            </span>
                        </div>

                        <div v-if="joinedParticipants.length === 0" class="text-center py-12 bg-gray-50 rounded-xl">
                            <div class="bg-gray-200 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">No participants have joined yet</p>
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div 
                                v-for="participant in joinedParticipants" 
                                :key="participant.id"
                                class="p-4 border-2 border-green-200 rounded-xl bg-green-50 hover:shadow-lg hover:border-green-300 transition-all"
                            >
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="bg-green-200 p-2 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-gray-900">{{ participant.user.name }}</h4>
                                        <p class="text-sm text-gray-600">{{ participant.user.email }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button 
                                        @click="rejectParticipant(participant.id)"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border border-gray-200 rounded-xl p-6 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Participants</p>
                                    <p class="text-4xl font-black text-gray-900 mt-2">
                                        {{ participants.length }}
                                    </p>
                                </div>
                                <div class="bg-blue-500 p-4 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Joined</p>
                                    <p class="text-4xl font-black text-gray-900 mt-2">
                                        {{ joinedParticipants.length }}
                                    </p>
                                </div>
                                <div class="bg-green-500 p-4 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border border-yellow-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Pending</p>
                                    <p class="text-4xl font-black text-gray-900 mt-2">
                                        {{ pendingParticipants.length }}
                                    </p>
                                </div>
                                <div class="bg-yellow-500 p-4 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
