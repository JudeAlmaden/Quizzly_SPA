<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useBreadcrumbs } from '@/composables/useBreadcrumbs';

const showingNavigationDropdown = ref(false);
const { breadcrumbs } = useBreadcrumbs();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-800 via-purple-800 to-purple-700">
        <nav
            class="border-b border-purple-500/20 bg-purple-900/40 backdrop-blur-md dark:border-purple-700/30 dark:bg-purple-900/50"
        >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center text-white">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current "
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-white"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    class="text-white"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Breadcrumbs -->
            <div
                v-if="breadcrumbs && breadcrumbs.length > 0"
                class="text-white  dark:border-gray-700 text-white"
                style="background-color: rgba(33, 0, 70, 0.8)"
            >
                <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                                <svg v-if="index > 0" class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <Link
                                    v-if="crumb.url"
                                    :href="crumb.url"
                                    class="inline-flex items-center text-sm font-medium hover:text-blue-600 dark:hover:text-white"
                                >
                                    {{ crumb.label }}
                                </Link>
                                <span
                                    v-else
                                    class="inline-flex items-center text-sm font-medium  "
                                >
                                    {{ crumb.label }}
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Page Heading -->
            <header
                class="hadow dark:bg-gray-800 text-white"
                style="background-color: rgba(30, 0, 50, 0.5)"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content with Animated Background -->
            <main class="main-with-background">
                <!-- Animated Background -->
                <div class="background-animation">
                    <div class="question-marks layer-1"></div>
                </div>
                
                <!-- Content -->
                <div class="relative z-10">
                    <slot />
                </div>
            </main>
    </div>

</template>          

<style scoped>
.main-with-background {
    position: relative;
    min-height: calc(100vh - 64px); /* Subtract navbar height */
    overflow: hidden;
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
    opacity: 0.60;
}

.layer-1 {
    animation: scroll-diagonal-1 60s linear infinite;
}

.layer-2 {
    animation: scroll-diagonal-2 80s linear infinite;
    opacity: 0.1;
}

.layer-3 {
    animation: scroll-diagonal-3 100s linear infinite;
    opacity: 0.08;
}

@keyframes scroll-diagonal-1 {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(-50%, -50%);
    }
}

@keyframes scroll-diagonal-2 {
    0% {
        transform: translate(-25%, -25%) rotate(0deg);
    }
    100% {
        transform: translate(-75%, -75%) rotate(360deg);
    }
}

@keyframes scroll-diagonal-3 {
    0% {
        transform: translate(-10%, -10%);
    }
    100% {
        transform: translate(-60%, -60%);
    }
}
</style>
